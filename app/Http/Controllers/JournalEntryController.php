<?php

namespace App\Http\Controllers;

use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JournalEntryController extends Controller
{
    public function index(Request $request)
    {
        $query = JournalEntry::with('user');
        if ($request->filled('date_from')) {
            $query->where('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('date', '<=', $request->date_to);
        }
        if ($request->filled('account_id')) {
            $query->whereHas('lines', function($q) use ($request) {
                $q->where('account_id', $request->account_id);
            });
        }
        if ($request->filled('user_id')) {
            $query->where('created_by', $request->user_id);
        }
        $entries = $query->latest()->paginate(20)->appends($request->all());
        $accounts = Account::all();
        return view('journal_entries.index', compact('entries', 'accounts'));
    }

    public function show(JournalEntry $journalEntry)
    {
        $journalEntry->load('lines.account', 'user');
        return view('journal_entries.show', compact('journalEntry'));
    }

    public function create()
    {
        $accounts = Account::where('is_group', 0)->get();
        return view('journal_entries.create', compact('accounts'));
    }

    public function store(Request $request)
    {
        \Log::info('==== JournalEntryController@store: REQUEST DATA ====', [
            'all_request' => $request->all(),
        ]);
        $validated = $request->validate([
            'date' => 'required|date',
            'description' => 'nullable|string|max:1000',
            'lines' => 'required|array|min:2',
            'lines.*.account_id' => 'required|exists:accounts,id',
            'lines.*.description' => 'nullable|string|max:255',
            'lines.*.debit' => 'required|numeric|min:0',
            'lines.*.credit' => 'required|numeric|min:0',
            'lines.*.currency' => 'required|string|max:3',
            'lines.*.exchange_rate' => 'required|numeric|min:0.000001',
        ]);
        \Log::info('==== JournalEntryController@store: VALIDATED DATA ====', [
            'validated' => $validated,
        ]);
        $totalDebit = collect($validated['lines'])->sum('debit');
        $totalCredit = collect($validated['lines'])->sum('credit');
        $hasDebit = collect($validated['lines'])->where('debit', '>', 0)->count() > 0;
        $hasCredit = collect($validated['lines'])->where('credit', '>', 0)->count() > 0;
        $uniqueAccounts = collect($validated['lines'])->pluck('account_id')->unique()->count();
        if (count($validated['lines']) < 2 || !$hasDebit || !$hasCredit || $uniqueAccounts < 2) {
            return back()->withErrors(['lines'=>'يجب أن يحتوي القيد على سطرين على الأقل (مدين ودائن) ولكل منهما حساب مختلف.'])->withInput();
        }
        // تحقق من مطابقة العملة مع الحساب
        foreach ($validated['lines'] as $idx => $line) {
            $account = \App\Models\Account::find($line['account_id']);
            if (!$account || $account->currency !== $line['currency']) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    "lines.$idx.account_id" => ["عملة الحساب يجب أن تطابق العملة المدخلة في السطر."]
                ]);
            }
        }
        // تحقق من توازن القيد بعد التحويل للعملة الأساسية (IQD)
        $totalDebitIQD = 0;
        $totalCreditIQD = 0;
        foreach ($validated['lines'] as $idx => $line) {
            $rate = floatval($line['exchange_rate']);
            $debit = floatval($line['debit']);
            $credit = floatval($line['credit']);

            if ($line['currency'] !== 'IQD') {
                $debit_converted = $debit * $rate;
                $credit_converted = $credit * $rate;
            } else {
                $debit_converted = $debit;
                $credit_converted = $credit;
            }

            $totalDebitIQD += $debit_converted;
            $totalCreditIQD += $credit_converted;

            \Log::info("JournalEntry MC Line $idx", [
                'debit' => $debit,
                'credit' => $credit,
                'rate' => $rate,
                'debit_converted' => $debit_converted,
                'credit_converted' => $credit_converted,
                'currency' => $line['currency'],
            ]);
        }
        \Log::info('==== JournalEntryController@store: TOTALS ====', [
            'totalDebitIQD' => $totalDebitIQD,
            'totalCreditIQD' => $totalCreditIQD,
            'lines' => $validated['lines'],
        ]);
        if (round($totalDebitIQD, 2) !== round($totalCreditIQD, 2)) {
            return back()->withErrors([
                'lines' => 'يجب أن يتساوى مجموع المدين مع مجموع الدائن بعد التحويل للعملة الأساسية (IQD)'
            ])->withInput();
        }
        DB::transaction(function() use ($validated, $totalDebit, $totalCredit) {
            $entry = JournalEntry::create([
                'date' => $validated['date'],
                'description' => $validated['description'],
                'created_by' => auth()->id(),
                'currency' => $validated['lines'][0]['currency'],
                'exchange_rate' => $validated['lines'][0]['exchange_rate'],
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
            ]);
            foreach ($validated['lines'] as $line) {
                $entry->lines()->create($line);
            }
        });
        return redirect()->route('journal-entries.index')->with('success', 'تم إنشاء القيد بنجاح.');
    }

    public function cancel(JournalEntry $journalEntry)
    {
        // فقط القيود اليدوية (بدون مصدر أو source_type = manual)
        if ($journalEntry->status === 'canceled') {
            return redirect()->back()->with('error', 'القيد ملغي بالفعل.');
        }
        if ($journalEntry->source_type && $journalEntry->source_type !== 'manual') {
            return redirect()->back()->with('error', 'لا يمكن إلغاء هذا القيد لأنه مرتبط بعملية آلية.');
        }
        // تحقق إذا كان هناك قيد عكسي سابق لهذا القيد
        $existingReverse = JournalEntry::where('source_type', 'manual')
            ->where('source_id', $journalEntry->id)
            ->where('description', 'like', '%قيد عكسي%')
            ->first();
        if ($existingReverse) {
            return redirect()->route('journal-entries.show', $journalEntry)->with('error', 'تم إلغاء القيد وتوليد قيد عكسي مسبقًا.');
        }
        DB::transaction(function () use ($journalEntry) {
            $journalEntry->update(['status' => 'canceled']);
            \Log::info('==== JournalEntryController@cancel: UPDATED STATUS ====', [
                'id' => $journalEntry->id,
                'status' => $journalEntry->fresh()->status,
            ]);
            // توليد قيد عكسي
            $reverse = $journalEntry->replicate();
            $reverse->date = now();
            $reverse->description = 'قيد عكسي لإلغاء القيد اليدوي #' . $journalEntry->id;
            $reverse->status = 'active';
            $reverse->source_type = 'manual';
            $reverse->source_id = $journalEntry->id;
            $reverse->created_by = auth()->id();
            $reverse->save();
            foreach ($journalEntry->lines as $line) {
                $reverse->lines()->create([
                    'account_id' => $line->account_id,
                    'description' => 'عكس: ' . $line->description,
                    'debit' => $line->credit,
                    'credit' => $line->debit,
                    'currency' => $line->currency,
                    'exchange_rate' => $line->exchange_rate,
                ]);
            }
        });
        return redirect()->route('journal-entries.show', $journalEntry->id)->with('success', 'تم إلغاء القيد وتوليد قيد عكسي بنجاح.');
    }

    // دالة تعرض تفاصيل القيد في نافذة منبثقة (AJAX)
    public function modal($id)
    {
        $entry = JournalEntry::with(['lines.account', 'user'])->findOrFail($id);
        return view('journal_entries.modal', compact('entry'));
    }

    /**
     * طباعة القيد المحاسبي
     */
    public function print($id)
    {
        $journalEntry = \App\Models\JournalEntry::with('lines.account', 'user')->findOrFail($id);
        return view('journal_entries.print', compact('journalEntry'));
    }

    public function createSingleCurrency()
    {
        $accounts = Account::where('is_group', 0)->get();
        $currencies = \App\Models\Currency::all();
        $defaultCurrency = \App\Models\Currency::getDefaultCode();
        return view('journal_entries.create_single_currency', compact('accounts', 'currencies', 'defaultCurrency'));
    }

    public function createMultiCurrency()
    {
        $accounts = Account::where('is_group', 0)->get();
        $currencies = \App\Models\Currency::all();
        return view('journal_entries.create_multi_currency', compact('accounts', 'currencies'));
    }
} 