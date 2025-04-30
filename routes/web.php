<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->middleware('auth')->name('dashboard');
Route::resource('branches', App\Http\Controllers\BranchController::class)->middleware('auth');
Route::resource('accounts', App\Http\Controllers\AccountController::class)->middleware('auth');
Route::get('chart', [App\Http\Controllers\AccountController::class, 'chart'])->name('accounts.chart');
use App\Http\Controllers\TransactionController;

Route::resource('transactions', TransactionController::class);
use App\Http\Controllers\VoucherController;

Route::resource('vouchers', VoucherController::class);

// طباعة السند
Route::get('vouchers/{voucher}/print', [VoucherController::class, 'print'])->name('vouchers.print');
