<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * قائمة بالجداول التي سيضاف لها حقل tenant_id
     */
    protected $tables = [
        'accounts',
        'account_balances',
        'currencies',
        'vouchers',
        'transactions',
        'invoices',
        'invoice_items',
        'items',
        'customers',
        'employees',
        'salaries',
        'salary_batches',
        'salary_payments',
        'journal_entries',
        'journal_entry_lines',
        'accounting_settings',
        'settings',
        'branches',
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach ($this->tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) {
                    // إضافة حقل tenant_id مع السماح بالقيم الفارغة حالياً
                    // سيكون nullable الآن وسيتم تعديله لاحقاً عند تفعيل نظام متعدد المستأجرين
                    $table->unsignedBigInteger('tenant_id')->nullable()->index()->after('id');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach ($this->tables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'tenant_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropColumn('tenant_id');
                });
            }
        }
    }
}; 