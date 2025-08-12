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
        Schema::create('print_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable();
            
            // Company Information
            $table->string('company_name')->nullable();
            $table->text('company_address')->nullable();
            $table->string('company_phone')->nullable();
            $table->string('company_email')->nullable();
            $table->string('company_website')->nullable();
            $table->string('company_logo')->nullable();
            
            // Color Scheme
            $table->string('primary_color', 7)->default('#007bff'); // اللون الأساسي
            $table->string('secondary_color', 7)->default('#6c757d'); // اللون الثانوي
            $table->string('accent_color', 7)->default('#28a745'); // لون التمييز
            $table->string('header_background', 7)->default('#f8f9fa'); // خلفية الرأس
            $table->string('header_text_color', 7)->default('#212529'); // لون نص الرأس
            $table->string('table_header_color', 7)->default('#e9ecef'); // لون رأس الجدول
            $table->string('border_color', 7)->default('#dee2e6'); // لون الحدود
            
            // Typography
            $table->string('font_family')->default('Tahoma, Arial, sans-serif');
            $table->integer('font_size')->default(12); // بيكسل
            $table->integer('header_font_size')->default(18);
            $table->boolean('font_bold_headers')->default(true);
            
            // Layout Settings
            $table->string('page_size')->default('A4'); // A4, A3, Letter
            $table->string('page_orientation')->default('portrait'); // portrait, landscape
            $table->integer('margin_top')->default(15); // ملم
            $table->integer('margin_bottom')->default(15);
            $table->integer('margin_left')->default(15);
            $table->integer('margin_right')->default(15);
            
            // Header/Footer Settings
            $table->boolean('show_company_logo')->default(true);
            $table->boolean('show_company_address')->default(true);
            $table->boolean('show_print_date')->default(true);
            $table->boolean('show_print_user')->default(true);
            $table->boolean('show_page_numbers')->default(true);
            $table->boolean('show_footer')->default(true);
            $table->text('custom_footer_text')->nullable();
            
            // Table Settings
            $table->boolean('table_borders')->default(true);
            $table->boolean('table_striped_rows')->default(true);
            $table->string('table_style')->default('professional'); // professional, minimal, bold
            
            // Invoice Specific Settings
            $table->boolean('show_invoice_qr_code')->default(false);
            $table->boolean('show_payment_terms')->default(true);
            $table->text('default_payment_terms')->nullable();
            $table->boolean('show_notes_section')->default(true);
            $table->boolean('show_signature_section')->default(true);
            
            // Watermark Settings
            $table->boolean('enable_watermark')->default(false);
            $table->string('watermark_text')->nullable();
            $table->string('watermark_color', 7)->default('#f8f9fa');
            $table->integer('watermark_opacity')->default(10); // 0-100
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('print_settings');
    }
};
