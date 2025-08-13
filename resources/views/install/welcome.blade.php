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
                        
                        <form method="POST" action="{{ route('install.process') }}" id="license-form">
                            @csrf
                            <div class="form-group mb-3">
                                <label for="license_key" class="form-label">مفتاح الترخيص</label>
                                <input type="text" class="form-control" id="license_key" name="license_key" 
                                       value="{{ old('license_key', 'DEV-2025-INTERNAL') }}">
                                <small class="form-text text-muted">للتطوير: DEV-2025-INTERNAL</small>
                                @if(session('license_error'))
                                    <div class="alert alert-danger mt-2">{{ session('license_error') }}</div>
                                @endif
                            </div>
                            <button type="submit" class="btn btn-success btn-lg btn-block">
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
// JavaScript مبسط ومباشر
document.addEventListener('DOMContentLoaded', function() {
    var form = document.getElementById('license-form');
    var btn = form ? form.querySelector('button[type="submit"]') : null;
    
    // التأكد من أن الزر نشط
    if (btn) {
        btn.disabled = false;
    }
    
    // إضافة معلومات debugging للمطورين
    if (window.location.hostname !== 'localhost') {
        console.log('🔧 نظام التثبيت - معلومات النظام:');
        console.log('📍 الدومين:', window.location.hostname);
        console.log('🔗 الرابط الحالي:', window.location.href);
        console.log('📝 النموذج موجود:', !!form);
    }
    
    // السماح بالإرسال دائماً
    if (form) {
        form.addEventListener('submit', function() {
            if (btn) btn.disabled = true; // منع الإرسال المتكرر
            return true;
        });
    }
});
</script>
@endsection 