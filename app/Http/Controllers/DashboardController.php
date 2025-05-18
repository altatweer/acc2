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
            $userCashBoxes = \App\Models\Account::where('is_cash_box', 1)->get()->map(function($box) {
                $balance = $box->journalEntryLines()->selectRaw('SUM(debit-credit) as bal')->value('bal') ?? 0;
                return [
                    'id' => $box->id,
                    'name' => $box->name,
                    'currency' => $box->currency,
                    'balance' => $balance,
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
            
            // جلب صناديق الموظف مع الرصيد
            $userCashBoxes = $user->cashBoxes()->get()->map(function($box) {
                $balance = $box->journalEntryLines()->selectRaw('SUM(debit-credit) as bal')->value('bal') ?? 0;
                return [
                    'id' => $box->id,
                    'name' => $box->name,
                    'currency' => $box->currency,
                    'balance' => $balance,
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
