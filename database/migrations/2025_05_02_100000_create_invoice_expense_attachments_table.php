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
        Schema::create('invoice_expense_attachments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invoice_id');
            $table->unsignedBigInteger('voucher_id')->nullable();
            $table->unsignedBigInteger('journal_entry_id')->nullable();
            $table->timestamps();
        });
        
        // Add foreign keys after table creation to ensure referenced tables exist
        // This migration runs after invoices, vouchers, and journal_entries tables are created
        Schema::table('invoice_expense_attachments', function (Blueprint $table) {
            // Foreign keys (using shorter names to avoid MySQL 64 character limit)
            $table->foreign('invoice_id', 'iea_invoice_id_fk')
                ->references('id')->on('invoices')->onDelete('cascade');
            $table->foreign('voucher_id', 'iea_voucher_id_fk')
                ->references('id')->on('vouchers')->onDelete('set null');
            $table->foreign('journal_entry_id', 'iea_journal_entry_id_fk')
                ->references('id')->on('journal_entries')->onDelete('set null');
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

