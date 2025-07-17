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
        Schema::table('accounts', function (Blueprint $table) {
            // إضافة حقول الرصيد الافتتاحي
            $table->boolean('has_opening_balance')->default(false)->after('require_currency_selection')
                ->comment('هل يحتوي على رصيد افتتاحي');
            
            $table->decimal('opening_balance', 18, 4)->default(0)->after('has_opening_balance')
                ->comment('مبلغ الرصيد الافتتاحي');
            
            $table->enum('opening_balance_type', ['debit', 'credit'])->nullable()->after('opening_balance')
                ->comment('نوع الرصيد الافتتاحي (مدين/دائن)');
            
            $table->date('opening_balance_date')->nullable()->after('opening_balance_type')
                ->comment('تاريخ الرصيد الافتتاحي');
            
            $table->unsignedBigInteger('opening_balance_journal_entry_id')->nullable()->after('opening_balance_date')
                ->comment('معرف القيد المحاسبي للرصيد الافتتاحي');
            
            // إضافة المفتاح الخارجي
            $table->foreign('opening_balance_journal_entry_id')
                  ->references('id')->on('journal_entries')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            // حذف المفتاح الخارجي أولاً
            $table->dropForeign(['opening_balance_journal_entry_id']);
            
            // حذف الحقول
            $table->dropColumn([
                'has_opening_balance',
                'opening_balance', 
                'opening_balance_type',
                'opening_balance_date',
                'opening_balance_journal_entry_id'
            ]);
        });
    }
};
