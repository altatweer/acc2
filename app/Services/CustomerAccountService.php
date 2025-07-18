<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Customer;
use App\Models\AccountingSetting;
use App\Models\Currency;

class CustomerAccountService
{
    /**
     * إنشاء حساب محاسبي للعميل
     * 
     * @param Customer|object $customer
     * @return Account
     */
    public static function createAccountForCustomer($customer): Account
    {
        // جلب فئة حسابات العملاء المختارة من الإعدادات
        $parentAccountId = AccountingSetting::get('default_customers_account');
        
        if (!$parentAccountId) {
            throw new \Exception('يجب تحديد فئة حسابات العملاء في إعدادات النظام المحاسبي أولاً.');
        }
        
        $parentAccount = Account::find($parentAccountId);
        
        if (!$parentAccount || !$parentAccount->is_group) {
            throw new \Exception('الفئة المحددة للعملاء غير موجودة أو ليست فئة صحيحة.');
        }
        
        // إنشاء كود الحساب التلقائي
        $accountCode = self::generateCustomerAccountCode($customer);
        
        // إنشاء الحساب
        $account = Account::create([
            'name' => $customer->name,
            'code' => $accountCode,
            'parent_id' => $parentAccountId,
            'type' => $parentAccount->type, // نفس نوع الفئة الأب
            'nature' => 'debit', // العملاء طبيعتهم مدينة
            'is_group' => false,
            'is_cash_box' => false,
            'supports_multi_currency' => true, // دعم العملات المتعددة
            'default_currency' => Currency::getDefaultCode(),
            'require_currency_selection' => false,
        ]);
        
        return $account;
    }
    
    /**
     * توليد كود الحساب للعميل
     * 
     * @param Customer|object $customer
     * @return string
     */
    private static function generateCustomerAccountCode($customer): string
    {
        // الحصول على كود الفئة الأب
        $parentAccountId = AccountingSetting::get('default_customers_account');
        $parentAccount = Account::find($parentAccountId);
        
        if (!$parentAccount) {
            throw new \Exception('فئة العملاء غير محددة في الإعدادات.');
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
     * تحديث اسم الحساب عند تحديث اسم العميل
     * 
     * @param Customer $customer
     * @return void
     */
    public static function updateAccountName(Customer $customer): void
    {
        if ($customer->account) {
            $customer->account->update([
                'name' => $customer->name
            ]);
        }
    }
    
    /**
     * التحقق من إمكانية حذف العميل
     * 
     * @param Customer $customer
     * @return bool
     */
    public static function canDeleteCustomer(Customer $customer): bool
    {
        if (!$customer->account) {
            return true;
        }
        
        // التحقق من وجود حركات محاسبية
        $hasTransactions = $customer->account->journalEntryLines()->exists();
        
        // التحقق من وجود فواتير
        $hasInvoices = $customer->invoices()->exists();
        
        return !$hasTransactions && !$hasInvoices;
    }
} 