<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Account;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::latest()->paginate(20);
        return view('transactions.index', compact('transactions'));
    }

    public function create()
    {
        $accounts = Account::where('is_group', 0)->get();
        return view('transactions.create', compact('accounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'type' => 'required|in:deposit,withdraw,transfer',
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|string|max:3',
            'exchange_rate' => 'required|numeric|min:0.000001',
            'account_id' => 'required|exists:accounts,id',
            'target_account_id' => 'nullable|exists:accounts,id',
            'description' => 'nullable|string|max:1000',
        ]);

        $validated['user_id'] = auth()->id();

        Transaction::create($validated);

        return redirect()->route('transactions.index')->with('success', 'تم تسجيل الحركة بنجاح.');
    }
}