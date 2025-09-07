<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. جدول المنتجات (items)
        if (Schema::hasTable('items')) {
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
            
            // تحديث البيانات الموجودة
            \DB::statement("UPDATE items SET currency = 'IQD' WHERE currency IS NULL");
            \DB::statement("UPDATE items SET cost_price = 0 WHERE cost_price IS NULL");
            \DB::statement("UPDATE items SET cost_currency = 'IQD' WHERE cost_currency IS NULL");
            \DB::statement("UPDATE items SET is_multi_currency = 1 WHERE is_multi_currency IS NULL");
        }

        // 2. جدول مدفوعات الرواتب (salary_payments)
        if (Schema::hasTable('salary_payments')) {
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
            
            // تحديث البيانات الموجودة
            \DB::statement("UPDATE salary_payments SET currency = 'IQD' WHERE currency IS NULL");
            \DB::statement("UPDATE salary_payments SET exchange_rate = 1.0000000000 WHERE exchange_rate IS NULL");
            \DB::statement("UPDATE salary_payments SET base_currency_net_salary = net_salary WHERE base_currency_net_salary IS NULL");
        }

        // 3. جدول العملاء (customers)
        if (Schema::hasTable('customers')) {
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
            
            // تحديث البيانات الموجودة
            \DB::statement("UPDATE customers SET default_currency = 'IQD' WHERE default_currency IS NULL");
            \DB::statement("UPDATE customers SET credit_limit = 0 WHERE credit_limit IS NULL");
            \DB::statement("UPDATE customers SET credit_limit_currency = 'IQD' WHERE credit_limit_currency IS NULL");
        }

        // 4. جدول الموظفين (employees)
        if (Schema::hasTable('employees')) {
            Schema::table('employees', function (Blueprint $table) {
                // إضافة عمود salary أولاً
                if (!Schema::hasColumn('employees', 'salary')) {
                    $table->decimal('salary', 18, 2)->default(0)->after('status')->comment('الراتب الأساسي');
                }
                // إضافة salary_currency بعد currency (موجود في جدول employees)
                if (!Schema::hasColumn('employees', 'salary_currency')) {
                    $table->string('salary_currency', 3)->default('IQD')->after('currency')->comment('عملة الراتب الأساسي');
                }
                // إضافة base_salary في النهاية
                if (!Schema::hasColumn('employees', 'base_salary')) {
                    $table->decimal('base_salary', 18, 4)->default(0)->after('salary_currency')->comment('الراتب الأساسي بالعملة الأساسية');
                }
            });
            
            // تحديث البيانات الموجودة
            \DB::statement("UPDATE employees SET salary_currency = 'IQD' WHERE salary_currency IS NULL");
            \DB::statement("UPDATE employees SET salary = 0 WHERE salary IS NULL");
            \DB::statement("UPDATE employees SET base_salary = COALESCE(salary, 0) WHERE base_salary IS NULL");
        }

        // إنشاء جداول مفقودة إذا لم تكن موجودة

        // 5. جدول أسعار المنتجات متعددة العملات
        if (!Schema::hasTable('item_prices')) {
            Schema::create('item_prices', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('item_id');
                $table->string('currency', 3);
                $table->decimal('price', 18, 4);
                $table->enum('price_type', ['selling', 'cost', 'wholesale', 'retail'])->default('selling');
                $table->boolean('is_active')->default(true);
                $table->date('effective_from')->nullable();
                $table->date('effective_to')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();

                // فهارس
                $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
                $table->index(['item_id', 'currency', 'price_type', 'is_active'], 'item_curr_price_idx');
                $table->unique(['item_id', 'currency', 'price_type'], 'item_curr_price_unique');
            });
        }

        // 6. جدول أرصدة العملاء متعددة العملات  
        if (!Schema::hasTable('customer_balances')) {
            Schema::create('customer_balances', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('customer_id');
                $table->string('currency', 3);
                $table->decimal('balance', 18, 4)->default(0);
                $table->decimal('credit_used', 18, 4)->default(0);
                $table->decimal('available_credit', 18, 4)->default(0);
                $table->timestamp('last_updated')->nullable();
                $table->timestamps();

                $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
                $table->unique(['customer_id', 'currency']);
                $table->index(['currency']);
            });
        }

        // 7. جدول تاريخ أسعار الصرف
        if (!Schema::hasTable('exchange_rate_history')) {
            Schema::create('exchange_rate_history', function (Blueprint $table) {
                $table->id();
                $table->string('currency', 3);
                $table->decimal('rate', 15, 10);
                $table->date('date');
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->unique(['currency', 'date']);
                $table->index(['date']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // حذف الجداول المنشأة حديثاً
        Schema::dropIfExists('exchange_rate_history');
        Schema::dropIfExists('customer_balances');
        Schema::dropIfExists('item_prices');

        // إزالة الأعمدة (اختياري - للأمان نتركها)
        $tables = [
            'items' => ['currency', 'cost_price', 'cost_currency', 'is_multi_currency'],
            'salary_payments' => ['currency', 'exchange_rate', 'base_currency_net_salary'],
            'customers' => ['default_currency', 'credit_limit', 'credit_limit_currency'],
            'employees' => ['base_salary', 'salary_currency', 'salary'],
        ];

        foreach ($tables as $tableName => $columns) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($columns) {
                    foreach ($columns as $column) {
                        if (Schema::hasColumn($table->getTable(), $column)) {
                            $table->dropColumn($column);
                        }
                    }
                });
            }
        }
    }
};