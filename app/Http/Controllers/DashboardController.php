<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Gather statistics for the dashboard
        $accountsCount = \App\Models\Account::count();
        $transactionsCount = \App\Models\Transaction::count();
        $vouchersCount = \App\Models\Voucher::count();
        $usersCount = \App\Models\User::count();
        $receiptCount = \App\Models\Voucher::where('type', 'receipt')->count();
        $paymentCount = \App\Models\Voucher::where('type', 'payment')->count();
        $transferCount = \App\Models\Voucher::where('type', 'transfer')->count();
        $depositCount = \App\Models\Voucher::where('type', 'deposit')->count();
        $withdrawCount = \App\Models\Voucher::where('type', 'withdraw')->count();

        return view('dashboard', compact(
            'accountsCount', 'transactionsCount', 'vouchersCount', 'usersCount',
            'receiptCount', 'paymentCount', 'transferCount', 'depositCount', 'withdrawCount'
        ));
    }
}
