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
        // Drop table if exists to avoid conflicts during migration
        Schema::dropIfExists('invoice_expense_attachment_lines');
        
        Schema::create('invoice_expense_attachment_lines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invoice_expense_attachment_id');
            $table->unsignedBigInteger('cash_account_id');
            $table->unsignedBigInteger('expense_account_id');
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3);
            $table->decimal('exchange_rate', 15, 6)->default(1);
            $table->text('description')->nullable();
            $table->timestamps();
        });
        
        // Add foreign keys after table creation to ensure referenced tables exist
        Schema::table('invoice_expense_attachment_lines', function (Blueprint $table) {
            // Foreign keys (using shorter names to avoid MySQL 64 character limit)
            $table->foreign('invoice_expense_attachment_id', 'ieal_attachment_id_fk')
                ->references('id')->on('invoice_expense_attachments')->onDelete('cascade');
            $table->foreign('cash_account_id', 'ieal_cash_account_id_fk')
                ->references('id')->on('accounts')->onDelete('restrict');
            $table->foreign('expense_account_id', 'ieal_expense_account_id_fk')
                ->references('id')->on('accounts')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_expense_attachment_lines');
    }
};

