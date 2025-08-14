<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixMulticurrencyCompatibility extends Migration
{
    /**
     * إصلاح مشاكل التوافق مع العملات المتعددة
     */
    public function up()
    {
        // التأكد من وجود الجداول الأساسية قبل تطبيق التحسينات
        $requiredTables = ['currencies', 'accounts'];
        foreach ($requiredTables as $table) {
            if (!Schema::hasTable($table)) {
                return; // تجاهل الهجرة إذا لم تكن الجداول الأساسية موجودة
            }
        }
        
        // 1. تحسين جدول العملات
        
        Schema::table('currencies', function (Blueprint $table) {
            if (!Schema::hasColumn('currencies', 'name_ar')) {
                $table->string('name_ar', 255)->after('name')->comment('اسم العملة بالعربية');
            }
            if (!Schema::hasColumn('currencies', 'decimal_places')) {
                $table->tinyInteger('decimal_places')->default(2)->after('symbol')
                    ->comment('عدد الخانات العشرية');
            }
            if (!Schema::hasColumn('currencies', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('decimal_places')
                    ->comment('العملة نشطة');
            }
            if (!Schema::hasColumn('currencies', 'country')) {
                $table->string('country', 100)->nullable()->after('is_active')
                    ->comment('اسم الدولة بالإنجليزية');
            }
            if (!Schema::hasColumn('currencies', 'country_ar')) {
                $table->string('country_ar', 100)->nullable()->after('country')
                    ->comment('اسم الدولة بالعربية');
            }
            if (!Schema::hasColumn('currencies', 'sort_order')) {
                $table->integer('sort_order')->default(0)->after('country_ar')
                    ->comment('ترتيب العرض');
            }
        });

        // 2. إضافة دعم العملات المتعددة لجدول الرواتب (التأكد من وجوده أولاً)
        if (!Schema::hasTable('salary_payments')) {
            return; // تجاهل باقي الهجرة إذا لم يكن الجدول موجود
        }
        
        Schema::table('salary_payments', function (Blueprint $table) {
            if (!Schema::hasColumn('salary_payments', 'currency')) {
                $table->string('currency', 3)->default('IQD')->after('net_salary')
                    ->comment('عملة الراتب');
            }
            if (!Schema::hasColumn('salary_payments', 'exchange_rate')) {
                $table->decimal('exchange_rate', 15, 10)->default(1.0000000000)->after('currency')
                    ->comment('سعر الصرف المستخدم');
            }
            if (!Schema::hasColumn('salary_payments', 'base_currency_net_salary')) {
                $table->decimal('base_currency_net_salary', 18, 2)->after('exchange_rate')
                    ->comment('صافي الراتب بالعملة الأساسية');
            }
        });

        // 3. إضافة دعم العملات المتعددة للعملاء (التأكد من وجوده أولاً)
        if (!Schema::hasTable('customers')) {
            return; // تجاهل باقي الهجرة إذا لم يكن الجدول موجود
        }
        
        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'default_currency')) {
                $table->string('default_currency', 3)->default('IQD')->after('account_id')
                    ->comment('العملة الافتراضية للعميل');
            }
            if (!Schema::hasColumn('customers', 'credit_limit')) {
                $table->decimal('credit_limit', 18, 4)->default(0)->after('default_currency')
                    ->comment('الحد الائتماني');
            }
            if (!Schema::hasColumn('customers', 'credit_limit_currency')) {
                $table->string('credit_limit_currency', 3)->default('IQD')->after('credit_limit')
                    ->comment('عملة الحد الائتماني');
            }
        });

        // 4. إضافة دعم العملات المتعددة للمنتجات
        Schema::table('items', function (Blueprint $table) {
            if (!Schema::hasColumn('items', 'currency')) {
                $table->string('currency', 3)->default('IQD')->after('unit_price')
                    ->comment('عملة السعر');
            }
            if (!Schema::hasColumn('items', 'cost_price')) {
                $table->decimal('cost_price', 15, 4)->default(0)->after('currency')
                    ->comment('سعر التكلفة');
            }
            if (!Schema::hasColumn('items', 'cost_currency')) {
                $table->string('cost_currency', 3)->default('IQD')->after('cost_price')
                    ->comment('عملة التكلفة');
            }
            if (!Schema::hasColumn('items', 'is_multi_currency')) {
                $table->boolean('is_multi_currency')->default(true)->after('cost_currency')
                    ->comment('يدعم أسعار متعددة العملات');
            }
        });

        // 5. إضافة دعم العملات المتعددة لبنود الفواتير
        Schema::table('invoice_items', function (Blueprint $table) {
            if (!Schema::hasColumn('invoice_items', 'currency')) {
                $table->string('currency', 3)->default('IQD')->after('line_total')
                    ->comment('عملة البند');
            }
            if (!Schema::hasColumn('invoice_items', 'exchange_rate')) {
                $table->decimal('exchange_rate', 15, 10)->default(1.0000000000)->after('currency')
                    ->comment('سعر الصرف المستخدم');
            }
            if (!Schema::hasColumn('invoice_items', 'base_currency_total')) {
                $table->decimal('base_currency_total', 18, 4)->after('exchange_rate')
                    ->comment('المجموع بالعملة الأساسية');
            }
        });

        // 6. تحسين جدول الموظفين
        Schema::table('employees', function (Blueprint $table) {
            if (!Schema::hasColumn('employees', 'salary_currency')) {
                $table->string('salary_currency', 3)->default('IQD')->after('currency')
                    ->comment('عملة الراتب الأساسي');
            }
            if (!Schema::hasColumn('employees', 'base_salary')) {
                $table->decimal('base_salary', 18, 4)->default(0)->after('salary_currency')
                    ->comment('الراتب الأساسي');
            }
        });

        // 7. إنشاء جدول أسعار المنتجات متعددة العملات
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
                $table->foreign('currency')->references('code')->on('currencies');
                $table->index(['item_id', 'currency', 'price_type', 'is_active'], 'item_curr_price_idx');
                $table->unique(['item_id', 'currency', 'price_type'], 'item_curr_price_unique');
            });
        }

        // 8. إنشاء جدول أرصدة العملاء متعددة العملات  
        if (!Schema::hasTable('customer_balances')) {
            Schema::create('customer_balances', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('customer_id');
                $table->string('currency', 3);
                $table->decimal('balance', 18, 4)->default(0);
                $table->decimal('credit_limit', 18, 4)->default(0);
                $table->date('last_transaction_date')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                // فهارس
                $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
                $table->foreign('currency')->references('code')->on('currencies');
                $table->unique(['customer_id', 'currency'], 'cust_curr_balance_unique');
                $table->index(['customer_id', 'currency', 'is_active']);
            });
        }

        // 9. إنشاء جدول سجل أسعار الصرف التاريخية
        if (!Schema::hasTable('exchange_rate_history')) {
            Schema::create('exchange_rate_history', function (Blueprint $table) {
                $table->id();
                $table->string('from_currency', 3);
                $table->string('to_currency', 3);
                $table->decimal('rate', 15, 10);
                $table->decimal('previous_rate', 15, 10)->nullable();
                $table->date('effective_date');
                $table->enum('source', ['manual', 'api', 'system'])->default('manual');
                $table->json('metadata')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();

                // فهارس
                $table->foreign('from_currency')->references('code')->on('currencies');
                $table->foreign('to_currency')->references('code')->on('currencies');
                $table->index(['from_currency', 'to_currency', 'effective_date'], 'exch_hist_curr_date_idx');
                $table->index(['effective_date', 'source'], 'exch_hist_date_source_idx');
            });
        }
    }

    /**
     * التراجع عن التغييرات
     */
    public function down()
    {
        // حذف الجداول الجديدة
        Schema::dropIfExists('exchange_rate_history');
        Schema::dropIfExists('customer_balances');
        Schema::dropIfExists('item_prices');

        // إزالة الأعمدة المضافة (اختياري - قد نتركها للأمان)
        if (Schema::hasColumn('currencies', 'sort_order')) {
            Schema::table('currencies', function (Blueprint $table) {
                $table->dropColumn(['name_ar', 'decimal_places', 'is_active', 'country', 'country_ar', 'sort_order']);
            });
        }

        if (Schema::hasColumn('salary_payments', 'base_currency_net_salary')) {
            Schema::table('salary_payments', function (Blueprint $table) {
                $table->dropColumn(['currency', 'exchange_rate', 'base_currency_net_salary']);
            });
        }

        if (Schema::hasColumn('customers', 'credit_limit_currency')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->dropColumn(['default_currency', 'credit_limit', 'credit_limit_currency']);
            });
        }

        if (Schema::hasColumn('items', 'is_multi_currency')) {
            Schema::table('items', function (Blueprint $table) {
                $table->dropColumn(['currency', 'cost_price', 'cost_currency', 'is_multi_currency']);
            });
        }

        if (Schema::hasColumn('invoice_items', 'base_currency_total')) {
            Schema::table('invoice_items', function (Blueprint $table) {
                $table->dropColumn(['currency', 'exchange_rate', 'base_currency_total']);
            });
        }

        if (Schema::hasColumn('employees', 'base_salary')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->dropColumn(['salary_currency', 'base_salary']);
            });
        }
    }
} 