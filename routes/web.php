<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\InvoicePaymentController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\JournalEntryController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\SalaryController;
use App\Http\Controllers\SalaryPaymentController;
use App\Http\Controllers\SalaryBatchController;
use App\Http\Controllers\AccountingSettingController;

// الصفحة الرئيسية
Route::get('/', function () {
   return redirect('/login');
});

Auth::routes();

// الصفحة الافتراضية بعد الدخول (لوحة المستخدم أو الرئيسية)
Route::get('/home', [HomeController::class, 'index'])->name('home');

// لوحة التحكم
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('auth')->name('dashboard');

// الفروع
Route::resource('branches', BranchController::class)->middleware('auth');

// الحسابات
Route::get('accounts', [AccountController::class, 'index'])->name('accounts.index')->middleware('auth'); // عرض الفئات
Route::get('accounts/real', [AccountController::class, 'realAccounts'])->name('accounts.real')->middleware('auth'); // عرض الحسابات الفعلية

// إنشاء فئة وحساب
Route::get('accounts/create-group', [AccountController::class, 'createGroup'])->name('accounts.createGroup')->middleware('auth');
Route::post('accounts/store-group', [AccountController::class, 'storeGroup'])->name('accounts.storeGroup')->middleware('auth');
Route::get('accounts/create-account', [AccountController::class, 'createAccount'])->name('accounts.createAccount')->middleware('auth');
Route::post('accounts/store-account', [AccountController::class, 'storeAccount'])->name('accounts.storeAccount')->middleware('auth');
Route::post('accounts/next-code', [AccountController::class, 'nextCode'])
    ->middleware('auth')
    ->name('accounts.nextCode');

// شجرة الحسابات (قبل تفاصيل الحساب)
Route::get('accounts/chart', [AccountController::class, 'chart'])->name('accounts.chart')->middleware('auth');

// تعديل وحذف الحسابات
Route::get('accounts/{account}/edit', [AccountController::class, 'edit'])->name('accounts.edit')->middleware('auth');
Route::put('accounts/{account}', [AccountController::class, 'update'])->name('accounts.update')->middleware('auth');
Route::delete('accounts/{account}', [AccountController::class, 'destroy'])->name('accounts.destroy')->middleware('auth');

// تفاصيل الحساب (يجب أن يأتي بعد جميع المسارات الخاصة)
Route::get('accounts/{account}', [AccountController::class, 'show'])->name('accounts.show')->middleware('auth');

// الحركات المالية
Route::resource('transactions', TransactionController::class)->middleware('auth');

// السندات
Route::resource('vouchers', VoucherController::class)->middleware('auth');
Route::get('vouchers/{voucher}/print', [VoucherController::class, 'print'])->name('vouchers.print')->middleware('auth');

// إدارة العملات
Route::resource('currencies', CurrencyController::class)->middleware('auth');

// العملاء
Route::resource('customers', CustomerController::class)->middleware('auth');

// المنتجات والخدمات
Route::resource('items', ItemController::class)->middleware('auth');

// إدارة مدفوعات الفواتير
Route::get('invoice-payments/create', [InvoicePaymentController::class, 'create'])
    ->middleware('auth')
    ->name('invoice-payments.create');
Route::post('invoice-payments', [InvoicePaymentController::class, 'store'])
    ->middleware('auth')
    ->name('invoice-payments.store');

// إدارة الفواتير (CRUD)
Route::resource('invoices', InvoiceController::class)->middleware('auth');

// After accounts CRUD routes
Route::get('accounts/next-code', [AccountController::class, 'nextCode'])
    ->middleware('auth')
    ->name('accounts.nextCode');

// القيود المحاسبية
Route::resource('journal-entries', JournalEntryController::class)->only(['index','show','create','store']);

Route::get('accounts/by-currency/{currency}', [\App\Http\Controllers\AccountController::class, 'byCurrency'])->middleware('auth');

// الموظفين
Route::resource('employees', EmployeeController::class)->middleware('auth');

// الرواتب
Route::resource('salaries', SalaryController::class)->middleware('auth');

// دفعات الرواتب
Route::resource('salary-payments', SalaryPaymentController::class)->middleware('auth');
Route::post('salary-payments/update-allowances-deductions', [SalaryPaymentController::class, 'updateAllowancesDeductions'])->name('salary-payments.update-allowances-deductions')->middleware('auth');

// كشوف الرواتب
Route::resource('salary-batches', SalaryBatchController::class)->only(['index','create','store','show','destroy'])->middleware('auth');
Route::post('salary-batches/{salaryBatch}/approve', [\App\Http\Controllers\SalaryBatchController::class, 'approve'])->name('salary-batches.approve')->middleware('auth');

// إعدادات الحسابات الافتراضية
Route::get('settings/accounting', [AccountingSettingController::class, 'edit'])->name('accounting-settings.edit')->middleware('auth');
Route::put('settings/accounting', [AccountingSettingController::class, 'update'])->name('accounting-settings.update')->middleware('auth');
