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
        
        // Check if required tables exist
        if (!Schema::hasTable('invoice_expense_attachments')) {
            throw new \Exception('Table "invoice_expense_attachments" must be created before "invoice_expense_attachment_lines"');
        }
        if (!Schema::hasTable('accounts')) {
            throw new \Exception('Table "accounts" must be created before "invoice_expense_attachment_lines"');
        }
        
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

            // Foreign keys (using shorter names to avoid MySQL 64 character limit)
            // Only create foreign key if the referenced table exists
            if (Schema::hasTable('invoice_expense_attachments')) {
                $table->foreign('invoice_expense_attachment_id', 'ieal_attachment_id_fk')
                    ->references('id')->on('invoice_expense_attachments')->onDelete('cascade');
            }
            if (Schema::hasTable('accounts')) {
                $table->foreign('cash_account_id', 'ieal_cash_account_id_fk')
                    ->references('id')->on('accounts')->onDelete('restrict');
                $table->foreign('expense_account_id', 'ieal_expense_account_id_fk')
                    ->references('id')->on('accounts')->onDelete('restrict');
            }
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

