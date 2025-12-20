<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $isAdmin = $user->isSuperAdmin() || $user->hasRole('admin');
        
        if ($isAdmin) {
            // إذا كان المستخدم مسؤول - جلب كل البيانات
            $accountsCount = \App\Models\Account::count();
            $transactionsCount = \App\Models\Transaction::count();
            $vouchersCount = \App\Models\Voucher::count();
            $usersCount = \App\Models\User::count();
            $receiptCount = \App\Models\Voucher::where('type', 'receipt')->count();
            $paymentCount = \App\Models\Voucher::where('type', 'payment')->count();
            $transferCount = \App\Models\Voucher::where('type', 'transfer')->count();
            $depositCount = \App\Models\Voucher::where('type', 'deposit')->count();
            $withdrawCount = \App\Models\Voucher::where('type', 'withdraw')->count();
            
            // جلب جميع الصناديق النقدية مع الأرصدة بالعملات المختلفة
            $cashBoxes = \App\Models\Account::where('is_cash_box', 1)->get();
            $currencies = \App\Models\Currency::where('is_active', true)->get();
            
            $userCashBoxes = $cashBoxes->map(function($box) use ($currencies) {
                $currencyBalances = [];
                
                foreach ($currencies as $currency) {
                    $balance = $box->balance($currency->code);
                    if ($balance != 0) { // فقط إظهار العملات التي بها رصيد
                        $currencyBalances[] = [
                            'currency' => $currency->code,
                            'balance' => $balance,
                            'formatted_balance' => number_format($balance, 2),
                        ];
                    }
                }
                
                return [
                    'id' => $box->id,
                    'name' => $box->name,
                    'default_currency' => $box->default_currency ?? $box->currency ?? 'IQD',
                    'currency_balances' => $currencyBalances,
                    'has_balances' => count($currencyBalances) > 0,
                ];
            });
        } else {
            // إذا كان المستخدم عادي - جلب البيانات المرتبطة بالمستخدم فقط
            $accountsCount = $user->cashBoxes()->count();
            $transactionsCount = \App\Models\Transaction::where('user_id', $user->id)->count();
            $vouchersCount = \App\Models\Voucher::where('created_by', $user->id)->count();
            $usersCount = 1;
            $receiptCount = \App\Models\Voucher::where('type', 'receipt')->where('created_by', $user->id)->count();
            $paymentCount = \App\Models\Voucher::where('type', 'payment')->where('created_by', $user->id)->count();
            $transferCount = \App\Models\Voucher::where('type', 'transfer')->where('created_by', $user->id)->count();
            $depositCount = \App\Models\Voucher::where('type', 'deposit')->where('created_by', $user->id)->count();
            $withdrawCount = \App\Models\Voucher::where('type', 'withdraw')->where('created_by', $user->id)->count();
            
            // جلب صناديق الموظف مع الأرصدة بالعملات المختلفة
            $cashBoxes = $user->cashBoxes()->get();
            $currencies = \App\Models\Currency::where('is_active', true)->get();
            
            $userCashBoxes = $cashBoxes->map(function($box) use ($currencies) {
                $currencyBalances = [];
                
                foreach ($currencies as $currency) {
                    $balance = $box->balance($currency->code);
                    if ($balance != 0) { // فقط إظهار العملات التي بها رصيد
                        $currencyBalances[] = [
                            'currency' => $currency->code,
                            'balance' => $balance,
                            'formatted_balance' => number_format($balance, 2),
                        ];
                    }
                }
                
                return [
                    'id' => $box->id,
                    'name' => $box->name,
                    'default_currency' => $box->default_currency ?? $box->currency ?? 'IQD',
                    'currency_balances' => $currencyBalances,
                    'has_balances' => count($currencyBalances) > 0,
                ];
            });
        }
        
        return view('dashboard', compact(
            'accountsCount', 'transactionsCount', 'vouchersCount', 'usersCount',
            'receiptCount', 'paymentCount', 'transferCount', 'depositCount', 'withdrawCount',
            'userCashBoxes'
        ));
    }
}
