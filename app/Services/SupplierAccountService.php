<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Supplier;
use App\Models\AccountingSetting;
use App\Models\Currency;

class SupplierAccountService
{
    /**
     * إنشاء حساب محاسبي للمورد
     * 
     * @param Supplier $supplier
     * @return Account
     */
    public static function createAccountForSupplier(Supplier $supplier): Account
    {
        // جلب فئة حسابات الموردين المختارة من الإعدادات
        $parentAccountId = AccountingSetting::get('default_suppliers_account');
        
        if (!$parentAccountId) {
            throw new \Exception('يجب تحديد فئة حسابات الموردين في إعدادات النظام المحاسبي أولاً.');
        }
        
        $parentAccount = Account::find($parentAccountId);
        
        if (!$parentAccount || !$parentAccount->is_group) {
            throw new \Exception('الفئة المحددة للموردين غير موجودة أو ليست فئة صحيحة.');
        }
        
        // إنشاء كود الحساب التلقائي
        $accountCode = self::generateSupplierAccountCode($supplier);
        
        // إنشاء الحساب
        $account = Account::create([
            'name' => $supplier->name,
            'code' => $accountCode,
            'parent_id' => $parentAccountId,
            'type' => $parentAccount->type, // نفس نوع الفئة الأب
            'nature' => 'credit', // الموردين طبيعتهم دائنة
            'is_group' => false,
            'is_cash_box' => false,
            'supports_multi_currency' => true, // دعم العملات المتعددة
            'default_currency' => Currency::getDefaultCode(),
            'require_currency_selection' => false,
        ]);
        
        return $account;
    }
    
    /**
     * توليد كود الحساب للمورد
     * 
     * @param Supplier $supplier
     * @return string
     */
    private static function generateSupplierAccountCode(Supplier $supplier): string
    {
        // الحصول على كود الفئة الأب
        $parentAccountId = AccountingSetting::get('default_suppliers_account');
        $parentAccount = Account::find($parentAccountId);
        
        if (!$parentAccount) {
            throw new \Exception('فئة الموردين غير محددة في الإعدادات.');
        }
        
        $parentCode = $parentAccount->code;
        
        // البحث عن آخر كود مستخدم تحت هذه الفئة
        $lastAccount = Account::where('parent_id', $parentAccountId)
            ->where('code', 'like', $parentCode . '%')
            ->orderBy('code', 'desc')
            ->first();
        
        if ($lastAccount) {
            // استخراج الرقم التسلسلي من آخر كود
            $lastNumber = (int) substr($lastAccount->code, strlen($parentCode));
            $nextNumber = $lastNumber + 1;
        } else {
            // أول حساب تحت هذه الفئة
            $nextNumber = 1;
        }
        
        // تكوين الكود الجديد مع padding بالأصفار
        return $parentCode . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }
    
    /**
     * تحديث اسم الحساب عند تحديث اسم المورد
     * 
     * @param Supplier $supplier
     * @return void
     */
    public static function updateAccountName(Supplier $supplier): void
    {
        if ($supplier->account) {
            $supplier->account->update([
                'name' => $supplier->name
            ]);
        }
    }
    
    /**
     * التحقق من إمكانية حذف المورد
     * 
     * @param Supplier $supplier
     * @return bool
     */
    public static function canDeleteSupplier(Supplier $supplier): bool
    {
        if (!$supplier->account) {
            return true;
        }
        
        // التحقق من وجود حركات محاسبية
        $hasTransactions = $supplier->account->journalEntryLines()->exists();
        
        // التحقق من وجود فواتير شراء (إذا كانت موجودة)
        $hasPurchaseInvoices = method_exists($supplier, 'purchaseInvoices') 
            ? $supplier->purchaseInvoices()->exists() 
            : false;
        
        return !$hasTransactions && !$hasPurchaseInvoices;
    }
} 