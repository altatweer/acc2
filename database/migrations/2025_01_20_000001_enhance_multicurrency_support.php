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
        // التأكد من وجود جدول accounts أولاً قبل تعديله
        if (!Schema::hasTable('accounts')) {
            return; // تجاهل هذه الهجرة إذا لم يكن الجدول موجود
        }
        
        // تحديث جدول accounts لدعم العملات المتعددة بشكل أفضل
        Schema::table('accounts', function (Blueprint $table) {
            // إزالة عمود currency القديم إذا كان موجوداً (فقط)
            // ملاحظة: إضافة أعمدة دعم العملات المتعددة تتم في هجرة منفصلة
            if (Schema::hasColumn('accounts', 'currency')) {
                $table->dropColumn('currency');
            }
        });

        // تحديث جدول transactions (التأكد من وجوده أولاً)
        if (!Schema::hasTable('transactions')) {
            return; // تجاهل باقي الهجرة إذا لم يكن جدول transactions موجود
        }
        
        Schema::table('transactions', function (Blueprint $table) {
            // تحسين دقة أسعار الصرف (من 6 إلى 10 خانات عشرية)
            if (Schema::hasColumn('transactions', 'exchange_rate')) {
                $table->decimal('exchange_rate', 15, 10)->default(1)->change()
                    ->comment('سعر الصرف بدقة عالية');
            }
            
            // إضافة عملة الهدف في حالة التحويل
            if (!Schema::hasColumn('transactions', 'target_currency')) {
                $table->string('target_currency', 3)->nullable()->after('currency')
                    ->comment('عملة الحساب المستهدف في التحويل');
            }
        });

        // تحديث جدول journal_entry_lines لدقة أفضل (التأكد من وجوده أولاً)
        if (!Schema::hasTable('journal_entry_lines')) {
            return; // تجاهل باقي الهجرة إذا لم يكن الجدول موجود
        }
        
        Schema::table('journal_entry_lines', function (Blueprint $table) {
            // تحسين دقة المبالغ والصرف
            $table->decimal('debit', 18, 4)->default(0)->change();
            $table->decimal('credit', 18, 4)->default(0)->change();
            
            if (Schema::hasColumn('journal_entry_lines', 'exchange_rate')) {
                $table->decimal('exchange_rate', 15, 10)->default(1)->change();
            }
        });

        // تحديث جدول account_balances (التأكد من وجوده أولاً)
        if (!Schema::hasTable('account_balances')) {
            return; // تجاهل باقي الهجرة إذا لم يكن الجدول موجود
        }
        
        Schema::table('account_balances', function (Blueprint $table) {
            // تحسين دقة الأرصدة
            $table->decimal('balance', 18, 4)->default(0)->change();
            
            // إضافة حقول إضافية لتتبع أفضل
            if (!Schema::hasColumn('account_balances', 'last_transaction_date')) {
                $table->timestamp('last_transaction_date')->nullable()
                    ->comment('تاريخ آخر معاملة');
            }
            
            if (!Schema::hasColumn('account_balances', 'is_active')) {
                $table->boolean('is_active')->default(true)
                    ->comment('الرصيد نشط');
            }
        });

        // إنشاء جدول currency_rates لأسعار الصرف
        if (!Schema::hasTable('currency_rates')) {
            Schema::create('currency_rates', function (Blueprint $table) {
                $table->id();
                $table->string('from_currency', 3)->comment('العملة المصدر');
                $table->string('to_currency', 3)->comment('العملة المستهدفة');
                $table->decimal('rate', 15, 10)->comment('سعر الصرف بدقة عالية');
                $table->date('effective_date')->comment('تاريخ سريان السعر');
                $table->boolean('is_active')->default(true);
                $table->text('notes')->nullable();
                $table->timestamps();

                // فهارس
                $table->index(['from_currency', 'to_currency']);
                $table->index(['effective_date']);
                $table->unique(['from_currency', 'to_currency', 'effective_date'], 'currency_rates_unique_clean');
            });
        }

        // إنشاء جدول multi_currency_transactions لتتبع التحويلات المعقدة
        if (!Schema::hasTable('multi_currency_transactions')) {
            Schema::create('multi_currency_transactions', function (Blueprint $table) {
                $table->id();
                $table->string('transaction_reference')->unique()
                    ->comment('مرجع المعاملة الموحد');
                $table->date('transaction_date');
                $table->enum('type', ['transfer', 'exchange', 'payment', 'receipt'])
                    ->comment('نوع المعاملة');
                
                // تفاصيل العملة المصدر
                $table->unsignedBigInteger('source_account_id');
                $table->string('source_currency', 3);
                $table->decimal('source_amount', 18, 4);
                
                // تفاصيل العملة المستهدفة
                $table->unsignedBigInteger('target_account_id')->nullable();
                $table->string('target_currency', 3)->nullable();
                $table->decimal('target_amount', 18, 4)->nullable();
                
                // سعر الصرف المستخدم
                $table->decimal('exchange_rate_used', 15, 10)->default(1);
                $table->decimal('exchange_gain_loss', 18, 4)->default(0)
                    ->comment('ربح أو خسارة الصرف');
                
                $table->text('description')->nullable();
                $table->json('metadata')->nullable()->comment('بيانات إضافية');
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('branch_id')->nullable();
                $table->timestamps();

                // المفاتيح الخارجية
                $table->foreign('source_account_id')->references('id')->on('accounts');
                $table->foreign('target_account_id')->references('id')->on('accounts');
                $table->foreign('user_id')->references('id')->on('users');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // حذف الجداول الجديدة
        Schema::dropIfExists('multi_currency_transactions');
        Schema::dropIfExists('currency_rates');

        // إعادة الحقول في الجداول المعدلة
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropColumn(['supports_multi_currency', 'default_currency', 'require_currency_selection']);
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['target_currency']);
        });

        Schema::table('account_balances', function (Blueprint $table) {
            $table->dropColumn(['last_transaction_date', 'is_active']);
        });
    }
}; 