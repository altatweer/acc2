<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        // جلب جميع السطور المحاسبية مع القيد والحساب
        $lines = JournalEntryLine::with(['journalEntry', 'account'])
            ->orderByDesc('created_at')
            ->paginate(30);
        return view('transactions.index', compact('lines'));
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

        // تحقق من مطابقة العملة مع الحسابات
        $account = \App\Models\Account::find($validated['account_id']);
        if (!$account || $account->currency !== $validated['currency']) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'account_id' => ['عملة الحساب يجب أن تطابق العملة المدخلة.']
            ]);
        }
        if (!empty($validated['target_account_id'])) {
            $target = \App\Models\Account::find($validated['target_account_id']);
            if (!$target || $target->currency !== $validated['currency']) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'target_account_id' => ['عملة الحساب الهدف يجب أن تطابق العملة المدخلة.']
                ]);
            }
        }

        Transaction::create($validated);

        return redirect()->route('transactions.index')->with('success', 'تم تسجيل الحركة بنجاح.');
    }
}