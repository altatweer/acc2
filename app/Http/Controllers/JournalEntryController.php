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
        $totalDebit = collect($validated['lines'])->sum('debit');
        $totalCredit = collect($validated['lines'])->sum('credit');
        $hasDebit = collect($validated['lines'])->where('debit', '>', 0)->count() > 0;
        $hasCredit = collect($validated['lines'])->where('credit', '>', 0)->count() > 0;
        $uniqueAccounts = collect($validated['lines'])->pluck('account_id')->unique()->count();
        if (count($validated['lines']) < 2 || !$hasDebit || !$hasCredit || $uniqueAccounts < 2) {
            return back()->withErrors(['lines'=>'يجب أن يحتوي القيد على سطرين على الأقل (مدين ودائن) ولكل منهما حساب مختلف.'])->withInput();
        }
        if (round($totalDebit,2) !== round($totalCredit,2)) {
            return back()->withErrors(['lines'=>'يجب أن يتساوى مجموع المدين مع مجموع الدائن'])->withInput();
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
        DB::transaction(function () use ($journalEntry) {
            $journalEntry->update(['status' => 'canceled']);
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
        return redirect()->route('journal-entries.show', $journalEntry)->with('success', 'تم إلغاء القيد وتوليد قيد عكسي بنجاح.');
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
} 