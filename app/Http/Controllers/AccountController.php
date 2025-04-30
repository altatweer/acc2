<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index()
    {
        // فقط الحسابات الفعلية (is_group = 0)
        $accounts = Account::where('is_group', 0)->get();
        return view('accounts.index', compact('accounts'));
    }

    public function create()
    {
        // استرجاع الفئات فقط لاختيار الفئة الأب
        $categories = Account::where('is_group', 1)->get();
        return view('accounts.create', compact('categories'));
    }

    public function store(Request $request)
    {
     $validated = $request->validate([
    'name' => 'required|string|max:255',
    'code' => 'required|string|max:255|unique:accounts,code',
    'record_type' => 'required|in:category,account',
    'type' => 'nullable|in:asset,liability,revenue,expense,equity',
    'parent_id' => 'nullable|exists:accounts,id',
    'nature' => 'nullable|in:debit,credit',
]);


        if ($request->is_group == 1) {
            // إنشاء فئة
            Account::create([
                'name' => $request->name,
                'type' => $request->type,
                'is_group' => 1,
                'parent_id' => $request->parent_id,
                'nature' => null,
            ]);
        } else {
            // إنشاء حساب فعلي
            $parent = Account::findOrFail($request->parent_id);
            Account::create([
                'name' => $request->name,
                'parent_id' => $request->parent_id,
                'type' => $parent->type,
                'is_group' => 0,
                'nature' => $request->nature,
            ]);
        }

        return redirect()->route('accounts.index')->with('success', 'تمت الإضافة بنجاح.');
    }

    public function edit(Account $account)
    {
        $categories = Account::where('is_group', 1)->where('id', '!=', $account->id)->get();
        return view('accounts.edit', compact('account', 'categories'));
    }

    public function update(Request $request, Account $account)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'is_group' => 'required|in:0,1',
            'type' => 'nullable|in:asset,liability,revenue,expense,equity',
            'parent_id' => 'nullable|exists:accounts,id',
            'nature' => 'nullable|in:debit,credit',
        ]);

        if ($request->is_group == 1) {
            // تحديث فئة
            $account->update([
                'name' => $request->name,
                'type' => $request->type,
                'parent_id' => $request->parent_id,
                'is_group' => 1,
                'nature' => null,
            ]);
        } else {
            // تحديث حساب فعلي
            $parent = Account::findOrFail($request->parent_id);
            $account->update([
                'name' => $request->name,
                'parent_id' => $request->parent_id,
                'type' => $parent->type,
                'is_group' => 0,
                'nature' => $request->nature,
            ]);
        }

        return redirect()->route('accounts.index')->with('success', 'تم تحديث الحساب بنجاح.');
    }

    public function destroy(Account $account)
    {
        $account->delete();
        return redirect()->route('accounts.index')->with('success', 'تم حذف الحساب.');
    }

    public function chart()
    {
        $accounts = Account::whereNull('parent_id')->get(); // عرض الشجرة من الجذور
        return view('accounts.chart', compact('accounts'));
    }
}
