@extends('layouts.install')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white text-center">
                    <h2>مرحباً بك في مثبت نظام المحاسبة المحترف</h2>
                    <p class="mb-0">معالج تثبيت خطوة بخطوة لتثبيت نظام المحاسبة الخاص بك بسهولة واحترافية</p>
                </div>
                <div class="card-body">
                    @if(session('install_notice'))
                        <div class="alert alert-warning text-center">{{ session('install_notice') }}</div>
                    @endif
                    <h4 class="mb-3">System Requirements Check</h4>
                    @if(!empty($requirements['installer_dirs']))
                        <div class="alert alert-danger">
                            <b>بعض المجلدات لم يتم إنشاؤها أو لم يتم ضبط الصلاحيات تلقائيًا:</b>
                            <ul style="margin-top:10px;">
                                @foreach($requirements['installer_dirs'] as $err)
                                    <li>{!! $err !!}</li>
                                @endforeach
                            </ul>
                            <div class="mt-2">يرجى إنشاء هذه المجلدات وضبط الصلاحيات يدويًا عبر لوحة تحكم الاستضافة (File Manager).</div>
                        </div>
                    @endif
                    <ul class="list-group mb-4">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Required PHP Version: <strong>{{ $requirements['php']['required'] }}+</strong></span>
                            <span class="badge badge-{{ $requirements['php']['ok'] ? 'success' : 'danger' }}">{{ $requirements['php']['current'] }}</span>
                        </li>
                        @foreach($requirements['extensions'] as $ext => $ok)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>PHP Extension: <strong>{{ strtoupper($ext) }}</strong></span>
                            <span class="badge badge-{{ $ok ? 'success' : 'danger' }}">{{ $ok ? 'Available' : 'Missing' }}</span>
                        </li>
                        @endforeach
                        @foreach($requirements['permissions'] as $perm => $ok)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Write Permission: <strong>{{ $perm == 'storage' ? 'storage folder' : '.env file' }}</strong></span>
                            <span class="badge badge-{{ $ok ? 'success' : 'danger' }}">{{ $ok ? 'Writable' : 'Not Writable' }}</span>
                        </li>
                        @endforeach
                    </ul>
                    @if($requirements['php']['ok'] && !in_array(false, $requirements['extensions']) && !in_array(false, $requirements['permissions']))
                        <div class="alert alert-info mb-4">
                            <h5><i class="fas fa-key me-2"></i>مفتاح الترخيص</h5>
                            <p class="mb-2">أدخل مفتاح الترخيص للمتابعة. للتطوير والاختبار، استخدم:</p>
                            <code class="text-primary">DEV-2025-INTERNAL</code>
                            <small class="d-block mt-1 text-muted">هذا المفتاح صالح لسنة واحدة للتطوير والاختبار</small>
                        </div>
                        
                        <form method="POST" action="{{ route('install.process') }}" id="license-form" novalidate>
                            @csrf
                            <div class="form-group mb-3">
                                <label for="license_key" class="form-label">مفتاح الترخيص <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="license_key" name="license_key" 
                                       value="{{ old('license_key', 'DEV-2025-INTERNAL') }}" 
                                       placeholder="DEV-2025-XXXXXXXX">
                                <small class="form-text text-muted">يجب إدخال مفتاح ترخيص صحيح للمتابعة</small>
                                <div id="license-status" class="mt-2"></div>
                                @if(session('license_error'))
                                    <div class="alert alert-danger mt-2">{{ session('license_error') }}</div>
                                @endif
                            </div>
                            <button type="submit" class="btn btn-success btn-lg btn-block" id="continue-btn">
                                <i class="fas fa-arrow-right me-2"></i>متابعة التثبيت
                            </button>
                        </form>
                    @else
                        <div class="alert alert-danger text-center">
                            Please make sure all requirements above are met before continuing.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<script>
document.getElementById('license_key').addEventListener('input', function() {
    var key = this.value.trim();
    var status = document.getElementById('license-status');
    var btn = document.getElementById('continue-btn');
    
    if (!key) { 
        status.innerHTML = ''; 
        return; 
    }
    
    // تحقق خاص للمفتاح الافتراضي
    if (key === 'DEV-2025-INTERNAL' || key === 'DEV-2025-TESTING') {
        status.innerHTML = '<span class="text-success"><i class="fas fa-check-circle me-1"></i>مفتاح ترخيص تطوير صحيح</span>';
        btn.disabled = false;
    }
    // التحقق من تنسيق مفتاح التطوير
    else if (key.match(/^DEV-\d{4}-[A-Z0-9]{4,}$/i)) {
        status.innerHTML = '<span class="text-success"><i class="fas fa-check-circle me-1"></i>مفتاح ترخيص تطوير صحيح</span>';
        btn.disabled = false;
    } 
    // التحقق من تنسيق مفتاح الإنتاج (للمستقبل)
    else if (key.match(/^PROD-\d{4}-[A-Z0-9]{12}$/i)) {
        status.innerHTML = '<span class="text-success"><i class="fas fa-check-circle me-1"></i>مفتاح ترخيص إنتاج صحيح</span>';
        btn.disabled = false;
    }
    else if (key.length >= 3) {
        // السماح بالمفاتيح الأخرى لكن بتحذير
        status.innerHTML = '<span class="text-info"><i class="fas fa-info-circle me-1"></i>سيتم التحقق من صحة المفتاح عند المتابعة</span>';
        btn.disabled = false;
    }
    else {
        status.innerHTML = '<span class="text-warning"><i class="fas fa-exclamation-triangle me-1"></i>يرجى إدخال مفتاح الترخيص</span>';
        btn.disabled = true;
    }
});

// تحقق أولي عند تحميل الصفحة
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('license_key').dispatchEvent(new Event('input'));
    
    // آلية احتياطية للـ form submission
    document.getElementById('license-form').addEventListener('submit', function(e) {
        var key = document.getElementById('license_key').value.trim();
        if (!key || key.length < 3) {
            e.preventDefault();
            alert('يرجى إدخال مفتاح ترخيص صحيح');
            return false;
        }
        return true;
    });
});
</script>
@endsection 