<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index() // عرض الفئات
    {
        $categories = Account::where('is_group', 1)->with('parent')->paginate(20);
        return view('accounts.index_group', compact('categories'));
    }

    public function realAccounts() // عرض الحسابات الفعلية
    {
        $accounts = Account::where('is_group', 0)->with('parent')->paginate(20);
        return view('accounts.index_real', compact('accounts'));
    }

    public function createGroup()
    {
        $categories = Account::where('is_group', 1)->get();
        return view('accounts.create-group', compact('categories'));
    }

    public function storeGroup(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:accounts,code',
            'type' => 'required|in:asset,liability,revenue,expense,equity',
            'parent_id' => 'nullable|exists:accounts,id',
        ]);

        $validated['is_group'] = 1;
        $validated['is_cash_box'] = 0;
        $validated['nature'] = null;

        Account::create($validated);

        return redirect()->route('accounts.index')->with('success', 'تمت إضافة الفئة بنجاح.');
    }

    public function createAccount()
    {
        $categories = Account::where('is_group', 1)->get();
        return view('accounts.create-account', compact('categories'));
    }

    public function storeAccount(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:accounts,code',
            'parent_id' => 'required|exists:accounts,id',
            'nature' => 'required|in:debit,credit',
            'is_cash_box' => 'nullable|boolean',
        ]);

        $parent = Account::find($validated['parent_id']);

        $validated['type'] = $parent->type ?? 'asset';
        $validated['is_group'] = 0;
        $validated['is_cash_box'] = $request->has('is_cash_box') ? 1 : 0;

        Account::create($validated);

        return redirect()->route('accounts.real')->with('success', 'تمت إضافة الحساب بنجاح.');
    }

    public function edit(Account $account)
    {
        $categories = Account::where('is_group', 1)->where('id', '!=', $account->id)->get();

        if ($account->is_group) {
            return view('accounts.edit-group', compact('account', 'categories'));
        } else {
            return view('accounts.edit-account', compact('account', 'categories'));
        }
    }

    public function update(Request $request, Account $account)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:20|unique:accounts,code,' . $account->id,
            'parent_id' => 'nullable|exists:accounts,id',
            'type' => 'nullable|in:asset,liability,revenue,expense,equity',
            'nature' => 'nullable|in:debit,credit',
            'is_cash_box' => 'nullable|boolean',
            'is_group' => 'required|boolean',
        ]);

        if ($validated['is_group']) {
            $account->update([
                'name' => $validated['name'],
                'code' => $validated['code'],
                'parent_id' => $validated['parent_id'],
                'type' => $validated['type'],
                'nature' => null,
                'is_cash_box' => 0,
                'is_group' => 1,
            ]);

            return redirect()->route('accounts.index')->with('success', 'تم تحديث الفئة بنجاح.');
        }

        $account->update([
            'name' => $validated['name'],
            'code' => $validated['code'],
            'parent_id' => $validated['parent_id'],
            'type' => $account->parent->type ?? 'asset',
            'nature' => $validated['nature'],
            'is_cash_box' => $request->has('is_cash_box') ? 1 : 0,
            'is_group' => 0,
        ]);

        return redirect()->route('accounts.real')->with('success', 'تم تحديث الحساب بنجاح.');
    }

    public function destroy(Account $account)
    {
        $account->delete();
        return back()->with('success', 'تم الحذف بنجاح.');
    }

    public function chart()
    {
        $accounts = Account::whereNull('parent_id')->with('childrenRecursive')->get();
        return view('accounts.chart', compact('accounts'));
    }
}
