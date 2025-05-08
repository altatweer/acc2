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
use App\Http\Controllers\SettingController;
use App\Http\Controllers\LedgerController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\TestLanguageController;

// Default root route redirects to login or dashboard
Route::get('/', function () {
   return redirect('/login');
});

// Auth routes outside language prefix
Auth::routes();

// تحديد اللغة
Route::get('/language/{lang}', function ($lang) {
    if (in_array($lang, ['ar', 'en'])) {
        session(['locale' => $lang]);
    }
    return redirect()->back();
})->name('change.language');

// Test routes 
Route::prefix('test-language')->group(function () {
    Route::get('/test', [TestLanguageController::class, 'test'])->name('test.language');
    Route::get('/set-english', [TestLanguageController::class, 'setEnglish'])->name('set.english');
    Route::get('/set-arabic', [TestLanguageController::class, 'setArabic'])->name('set.arabic');
});

// Direct language force routes for testing
Route::get('/force-english', function () { 
    echo '<!DOCTYPE html>
    <html>
    <head>
        <title>Force English</title>
        <meta charset="UTF-8">
        <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
        <meta http-equiv="Pragma" content="no-cache">
        <meta http-equiv="Expires" content="0">
        <style>
            body { font-family: Arial, sans-serif; padding: 20px; }
            .result { background: #f0f0f0; padding: 15px; margin-bottom: 20px; }
            .links a { display: inline-block; margin: 10px; padding: 10px 15px; background: #007bff; color: white; text-decoration: none; }
        </style>
    </head>
    <body>
        <h1>Redirecting to English</h1>
    </body>
    </html>';
    
    // Redirect to English version of dashboard
    return redirect('/en/dashboard');
});

Route::get('/force-arabic', function () {
    echo '<!DOCTYPE html>
    <html dir="rtl">
    <head>
        <title>فرض اللغة العربية</title>
        <meta charset="UTF-8">
        <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
        <meta http-equiv="Pragma" content="no-cache">
        <meta http-equiv="Expires" content="0">
        <style>
            body { font-family: Arial, sans-serif; padding: 20px; }
            .result { background: #f0f0f0; padding: 15px; margin-bottom: 20px; }
            .links a { display: inline-block; margin: 10px; padding: 10px 15px; background: #007bff; color: white; text-decoration: none; }
        </style>
    </head>
    <body>
        <h1>جاري التحويل للعربية</h1>
    </body>
    </html>';
    
    // Redirect to Arabic version of dashboard
    return redirect('/ar/dashboard');
});

// كل المسارات تحت مجموعة واحدة، بدون prefixes للغة
Route::middleware(['auth'])->group(function () {
    // Home/Dashboard routes
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Branches 
    Route::resource('branches', BranchController::class);

    // Accounts
    Route::get('accounts', [AccountController::class, 'index'])->name('accounts.index');
    Route::get('accounts/real', [AccountController::class, 'realAccounts'])->name('accounts.real');
    Route::get('accounts/create-group', [AccountController::class, 'createGroup'])->name('accounts.createGroup');
    Route::post('accounts/store-group', [AccountController::class, 'storeGroup'])->name('accounts.storeGroup');
    Route::get('accounts/create-account', [AccountController::class, 'createAccount'])->name('accounts.createAccount');
    Route::post('accounts/store-account', [AccountController::class, 'storeAccount'])->name('accounts.storeAccount');
    Route::post('accounts/next-code', [AccountController::class, 'nextCode'])->name('accounts.nextCode');
    Route::get('accounts/chart', [AccountController::class, 'chart'])->name('accounts.chart');
    Route::get('accounts/{account}/edit', [AccountController::class, 'edit'])->name('accounts.edit');
    Route::put('accounts/{account}', [AccountController::class, 'update'])->name('accounts.update');
    Route::delete('accounts/{account}', [AccountController::class, 'destroy'])->name('accounts.destroy');
    Route::get('accounts/{account}', [AccountController::class, 'show'])->name('accounts.show');
    Route::get('accounts/by-currency/{currency}', [AccountController::class, 'byCurrency']);

    // Transactions
    Route::resource('transactions', TransactionController::class);

    // Vouchers
    Route::resource('vouchers', VoucherController::class);
    Route::get('vouchers/{voucher}/print', [VoucherController::class, 'print'])->name('vouchers.print');
    Route::post('vouchers/{voucher}/cancel', [VoucherController::class, 'cancel'])->name('vouchers.cancel');
    Route::get('vouchers/transfer/create', [VoucherController::class, 'transferCreate'])->name('vouchers.transfer.create');
    Route::post('vouchers/transfer/store', [VoucherController::class, 'transferStore'])->name('vouchers.transfer.store');

    // Currencies
    Route::resource('currencies', CurrencyController::class);

    // Customers
    Route::resource('customers', CustomerController::class);

    // Items
    Route::resource('items', ItemController::class);

    // Invoice payments
    Route::get('invoice-payments/create', [InvoicePaymentController::class, 'create'])->name('invoice-payments.create');
    Route::post('invoice-payments', [InvoicePaymentController::class, 'store'])->name('invoice-payments.store');

    // Invoices
    Route::resource('invoices', InvoiceController::class);
    Route::post('invoices/{invoice}/approve', [InvoiceController::class, 'approve'])->name('invoices.approve');
    Route::post('invoices/{invoice}/cancel', [InvoiceController::class, 'cancel'])->name('invoices.cancel');
    Route::get('invoices/{invoice}/print', [InvoiceController::class, 'print'])->name('invoices.print');

    // Journal entries
    Route::resource('journal-entries', JournalEntryController::class)->only(['index','show','create','store']);
    Route::get('journal-entries/{id}/modal', [JournalEntryController::class, 'modal']);
    Route::get('journal-entries/{id}/print', [JournalEntryController::class, 'print'])->name('journal-entries.print');

    // Employees
    Route::resource('employees', EmployeeController::class);

    // Salaries
    Route::resource('salaries', SalaryController::class);

    // Salary payments
    Route::resource('salary-payments', SalaryPaymentController::class);
    Route::post('salary-payments/update-allowances-deductions', [SalaryPaymentController::class, 'updateAllowancesDeductions'])
        ->name('salary-payments.update-allowances-deductions');

    // Salary batches
    Route::resource('salary-batches', SalaryBatchController::class)->only(['index','create','store','show','destroy']);
    Route::post('salary-batches/{salaryBatch}/approve', [SalaryBatchController::class, 'approve'])->name('salary-batches.approve');

    // Settings
    Route::get('settings/accounting', [AccountingSettingController::class, 'edit'])->name('accounting-settings.edit');
    Route::put('settings/accounting', [AccountingSettingController::class, 'update'])->name('accounting-settings.update');
    Route::get('settings/system', [SettingController::class, 'edit'])->name('settings.system.edit');
    Route::put('settings/system', [SettingController::class, 'update'])->name('settings.system.update');

    // Admin routes
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('roles', \App\Http\Controllers\Admin\RoleController::class);
        Route::resource('permissions', \App\Http\Controllers\Admin\PermissionController::class);
        Route::resource('user-roles', \App\Http\Controllers\Admin\UserRoleController::class);
        Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
    });

    // Ledger
    Route::get('ledger', [LedgerController::class, 'index'])->name('ledger.index');

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('trial-balance', [ReportsController::class, 'trialBalance'])->name('trial-balance');
        Route::get('balance-sheet', [ReportsController::class, 'balanceSheet'])->name('balance-sheet');
        Route::get('income-statement', [ReportsController::class, 'incomeStatement'])->name('income-statement');
        Route::get('payroll', [ReportsController::class, 'payroll'])->name('payroll');
        Route::get('expenses-revenues', [ReportsController::class, 'expensesRevenues'])->name('expenses-revenues');
        Route::get('balance-sheet/excel', [ReportsController::class, 'exportBalanceSheetExcel'])->name('balance-sheet.excel');
        Route::get('income-statement/excel', [ReportsController::class, 'exportIncomeStatementExcel'])->name('income-statement.excel');
        Route::get('expenses-revenues/excel', [ReportsController::class, 'exportExpensesRevenuesExcel'])->name('expenses-revenues.excel');
        Route::get('payroll/excel', [ReportsController::class, 'exportPayrollExcel'])->name('payroll.excel');
        Route::get('balance-sheet/pdf', [ReportsController::class, 'exportBalanceSheetPdf'])->name('balance-sheet.pdf');
        Route::get('income-statement/pdf', [ReportsController::class, 'exportIncomeStatementPdf'])->name('income-statement.pdf');
        Route::get('expenses-revenues/pdf', [ReportsController::class, 'exportExpensesRevenuesPdf'])->name('expenses-revenues.pdf');
        Route::get('payroll/pdf', [ReportsController::class, 'exportPayrollPdf'])->name('payroll.pdf');
    });
});

// Special debug route
Route::get('/test-arabic-mpdf', [ReportsController::class, 'testArabicMpdf']);
