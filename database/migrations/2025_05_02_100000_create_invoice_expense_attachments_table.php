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
        // Check if invoices table exists before creating foreign key
        if (!Schema::hasTable('invoices')) {
            throw new \Exception('Table "invoices" must be created before "invoice_expense_attachments"');
        }
        
        Schema::create('invoice_expense_attachments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invoice_id');
            $table->unsignedBigInteger('voucher_id')->nullable();
            $table->unsignedBigInteger('journal_entry_id')->nullable();
            $table->timestamps();

            // Foreign keys (using shorter names to avoid MySQL 64 character limit)
            // Only create foreign key if the referenced table exists
            if (Schema::hasTable('invoices')) {
                $table->foreign('invoice_id', 'iea_invoice_id_fk')
                    ->references('id')->on('invoices')->onDelete('cascade');
            }
            if (Schema::hasTable('vouchers')) {
                $table->foreign('voucher_id', 'iea_voucher_id_fk')
                    ->references('id')->on('vouchers')->onDelete('set null');
            }
            if (Schema::hasTable('journal_entries')) {
                $table->foreign('journal_entry_id', 'iea_journal_entry_id_fk')
                    ->references('id')->on('journal_entries')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_expense_attachments');
    }
};

