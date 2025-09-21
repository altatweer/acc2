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
            
            // جلب جميع الصناديق النقدية مع الرصيد
            $cashBoxes = \App\Models\Account::where('is_cash_box', 1)->get();
            
            // حساب الأرصدة الفردية لكل صندوق
            $userCashBoxes = $cashBoxes->map(function($box) {
                $balance = $box->journalEntryLines()->selectRaw('SUM(debit-credit) as bal')->value('bal') ?? 0;
                return [
                    'id' => $box->id,
                    'name' => $box->name,
                    'currency' => $box->default_currency ?? $box->currency ?? 'IQD',
                    'balance' => $balance,
                ];
            });

            // تجميع الأرصدة حسب العملة
            $currencyBalances = $cashBoxes->map(function($box) {
                $balance = $box->journalEntryLines()->selectRaw('SUM(debit-credit) as bal')->value('bal') ?? 0;
                return [
                    'currency' => $box->default_currency ?? $box->currency ?? 'IQD',
                    'balance' => $balance,
                ];
            })->groupBy('currency')->map(function($group, $currency) {
                $totalBalance = $group->sum('balance');
                $accountCount = $group->count();
                return [
                    'currency' => $currency,
                    'total_balance' => $totalBalance,
                    'accounts_count' => $accountCount,
                    'formatted_balance' => number_format($totalBalance, 2),
                ];
            })->values();
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
            
            // جلب صناديق الموظف مع الرصيد
            $cashBoxes = $user->cashBoxes()->get();
            
            // حساب الأرصدة الفردية لكل صندوق
            $userCashBoxes = $cashBoxes->map(function($box) {
                $balance = $box->journalEntryLines()->selectRaw('SUM(debit-credit) as bal')->value('bal') ?? 0;
                return [
                    'id' => $box->id,
                    'name' => $box->name,
                    'currency' => $box->default_currency ?? $box->currency ?? 'IQD',
                    'balance' => $balance,
                ];
            });

            // تجميع الأرصدة حسب العملة
            $currencyBalances = $cashBoxes->map(function($box) {
                $balance = $box->journalEntryLines()->selectRaw('SUM(debit-credit) as bal')->value('bal') ?? 0;
                return [
                    'currency' => $box->default_currency ?? $box->currency ?? 'IQD',
                    'balance' => $balance,
                ];
            })->groupBy('currency')->map(function($group, $currency) {
                $totalBalance = $group->sum('balance');
                $accountCount = $group->count();
                return [
                    'currency' => $currency,
                    'total_balance' => $totalBalance,
                    'accounts_count' => $accountCount,
                    'formatted_balance' => number_format($totalBalance, 2),
                ];
            })->values();
        }
        
        return view('dashboard', compact(
            'accountsCount', 'transactionsCount', 'vouchersCount', 'usersCount',
            'receiptCount', 'paymentCount', 'transferCount', 'depositCount', 'withdrawCount',
            'userCashBoxes', 'currencyBalances'
        ));
    }
}
