<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->enum('type', ['asset', 'liability', 'revenue', 'expense', 'equity']);
            $table->enum('nature', ['debit', 'credit'])->nullable();
            $table->boolean('is_group')->default(false);
            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('accounts')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('accounts');
    }
};
