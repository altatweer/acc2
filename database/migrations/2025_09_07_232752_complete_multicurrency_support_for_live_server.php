<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * هجرة شاملة لإضافة دعم العملات المتعددة الكامل للخادم المباشر
     * تم تصميمها بناءً على فحص النظام المحلي لضمان التطابق 100%
     */
    public function up(): void
    {
        \Log::info('🚀 بدء تطبيق دعم العملات المتعددة الشامل');
        
        // 1. جدول العملات (currencies)
        if (Schema::hasTable('currencies')) {
            \Log::info('📊 تحديث جدول currencies');
            Schema::table('currencies', function (Blueprint $table) {
                if (!Schema::hasColumn('currencies', 'is_active')) {
                    $table->boolean('is_active')->default(true)->after('exchange_rate')->comment('العملة نشطة');
                }
                if (!Schema::hasColumn('currencies', 'name_ar')) {
                    $table->string('name_ar', 255)->nullable()->after('name')->comment('اسم العملة بالعربية');
                }
                if (!Schema::hasColumn('currencies', 'decimal_places')) {
                    $table->tinyInteger('decimal_places')->default(2)->after('symbol')->comment('عدد الخانات العشرية');
                }
                if (!Schema::hasColumn('currencies', 'country')) {
                    $table->string('country', 100)->nullable()->after('decimal_places')->comment('اسم الدولة بالإنجليزية');
                }
                if (!Schema::hasColumn('currencies', 'country_ar')) {
                    $table->string('country_ar', 100)->nullable()->after('country')->comment('اسم الدولة بالعربية');
                }
                if (!Schema::hasColumn('currencies', 'sort_order')) {
                    $table->integer('sort_order')->default(0)->after('country_ar')->comment('ترتيب العرض');
                }
            });
        } else {
            \Log::warning('⚠️ جدول currencies غير موجود');
        }

        // 2. جدول المنتجات (items)
        if (Schema::hasTable('items')) {
            \Log::info('📊 تحديث جدول items');
            Schema::table('items', function (Blueprint $table) {
                if (!Schema::hasColumn('items', 'currency')) {
                    $table->string('currency', 3)->default('IQD')->after('unit_price')->comment('عملة السعر');
                }
                if (!Schema::hasColumn('items', 'cost_price')) {
                    $table->decimal('cost_price', 15, 4)->default(0)->after('currency')->comment('سعر التكلفة');
                }
                if (!Schema::hasColumn('items', 'cost_currency')) {
                    $table->string('cost_currency', 3)->default('IQD')->after('cost_price')->comment('عملة التكلفة');
                }
                if (!Schema::hasColumn('items', 'is_multi_currency')) {
                    $table->boolean('is_multi_currency')->default(true)->after('cost_currency')->comment('يدعم أسعار متعددة العملات');
                }
            });
        } else {
            \Log::warning('⚠️ جدول items غير موجود');
        }

        // 3. جدول بنود الفواتير (invoice_items) - الأهم!
        if (Schema::hasTable('invoice_items')) {
            \Log::info('📊 تحديث جدول invoice_items (الأهم!)');
            Schema::table('invoice_items', function (Blueprint $table) {
                if (!Schema::hasColumn('invoice_items', 'currency')) {
                    $table->string('currency', 3)->default('IQD')->after('line_total')->comment('عملة البند');
                }
                if (!Schema::hasColumn('invoice_items', 'exchange_rate')) {
                    $table->decimal('exchange_rate', 15, 10)->default(1.0000000000)->after('currency')->comment('سعر الصرف المستخدم');
                }
                if (!Schema::hasColumn('invoice_items', 'base_currency_total')) {
                    $table->decimal('base_currency_total', 18, 4)->after('exchange_rate')->comment('المجموع بالعملة الأساسية');
                }
            });
        } else {
            \Log::warning('⚠️ جدول invoice_items غير موجود');
        }

        // 4. جدول مدفوعات الرواتب (salary_payments)
        if (Schema::hasTable('salary_payments')) {
            \Log::info('📊 تحديث جدول salary_payments');
            Schema::table('salary_payments', function (Blueprint $table) {
                if (!Schema::hasColumn('salary_payments', 'currency')) {
                    $table->string('currency', 3)->default('IQD')->after('net_salary')->comment('عملة الراتب');
                }
                if (!Schema::hasColumn('salary_payments', 'exchange_rate')) {
                    $table->decimal('exchange_rate', 15, 10)->default(1.0000000000)->after('currency')->comment('سعر الصرف المستخدم');
                }
                if (!Schema::hasColumn('salary_payments', 'base_currency_net_salary')) {
                    $table->decimal('base_currency_net_salary', 18, 2)->after('exchange_rate')->comment('صافي الراتب بالعملة الأساسية');
                }
            });
        } else {
            \Log::warning('⚠️ جدول salary_payments غير موجود');
        }

        // 5. جدول العملاء (customers)
        if (Schema::hasTable('customers')) {
            \Log::info('📊 تحديث جدول customers');
            Schema::table('customers', function (Blueprint $table) {
                if (!Schema::hasColumn('customers', 'default_currency')) {
                    $table->string('default_currency', 3)->default('IQD')->after('account_id')->comment('العملة الافتراضية للعميل');
                }
                if (!Schema::hasColumn('customers', 'credit_limit')) {
                    $table->decimal('credit_limit', 18, 4)->default(0)->after('default_currency')->comment('الحد الائتماني');
                }
                if (!Schema::hasColumn('customers', 'credit_limit_currency')) {
                    $table->string('credit_limit_currency', 3)->default('IQD')->after('credit_limit')->comment('عملة الحد الائتماني');
                }
            });
        } else {
            \Log::warning('⚠️ جدول customers غير موجود');
        }

        // 6. جدول الموظفين (employees)
        if (Schema::hasTable('employees')) {
            \Log::info('📊 تحديث جدول employees');
            Schema::table('employees', function (Blueprint $table) {
                if (!Schema::hasColumn('employees', 'salary')) {
                    $table->decimal('salary', 18, 2)->default(0)->after('status')->comment('الراتب الأساسي');
                }
                if (!Schema::hasColumn('employees', 'salary_currency')) {
                    $table->string('salary_currency', 3)->default('IQD')->after('currency')->comment('عملة الراتب الأساسي');
                }
                if (!Schema::hasColumn('employees', 'base_salary')) {
                    $table->decimal('base_salary', 18, 4)->default(0)->after('salary_currency')->comment('الراتب الأساسي بالعملة الأساسية');
                }
            });
        } else {
            \Log::warning('⚠️ جدول employees غير موجود');
        }

        // تحديث البيانات الموجودة بقيم آمنة
        $this->updateExistingData();
        
        \Log::info('✅ انتهاء تطبيق دعم العملات المتعددة الشامل');
    }
    
    /**
     * تحديث البيانات الموجودة بقيم آمنة
     */
    private function updateExistingData()
    {
        \Log::info('🔄 تحديث البيانات الموجودة');
        
        try {
            // تحديث العملات
            if (Schema::hasTable('currencies') && Schema::hasColumn('currencies', 'is_active')) {
                \DB::statement("UPDATE currencies SET is_active = 1 WHERE is_active IS NULL");
            }
            
            // تحديث المنتجات
            if (Schema::hasTable('items')) {
                if (Schema::hasColumn('items', 'currency')) {
                    \DB::statement("UPDATE items SET currency = 'IQD' WHERE currency IS NULL OR currency = ''");
                }
                if (Schema::hasColumn('items', 'cost_currency')) {
                    \DB::statement("UPDATE items SET cost_currency = 'IQD' WHERE cost_currency IS NULL OR cost_currency = ''");
                }
                if (Schema::hasColumn('items', 'cost_price')) {
                    \DB::statement("UPDATE items SET cost_price = 0 WHERE cost_price IS NULL");
                }
                if (Schema::hasColumn('items', 'is_multi_currency')) {
                    \DB::statement("UPDATE items SET is_multi_currency = 1 WHERE is_multi_currency IS NULL");
                }
            }
            
            // تحديث بنود الفواتير - الأهم!
            if (Schema::hasTable('invoice_items')) {
                if (Schema::hasColumn('invoice_items', 'currency')) {
                    \DB::statement("UPDATE invoice_items SET currency = 'IQD' WHERE currency IS NULL OR currency = ''");
                }
                if (Schema::hasColumn('invoice_items', 'exchange_rate')) {
                    \DB::statement("UPDATE invoice_items SET exchange_rate = 1.0000000000 WHERE exchange_rate IS NULL OR exchange_rate = 0");
                }
                if (Schema::hasColumn('invoice_items', 'base_currency_total')) {
                    \DB::statement("UPDATE invoice_items SET base_currency_total = line_total WHERE base_currency_total IS NULL");
                }
            }
            
            // تحديث مدفوعات الرواتب
            if (Schema::hasTable('salary_payments')) {
                if (Schema::hasColumn('salary_payments', 'currency')) {
                    \DB::statement("UPDATE salary_payments SET currency = 'IQD' WHERE currency IS NULL OR currency = ''");
                }
                if (Schema::hasColumn('salary_payments', 'exchange_rate')) {
                    \DB::statement("UPDATE salary_payments SET exchange_rate = 1.0000000000 WHERE exchange_rate IS NULL OR exchange_rate = 0");
                }
                if (Schema::hasColumn('salary_payments', 'base_currency_net_salary')) {
                    \DB::statement("UPDATE salary_payments SET base_currency_net_salary = net_salary WHERE base_currency_net_salary IS NULL");
                }
            }
            
            // تحديث العملاء
            if (Schema::hasTable('customers')) {
                if (Schema::hasColumn('customers', 'default_currency')) {
                    \DB::statement("UPDATE customers SET default_currency = 'IQD' WHERE default_currency IS NULL OR default_currency = ''");
                }
                if (Schema::hasColumn('customers', 'credit_limit')) {
                    \DB::statement("UPDATE customers SET credit_limit = 0 WHERE credit_limit IS NULL");
                }
                if (Schema::hasColumn('customers', 'credit_limit_currency')) {
                    \DB::statement("UPDATE customers SET credit_limit_currency = 'IQD' WHERE credit_limit_currency IS NULL OR credit_limit_currency = ''");
                }
            }
            
            // تحديث الموظفين
            if (Schema::hasTable('employees')) {
                if (Schema::hasColumn('employees', 'salary_currency')) {
                    \DB::statement("UPDATE employees SET salary_currency = 'IQD' WHERE salary_currency IS NULL OR salary_currency = ''");
                }
                if (Schema::hasColumn('employees', 'salary') && Schema::hasColumn('employees', 'base_salary')) {
                    \DB::statement("UPDATE employees SET salary = COALESCE(base_salary, 0) WHERE salary IS NULL");
                    \DB::statement("UPDATE employees SET base_salary = COALESCE(salary, 0) WHERE base_salary IS NULL");
                }
            }
            
            \Log::info('✅ تم تحديث البيانات الموجودة بنجاح');
            
        } catch (\Exception $e) {
            \Log::error('❌ خطأ في تحديث البيانات: ' . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \Log::info('🔄 التراجع عن هجرة العملات المتعددة');
        
        $tables = [
            'currencies' => ['sort_order', 'country_ar', 'country', 'decimal_places', 'name_ar', 'is_active'],
            'items' => ['is_multi_currency', 'cost_currency', 'cost_price', 'currency'],
            'invoice_items' => ['base_currency_total', 'exchange_rate', 'currency'],
            'salary_payments' => ['base_currency_net_salary', 'exchange_rate', 'currency'],
            'customers' => ['credit_limit_currency', 'credit_limit', 'default_currency'],
            'employees' => ['base_salary', 'salary_currency', 'salary'],
        ];

        foreach ($tables as $tableName => $columns) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($columns, $tableName) {
                    foreach ($columns as $column) {
                        if (Schema::hasColumn($tableName, $column)) {
                            $table->dropColumn($column);
                        }
                    }
                });
            }
        }
        
        \Log::info('✅ تم التراجع عن هجرة العملات المتعددة بنجاح');
    }
};