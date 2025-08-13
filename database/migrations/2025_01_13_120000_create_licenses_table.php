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
        Schema::create('licenses', function (Blueprint $table) {
            $table->id();
            $table->string('license_key')->unique();
            $table->enum('type', ['development', 'beta', 'production'])->default('development');
            $table->enum('status', ['active', 'expired', 'suspended', 'revoked'])->default('active');
            $table->string('domain')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('last_check')->nullable();
            $table->json('features')->nullable(); // الميزات المتاحة
            $table->json('limits')->nullable(); // الحدود (عدد المستخدمين، إلخ)
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['status', 'expires_at']);
            $table->index(['domain', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('licenses');
    }
};
