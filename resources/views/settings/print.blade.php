@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card mt-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-print"></i>
                        إعدادات الطباعة المخصصة
                    </h4>
                    <div class="btn-group">
                        <button type="button" class="btn btn-light btn-sm" onclick="previewInvoice()">
                            <i class="fas fa-eye"></i> معاينة فاتورة
                        </button>
                        <button type="button" class="btn btn-light btn-sm" onclick="previewVoucher()">
                            <i class="fas fa-eye"></i> معاينة سند
                        </button>
                    </div>
                </div>
                
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="fas fa-check-circle"></i>
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert">
                                <span>&times;</span>
                            </button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show">
                            <strong>خطأ في البيانات:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="close" data-dismiss="alert">
                                <span>&times;</span>
                            </button>
                        </div>
                    @endif

                    <form action="{{ route('print-settings.update') }}" method="POST" enctype="multipart/form-data" id="printSettingsForm">
                        @csrf
                        @method('PUT')

                        <!-- Company Information Section -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card mb-4">
                                    <div class="card-header bg-info text-white">
                                        <h5 class="mb-0">
                                            <i class="fas fa-building"></i>
                                            معلومات الشركة
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>اسم الشركة</label>
                                                    <input type="text" name="company_name" class="form-control" 
                                                           value="{{ old('company_name', $settings->company_name) }}">
                                                </div>
                                                
                                                <div class="form-group">
                                                    <label>رقم الهاتف</label>
                                                    <input type="text" name="company_phone" class="form-control" 
                                                           value="{{ old('company_phone', $settings->company_phone) }}">
                                                </div>
                                                
                                                <div class="form-group">
                                                    <label>البريد الإلكتروني</label>
                                                    <input type="email" name="company_email" class="form-control" 
                                                           value="{{ old('company_email', $settings->company_email) }}">
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>العنوان</label>
                                                    <textarea name="company_address" class="form-control" rows="3">{{ old('company_address', $settings->company_address) }}</textarea>
                                                </div>
                                                
                                                <div class="form-group">
                                                    <label>الموقع الإلكتروني</label>
                                                    <input type="url" name="company_website" class="form-control" 
                                                           value="{{ old('company_website', $settings->company_website) }}">
                                                </div>
                                                
                                                <div class="form-group">
                                                    <label>شعار الشركة</label>
                                                    @if($settings->company_logo)
                                                        <div class="mb-2">
                                                            <img src="{{ asset('storage/' . $settings->company_logo) }}" 
                                                                 alt="شعار الشركة" style="max-height: 80px; max-width: 200px;">
                                                        </div>
                                                    @endif
                                                    <input type="file" name="company_logo" class="form-control-file" accept="image/*">
                                                    <small class="text-muted">يُفضل حجم 200x80 بيكسل أو أصغر</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Color Scheme Section -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card mb-4">
                                    <div class="card-header bg-warning text-dark">
                                        <h5 class="mb-0">
                                            <i class="fas fa-palette"></i>
                                            مخطط الألوان
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>اللون الأساسي</label>
                                                    <div class="input-group">
                                                        <input type="color" name="primary_color" class="form-control color-picker" 
                                                               value="{{ old('primary_color', $settings->primary_color) }}">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">{{ $settings->primary_color }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>اللون الثانوي</label>
                                                    <div class="input-group">
                                                        <input type="color" name="secondary_color" class="form-control color-picker" 
                                                               value="{{ old('secondary_color', $settings->secondary_color) }}">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">{{ $settings->secondary_color }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>لون التمييز</label>
                                                    <div class="input-group">
                                                        <input type="color" name="accent_color" class="form-control color-picker" 
                                                               value="{{ old('accent_color', $settings->accent_color) }}">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">{{ $settings->accent_color }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>خلفية الرأس</label>
                                                    <div class="input-group">
                                                        <input type="color" name="header_background" class="form-control color-picker" 
                                                               value="{{ old('header_background', $settings->header_background) }}">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">{{ $settings->header_background }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>لون نص الرأس</label>
                                                    <div class="input-group">
                                                        <input type="color" name="header_text_color" class="form-control color-picker" 
                                                               value="{{ old('header_text_color', $settings->header_text_color) }}">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">{{ $settings->header_text_color }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>لون رأس الجدول</label>
                                                    <div class="input-group">
                                                        <input type="color" name="table_header_color" class="form-control color-picker" 
                                                               value="{{ old('table_header_color', $settings->table_header_color) }}">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">{{ $settings->table_header_color }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>لون الحدود</label>
                                                    <div class="input-group">
                                                        <input type="color" name="border_color" class="form-control color-picker" 
                                                               value="{{ old('border_color', $settings->border_color) }}">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">{{ $settings->border_color }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Typography & Layout Section -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header bg-success text-white">
                                        <h5 class="mb-0">
                                            <i class="fas fa-font"></i>
                                            الخط والطباعة
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label>نوع الخط</label>
                                            <select name="font_family" class="form-control">
                                                <option value="Tahoma, Arial, sans-serif" {{ $settings->font_family == 'Tahoma, Arial, sans-serif' ? 'selected' : '' }}>Tahoma (افتراضي)</option>
                                                <option value="Arial, sans-serif" {{ $settings->font_family == 'Arial, sans-serif' ? 'selected' : '' }}>Arial</option>
                                                <option value="Times New Roman, serif" {{ $settings->font_family == 'Times New Roman, serif' ? 'selected' : '' }}>Times New Roman</option>
                                                <option value="Calibri, sans-serif" {{ $settings->font_family == 'Calibri, sans-serif' ? 'selected' : '' }}>Calibri</option>
                                            </select>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>حجم الخط العادي</label>
                                                    <input type="number" name="font_size" class="form-control" 
                                                           value="{{ old('font_size', $settings->font_size) }}" min="8" max="24">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>حجم خط العناوين</label>
                                                    <input type="number" name="header_font_size" class="form-control" 
                                                           value="{{ old('header_font_size', $settings->header_font_size) }}" min="12" max="36">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="form-check">
                                            <input type="checkbox" name="font_bold_headers" class="form-check-input" 
                                                   {{ old('font_bold_headers', $settings->font_bold_headers) ? 'checked' : '' }}>
                                            <label class="form-check-label">عناوين بخط عريض</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header bg-secondary text-white">
                                        <h5 class="mb-0">
                                            <i class="fas fa-file-alt"></i>
                                            إعدادات الصفحة
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>حجم الصفحة</label>
                                                    <select name="page_size" class="form-control">
                                                        <option value="A4" {{ $settings->page_size == 'A4' ? 'selected' : '' }}>A4</option>
                                                        <option value="A3" {{ $settings->page_size == 'A3' ? 'selected' : '' }}>A3</option>
                                                        <option value="Letter" {{ $settings->page_size == 'Letter' ? 'selected' : '' }}>Letter</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>اتجاه الصفحة</label>
                                                    <select name="page_orientation" class="form-control">
                                                        <option value="portrait" {{ $settings->page_orientation == 'portrait' ? 'selected' : '' }}>عمودي</option>
                                                        <option value="landscape" {{ $settings->page_orientation == 'landscape' ? 'selected' : '' }}>أفقي</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <label class="form-label">الهوامش (مليمتر):</label>
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label>علوي</label>
                                                    <input type="number" name="margin_top" class="form-control" 
                                                           value="{{ old('margin_top', $settings->margin_top) }}" min="5" max="50">
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label>سفلي</label>
                                                    <input type="number" name="margin_bottom" class="form-control" 
                                                           value="{{ old('margin_bottom', $settings->margin_bottom) }}" min="5" max="50">
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label>يسار</label>
                                                    <input type="number" name="margin_left" class="form-control" 
                                                           value="{{ old('margin_left', $settings->margin_left) }}" min="5" max="50">
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label>يمين</label>
                                                    <input type="number" name="margin_right" class="form-control" 
                                                           value="{{ old('margin_right', $settings->margin_right) }}" min="5" max="50">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Display Options Section -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card mb-4">
                                    <div class="card-header bg-info text-white">
                                        <h5 class="mb-0">
                                            <i class="fas fa-eye"></i>
                                            خيارات العرض
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-check mb-2">
                                            <input type="checkbox" name="show_company_logo" class="form-check-input" 
                                                   {{ old('show_company_logo', $settings->show_company_logo) ? 'checked' : '' }}>
                                            <label class="form-check-label">عرض شعار الشركة</label>
                                        </div>
                                        
                                        <div class="form-check mb-2">
                                            <input type="checkbox" name="show_company_address" class="form-check-input" 
                                                   {{ old('show_company_address', $settings->show_company_address) ? 'checked' : '' }}>
                                            <label class="form-check-label">عرض عنوان الشركة</label>
                                        </div>
                                        
                                        <div class="form-check mb-2">
                                            <input type="checkbox" name="show_print_date" class="form-check-input" 
                                                   {{ old('show_print_date', $settings->show_print_date) ? 'checked' : '' }}>
                                            <label class="form-check-label">عرض تاريخ الطباعة</label>
                                        </div>
                                        
                                        <div class="form-check mb-2">
                                            <input type="checkbox" name="show_print_user" class="form-check-input" 
                                                   {{ old('show_print_user', $settings->show_print_user) ? 'checked' : '' }}>
                                            <label class="form-check-label">عرض اسم المستخدم</label>
                                        </div>
                                        
                                        <div class="form-check mb-2">
                                            <input type="checkbox" name="show_page_numbers" class="form-check-input" 
                                                   {{ old('show_page_numbers', $settings->show_page_numbers) ? 'checked' : '' }}>
                                            <label class="form-check-label">عرض أرقام الصفحات</label>
                                        </div>
                                        
                                        <div class="form-check mb-3">
                                            <input type="checkbox" name="show_footer" class="form-check-input" 
                                                   {{ old('show_footer', $settings->show_footer) ? 'checked' : '' }}>
                                            <label class="form-check-label">عرض التذييل</label>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label>نص مخصص للتذييل</label>
                                            <textarea name="custom_footer_text" class="form-control" rows="2">{{ old('custom_footer_text', $settings->custom_footer_text) }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="card mb-4">
                                    <div class="card-header bg-warning text-dark">
                                        <h5 class="mb-0">
                                            <i class="fas fa-table"></i>
                                            إعدادات الجداول
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-check mb-2">
                                            <input type="checkbox" name="table_borders" class="form-check-input" 
                                                   {{ old('table_borders', $settings->table_borders) ? 'checked' : '' }}>
                                            <label class="form-check-label">حدود الجداول</label>
                                        </div>
                                        
                                        <div class="form-check mb-3">
                                            <input type="checkbox" name="table_striped_rows" class="form-check-input" 
                                                   {{ old('table_striped_rows', $settings->table_striped_rows) ? 'checked' : '' }}>
                                            <label class="form-check-label">صفوف متناوبة الألوان</label>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label>نمط الجدول</label>
                                            <select name="table_style" class="form-control">
                                                <option value="professional" {{ $settings->table_style == 'professional' ? 'selected' : '' }}>احترافي</option>
                                                <option value="minimal" {{ $settings->table_style == 'minimal' ? 'selected' : '' }}>بسيط</option>
                                                <option value="bold" {{ $settings->table_style == 'bold' ? 'selected' : '' }}>عريض</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="card mb-4">
                                    <div class="card-header bg-success text-white">
                                        <h5 class="mb-0">
                                            <i class="fas fa-file-invoice"></i>
                                            إعدادات الفواتير
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-check mb-2">
                                            <input type="checkbox" name="show_invoice_qr_code" class="form-check-input" 
                                                   {{ old('show_invoice_qr_code', $settings->show_invoice_qr_code) ? 'checked' : '' }}>
                                            <label class="form-check-label">رمز QR للفاتورة</label>
                                        </div>
                                        
                                        <div class="form-check mb-2">
                                            <input type="checkbox" name="show_payment_terms" class="form-check-input" 
                                                   {{ old('show_payment_terms', $settings->show_payment_terms) ? 'checked' : '' }}>
                                            <label class="form-check-label">شروط الدفع</label>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label>شروط الدفع الافتراضية</label>
                                            <textarea name="default_payment_terms" class="form-control" rows="2">{{ old('default_payment_terms', $settings->default_payment_terms) }}</textarea>
                                        </div>
                                        
                                        <div class="form-check mb-2">
                                            <input type="checkbox" name="show_notes_section" class="form-check-input" 
                                                   {{ old('show_notes_section', $settings->show_notes_section) ? 'checked' : '' }}>
                                            <label class="form-check-label">قسم الملاحظات</label>
                                        </div>
                                        
                                        <div class="form-check">
                                            <input type="checkbox" name="show_signature_section" class="form-check-input" 
                                                   {{ old('show_signature_section', $settings->show_signature_section) ? 'checked' : '' }}>
                                            <label class="form-check-label">قسم التوقيعات</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Watermark Section -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card mb-4">
                                    <div class="card-header bg-dark text-white">
                                        <h5 class="mb-0">
                                            <i class="fas fa-water"></i>
                                            العلامة المائية
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-check mb-3">
                                                    <input type="checkbox" name="enable_watermark" class="form-check-input" 
                                                           {{ old('enable_watermark', $settings->enable_watermark) ? 'checked' : '' }}>
                                                    <label class="form-check-label">تفعيل العلامة المائية</label>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>نص العلامة المائية</label>
                                                    <input type="text" name="watermark_text" class="form-control" 
                                                           value="{{ old('watermark_text', $settings->watermark_text) }}">
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>لون العلامة المائية</label>
                                                    <input type="color" name="watermark_color" class="form-control color-picker" 
                                                           value="{{ old('watermark_color', $settings->watermark_color ?? '#f8f9fa') }}">
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>شفافية العلامة المائية (%)</label>
                                                    <input type="number" name="watermark_opacity" class="form-control" 
                                                           value="{{ old('watermark_opacity', $settings->watermark_opacity) }}" min="1" max="100">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <button type="submit" class="btn btn-primary btn-lg">
                                            <i class="fas fa-save"></i>
                                            حفظ الإعدادات
                                        </button>
                                        
                                        <button type="button" class="btn btn-warning btn-lg ml-2" onclick="resetSettings()">
                                            <i class="fas fa-undo"></i>
                                            إعادة تعيين افتراضي
                                        </button>
                                        
                                        <button type="button" class="btn btn-info btn-lg ml-2" onclick="previewInvoice()">
                                            <i class="fas fa-eye"></i>
                                            معاينة فاتورة
                                        </button>
                                        
                                        <button type="button" class="btn btn-secondary btn-lg ml-2" onclick="previewVoucher()">
                                            <i class="fas fa-eye"></i>
                                            معاينة سند
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Color picker functionality
document.querySelectorAll('.color-picker').forEach(function(input) {
    input.addEventListener('change', function() {
        const textSpan = this.parentElement.querySelector('.input-group-text');
        if (textSpan) {
            textSpan.textContent = this.value;
            textSpan.style.backgroundColor = this.value;
            textSpan.style.color = getContrastColor(this.value);
        }
    });
});

// Get contrast color for better text visibility
function getContrastColor(hexcolor) {
    const r = parseInt(hexcolor.substr(1,2), 16);
    const g = parseInt(hexcolor.substr(3,2), 16);
    const b = parseInt(hexcolor.substr(5,2), 16);
    const yiq = ((r*299) + (g*587) + (b*114)) / 1000;
    return (yiq >= 128) ? '#000000' : '#ffffff';
}

// Preview functions
function previewInvoice() {
    window.open('{{ route("print-settings.preview-invoice") }}', '_blank', 'width=1000,height=800');
}

function previewVoucher() {
    window.open('{{ route("print-settings.preview-voucher") }}', '_blank', 'width=1000,height=800');
}

// Reset settings
function resetSettings() {
    if (confirm('هل أنت متأكد من إعادة تعيين جميع إعدادات الطباعة إلى الوضع الافتراضي؟')) {
        fetch('{{ route("print-settings.reset") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
}

// Initialize color display on page load
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.color-picker').forEach(function(input) {
        const textSpan = input.parentElement.querySelector('.input-group-text');
        if (textSpan) {
            textSpan.style.backgroundColor = input.value;
            textSpan.style.color = getContrastColor(input.value);
        }
    });
});
</script>
@endpush 