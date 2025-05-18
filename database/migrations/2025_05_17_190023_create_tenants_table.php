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
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('subdomain')->unique();
            $table->string('domain')->nullable()->unique();
            $table->string('database')->nullable();
            $table->string('logo')->nullable();
            $table->string('contact_email');
            $table->string('contact_phone')->nullable();
            $table->text('address')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('subscription_plan_id')->nullable();
            $table->timestamp('subscription_starts_at')->nullable();
            $table->timestamp('subscription_ends_at')->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->json('settings')->nullable();
            $table->timestamps();
        });

        // إنشاء جدول خطط الاشتراك إذا لم يكن موجوداً
        if (!Schema::hasTable('subscription_plans')) {
            Schema::create('subscription_plans', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('code')->unique();
                $table->text('description')->nullable();
                $table->decimal('price', 10, 2);
                $table->string('billing_cycle')->default('monthly'); // monthly, quarterly, yearly
                $table->integer('trial_days')->default(0);
                $table->json('features')->nullable(); // ميزات الخطة المضمنة
                $table->json('limits')->nullable(); // حدود الاستخدام (مثل عدد المستخدمين, السندات, الفواتير...)
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // إنشاء جدول ميزات المستأجر (لتتبع الميزات المفعلة لكل مستأجر)
        if (!Schema::hasTable('tenant_features')) {
            Schema::create('tenant_features', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('tenant_id');
                $table->string('feature_code');
                $table->boolean('is_enabled')->default(true);
                $table->json('settings')->nullable();
                $table->timestamps();
                
                $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
                $table->unique(['tenant_id', 'feature_code']);
            });
        }

        // إنشاء جدول إحصائيات استخدام المستأجر
        if (!Schema::hasTable('tenant_usage_stats')) {
            Schema::create('tenant_usage_stats', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('tenant_id');
                $table->string('resource_type'); // مثل 'users', 'vouchers', 'invoices', إلخ
                $table->integer('count')->default(0);
                $table->integer('monthly_count')->default(0);
                $table->integer('limit')->default(0); // الحد الأقصى المسموح به
                $table->timestamp('last_updated_at');
                $table->timestamps();
                
                $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
                $table->unique(['tenant_id', 'resource_type']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_usage_stats');
        Schema::dropIfExists('tenant_features');
        Schema::dropIfExists('subscription_plans');
        Schema::dropIfExists('tenants');
    }
};
