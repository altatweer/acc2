<?php

namespace App\Http\Controllers;

use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JournalEntryController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:cancel_journal_entries')->only(['cancel']);
    }

    public function index(Request $request)
    {
        $query = JournalEntry::with('user');
        if (!auth()->user()->can('view_all_journal_entries')) {
            $query->where('created_by', auth()->id());
        }
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
        // إذا كان القيد ملغي مسبقًا
        if ($journalEntry->status === 'canceled') {
            return redirect()->back()->with('error', 'القيد ملغي بالفعل.');
        }
        // إذا كان القيد تلقائي (ناتج عن سند أو عملية آلية)
        if ($journalEntry->source_type && $journalEntry->source_type !== 'manual') {
            $journalEntry->update(['status' => 'canceled']);
            \Log::info('JournalEntryController@cancel: Auto entry canceled', [
                'id' => $journalEntry->id,
                'status' => $journalEntry->fresh()->status,
            ]);
            return redirect()->route('journal-entries.show', $journalEntry->id)
                ->with('success', 'تم إلغاء القيد التلقائي بنجاح ولن يتم إنشاء قيد عكسي.');
        }
        // تحقق إذا كان هناك قيد عكسي سابق لهذا القيد
        $existingReverse = JournalEntry::where('source_type', 'manual')
            ->where('source_id', $journalEntry->id)
            ->where('description', 'like', '%قيد عكسي%')
            ->first();
        if ($existingReverse) {
            return redirect()->route('journal-entries.show', $journalEntry->id)->with('error', 'تم إلغاء القيد وتوليد قيد عكسي مسبقًا.');
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
        
        // Add print settings for consistent styling
        $printSettings = \App\Models\PrintSetting::current();
        
        return view('journal_entries.print', compact('journalEntry', 'printSettings'));
    }

    public function createSingleCurrency()
    {
        // جلب الحسابات الحقيقية (غير المجموعات) مع ترتيب حسب الرمز
        $accounts = Account::where('is_group', 0)
            ->select('id', 'code', 'name', 'type', 'default_currency')
            ->orderBy('code')
            ->get();
            
        $currencies = \App\Models\Currency::all();
        $defaultCurrency = \App\Models\Currency::getDefaultCode();
        
        // التأكد من وجود حسابات
        if ($accounts->isEmpty()) {
            return redirect()->route('accounts.create')
                ->with('error', 'لا توجد حسابات محاسبية. يجب إنشاء حسابات أولاً لتتمكن من إنشاء القيود.');
        }
        
        return view('journal_entries.create_single_currency', compact('accounts', 'currencies', 'defaultCurrency'));
    }

    public function createMultiCurrency()
    {
        // جلب الحسابات الحقيقية (غير المجموعات) مع ترتيب حسب الرمز
        $accounts = Account::where('is_group', 0)
            ->select('id', 'code', 'name', 'type', 'default_currency')
            ->orderBy('code')
            ->get();
            
        $currencies = \App\Models\Currency::all();
        
        // التأكد من وجود حسابات
        if ($accounts->isEmpty()) {
            return redirect()->route('accounts.create')
                ->with('error', 'لا توجد حسابات محاسبية. يجب إنشاء حسابات أولاً لتتمكن من إنشاء القيود.');
        }
        
        return view('journal_entries.create_multi_currency', compact('accounts', 'currencies'));
    }
    
    public function storeSingleCurrency(Request $request)
    {
        // تسجيل البيانات المستلمة للـ debugging
        \Log::info('📥 تم استلام بيانات القيد الجديد', [
            'request_data' => $request->all(),
            'user_id' => auth()->id()
        ]);
        
        try {
            $request->validate([
                'currency' => 'required|string|max:3',
                'date' => 'required|date',
                'description' => 'required|string|max:255',
                'lines' => 'required|array|min:2',
                'lines.*.account_id' => 'required|exists:accounts,id',
                'lines.*.description' => 'nullable|string|max:255',
                'lines.*.debit' => 'nullable|numeric|min:0',
                'lines.*.credit' => 'nullable|numeric|min:0',
                'lines.*.currency' => 'required|string|max:3',
                'lines.*.exchange_rate' => 'required|numeric|min:0.0001',
            ]);
            
            \Log::info('✅ تم اجتياز validation بنجاح');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('❌ فشل في validation', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            throw $e;
        }

        DB::beginTransaction();
        try {
            // إنشاء القيد المحاسبي
            $journalEntry = JournalEntry::create([
                'date' => $request->date,
                'description' => $request->description,
                'currency' => $request->currency,
                'status' => 'approved',
                'created_by' => auth()->id(),
            ]);

            $totalDebit = 0;
            $totalCredit = 0;

            // إضافة السطور
            foreach ($request->lines as $line) {
                if ((floatval($line['debit'] ?? 0) > 0) || (floatval($line['credit'] ?? 0) > 0)) {
                    JournalEntryLine::create([
                        'journal_entry_id' => $journalEntry->id,
                        'account_id' => $line['account_id'],
                        'description' => $line['description'] ?? null,
                        'debit' => floatval($line['debit'] ?? 0),
                        'credit' => floatval($line['credit'] ?? 0),
                        'currency' => $line['currency'],
                        'exchange_rate' => floatval($line['exchange_rate'] ?? 1),
                    ]);
                    
                    $totalDebit += floatval($line['debit'] ?? 0);
                    $totalCredit += floatval($line['credit'] ?? 0);
                }
            }

            // فحص التوازن
            if (abs($totalDebit - $totalCredit) > 0.01) {
                throw new \Exception("القيد غير متوازن. المدين: $totalDebit، الدائن: $totalCredit");
            }

            DB::commit();
            
            \Log::info('🎉 تم إنشاء القيد بنجاح', [
                'journal_entry_id' => $journalEntry->id,
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit
            ]);
            
            return redirect()->route('journal-entries.show', $journalEntry)
                ->with('success', 'تم إنشاء القيد المحاسبي بنجاح');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('❌ فشل في حفظ القيد', [
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            
            return back()->withErrors(['error' => 'حدث خطأ أثناء حفظ القيد: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function storeMultiCurrency(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'description' => 'required|string|max:255',
            'lines' => 'required|array|min:2',
            'lines.*.account_id' => 'required|exists:accounts,id',
            'lines.*.description' => 'nullable|string|max:255',
            'lines.*.debit' => 'nullable|numeric|min:0',
            'lines.*.credit' => 'nullable|numeric|min:0',
            'lines.*.currency' => 'required|string|max:3',
            'lines.*.exchange_rate' => 'required|numeric|min:0.0001',
        ]);

        DB::beginTransaction();
        try {
            // إنشاء القيد المحاسبي
            $journalEntry = JournalEntry::create([
                'date' => $request->date,
                'description' => $request->description,
                'currency' => 'MIXED', // للقيود متعددة العملات
                'status' => 'approved',
                'created_by' => auth()->id(),
            ]);

            $totalDebitBase = 0;
            $totalCreditBase = 0;

            // إضافة السطور
            foreach ($request->lines as $line) {
                if ((floatval($line['debit'] ?? 0) > 0) || (floatval($line['credit'] ?? 0) > 0)) {
                    $debit = floatval($line['debit'] ?? 0);
                    $credit = floatval($line['credit'] ?? 0);
                    $exchangeRate = floatval($line['exchange_rate'] ?? 1);
                    
                    JournalEntryLine::create([
                        'journal_entry_id' => $journalEntry->id,
                        'account_id' => $line['account_id'],
                        'description' => $line['description'] ?? null,
                        'debit' => $debit,
                        'credit' => $credit,
                        'currency' => $line['currency'],
                        'exchange_rate' => $exchangeRate,
                    ]);
                    
                    // حساب القيم بالعملة الأساسية
                    $totalDebitBase += $debit * $exchangeRate;
                    $totalCreditBase += $credit * $exchangeRate;
                }
            }

            // فحص التوازن بالعملة الأساسية
            if (abs($totalDebitBase - $totalCreditBase) > 0.01) {
                throw new \Exception("القيد غير متوازن بالعملة الأساسية. المدين: $totalDebitBase، الدائن: $totalCreditBase");
            }

            DB::commit();
            return redirect()->route('journal-entries.show', $journalEntry)
                ->with('success', 'تم إنشاء القيد المحاسبي متعدد العملات بنجاح');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'حدث خطأ أثناء حفظ القيد: ' . $e->getMessage()])
                ->withInput();
        }
    }
} 