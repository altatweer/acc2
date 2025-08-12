<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class PrintSetting extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        // Company Information
        'company_name',
        'company_address',
        'company_phone',
        'company_email',
        'company_website',
        'company_logo',
        
        // Color Scheme
        'primary_color',
        'secondary_color',
        'accent_color',
        'header_background',
        'header_text_color',
        'table_header_color',
        'border_color',
        
        // Typography
        'font_family',
        'font_size',
        'header_font_size',
        'font_bold_headers',
        
        // Layout Settings
        'page_size',
        'page_orientation',
        'margin_top',
        'margin_bottom',
        'margin_left',
        'margin_right',
        
        // Header/Footer Settings
        'show_company_logo',
        'show_company_address',
        'show_print_date',
        'show_print_user',
        'show_page_numbers',
        'show_footer',
        'custom_footer_text',
        
        // Table Settings
        'table_borders',
        'table_striped_rows',
        'table_style',
        
        // Invoice Specific Settings
        'show_invoice_qr_code',
        'show_payment_terms',
        'default_payment_terms',
        'show_notes_section',
        'show_signature_section',
        
        // Watermark Settings
        'enable_watermark',
        'watermark_text',
        'watermark_color',
        'watermark_opacity',
    ];

    protected $casts = [
        'show_company_logo' => 'boolean',
        'show_company_address' => 'boolean',
        'show_print_date' => 'boolean',
        'show_print_user' => 'boolean',
        'show_page_numbers' => 'boolean',
        'show_footer' => 'boolean',
        'font_bold_headers' => 'boolean',
        'table_borders' => 'boolean',
        'table_striped_rows' => 'boolean',
        'show_invoice_qr_code' => 'boolean',
        'show_payment_terms' => 'boolean',
        'show_notes_section' => 'boolean',
        'show_signature_section' => 'boolean',
        'enable_watermark' => 'boolean',
    ];

    /**
     * Get the current print settings for the tenant
     */
    public static function current()
    {
        $tenantId = session('tenant_id', 1);
        
        $settings = static::where('tenant_id', $tenantId)->first();
        
        if (!$settings) {
            // Create default settings if none exist
            $settings = static::createDefault($tenantId);
        }
        
        return $settings;
    }

    /**
     * Create default print settings
     */
    public static function createDefault($tenantId = null)
    {
        $tenantId = $tenantId ?? session('tenant_id', 1);
        
        // Get company info from existing settings
        $companyName = \App\Models\Setting::get('company_name', 'شركة المحاسبة');
        $companyLogo = \App\Models\Setting::get('company_logo');
        
        return static::create([
            'tenant_id' => $tenantId,
            'company_name' => $companyName,
            'company_logo' => $companyLogo,
            'primary_color' => '#2c3e50',
            'secondary_color' => '#34495e',
            'accent_color' => '#3498db',
            'header_background' => '#ecf0f1',
            'header_text_color' => '#2c3e50',
            'table_header_color' => '#bdc3c7',
            'border_color' => '#95a5a6',
            'font_family' => 'Tahoma, Arial, sans-serif',
            'font_size' => 12,
            'header_font_size' => 18,
            'font_bold_headers' => true,
            'page_size' => 'A4',
            'page_orientation' => 'portrait',
            'margin_top' => 15,
            'margin_bottom' => 15,
            'margin_left' => 15,
            'margin_right' => 15,
            'show_company_logo' => true,
            'show_company_address' => true,
            'show_print_date' => true,
            'show_print_user' => true,
            'show_page_numbers' => true,
            'show_footer' => true,
            'table_borders' => true,
            'table_striped_rows' => true,
            'table_style' => 'professional',
            'show_invoice_qr_code' => false,
            'show_payment_terms' => true,
            'show_notes_section' => true,
            'show_signature_section' => true,
            'enable_watermark' => false,
            'watermark_opacity' => 10,
        ]);
    }

    /**
     * Get CSS variables for the current print settings
     */
    public function getCssVariables()
    {
        return [
            '--print-primary-color' => $this->primary_color,
            '--print-secondary-color' => $this->secondary_color,
            '--print-accent-color' => $this->accent_color,
            '--print-header-bg' => $this->header_background,
            '--print-header-text' => $this->header_text_color,
            '--print-table-header' => $this->table_header_color,
            '--print-border-color' => $this->border_color,
            '--print-font-family' => $this->font_family,
            '--print-font-size' => $this->font_size . 'px',
            '--print-header-font-size' => $this->header_font_size . 'px',
            '--print-watermark-color' => $this->watermark_color ?? '#f8f9fa',
            '--print-watermark-opacity' => ($this->watermark_opacity ?? 10) / 100,
        ];
    }

    /**
     * Generate CSS string for print styles
     */
    public function generatePrintCss()
    {
        $variables = $this->getCssVariables();
        $css = ":root {\n";
        foreach ($variables as $name => $value) {
            $css .= "    {$name}: {$value};\n";
        }
        $css .= "}\n";
        
        return $css;
    }

    /**
     * Get margin CSS for page setup
     */
    public function getPageMargins()
    {
        return [
            'top' => $this->margin_top . 'mm',
            'bottom' => $this->margin_bottom . 'mm',
            'left' => $this->margin_left . 'mm',
            'right' => $this->margin_right . 'mm',
        ];
    }
}
