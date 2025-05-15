<?php
namespace App\Http\Controllers;

use App\Models\SalaryPayment;
use App\Models\Employee;
use App\Models\Salary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Account;
use App\Models\Currency;
use App\Models\JournalEntry;

class SalaryPaymentController extends Controller
{
    public function index(Request $request)
    {
        $employeeId = $request->get('employee_id');
        $query = SalaryPayment::with('employee');
        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }
        $payments = $query->latest()->paginate(20);
        return view('salary_payments.index', compact('payments', 'employeeId'));
    }

    public function create(Request $request)
    {
        // جلب الشهور التي بها كشف معتمد ويوجد بها موظفين لم يستلموا رواتبهم
        $batches = \App\Models\SalaryBatch::where('status', 'approved')->orderByDesc('month')->get();
        $selectedBatchId = $request->get('salary_batch_id');
        $selectedEmployeeId = $request->get('employee_id');
        $employees = collect();
        $salary = null;
        $cashAccounts = collect();
        if ($selectedBatchId) {
            $batch = \App\Models\SalaryBatch::find($selectedBatchId);
            if ($batch) {
                // الموظفون الذين لم يستلموا رواتبهم بعد لهذا الشهر
                $employees = $batch->salaryPayments()->where('status', 'pending')->with('employee')->get()->map(function($p) {
                    return $p->employee;
                })->filter();
            }
        }
        if ($selectedBatchId && $selectedEmployeeId) {
            $salaryPayment = \App\Models\SalaryPayment::where('salary_batch_id', $selectedBatchId)
                ->where('employee_id', $selectedEmployeeId)
                ->first();
            if ($salaryPayment) {
                $salary = $salaryPayment;
                // جلب الصناديق المصرح بها للموظف فقط أو جميعها للسوبر أدمن
                $currency = $salaryPayment->employee->currency;
                $user = $salaryPayment->employee->user;
                if (auth()->user()->isSuperAdmin()) {
                    // السوبر أدمن يرى كل الصناديق النقدية بعملة الموظف
                    $cashAccounts = \App\Models\Account::where('is_cash_box', 1)
                        ->where('currency', $currency)
                        ->get();
                } elseif ($user && method_exists($user, 'cashBoxes')) {
                    $cashAccounts = $user->cashBoxes()
                        ->where('is_cash_box', 1)
                        ->where('currency', $currency)
                        ->get();
                } else {
                    $cashAccounts = collect();
                }
            }
        }
        return view('salary_payments.create', compact('batches', 'selectedBatchId', 'employees', 'selectedEmployeeId', 'salary', 'cashAccounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'salary_batch_id' => 'required|exists:salary_batches,id',
            'employee_id' => 'required|exists:employees,id',
            'cash_account_id' => 'required|exists:accounts,id',
            'payment_date' => 'required|date',
        ]);
        $batch = \App\Models\SalaryBatch::findOrFail($validated['salary_batch_id']);
        if ($batch->status !== 'approved') {
            return back()->withErrors(['لا يمكن دفع الرواتب إلا من كشف معتمد.']);
        }
        $salaryPayment = \App\Models\SalaryPayment::where('salary_batch_id', $batch->id)
            ->where('employee_id', $validated['employee_id'])
            ->first();
        if (!$salaryPayment || $salaryPayment->status !== 'pending') {
            return back()->withErrors(['لا يمكن دفع هذا الراتب (قد يكون مدفوعًا أو غير متوفر).']);
        }
        $employee = $salaryPayment->employee;
        $cashAccount = \App\Models\Account::findOrFail($validated['cash_account_id']);
        if ($cashAccount->currency !== $employee->currency) {
            return back()->withErrors(['عملة الصندوق لا تطابق عملة الموظف.']);
        }
        // تحقق من رصيد الصندوق النقدي
        $cashBalance = $cashAccount->balance(); // يفترض وجود دالة balance() في نموذج الحساب
        if ($cashBalance < $salaryPayment->net_salary) {
            return back()->withErrors(['لا يوجد رصيد كافٍ في الصندوق النقدي لتنفيذ عملية الدفع.']);
        }
        $currency = \App\Models\Currency::where('code', $employee->currency)->first();
        $exchangeRate = $currency ? $currency->exchange_rate : 1;
        // تنفيذ الدفع وإنشاء القيد المحاسبي
        return DB::transaction(function() use ($salaryPayment, $validated, $employee, $cashAccount, $exchangeRate) {
            $salaryPayment->update([
                'payment_date' => $validated['payment_date'],
                'status' => 'paid',
            ]);

            // إنشاء سند صرف
            $voucher = \App\Models\Voucher::create([
                'voucher_number' => $this->generateVoucherNumber(),
                'type' => 'payment',
                'date' => $validated['payment_date'],
                'currency' => $employee->currency,
                'exchange_rate' => $exchangeRate,
                'description' => __('messages.salary_journal_desc', ['month' => $salaryPayment->salary_month, 'name' => $employee->name]),
                'recipient_name' => $employee->name,
                'created_by' => auth()->id(),
            ]);
            $salaryPayment->update([
                'voucher_id' => $voucher->id,
            ]);

            // إنشاء القيد المحاسبي
            $settings = \App\Models\AccountingSetting::where('currency', $employee->currency)->first();
            $liabilityAccountId = $settings?->liabilities_account_id;
            if (!$liabilityAccountId) {
                throw new \Exception('لم يتم العثور على حساب الذمم المستحقة للموظفين بعملة الموظف.');
            }
            $journal = \App\Models\JournalEntry::create([
                'date' => $validated['payment_date'],
                'description' => __('messages.salary_journal_desc', ['month' => $salaryPayment->salary_month, 'name' => $employee->name]),
                'source_type' => \App\Models\Voucher::class,
                'source_id' => $voucher->id,
                'created_by' => auth()->id(),
                'currency' => $employee->currency,
                'exchange_rate' => $exchangeRate,
                'total_debit' => $salaryPayment->net_salary,
                'total_credit' => $salaryPayment->net_salary,
            ]);
            $journal->lines()->create([
                'account_id' => $liabilityAccountId,
                'description' => __('messages.salary_payment_desc', ['name' => $employee->name]),
                'debit' => $salaryPayment->net_salary,
                'credit' => 0,
                'currency' => $employee->currency,
                'exchange_rate' => $exchangeRate,
            ]);
            $journal->lines()->create([
                'account_id' => $cashAccount->id,
                'description' => __('messages.salary_payment_desc', ['name' => $employee->name]),
                'debit' => 0,
                'credit' => $salaryPayment->net_salary,
                'currency' => $employee->currency,
                'exchange_rate' => $exchangeRate,
            ]);
            $salaryPayment->update(['journal_entry_id' => $journal->id]);
            return redirect()->route('salary-payments.index')->with('success', __('messages.created_success'));
        });
    }

    public function show(SalaryPayment $salaryPayment)
    {
        $salaryPayment->load('employee');
        return view('salary_payments.show', compact('salaryPayment'));
    }

    public function edit(SalaryPayment $salaryPayment)
    {
        // عادة لا يتم تعديل دفعة راتب بعد الدفع، لكن يمكن عرضها فقط
        return redirect()->route('salary-payments.show', $salaryPayment);
    }

    public function update(Request $request, SalaryPayment $salaryPayment)
    {
        // عادة لا يتم تعديل دفعة راتب بعد الدفع
        return redirect()->route('salary-payments.show', $salaryPayment);
    }

    public function destroy(SalaryPayment $salaryPayment)
    {
        $salaryPayment->delete();
        return redirect()->route('salary-payments.index')->with('success', __('messages.deleted_success'));
    }

    public function updateAllowancesDeductions(Request $request)
    {
        $validated = $request->validate([
            'salary_payment_id' => 'required|exists:salary_payments,id',
            'total_allowances' => 'required|numeric',
            'total_deductions' => 'required|numeric',
        ]);
        $payment = \App\Models\SalaryPayment::findOrFail($validated['salary_payment_id']);
        $net = $payment->gross_salary + $validated['total_allowances'] - $validated['total_deductions'];
        if ($net < 0) {
            return back()->withErrors(['لا يمكن أن يكون صافي الراتب أقل من صفر.']);
        }
        $payment->total_allowances = $validated['total_allowances'];
        $payment->total_deductions = $validated['total_deductions'];
        $payment->net_salary = $net;
        $payment->save();
        return redirect()->route('salary-batches.show', $payment->salaryBatch)->with('success', __('messages.updated_success'));
    }

    // دالة توليد رقم سند الصرف
    private function generateVoucherNumber()
    {
        $lastId = \App\Models\Voucher::max('id') ?? 0;
        return 'VCH-' . str_pad($lastId + 1, 5, '0', STR_PAD_LEFT);
    }
} 