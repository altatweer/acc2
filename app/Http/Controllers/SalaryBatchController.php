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
        return view('salary_batches.show', compact('salaryBatch'));
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
        // عند الاعتماد: توليد قيد الاستحقاق
        // تجميع الرواتب حسب العملة
        $payments = \App\Models\SalaryPayment::where('salary_batch_id', $salaryBatch->id)->get();
        $byCurrency = $payments->groupBy(function($p) { return $p->employee->currency; });
        foreach ($byCurrency as $currency => $rows) {
            $settings = \App\Models\AccountingSetting::where('currency', $currency)->first();
            $expenseAccountId = $settings?->expenses_account_id;
            $liabilityAccountId = $settings?->liabilities_account_id;
            if (!$expenseAccountId || !$liabilityAccountId) continue;
            $totalGross = $rows->sum('gross_salary');
            $totalAllowances = $rows->sum('total_allowances');
            $totalDeductions = $rows->sum('total_deductions');
            $totalNet = $rows->sum('net_salary');
            $lines = [
                [
                    'account_id' => $expenseAccountId,
                    'description' => 'استحقاق رواتب شهر ' . $salaryBatch->month,
                    'debit' => $totalGross + $totalAllowances,
                    'credit' => 0,
                    'currency' => $currency,
                    'exchange_rate' => 1,
                ],
                [
                    'account_id' => $liabilityAccountId,
                    'description' => 'ذمم مستحقة للموظفين عن رواتب شهر ' . $salaryBatch->month,
                    'debit' => 0,
                    'credit' => $totalNet,
                    'currency' => $currency,
                    'exchange_rate' => 1,
                ],
            ];
            $deductionSum = $rows->sum('total_deductions');
            if ($deductionSum > 0) {
                $deductionAccountId = $settings?->deductions_account_id;
                if ($deductionAccountId) {
                    $lines[] = [
                        'account_id' => $deductionAccountId,
                        'description' => 'خصومات رواتب شهر ' . $salaryBatch->month,
                        'debit' => 0,
                        'credit' => $deductionSum,
                        'currency' => $currency,
                        'exchange_rate' => 1,
                    ];
                }
            }
            $journal = \App\Models\JournalEntry::create([
                'date' => Carbon::createFromFormat('Y-m', $salaryBatch->month)->endOfMonth()->toDateString(),
                'description' => 'قيد استحقاق رواتب شهر ' . $salaryBatch->month,
                'source_type' => \App\Models\SalaryBatch::class,
                'source_id' => $salaryBatch->id,
                'created_by' => auth()->id(),
                'currency' => $currency,
                'exchange_rate' => 1,
                'total_debit' => $totalGross + $totalAllowances,
                'total_credit' => $totalNet + ($deductionSum > 0 ? $deductionSum : 0),
            ]);
            foreach ($lines as $line) {
                $journal->lines()->create($line);
            }
        }
        return back()->with('success', 'تم اعتماد كشف الرواتب بنجاح وتم توليد قيد الاستحقاق.');
    }
} 