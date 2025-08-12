<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PrintSetting;
use App\Models\Invoice;
use App\Models\Voucher;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PrintSettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show print settings form
     */
    public function edit()
    {
        $settings = PrintSetting::current();
        
        return view('settings.print', compact('settings'));
    }

    /**
     * Update print settings
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            // Company Information
            'company_name' => 'nullable|string|max:255',
            'company_address' => 'nullable|string|max:1000',
            'company_phone' => 'nullable|string|max:50',
            'company_email' => 'nullable|email|max:255',
            'company_website' => 'nullable|url|max:255',
            'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            
            // Color Scheme
            'primary_color' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'secondary_color' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'accent_color' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'header_background' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'header_text_color' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'table_header_color' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'border_color' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            
            // Typography
            'font_family' => 'required|string|max:255',
            'font_size' => 'required|integer|min:8|max:24',
            'header_font_size' => 'required|integer|min:12|max:36',
            
            // Layout Settings
            'page_size' => ['required', Rule::in(['A4', 'A3', 'Letter'])],
            'page_orientation' => ['required', Rule::in(['portrait', 'landscape'])],
            'margin_top' => 'required|integer|min:5|max:50',
            'margin_bottom' => 'required|integer|min:5|max:50',
            'margin_left' => 'required|integer|min:5|max:50',
            'margin_right' => 'required|integer|min:5|max:50',
            
            // Header/Footer Settings - Made nullable
            'custom_footer_text' => 'nullable|string|max:500',
            
            // Table Settings
            'table_style' => ['required', Rule::in(['professional', 'minimal', 'bold'])],
            
            // Invoice Specific Settings
            'default_payment_terms' => 'nullable|string|max:1000',
            
            // Watermark Settings
            'watermark_text' => 'nullable|string|max:100',
            'watermark_color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'watermark_opacity' => 'nullable|integer|min:1|max:100',
        ]);

        $settings = PrintSetting::current();

        // Handle logo upload
        if ($request->hasFile('company_logo')) {
            // Delete old logo if exists
            if ($settings->company_logo && Storage::disk('public')->exists($settings->company_logo)) {
                Storage::disk('public')->delete($settings->company_logo);
            }
            
            // Store new logo
            $logoPath = $request->file('company_logo')->store('print-logos', 'public');
            $validated['company_logo'] = $logoPath;
        }

        // Handle boolean fields properly
        $booleanFields = [
            'font_bold_headers', 'show_company_logo', 'show_company_address',
            'show_print_date', 'show_print_user', 'show_page_numbers', 'show_footer',
            'table_borders', 'table_striped_rows', 'show_invoice_qr_code',
            'show_payment_terms', 'show_notes_section', 'show_signature_section',
            'enable_watermark'
        ];

        foreach ($booleanFields as $field) {
            $validated[$field] = $request->has($field) ? true : false;
        }

        $settings->update($validated);

        return redirect()->back()->with('success', 'تم تحديث إعدادات الطباعة بنجاح.');
    }

    /**
     * Preview invoice print with current settings
     */
    public function previewInvoice($invoiceId = null)
    {
        $printSettings = PrintSetting::current();
        
        // Get a sample invoice or create mock data
        if ($invoiceId) {
            $invoice = Invoice::with(['customer', 'invoiceItems.item'])->findOrFail($invoiceId);
        } else {
            $invoice = $this->getMockInvoiceData();
        }

        // Get company logo path
        $companyLogo = $printSettings->company_logo;

        return view('settings.print-preview-invoice', compact('printSettings', 'invoice', 'companyLogo'));
    }

    /**
     * Preview voucher print with current settings
     */
    public function previewVoucher($voucherId = null)
    {
        $printSettings = PrintSetting::current();
        
        // Get a sample voucher or create mock data
        if ($voucherId) {
            $voucher = Voucher::with(['user', 'journalEntry.lines.account'])->findOrFail($voucherId);
            $transactions = \App\Models\Transaction::where('voucher_id', $voucher->id)
                ->with(['account', 'targetAccount'])
                ->get();
        } else {
            $voucher = $this->getMockVoucherData();
            $transactions = collect();
        }

        // Get company logo path
        $companyLogo = $printSettings->company_logo;

        return view('settings.print-preview-voucher', compact('printSettings', 'voucher', 'transactions', 'companyLogo'));
    }

    /**
     * Reset print settings to default
     */
    public function reset()
    {
        $settings = PrintSetting::current();
        
        // Delete current logo if exists
        if ($settings->company_logo && Storage::disk('public')->exists($settings->company_logo)) {
            Storage::disk('public')->delete($settings->company_logo);
        }

        // Delete current settings
        $settings->delete();

        // Create new default settings
        PrintSetting::createDefault();

        return redirect()->back()->with('success', 'تم إعادة تعيين إعدادات الطباعة إلى الافتراضية.');
    }

    /**
     * Get mock invoice data for preview
     */
    private function getMockInvoiceData()
    {
        $mockInvoice = (object) [
            'id' => 999,
            'invoice_number' => 'INV-2025-001',
            'date' => now(),
            'currency' => 'IQD',
            'exchange_rate' => 1,
            'total' => 1500000,
            'status' => 'unpaid',
            'type' => 'sales',
            'customer' => (object) [
                'name' => 'عميل تجريبي',
                'email' => 'customer@example.com',
                'phone' => '07901234567'
            ],
            'invoiceItems' => collect([
                (object) [
                    'quantity' => 2,
                    'unit_price' => 500000,
                    'line_total' => 1000000,
                    'discount' => 0,
                    'item' => (object) [
                        'name' => 'منتج تجريبي أول',
                        'code' => 'PROD001',
                        'unit' => 'قطعة'
                    ]
                ],
                (object) [
                    'quantity' => 1,
                    'unit_price' => 500000,
                    'line_total' => 500000,
                    'discount' => 0,
                    'item' => (object) [
                        'name' => 'منتج تجريبي ثاني',
                        'code' => 'PROD002',
                        'unit' => 'قطعة'
                    ]
                ]
            ])
        ];

        return $mockInvoice;
    }

    /**
     * Get mock voucher data for preview
     */
    private function getMockVoucherData()
    {
        $mockVoucher = (object) [
            'id' => 999,
            'voucher_number' => 'REC-2025-001',
            'type' => 'receipt',
            'date' => now(),
            'currency' => 'IQD',
            'exchange_rate' => 1,
            'amount' => 1500000,
            'description' => 'سند قبض تجريبي',
            'recipient_name' => 'عميل تجريبي',
            'notes' => 'ملاحظات تجريبية للسند',
            'status' => 'approved',
            'created_at' => now(),
            'user' => (object) [
                'name' => 'المستخدم التجريبي'
            ],
            'journalEntry' => (object) [
                'description' => 'قيد محاسبي تجريبي',
                'total_debit' => 1500000,
                'total_credit' => 1500000,
                'lines' => collect([
                    (object) [
                        'debit' => 1500000,
                        'credit' => 0,
                        'description' => 'استلام نقدية',
                        'account' => (object) [
                            'account_code' => '1001',
                            'account_name' => 'الصندوق'
                        ]
                    ],
                    (object) [
                        'debit' => 0,
                        'credit' => 1500000,
                        'description' => 'مبيعات نقدية',
                        'account' => (object) [
                            'account_code' => '4001',
                            'account_name' => 'المبيعات'
                        ]
                    ]
                ])
            ]
        ];

        return $mockVoucher;
    }
}
