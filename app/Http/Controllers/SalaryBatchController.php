<?php
namespace App\Http\Controllers;

use App\Models\SalaryBatch;
use App\Models\Employee;
use App\Models\Salary;
use App\Models\SalaryPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SalaryBatchController extends Controller
{
    public function index()
    {
        $batches = SalaryBatch::orderByDesc('month')->paginate(20);
        return view('salary_batches.index', compact('batches'));
    }

    public function create()
    {
        return view('salary_batches.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'month' => 'required|date_format:Y-m',
        ]);
        // تحقق من عدم وجود كشف لنفس الشهر
        if (SalaryBatch::where('month', $validated['month'])->exists()) {
            return back()->withErrors(['تم توليد كشف رواتب لهذا الشهر بالفعل.']);
        }
        // تحقق من أن الشهر المختار انتهى بالكامل
        $selectedMonth = Carbon::createFromFormat('Y-m', $validated['month']);
        $now = Carbon::now();
        if ($selectedMonth->year > $now->year || ($selectedMonth->year == $now->year && $selectedMonth->month >= $now->month)) {
            return back()->withErrors(['لا يمكن توليد كشف رواتب للشهر الجاري أو شهر مستقبلي قبل انتهاء الشهر.']);
        }
        // جلب جميع الموظفين النشطين فقط
        $employees = Employee::where('status', 'active')->get();
        DB::beginTransaction();
        try {
            $batch = SalaryBatch::create([
                'month' => $validated['month'],
                'status' => 'pending',
                'created_by' => auth()->id(),
            ]);
            foreach ($employees as $emp) {
                $monthStart = Carbon::createFromFormat('Y-m', $validated['month'])->startOfMonth();
                $monthEnd = Carbon::createFromFormat('Y-m', $validated['month'])->endOfMonth();
                $salary = Salary::where('employee_id', $emp->id)
                    ->where('effective_from', '<=', $monthEnd->toDateString())
                    ->where(function($q) use ($monthStart) {
                        $q->whereNull('effective_to')->orWhere('effective_to', '>=', $monthStart->toDateString());
                    })
                    ->orderBy('effective_from', 'desc')
                    ->first();

                // سجل للتشخيص
                if (!$salary) {
                    \Log::warning("No salary found for employee {$emp->id} in month {$validated['month']}");
                    continue;
                }

                $gross = $salary->basic_salary;
                $totalAllowances = collect($salary->allowances)->sum('amount');
                $totalDeductions = collect($salary->deductions)->sum('amount');
                $net = $gross + $totalAllowances - $totalDeductions;
                SalaryPayment::create([
                    'salary_batch_id' => $batch->id,
                    'employee_id' => $emp->id,
                    'salary_month' => $validated['month'],
                    'gross_salary' => $gross,
                    'total_allowances' => $totalAllowances,
                    'total_deductions' => $totalDeductions,
                    'net_salary' => $net,
                    'payment_date' => $monthEnd->toDateString(),
                    'status' => 'pending',
                ]);
            }
            DB::commit();
            return redirect()->route('salary-batches.show', $batch)->with('success', 'تم توليد كشف الرواتب بنجاح.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['حدث خطأ أثناء توليد الكشف: '.$e->getMessage()]);
        }
    }

    public function show(SalaryBatch $salaryBatch)
    {
        $salaryBatch->load(['salaryPayments.employee']);
        
        // Group payments by currency
        $paymentsByCurrency = $salaryBatch->salaryPayments->groupBy(function($payment) {
            return $payment->employee->currency;
        });
        
        // Calculate totals by currency
        $totalsByCurrency = [];
        $grandTotalInDefaultCurrency = [
            'gross' => 0,
            'allowances' => 0,
            'deductions' => 0,
            'net' => 0,
        ];
        
        $defaultCurrency = \App\Models\Currency::getDefaultCode();
        
        foreach ($paymentsByCurrency as $currency => $payments) {
            $totalsByCurrency[$currency] = [
                'gross' => $payments->sum('gross_salary'),
                'allowances' => $payments->sum('total_allowances'),
                'deductions' => $payments->sum('total_deductions'),
                'net' => $payments->sum('net_salary'),
            ];
            
            // Convert to default currency for grand total
            if ($currency != $defaultCurrency) {
                $grandTotalInDefaultCurrency['gross'] += \App\Helpers\CurrencyHelper::convert(
                    $totalsByCurrency[$currency]['gross'], 
                    $currency, 
                    $defaultCurrency
                );
                $grandTotalInDefaultCurrency['allowances'] += \App\Helpers\CurrencyHelper::convert(
                    $totalsByCurrency[$currency]['allowances'], 
                    $currency, 
                    $defaultCurrency
                );
                $grandTotalInDefaultCurrency['deductions'] += \App\Helpers\CurrencyHelper::convert(
                    $totalsByCurrency[$currency]['deductions'], 
                    $currency, 
                    $defaultCurrency
                );
                $grandTotalInDefaultCurrency['net'] += \App\Helpers\CurrencyHelper::convert(
                    $totalsByCurrency[$currency]['net'], 
                    $currency, 
                    $defaultCurrency
                );
            } else {
                $grandTotalInDefaultCurrency['gross'] += $totalsByCurrency[$currency]['gross'];
                $grandTotalInDefaultCurrency['allowances'] += $totalsByCurrency[$currency]['allowances'];
                $grandTotalInDefaultCurrency['deductions'] += $totalsByCurrency[$currency]['deductions'];
                $grandTotalInDefaultCurrency['net'] += $totalsByCurrency[$currency]['net'];
            }
        }
        
        // Load all currencies for the view
        $currencies = \App\Models\Currency::all();
        
        return view('salary_batches.show', compact(
            'salaryBatch', 
            'paymentsByCurrency', 
            'totalsByCurrency', 
            'grandTotalInDefaultCurrency', 
            'defaultCurrency',
            'currencies'
        ));
    }

    public function approve(SalaryBatch $salaryBatch)
    {
        if ($salaryBatch->status !== 'pending') {
            return back()->withErrors(['لا يمكن اعتماد كشف تم اعتماده أو إغلاقه مسبقًا.']);
        }
        
        $salaryBatch->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);
        
        // Get default currency and load all currencies
        $defaultCurrency = \App\Models\Currency::getDefaultCode();
        $currencies = \App\Models\Currency::all()->keyBy('code');
        
        // Get payments and group by currency
        $payments = \App\Models\SalaryPayment::where('salary_batch_id', $salaryBatch->id)
            ->with('employee')
            ->get();
        
        $byCurrency = $payments->groupBy(function($p) { 
            return $p->employee->currency; 
        });
        
        // Create a single multi-currency journal entry
        $journalDate = Carbon::createFromFormat('Y-m', $salaryBatch->month)
            ->endOfMonth()
            ->toDateString();
        
        $journalDescription = 'قيد استحقاق رواتب شهر ' . $salaryBatch->month;
        
        // Create journal entry with the default currency
        $journal = \App\Models\JournalEntry::create([
            'date' => $journalDate,
            'description' => $journalDescription,
            'source_type' => \App\Models\SalaryBatch::class,
            'source_id' => $salaryBatch->id,
            'created_by' => auth()->id(),
            'currency' => $defaultCurrency,
            'exchange_rate' => 1,
            'is_multi_currency' => true,
            'total_debit' => 0, // Will calculate after adding lines
            'total_credit' => 0, // Will calculate after adding lines
        ]);
        
        $totalDebitInDefaultCurrency = 0;
        $totalCreditInDefaultCurrency = 0;
        
        // Add journal entry lines for each currency
        foreach ($byCurrency as $currency => $rows) {
            $expenseAccountId = \App\Models\AccountingSetting::get('salary_expense_account', $currency);
            $liabilityAccountId = \App\Models\AccountingSetting::get('employee_payables_account', $currency);
            
            if (!$expenseAccountId || !$liabilityAccountId) {
                continue;
            }
            
            $totalGross = $rows->sum('gross_salary');
            $totalAllowances = $rows->sum('total_allowances');
            $totalDeductions = $rows->sum('total_deductions');
            $totalNet = $rows->sum('net_salary');
            
            // Get exchange rate for this currency
            $exchangeRate = 1;
            if ($currency !== $defaultCurrency && isset($currencies[$currency])) {
                $exchangeRate = $currencies[$currency]->exchange_rate;
            }
            
            // Add expense line
            $expenseAmount = $totalGross + $totalAllowances;
            $journal->lines()->create([
                'account_id' => $expenseAccountId,
                'description' => 'استحقاق رواتب شهر ' . $salaryBatch->month,
                'debit' => $expenseAmount,
                'credit' => 0,
                'currency' => $currency,
                'exchange_rate' => $exchangeRate,
            ]);
            
            $totalDebitInDefaultCurrency += ($expenseAmount * $exchangeRate);
            
            // Add payable line
            $journal->lines()->create([
                'account_id' => $liabilityAccountId,
                'description' => 'ذمم مستحقة للموظفين عن رواتب شهر ' . $salaryBatch->month,
                'debit' => 0,
                'credit' => $totalNet,
                'currency' => $currency,
                'exchange_rate' => $exchangeRate,
            ]);
            
            $totalCreditInDefaultCurrency += ($totalNet * $exchangeRate);
            
            // Add deductions line if needed
            if ($totalDeductions > 0) {
                $deductionAccountId = \App\Models\AccountingSetting::get('deductions_account_id', $currency);
                if ($deductionAccountId) {
                    $journal->lines()->create([
                        'account_id' => $deductionAccountId,
                        'description' => 'خصومات رواتب شهر ' . $salaryBatch->month,
                        'debit' => 0,
                        'credit' => $totalDeductions,
                        'currency' => $currency,
                        'exchange_rate' => $exchangeRate,
                    ]);
                    
                    $totalCreditInDefaultCurrency += ($totalDeductions * $exchangeRate);
                }
            }
        }
        
        // Update journal totals
        $journal->update([
            'total_debit' => $totalDebitInDefaultCurrency,
            'total_credit' => $totalCreditInDefaultCurrency,
        ]);
        
        return back()->with('success', 'تم اعتماد كشف الرواتب بنجاح وتم توليد قيد الاستحقاق متعدد العملات.');
    }

    public function destroy(SalaryBatch $salaryBatch)
    {
        // فقط إذا كان الكشف غير معتمد
        if ($salaryBatch->status !== 'pending') {
            return back()->withErrors(['لا يمكن حذف كشف معتمد أو مغلق.']);
        }
        // حذف جميع دفعات الرواتب المرتبطة
        $salaryBatch->salaryPayments()->delete();
        $salaryBatch->delete();
        return redirect()->route('salary-batches.index')->with('success', 'تم حذف كشف الرواتب بنجاح.');
    }
} 