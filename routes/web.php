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
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

// Default root route redirects to login or dashboard
Route::get('/', function () {
   if (!file_exists(storage_path('app/install.lock'))) {
       return redirect('/install');
   }
   return redirect('/login');
});

// Auth routes outside language prefix
Auth::routes();

// تحديد اللغة - طريقة جديدة مُحسنة
Route::get('/language/{lang}', [LanguageController::class, 'switchLang'])->name('change.language');

// طريقة مباشرة لتبديل اللغة (أكثر موثوقية)
Route::get('/set-language/{lang}', [LanguageController::class, 'forceLang'])->name('force.language');

// Extended language testing routes
Route::get('/test-language/force/{lang}', [TestLanguageController::class, 'forceLanguage'])->name('test.force.language');

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
    
    // Accounts Tree Export
    Route::get('accounts/tree', [\App\Http\Controllers\AccountsTreeController::class, 'index'])->name('accounts.tree.index');
    Route::get('accounts/tree/export', [\App\Http\Controllers\AccountsTreeController::class, 'exportToExcel'])->name('accounts.tree.export');
    Route::get('accounts/tree/preview', [\App\Http\Controllers\AccountsTreeController::class, 'preview'])->name('accounts.tree.preview');
    Route::get('accounts/tree/print', [\App\Http\Controllers\AccountsTreeController::class, 'printTree'])->name('accounts.tree.print');
    Route::get('accounts/tree/json', [\App\Http\Controllers\AccountsTreeController::class, 'getTreeData'])->name('accounts.tree.json');
    Route::post('accounts/store-account', [AccountController::class, 'storeAccount'])->name('accounts.storeAccount');
    Route::post('accounts/next-code', [AccountController::class, 'nextCode'])->name('accounts.nextCode');
    Route::get('accounts/chart', [AccountController::class, 'chart'])->name('accounts.chart');
    Route::get('accounts/fix-zero-codes', [AccountController::class, 'fixZeroCodes'])->name('accounts.fixZeroCodes');
    Route::get('accounts/by-currency/{currency}', [AccountController::class, 'byCurrency']);
    Route::get('accounts/{account}/edit', [AccountController::class, 'edit'])->name('accounts.edit');
    Route::put('accounts/{account}', [AccountController::class, 'update'])->name('accounts.update');
    Route::delete('accounts/{account}', [AccountController::class, 'destroy'])->name('accounts.destroy');
    Route::get('accounts/{account}', [AccountController::class, 'show'])->name('accounts.show');

    // Transactions
    Route::resource('transactions', TransactionController::class);

    // Vouchers
    Route::resource('vouchers', VoucherController::class);
    Route::get('vouchers/{voucher}/print', [VoucherController::class, 'print'])->name('vouchers.print');
    Route::post('vouchers/{voucher}/cancel', [VoucherController::class, 'cancel'])->middleware(\App\Http\Middleware\PreventDuplicateSubmission::class)->name('vouchers.cancel');
    Route::get('vouchers/transfer/create', [VoucherController::class, 'transferCreate'])->name('vouchers.transfer.create');
    Route::post('vouchers/transfer/store', [VoucherController::class, 'transferStore'])->middleware(\App\Http\Middleware\PreventDuplicateSubmission::class)->name('vouchers.transfer.store');

    // Currencies
    Route::resource('currencies', CurrencyController::class);

    // Customers
    Route::resource('customers', CustomerController::class);

    // Items
    Route::resource('items', ItemController::class);

    // Invoice payments
    Route::get('invoice-payments/create', [InvoicePaymentController::class, 'create'])->name('invoice-payments.create');
    Route::post('invoice-payments', [InvoicePaymentController::class, 'store'])->middleware(\App\Http\Middleware\PreventDuplicateSubmission::class)->name('invoice-payments.store');

    // Invoices
    Route::resource('invoices', InvoiceController::class);
    Route::post('invoices/{invoice}/approve', [InvoiceController::class, 'approve'])->middleware(\App\Http\Middleware\PreventDuplicateSubmission::class)->name('invoices.approve');
    Route::post('invoices/{invoice}/cancel', [InvoiceController::class, 'cancel'])->middleware(\App\Http\Middleware\PreventDuplicateSubmission::class)->name('invoices.cancel');
    Route::get('invoices/{invoice}/print', [InvoiceController::class, 'print'])->name('invoices.print');

    // Journal entries
    Route::resource('journal-entries', JournalEntryController::class)->only(['index','show','create','store']);
    Route::get('journal-entries/{id}/modal', [JournalEntryController::class, 'modal']);
    Route::get('journal-entries/{id}/print', [JournalEntryController::class, 'print'])->name('journal-entries.print');
    Route::get('journal-entries/single-currency/create', [JournalEntryController::class, 'createSingleCurrency'])->name('journal-entries.single-currency.create');
    Route::get('journal-entries/multi-currency/create', [JournalEntryController::class, 'createMultiCurrency'])->name('journal-entries.multi-currency.create');
    Route::post('journal-entries/{journalEntry}/cancel', [JournalEntryController::class, 'cancel'])->middleware(\App\Http\Middleware\PreventDuplicateSubmission::class)->name('journal-entries.cancel');

    // Employees
    Route::resource('employees', EmployeeController::class);

    // Salaries
    Route::resource('salaries', SalaryController::class);

    // Salary payments
    Route::resource('salary-payments', SalaryPaymentController::class);
    Route::post('salary-payments/update-allowances-deductions', [SalaryPaymentController::class, 'updateAllowancesDeductions'])
        ->middleware(\App\Http\Middleware\PreventDuplicateSubmission::class)->name('salary-payments.update-allowances-deductions');

    // Salary batches
    Route::resource('salary-batches', SalaryBatchController::class)->only(['index','create','store','show','destroy']);
    Route::post('salary-batches/{salaryBatch}/approve', [SalaryBatchController::class, 'approve'])->middleware(\App\Http\Middleware\PreventDuplicateSubmission::class)->name('salary-batches.approve');
    Route::get('salary-batches/{salaryBatch}/print', [SalaryBatchController::class, 'print'])->name('salary-batches.print');

    // Settings
    Route::get('settings/accounting', [AccountingSettingController::class, 'edit'])->name('accounting-settings.edit');
    Route::put('settings/accounting', [AccountingSettingController::class, 'update'])->name('accounting-settings.update');
    Route::get('settings/system', [SettingController::class, 'edit'])->name('settings.system.edit');
    Route::put('settings/system', [SettingController::class, 'update'])->name('settings.system.update');
    
    // Print Settings
    Route::get('settings/print', [\App\Http\Controllers\PrintSettingController::class, 'edit'])->name('print-settings.edit');
    Route::put('settings/print', [\App\Http\Controllers\PrintSettingController::class, 'update'])->name('print-settings.update');
    Route::post('settings/print/reset', [\App\Http\Controllers\PrintSettingController::class, 'reset'])->name('print-settings.reset');
    Route::get('settings/print/preview/invoice/{invoiceId?}', [\App\Http\Controllers\PrintSettingController::class, 'previewInvoice'])->name('print-settings.preview-invoice');
    Route::get('settings/print/preview/voucher/{voucherId?}', [\App\Http\Controllers\PrintSettingController::class, 'previewVoucher'])->name('print-settings.preview-voucher');

    // Language management
    Route::get('languages', [LanguageController::class, 'index'])->name('languages.index');
    Route::get('languages/upload', [LanguageController::class, 'uploadForm'])->name('languages.uploadForm');
    Route::post('languages/upload', [LanguageController::class, 'upload'])->name('languages.upload');
    Route::get('languages/{code}/download', [LanguageController::class, 'download'])->name('languages.download');

    // Admin routes
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('roles', \App\Http\Controllers\Admin\RoleController::class);
        Route::resource('permissions', \App\Http\Controllers\Admin\PermissionController::class);
        Route::resource('user-roles', \App\Http\Controllers\Admin\UserRoleController::class);
        Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
        Route::get('users/{user}/cash-boxes', [\App\Http\Controllers\Admin\UserController::class, 'editCashBoxes'])->name('users.cash_boxes.edit');
        Route::post('users/{user}/cash-boxes', [\App\Http\Controllers\Admin\UserController::class, 'updateCashBoxes'])->name('users.cash_boxes.update');
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
        Route::get('currency-comparison', [ReportsController::class, 'currencyComparison'])->name('currency-comparison');
        Route::get('cash-flow', [ReportsController::class, 'cashFlow'])->name('cash-flow');
        Route::get('balance-sheet/excel', [ReportsController::class, 'exportBalanceSheetExcel'])->name('balance-sheet.excel');
        Route::get('income-statement/excel', [ReportsController::class, 'exportIncomeStatementExcel'])->name('income-statement.excel');
        Route::get('expenses-revenues/excel', [ReportsController::class, 'exportExpensesRevenuesExcel'])->name('expenses-revenues.excel');
        Route::get('payroll/excel', [ReportsController::class, 'exportPayrollExcel'])->name('payroll.excel');
        Route::get('balance-sheet/pdf', [ReportsController::class, 'exportBalanceSheetPdf'])->name('balance-sheet.pdf');
        Route::get('income-statement/pdf', [ReportsController::class, 'exportIncomeStatementPdf'])->name('income-statement.pdf');
        Route::get('expenses-revenues/pdf', [ReportsController::class, 'exportExpensesRevenuesPdf'])->name('expenses-revenues.pdf');
        Route::get('payroll/pdf', [ReportsController::class, 'exportPayrollPdf'])->name('payroll.pdf');
    });

    // Test route for debugging income statement
    Route::get('/debug-income', function() {
        $defaultCurrency = \App\Models\Currency::getDefaultCode();
        $accounts = \App\Models\Account::where('is_group', false)
            ->where(function($q) {
                $q->whereHas('parent', function($subQ) {
                    $subQ->whereIn('type', ['إيراد', 'مصروف', 'revenue', 'expense']);
                })->orWhere(function($subQ) {
                    $subQ->whereNull('parent_id')->whereIn('type', ['إيراد', 'مصروف', 'revenue', 'expense']);
                });
            })->get();

        $rows = [];
        $rowsByCurrency = collect();
        
        foreach ($accounts as $account) {
            $lines = $account->journalEntryLines()->get();
            $linesByCurrency = $lines->groupBy('currency');
            
            foreach ($linesByCurrency as $currency => $currencyLines) {
                $debit = $currencyLines->sum('debit');
                $credit = $currencyLines->sum('credit');
                $balance = $debit - $credit;
                
                if ($debit == 0 && $credit == 0 && $balance == 0) {
                    continue;
                }
                
                $parentType = $account->parent ? $account->parent->type : $account->type;
                $actualCurrency = $currency ?: $defaultCurrency;
                
                $row = [
                    'account' => $account->name,
                    'debit' => $debit,
                    'credit' => $credit,
                    'balance' => $balance,
                    'type' => $parentType,
                    'currency' => $actualCurrency,
                ];
                
                $rows[] = $row;
                
                if (!$rowsByCurrency->has($actualCurrency)) {
                    $rowsByCurrency[$actualCurrency] = collect();
                }
                $rowsByCurrency[$actualCurrency]->push($row);
            }
        }
        
        return response()->json([
            'total_rows' => count($rows),
            'currencies' => $rowsByCurrency->keys()->toArray(),
            'data_by_currency' => $rowsByCurrency->toArray(),
            'sample_data' => $rows
        ]);
    });

    // Test route for currency functions
    Route::get('/test-currency', function () {
        return view('currencies.test');
    })->name('currencies.test');
});

// Special debug route
Route::get('/test-arabic-mpdf', [ReportsController::class, 'testArabicMpdf']);

// Installer routes
Route::group(['middleware' => ['web'], 'prefix' => 'install'], function () {
    // التثبيت الأساسي
    Route::get('/', [\App\Http\Controllers\InstallController::class, 'index'])->name('install.index');
    // تشخيص الـ route مباشرة
    Route::post('/', function(\Illuminate\Http\Request $request) {
        file_put_contents(storage_path('route_debug.txt'), 
            "=== ROUTE DEBUG " . date('Y-m-d H:i:s') . " ===\n" .
            "Route Hit: install.process\n" .
            "Method: " . $request->method() . "\n" .
            "License Key: " . $request->input('license_key', 'NONE') . "\n\n",
            FILE_APPEND
        );
        
        // استدعاء الـ controller
        return app(\App\Http\Controllers\InstallController::class)->processStep($request);
    })->name('install.process');
    Route::get('/database', [\App\Http\Controllers\InstallController::class, 'database'])->name('install.database');
    Route::post('/database', [\App\Http\Controllers\InstallController::class, 'saveDatabase'])->name('install.saveDatabase');
    Route::get('/migrate', [\App\Http\Controllers\InstallController::class, 'migrate'])->name('install.migrate');
    Route::post('/migrate', [\App\Http\Controllers\InstallController::class, 'runMigrate'])->name('install.runMigrate');
    Route::get('/admin', [\App\Http\Controllers\InstallController::class, 'admin'])->name('install.admin');
    Route::post('/admin', [\App\Http\Controllers\InstallController::class, 'saveAdmin'])->name('install.saveAdmin');
    Route::get('/currencies', [\App\Http\Controllers\InstallController::class, 'currencies'])->name('install.currencies');
    Route::post('/currencies', [\App\Http\Controllers\InstallController::class, 'saveCurrencies'])->name('install.saveCurrencies');
    Route::get('/chart', [\App\Http\Controllers\InstallController::class, 'chart'])->name('install.chart');
    Route::post('/chart/import', [\App\Http\Controllers\InstallController::class, 'importChart'])->name('install.importChart');
    
    // أدوات الصيانة والإدارة
    Route::get('/maintenance', [\App\Http\Controllers\InstallController::class, 'maintenance'])->name('install.maintenance');
    Route::match(['get', 'post'], '/system-check', [\App\Http\Controllers\InstallController::class, 'systemCheck'])->name('install.system_check');
    Route::match(['get', 'post'], '/backup', [\App\Http\Controllers\InstallController::class, 'backup'])->name('install.backup');
    Route::match(['get', 'post'], '/update', [\App\Http\Controllers\InstallController::class, 'update'])->name('install.update');
    Route::post('/clear-cache', [\App\Http\Controllers\InstallController::class, 'clearCache'])->name('install.clear_cache');
    Route::post('/chart/skip', [\App\Http\Controllers\InstallController::class, 'skipChart'])->name('install.skipChart');
    Route::get('/finish', [\App\Http\Controllers\InstallController::class, 'finish'])->name('install.finish');
});

// إضافة مسار اختبار في نهاية الملف
Route::get('/testpage', function () {
    try {
        // التحقق من صحة اتصال قاعدة البيانات
        $users = DB::table('users')->count();
        $tenants = DB::table('tenants')->count();
        $accounts = DB::table('accounts')->count();
        
        return response()->json([
            'status' => 'success',
            'data' => [
                'php_version' => PHP_VERSION,
                'laravel_version' => app()->version(),
                'app_name' => config('app.name'),
                'debug' => config('app.debug'),
                'multi_tenancy_enabled' => config('app.multi_tenancy_enabled'),
                'users_count' => $users,
                'tenants_count' => $tenants,
                'accounts_count' => $accounts,
                'memory_limit' => ini_get('memory_limit')
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => explode("\n", $e->getTraceAsString())
        ], 500);
    }
});

// إضافة مسار اختبار في نهاية الملف
Route::get('/debug-tenant', function () {
    try {
        // جمع معلومات التشخيص
        $debug = [
            'status' => 'success',
            'data' => [
                'tenant_id' => app('tenant_id'),
                'tenant_exists' => \Illuminate\Support\Facades\Schema::hasTable('tenants'),
                'subscription_plans_exists' => \Illuminate\Support\Facades\Schema::hasTable('subscription_plans'),
                'tenant_features_exists' => \Illuminate\Support\Facades\Schema::hasTable('tenant_features'),
                'tenant_usage_stats_exists' => \Illuminate\Support\Facades\Schema::hasTable('tenant_usage_stats'),
                'permissions_has_tenant_id' => \Illuminate\Support\Facades\Schema::hasColumn('permissions', 'tenant_id'),
                'users_has_tenant_id' => \Illuminate\Support\Facades\Schema::hasColumn('users', 'tenant_id'),
                'accounts_has_tenant_id' => \Illuminate\Support\Facades\Schema::hasColumn('accounts', 'tenant_id'),
            ]
        ];
        
        // جلب عدد السجلات في كل جدول
        $tables = [
            'tenants', 'subscription_plans', 'tenant_features', 'tenant_usage_stats',
            'users', 'roles', 'permissions', 'accounts', 'vouchers', 'transactions'
        ];
        
        foreach ($tables as $table) {
            if (\Illuminate\Support\Facades\Schema::hasTable($table)) {
                $debug['data']["counts_{$table}"] = \Illuminate\Support\Facades\DB::table($table)->count();
            } else {
                $debug['data']["counts_{$table}"] = null;
            }
        }
        
        // جلب معلومات عن المستأجر إذا كان موجودًا
        if (\Illuminate\Support\Facades\Schema::hasTable('tenants')) {
            $tenant = \Illuminate\Support\Facades\DB::table('tenants')->first();
            if ($tenant) {
                $debug['data']['default_tenant'] = [
                    'id' => $tenant->id,
                    'name' => $tenant->name,
                    'subdomain' => $tenant->subdomain,
                    'is_active' => $tenant->is_active,
                    'subscription_plan_id' => $tenant->subscription_plan_id,
                ];
            }
        }
        
        // جلب سجل وقت التنفيذ
        $debug['data']['runtime_config'] = [
            'multi_tenancy_enabled' => config('app.multi_tenancy_enabled', false),
            'memory_limit' => ini_get('memory_limit'),
            'php_version' => PHP_VERSION,
            'now' => now()->format('Y-m-d H:i:s'),
        ];
        
        return response()->json($debug);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => explode("\n", $e->getTraceAsString())
        ], 500);
    }
});

// API Routes for AJAX calls
Route::prefix('api')->middleware('auth')->group(function () {
    Route::get('accounts/{account}/balance', [AccountController::class, 'getBalance']);
});
