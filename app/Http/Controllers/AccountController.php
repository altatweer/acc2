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
        
        // Iniciar con un código temporal - será actualizado dinámicamente por AJAX cuando se seleccione el tipo
        $nextCode = "0000"; // Código temporal que se actualizará con JavaScript
        
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

        // تحسين توليد الأكواد هرمياً إذا لم يتم تقديمها
        if (empty($validated['code']) || $validated['code'] === '0000') {
            if (!empty($validated['parent_id'])) {
                // فئة فرعية تحت فئة رئيسية: زيادة بمقدار 100
                $parent = Account::find($validated['parent_id']);
                $base = (int) $parent->code;
                
                // الحصول على أقصى رمز للفئات الفرعية تحت نفس الأب
                $siblingCodes = Account::where('parent_id', $parent->id)
                    ->where('is_group', 1)
                    ->orderBy('code', 'desc')
                    ->pluck('code')
                    ->map(fn($c) => (int) $c);
                
                if ($siblingCodes->count() > 0) {
                    // إذا كان هناك فئات فرعية، استخدم أكبر رمز موجود + 100
                    $nextCode = $siblingCodes->first() + 100;
                } else {
                    // إذا لم تكن هناك فئات فرعية، استخدم رمز الأب + 100
                    $nextCode = $base + 100;
                }
                
                $validated['code'] = (string) $nextCode;
                \Log::info('تم توليد كود جديد للفئة الفرعية', [
                    'category_name' => $validated['name'],
                    'parent_id' => $validated['parent_id'],
                    'generated_code' => $validated['code']
                ]);
            } else {
                // فئة رئيسية: زيادة بمقدار 1000 بناءً على النوع
                $baseCodes = [
                    'asset' => 1000,
                    'liability' => 2000,
                    'revenue' => 3000,
                    'expense' => 4000,
                    'equity' => 5000,
                ];
                $baseType = $baseCodes[$validated['type']] ?? 1000;
                
                // الحصول على أقصى رمز للفئات الرئيسية من نفس النوع
                $topSiblingCodes = Account::whereNull('parent_id')
                    ->where('is_group', 1)
                    ->where('type', $validated['type'])
                    ->orderBy('code', 'desc')
                    ->pluck('code')
                    ->map(fn($c) => (int) $c);
                
                if ($topSiblingCodes->count() > 0) {
                    // إذا كان هناك فئات رئيسية من نفس النوع، استخدم أكبر رمز موجود + 1000
                    $lastCode = $topSiblingCodes->first();
                    // تحقق مما إذا كان الكود ضمن نطاق النوع الحالي
                    if ($lastCode >= $baseType && $lastCode < $baseType + 1000) {
                        $nextCode = $lastCode + 100;
                    } else {
                        $nextCode = $baseType;
                    }
                } else {
                    // إذا لم تكن هناك فئات رئيسية من نفس النوع، استخدم الرمز الأساسي
                    $nextCode = $baseType;
                }
                
                $validated['code'] = (string) $nextCode;
                \Log::info('تم توليد كود جديد للفئة الرئيسية', [
                    'category_name' => $validated['name'],
                    'type' => $validated['type'],
                    'generated_code' => $validated['code']
                ]);
            }
        } else {
            \Log::info('تم استخدام كود مخصص للفئة', [
                'category_name' => $validated['name'],
                'custom_code' => $validated['code']
            ]);
        }

        // تأكد من عدم إنشاء فئة بكود 0000
        if ($validated['code'] === '0000') {
            \Log::error('محاولة إنشاء فئة بكود صفري', [
                'category_name' => $validated['name']
            ]);
            return back()->withInput()->with('error', 'لا يمكن إنشاء فئة بكود 0000، الرجاء تحديد كود مناسب أو نوع للفئة');
        }

        $validated['is_group'] = 1;
        $validated['is_cash_box'] = 0;
        $validated['nature'] = null;

        Account::create($validated);

        return redirect()->localizedRoute('accounts.index')->with('success', __('messages.created_success'));
    }

    public function createAccount()
    {
        $categories = Account::where('is_group', 1)->get();
        $currencies = Currency::all();
        
        // Iniciar con un código temporal - será actualizado dinámicamente por AJAX cuando se seleccione la categoría
        $nextCode = "0000"; // Código temporal que se actualizará con JavaScript
        
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

        // تحسين توليد كود الحساب تحت الفئة: زيادة بمقدار 1
        if (empty($validated['code']) || $validated['code'] === '0000') {
            $parent = Account::find($validated['parent_id']);
            $parentCode = (int) $parent->code;
            
            // الحصول على أكبر كود حساب تحت نفس الفئة
            $siblingCodes = Account::where('parent_id', $parent->id)
                ->where('is_group', 0)
                ->orderBy('code', 'desc')
                ->pluck('code')
                ->map(fn($c) => (int) $c);
            
            if ($siblingCodes->count() > 0) {
                // إذا كان هناك حسابات فرعية، استخدم أكبر رمز موجود + 1
                $lastCode = $siblingCodes->first();
                // تأكد من أن الكود مناسب (أكبر من كود الفئة الأم)
                if ($lastCode >= $parentCode) {
                    $nextCode = $lastCode + 1;
                } else {
                    $nextCode = $parentCode + 1;
                }
            } else {
                // إذا لم تكن هناك حسابات فرعية، استخدم كود الفئة الأم + 1
                $nextCode = $parentCode + 1;
            }
            
            $validated['code'] = (string) $nextCode;
            \Log::info('تم توليد كود جديد للحساب', [
                'account_name' => $validated['name'],
                'parent_id' => $validated['parent_id'],
                'generated_code' => $validated['code']
            ]);
        } else {
            \Log::info('تم استخدام كود مخصص للحساب', [
                'account_name' => $validated['name'],
                'custom_code' => $validated['code']
            ]);
        }

        // تأكد من عدم إنشاء حساب بكود 0000
        if ($validated['code'] === '0000') {
            \Log::error('محاولة إنشاء حساب بكود صفري', [
                'account_name' => $validated['name']
            ]);
            return back()->withInput()->with('error', 'لا يمكن إنشاء حساب بكود 0000، الرجاء تحديد فئة أو كود مناسب');
        }

        Account::create($validated);

        return redirect()->localizedRoute('accounts.real')->with('success', __('messages.created_success'));
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

            return redirect()->localizedRoute('accounts.index')->with('success', __('messages.updated_success'));
        }

        // منع تغيير عملة الحساب إذا كان مرتبط بمعاملات مالية
        if (!$account->is_group && $account->currency !== $validated['currency']) {
            $hasJournalEntries = $account->journalEntryLines()->exists();
            $hasTransactions = $account->transactions()->exists();
            
            if ($hasJournalEntries || $hasTransactions) {
                return back()->withInput()->with('error', __('messages.cannot_change_account_currency_with_transactions'));
            }
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

        return redirect()->localizedRoute('accounts.real')->with('success', __('messages.updated_success'));
    }

    public function destroy(Account $account)
    {
        // منع الحذف إذا كان هناك حسابات أبناء للفئة
        if ($account->is_group && $account->children()->exists()) {
            return back()->with('error', __('messages.cannot_delete_account_with_children'));
        }
        
        // فحص مفصل للعمليات المالية
        $hasJournalEntries = $account->journalEntryLines()->exists();
        $hasTransactions = $account->transactions()->exists();
        
        // توفير رسالة خطأ أكثر تحديداً
        if ($hasJournalEntries || $hasTransactions) {
            // سجل دليل لمراقبة أي حسابات تحاول حذفها وبها عمليات مالية
            \Log::warning('محاولة حذف حساب به عمليات مالية', [
                'account_id' => $account->id,
                'account_name' => $account->name,
                'account_code' => $account->code,
                'has_journal_entries' => $hasJournalEntries,
                'has_transactions' => $hasTransactions,
                'user_id' => auth()->id(),
                'user_name' => auth()->user() ? auth()->user()->name : 'غير مسجل دخول'
            ]);
            
            return back()->with('error', __('messages.cannot_delete_account_with_transactions'));
        }
        
        try {
            $account->delete();
            return back()->with('success', __('messages.deleted_success'));
        } catch (\Exception $e) {
            \Log::error('خطأ في حذف الحساب', [
                'account_id' => $account->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', __('messages.error_deleting_account'));
        }
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

        \Log::debug('طلب توليد كود جديد', [
            'is_group' => $isGroup,
            'parent_id' => $parentId,
            'type' => $type
        ]);

        if ($isGroup) {
            // توليد كود للفئة
            if ($parentId) {
                // فئة فرعية تحت فئة رئيسية
                $parent = Account::find($parentId);
                if (!$parent) {
                    \Log::error('فئة الأب غير موجودة', ['parent_id' => $parentId]);
                    return response()->json(['error' => 'لم يتم العثور على فئة الأب'], 400);
                }
                
                $base = (int) $parent->code;
                \Log::debug('كود فئة الأب', ['base' => $base]);
                
                // الحصول على أقصى رمز للفئات الفرعية تحت نفس الأب
                $maxSibling = Account::where('parent_id', $parentId)
                    ->where('is_group', 1)
                    ->orderBy('code', 'desc')
                    ->first();
                
                if ($maxSibling) {
                    // إذا كان هناك فئات فرعية، استخدم أكبر رمز موجود + 100
                    $siblingCode = (int)$maxSibling->code;
                    $nextCode = $siblingCode + 100;
                    \Log::debug('تم إيجاد فئات فرعية', ['max_code' => $siblingCode, 'next_code' => $nextCode]);
                } else {
                    // إذا لم تكن هناك فئات فرعية، استخدم رمز الأب + 100
                    $nextCode = $base + 100;
                    \Log::debug('لا يوجد فئات فرعية', ['parent_code' => $base, 'next_code' => $nextCode]);
                }
            } else {
                // فئة رئيسية
                $baseCodes = [
                    'asset' => 1000,
                    'liability' => 2000,
                    'revenue' => 3000,
                    'expense' => 4000,
                    'equity' => 5000,
                ];
                
                if (!isset($baseCodes[$type])) {
                    \Log::error('نوع الحساب غير صالح', ['type' => $type]);
                    return response()->json(['error' => 'نوع الحساب غير صالح'], 400);
                }
                
                $baseType = $baseCodes[$type];
                \Log::debug('نوع الحساب والكود الأساسي', ['type' => $type, 'base_code' => $baseType]);
                
                // الحصول على أقصى رمز للفئات الرئيسية من نفس النوع
                $maxSibling = Account::whereNull('parent_id')
                    ->where('is_group', 1)
                    ->where('type', $type)
                    ->orderBy('code', 'desc')
                    ->first();
                
                if ($maxSibling) {
                    $lastCode = (int)$maxSibling->code;
                    \Log::debug('تم إيجاد فئة رئيسية', ['type' => $type, 'max_code' => $lastCode]);
                    
                    // تحقق مما إذا كان الكود ضمن نطاق النوع الحالي أو استخدم الكود الأساسي + 100
                    if ($lastCode >= $baseType && $lastCode < $baseType + 9000) {
                        $nextCode = $lastCode + 100;
                    } else {
                        $nextCode = $baseType + 100;
                    }
                } else {
                    // إذا لم تكن هناك فئات رئيسية من نفس النوع، استخدم الرمز الأساسي
                    $nextCode = $baseType;
                    \Log::debug('لا يوجد فئات رئيسية من نفس النوع', ['type' => $type, 'next_code' => $nextCode]);
                }
            }
        } else {
            // توليد كود للحساب
            if (!$parentId) {
                \Log::error('لم يتم تحديد فئة الأب للحساب');
                return response()->json(['error' => 'يجب تحديد فئة الأب للحساب'], 400);
            }
            
            $parent = Account::find($parentId);
            if (!$parent) {
                \Log::error('فئة الأب غير موجودة', ['parent_id' => $parentId]);
                return response()->json(['error' => 'لم يتم العثور على فئة الأب'], 400);
            }
            
            $base = (int) $parent->code;
            \Log::debug('كود فئة الأب للحساب', ['base' => $base]);
            
            // الحصول على أقصى كود للحسابات تحت نفس الفئة
            $maxSibling = Account::where('parent_id', $parentId)
                ->where('is_group', 0)
                ->orderBy('code', 'desc')
                ->first();
            
            if ($maxSibling) {
                // إذا كان هناك حسابات، استخدم أكبر رمز موجود + 1
                $siblingCode = (int)$maxSibling->code;
                $nextCode = $siblingCode + 1;
                \Log::debug('تم إيجاد حسابات أخرى في نفس الفئة', ['max_code' => $siblingCode, 'next_code' => $nextCode]);
            } else {
                // إذا لم تكن هناك حسابات، استخدم رمز الفئة الأم + 1
                $nextCode = $base + 1;
                \Log::debug('لا يوجد حسابات في هذه الفئة', ['parent_code' => $base, 'next_code' => $nextCode]);
            }
        }
        
        \Log::debug('تم توليد كود جديد بنجاح', ['next_code' => $nextCode]);
        return response()->json(['nextCode' => (string) $nextCode]);
    }

    // Add a new method to fix zero codes
    public function fixZeroCodes()
    {
        // تأكد من أن المستخدم لديه صلاحيات المسؤول
        if (!auth()->user()->isSuperAdmin() && !auth()->user()->hasRole('admin')) {
            \Log::warning('محاولة إصلاح الأكواد من مستخدم غير مصرح له', [
                'user_id' => auth()->id(),
                'user_name' => auth()->user()->name
            ]);
            return back()->with('error', 'ليس لديك صلاحية تنفيذ هذه العملية');
        }
        
        // البحث عن الحسابات ذات الأكواد الصفرية
        $zeroAccounts = Account::where('code', '0000')->get();
        
        if ($zeroAccounts->isEmpty()) {
            return back()->with('info', 'لا توجد حسابات بأكواد صفرية تحتاج إلى إصلاح');
        }
        
        $fixedCount = 0;
        $errors = [];
        
        foreach ($zeroAccounts as $account) {
            try {
                if ($account->is_group) {
                    // إصلاح كود الفئة
                    if ($account->parent_id) {
                        // فئة فرعية
                        $parent = Account::find($account->parent_id);
                        $basCode = (int) $parent->code;
                        
                        $maxSiblingCode = Account::where('parent_id', $account->parent_id)
                            ->where('is_group', 1)
                            ->where('id', '!=', $account->id)
                            ->orderBy('code', 'desc')
                            ->value('code');
                            
                        $newCode = $maxSiblingCode ? ((int) $maxSiblingCode) + 100 : $basCode + 100;
                    } else {
                        // فئة رئيسية
                        $baseCodes = [
                            'asset' => 1000,
                            'liability' => 2000,
                            'revenue' => 3000,
                            'expense' => 4000,
                            'equity' => 5000,
                        ];
                        
                        $baseCode = $baseCodes[$account->type] ?? 1000;
                        
                        $maxSiblingCode = Account::whereNull('parent_id')
                            ->where('is_group', 1)
                            ->where('type', $account->type)
                            ->where('id', '!=', $account->id)
                            ->orderBy('code', 'desc')
                            ->value('code');
                            
                        $newCode = $maxSiblingCode ? ((int) $maxSiblingCode) + 100 : $baseCode;
                    }
                } else {
                    // إصلاح كود الحساب
                    if (!$account->parent_id) {
                        $errors[] = "الحساب {$account->name} ليس له فئة أب، لا يمكن إصلاحه تلقائياً";
                        continue;
                    }
                    
                    $parent = Account::find($account->parent_id);
                    $baseCode = (int) $parent->code;
                    
                    $maxSiblingCode = Account::where('parent_id', $account->parent_id)
                        ->where('is_group', 0)
                        ->where('id', '!=', $account->id)
                        ->orderBy('code', 'desc')
                        ->value('code');
                        
                    $newCode = $maxSiblingCode ? ((int) $maxSiblingCode) + 1 : $baseCode + 1;
                }
                
                $account->update(['code' => $newCode]);
                $fixedCount++;
                
                \Log::info('تم إصلاح كود الحساب', [
                    'account_id' => $account->id,
                    'account_name' => $account->name,
                    'old_code' => '0000',
                    'new_code' => $newCode
                ]);
            } catch (\Exception $e) {
                \Log::error('خطأ في إصلاح كود الحساب', [
                    'account_id' => $account->id,
                    'account_name' => $account->name,
                    'error' => $e->getMessage()
                ]);
                
                $errors[] = "حدث خطأ في إصلاح حساب {$account->name}: {$e->getMessage()}";
            }
        }
        
        $message = "تم إصلاح {$fixedCount} حساب بنجاح";
        
        if (!empty($errors)) {
            $message .= ". حدثت أخطاء: " . implode(', ', $errors);
            return back()->with('warning', $message);
        }
        
        return back()->with('success', $message);
    }
}
