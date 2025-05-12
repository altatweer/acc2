<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Currency;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function __construct()
    {
        // أزل أو علق التحقق من الصلاحية عن دالة byCurrency
        // $this->middleware('can:عرض الحسابات')->only(['index', 'realAccounts', 'show', 'chart', 'byCurrency']);
        $this->middleware('can:add_account')->only(['createAccount', 'storeAccount']);
        $this->middleware('can:add_category')->only(['createGroup', 'storeGroup']);
        $this->middleware('can:edit_account')->only(['edit', 'update']);
        $this->middleware('can:delete_account')->only(['destroy']);
    }

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
        // Generate next group code (numeric max +100)
        $lastCode = Account::where('is_group',1)->max('code');
        $nextCode = $lastCode && is_numeric($lastCode) ? (int)$lastCode + 100 : 1000;
        return view('accounts.create-group', compact('categories', 'nextCode'));
    }

    public function storeGroup(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:20|unique:accounts,code',
            'type' => 'required|in:asset,liability,revenue,expense,equity',
            'parent_id' => 'nullable|exists:accounts,id',
        ]);

        // Auto-generate code hierarchically if not provided
        if (empty($validated['code'])) {
            if (!empty($validated['parent_id'])) {
                // Child group under parent: increment by 100
                $parent = Account::find($validated['parent_id']);
                $base = (int) $parent->code;
                $siblingCodes = Account::where('parent_id', $parent->id)
                    ->where('is_group', 1)
                    ->pluck('code')
                    ->map(fn($c) => (int) $c);
                $maxDiff = $siblingCodes->map(fn($code) => ($code - $base) / 100)
                    ->max() ?? 0;
                $nextIdx = (int) $maxDiff + 1;
                $validated['code'] = (string) ($base + $nextIdx * 100);
            } else {
                // Top-level group: increment by 100 based on existing top-level
                $baseCodes = [
                    'asset' => 1000,
                    'liability' => 2000,
                    'revenue' => 3000,
                    'expense' => 4000,
                    'equity' => 5000,
                ];
                $base = $baseCodes[$validated['type']] ?? 0;
                $siblingCodes = Account::whereNull('parent_id')
                    ->where('is_group', 1)
                    ->pluck('code')
                    ->map(fn($c) => (int) $c);
                $maxDiff = $siblingCodes->map(fn($code) => ($code - $base) / 100)
                    ->max() ?? 0;
                $nextIdx = (int) $maxDiff + 1;
                $validated['code'] = (string) ($base + $nextIdx * 100);
            }
        }

        $validated['is_group'] = 1;
        $validated['is_cash_box'] = 0;
        $validated['nature'] = null;

        Account::create($validated);

        return redirect()->localizedRoute('accounts.index')->with('success', 'تمت إضافة الفئة بنجاح.');
    }

    public function createAccount()
    {
        $categories = Account::where('is_group', 1)->get();
        $currencies = Currency::all();
        // Generate next account code under last code +1
        $lastCode = Account::where('is_group',0)->max('code');
        $nextCode = $lastCode && is_numeric($lastCode) ? (int)$lastCode + 1 : 1001;
        return view('accounts.create-account', compact('categories', 'currencies', 'nextCode'));
    }

    public function storeAccount(Request $request)
    {
        // Validate including currency only for actual accounts
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'code'         => 'nullable|string|max:20|unique:accounts,code,NULL,id,currency,' . $request->currency,
            'parent_id'    => 'required|exists:accounts,id',
            'nature'       => 'required|in:debit,credit',
            'currency'     => 'required|string|max:3|exists:currencies,code',
        ]);

        $parent = Account::find($validated['parent_id']);

        $validated['type']       = $parent->type ?? 'asset';
        $validated['is_group']   = 0;
        // Persist cash box flag
        $validated['is_cash_box'] = $request->boolean('is_cash_box');

        // Auto-generate account code under parent: increment by 1
        if (empty($validated['code'])) {
            $parent = Account::find($validated['parent_id']);
            $base = (int) $parent->code;
            $siblingCodes = Account::where('parent_id', $parent->id)
                ->where('is_group', 0)
                ->pluck('code')
                ->map(fn($c) => (int) $c);
            $maxDiff = $siblingCodes->map(fn($code) => $code - $base)
                ->max() ?? 0;
            $nextIdx = (int) $maxDiff + 1;
            $validated['code'] = (string) ($base + $nextIdx);
        }

        Account::create($validated);

        return redirect()->localizedRoute('accounts.real')->with('success', 'تمت إضافة الحساب بنجاح.');
    }

    public function edit(Account $account)
    {
        $categories = Account::where('is_group', 1)->where('id', '!=', $account->id)->get();
        $currencies = Currency::all();

        if ($account->is_group) {
            return view('accounts.edit-group', compact('account', 'categories'));
        } else {
            return view('accounts.edit-account', compact('account', 'categories', 'currencies'));
        }
    }

    public function update(Request $request, Account $account)
    {
        // Validate input based on is_group flag: 1=group (category), 0=actual account
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'code'        => 'nullable|string|max:20|unique:accounts,code,' . $account->id . ',id,currency,' . $request->currency,
            'parent_id'   => 'nullable|exists:accounts,id',
            'type'        => 'required_if:is_group,1|in:asset,liability,revenue,expense,equity',
            'nature'      => 'required_if:is_group,0|in:debit,credit',
            'is_cash_box' => 'required_if:is_group,0|boolean',
            'currency'    => 'required_if:is_group,0|string|max:3|exists:currencies,code',
            'is_group'    => 'required|boolean',
        ]);

        // Determine cash box flag
        $cashFlag = $request->boolean('is_cash_box');

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

            return redirect()->localizedRoute('accounts.index')->with('success', 'تم تحديث الفئة بنجاح.');
        }

        $account->update([
            'name'        => $validated['name'],
            'code'        => $validated['code'],
            'parent_id'   => $validated['parent_id'],
            'type'        => $account->parent->type ?? 'asset',
            'nature'      => $validated['nature'],
            'currency'    => $validated['currency'],
            'is_cash_box' => $cashFlag,
            'is_group'    => 0,
        ]);

        return redirect()->localizedRoute('accounts.real')->with('success', 'تم تحديث الحساب بنجاح.');
    }

    public function destroy(Account $account)
    {
        // منع الحذف إذا كان هناك حسابات أبناء للفئة
        if ($account->is_group && $account->children()->exists()) {
            return back()->with('error', 'لا يمكن حذف الفئة لوجود حسابات أو فئات تابعة لها.');
        }
        // منع الحذف إذا كان هناك حركات مالية
        $hasTransactions = $account->journalEntryLines()->exists() || $account->transactions()->exists();
        if ($hasTransactions) {
            return back()->with('error', 'لا يمكن حذف الحساب لوجود حركات مالية مرتبطة به.');
        }
        $account->delete();
        return back()->with('success', 'تم الحذف بنجاح.');
    }

    public function chart()
    {
        $accounts = Account::whereNull('parent_id')->with('childrenRecursive')->get();
        return view('accounts.chart', compact('accounts'));
    }

    /**
     * عرض تفاصيل الحساب بما في ذلك الأرصدة المحسوبة من المعاملات.
     */
    public function show(Account $account)
    {
        $defaultCurrency = Currency::where('is_default', true)->first();

        // جلب الحركات من القيود الجديدة فقط بعملة الحساب
        $lines = $account->journalEntryLines()
            ->where('currency', $account->currency)
            ->with('journalEntry')
            ->orderBy('created_at')
            ->get();

        // حساب الرصيد فقط بعملة الحساب
        $balance = $lines->reduce(function($carry, $line) {
            return $carry + $line->debit - $line->credit;
        }, 0);

        return view('accounts.show', compact('account', 'defaultCurrency', 'lines', 'balance'));
    }

    /**
     * Return cash and target accounts matching a currency (for AJAX).
     */
    public function byCurrency(string $currency)
    {
        $user = auth()->user();
        if ($user->isSuperAdmin() || $user->hasRole('admin')) {
            $cashAccounts = Account::where('is_cash_box', 1)
                ->where('currency', $currency)
                ->get(['id', 'code', 'name']);
        } else {
            $cashAccounts = $user->cashBoxes()
                ->where('is_cash_box', 1)
                ->where('currency', $currency)
                ->select('accounts.id', 'accounts.code', 'accounts.name')
                ->get();
        }
        $targetAccounts = Account::where('is_group', 0)
            ->where('is_cash_box', 0)
            ->where('currency', $currency)
            ->get(['id', 'code', 'name']);
        return response()->json(compact('cashAccounts', 'targetAccounts'));
    }

    /**
     * AJAX: return next code for group or account based on hierarchy.
     */
    public function nextCode(Request $request)
    {
        $isGroup = (bool) $request->input('is_group');
        $parentId = $request->input('parent_id');
        $type = $request->input('type');

        if ($isGroup) {
            // group code
            if ($parentId) {
                $parent = Account::find($parentId);
                $base = (int) $parent->code;
                $maxSibling = Account::where('parent_id', $parentId)
                    ->where('is_group', 1)
                    ->pluck('code')
                    ->map(fn($c) => (int)$c)
                    ->max() ?? 0;
                $nextCode = $maxSibling ? $maxSibling + 100 : $base + 100;
            } else {
                $baseCodes = [
                    'asset' => 1000,
                    'liability' => 2000,
                    'revenue' => 3000,
                    'expense' => 4000,
                    'equity' => 5000,
                ];
                $base = $baseCodes[$type] ?? 1000;
                $maxSibling = Account::whereNull('parent_id')
                    ->where('is_group', 1)
                    ->pluck('code')
                    ->map(fn($c) => (int)$c)
                    ->max() ?? 0;
                $nextCode = $maxSibling ? $maxSibling + 100 : $base;
            }
        } else {
            // account code under parent
            $parent = Account::find($parentId);
            $base = (int) $parent->code;
            $maxSibling = Account::where('parent_id', $parentId)
                ->where('is_group', 0)
                ->pluck('code')
                ->map(fn($c) => (int)$c)
                ->max() ?? 0;
            $nextCode = $maxSibling ? $maxSibling + 1 : $base + 1;
        }

        return response()->json(['nextCode' => (string) $nextCode]);
    }
}
