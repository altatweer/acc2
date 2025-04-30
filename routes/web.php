<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\HomeController;

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

Route::get('accounts/create-group', [AccountController::class, 'createGroup'])->name('accounts.createGroup')->middleware('auth');
Route::post('accounts/store-group', [AccountController::class, 'storeGroup'])->name('accounts.storeGroup')->middleware('auth');

Route::get('accounts/create-account', [AccountController::class, 'createAccount'])->name('accounts.createAccount')->middleware('auth');
Route::post('accounts/store-account', [AccountController::class, 'storeAccount'])->name('accounts.storeAccount')->middleware('auth');

Route::get('accounts/{account}/edit', [AccountController::class, 'edit'])->name('accounts.edit')->middleware('auth');
Route::put('accounts/{account}', [AccountController::class, 'update'])->name('accounts.update')->middleware('auth');
Route::delete('accounts/{account}', [AccountController::class, 'destroy'])->name('accounts.destroy')->middleware('auth');

Route::get('accounts/chart', [AccountController::class, 'chart'])->name('accounts.chart')->middleware('auth');

// الحركات المالية
Route::resource('transactions', TransactionController::class)->middleware('auth');

// السندات
Route::resource('vouchers', VoucherController::class)->middleware('auth');
Route::get('vouchers/{voucher}/print', [VoucherController::class, 'print'])->name('vouchers.print')->middleware('auth');
