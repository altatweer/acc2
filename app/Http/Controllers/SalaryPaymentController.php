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
                ->with('employee')
                ->first();
            if ($salaryPayment) {
                $salary = $salaryPayment;
                
                // جلب جميع الصناديق النقدية (بدلاً من تقييدها بعملة الموظف)
                if (auth()->user()->isSuperAdmin()) {
                    // السوبر أدمن يرى كل الصناديق النقدية
                    $cashAccounts = \App\Models\Account::where('is_cash_box', 1)->get();
                } else {
                    // المستخدم العادي يرى الصناديق المرتبطة به فقط
                    $cashAccounts = auth()->user()->cashBoxes()
                        ->where('is_cash_box', 1)
                        ->get();
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
            'payment_currency' => 'required|exists:currencies,code',
            'payment_amount' => 'required|numeric|min:0.01',
            'exchange_rate' => 'required|numeric|min:0.000001',
            'payment_date' => 'required|date',
        ]);

        \Log::info('Salary Payment Request Data:', $validated);

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
        $paymentCurrency = $validated['payment_currency'];
        $paymentAmount = $validated['payment_amount'];
        $exchangeRate = $validated['exchange_rate'];

        // حساب المبلغ المحول إلى عملة الراتب
        $convertedAmount = $paymentAmount * $exchangeRate;

        \Log::info('Salary Payment Calculation:', [
            'employee_currency' => $employee->currency,
            'payment_currency' => $paymentCurrency,
            'payment_amount' => $paymentAmount,
            'exchange_rate' => $exchangeRate,
            'converted_amount' => $convertedAmount,
            'salary_net' => $salaryPayment->net_salary
        ]);

        // التحقق من أن المبلغ المحول يطابق صافي الراتب تقريباً (فرق مقبول 0.01)
        if (abs($convertedAmount - $salaryPayment->net_salary) > 0.01) {
            return back()->withErrors(['المبلغ المحول (' . number_format($convertedAmount, 2) . ' ' . $employee->currency . ') لا يطابق صافي الراتب (' . number_format($salaryPayment->net_salary, 2) . ' ' . $employee->currency . ')']);
        }

        // تحقق من رصيد الصندوق النقدي بعملة الدفع
        $cashBalance = $cashAccount->balance($paymentCurrency);
        if ($cashBalance < $paymentAmount) {
            return back()->withErrors(['لا يوجد رصيد كافٍ في الصندوق النقدي لتنفيذ عملية الدفع. الرصيد الحالي: ' . number_format($cashBalance, 2) . ' ' . $paymentCurrency]);
        }

        // تنفيذ الدفع وإنشاء القيد المحاسبي
        try {
            return DB::transaction(function() use ($salaryPayment, $validated, $employee, $cashAccount, $paymentCurrency, $paymentAmount, $exchangeRate, $convertedAmount) {
                $salaryPayment->update([
                    'payment_date' => $validated['payment_date'],
                    'status' => 'paid',
                ]);

                // إنشاء سند صرف
                $voucher = \App\Models\Voucher::create([
                    'voucher_number' => $this->generateVoucherNumber(),
                    'type' => 'payment',
                    'date' => $validated['payment_date'],
                    'currency' => $paymentCurrency,
                    'exchange_rate' => $exchangeRate,
                    'description' => 'سداد راتب الموظف ' . $employee->name . ' عن شهر ' . $salaryPayment->salary_month,
                    'recipient_name' => $employee->name,
                    'created_by' => auth()->id(),
                ]);

                $salaryPayment->update([
                    'voucher_id' => $voucher->id,
                ]);

                // إنشاء القيد المحاسبي متعدد العملات
                $liabilityAccountId = \App\Models\AccountingSetting::get('employee_payables_account');
                if (!$liabilityAccountId) {
                    throw new \Exception('لم يتم العثور على حساب الذمم المستحقة للموظفين في إعدادات النظام.');
                }

                $journal = \App\Models\JournalEntry::create([
                    'date' => $validated['payment_date'],
                    'description' => 'سداد راتب الموظف ' . $employee->name . ' عن شهر ' . $salaryPayment->salary_month,
                    'source_type' => \App\Models\Voucher::class,
                    'source_id' => $voucher->id,
                    'created_by' => auth()->id(),
                    'currency' => $paymentCurrency, // عملة السند الرئيسية
                    'exchange_rate' => $exchangeRate,
                    'is_multi_currency' => ($paymentCurrency !== $employee->currency), // إشارة للعملات المتعددة
                    'total_debit' => $convertedAmount, // بعملة الراتب
                    'total_credit' => $convertedAmount, // بعملة الراتب
                ]);

                // خط القيد: مدين حساب الذمم المستحقة للموظفين (بعملة الراتب)
                $journal->lines()->create([
                    'account_id' => $liabilityAccountId,
                    'description' => 'إقفال استحقاق راتب ' . $employee->name,
                    'debit' => $convertedAmount,
                    'credit' => 0,
                    'currency' => $employee->currency,
                    'exchange_rate' => 1, // بالنسبة لعملة الراتب
                ]);

                // خط القيد: دائن الصندوق النقدي (بعملة الدفع)
                $journal->lines()->create([
                    'account_id' => $cashAccount->id,
                    'description' => 'دفع راتب ' . $employee->name . ' نقداً',
                    'debit' => 0,
                    'credit' => $paymentAmount,
                    'currency' => $paymentCurrency,
                    'exchange_rate' => $exchangeRate,
                ]);

                $salaryPayment->update(['journal_entry_id' => $journal->id]);

                \Log::info('Salary Payment Completed Successfully', [
                    'voucher_id' => $voucher->id,
                    'journal_id' => $journal->id,
                    'payment_amount' => $paymentAmount,
                    'payment_currency' => $paymentCurrency,
                    'converted_amount' => $convertedAmount,
                    'employee_currency' => $employee->currency
                ]);

                return redirect()->route('salary-payments.index')->with('success', 'تم دفع الراتب بنجاح');
            });
        } catch (\Exception $e) {
            \Log::error('Salary Payment Failed:', ['error' => $e->getMessage()]);
            return back()->withErrors(['حدث خطأ أثناء معالجة الدفع: ' . $e->getMessage()]);
        }
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
        return DB::transaction(function() {
            // استخدام القفل لتجنب race conditions
            $lastVoucher = \App\Models\Voucher::where('type', 'payment')
                ->where('voucher_number', 'LIKE', 'PAY%')
                ->where('voucher_number', 'NOT LIKE', '%-%') // تجنب الأرقام الشاذة مثل PAY000-24
                ->lockForUpdate()
                ->orderBy('id', 'desc')
                ->first();
            
            $lastNumber = 0;
            if ($lastVoucher && preg_match('/PAY(\d+)/', $lastVoucher->voucher_number, $matches)) {
                $lastNumber = intval($matches[1]);
            }
            
            $newNumber = $lastNumber + 1;
            $newVoucherNumber = 'PAY' . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
            
            // التأكد من عدم وجود تكرار
            $retryCount = 0;
            while (\App\Models\Voucher::where('voucher_number', $newVoucherNumber)->exists() && $retryCount < 10) {
                $newNumber++;
                $newVoucherNumber = 'PAY' . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
                $retryCount++;
            }
            
            if ($retryCount >= 10) {
                throw new \Exception('فشل في توليد رقم سند فريد بعد 10 محاولات');
            }
            
            return $newVoucherNumber;
        });
    }
} 