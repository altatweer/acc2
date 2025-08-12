@extends('layouts.install')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white text-center">
                    <h2><i class="fas fa-tools mr-2"></i>أدوات الصيانة والإدارة</h2>
                    <p class="mb-0">إدارة وصيانة نظام AurSuite للمحاسبة</p>
                </div>
                <div class="card-body">
                    <!-- معلومات النظام -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h4 class="mb-3"><i class="fas fa-info-circle text-primary"></i> معلومات النظام</h4>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <h5 class="card-title">إصدار PHP</h5>
                                            <p class="card-text"><strong>{{ $systemInfo['php_version'] }}</strong></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <h5 class="card-title">إصدار Laravel</h5>
                                            <p class="card-text"><strong>{{ $systemInfo['laravel_version'] }}</strong></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <h5 class="card-title">تاريخ التثبيت</h5>
                                            <p class="card-text"><strong>{{ $systemInfo['install_date'] ?? 'غير محدد' }}</strong></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- إحصائيات النظام -->
                            <div class="row mt-3">
                                <div class="col-md-4 mb-3">
                                    <div class="card bg-info text-white">
                                        <div class="card-body text-center">
                                            <h5 class="card-title">المستخدمين</h5>
                                            <p class="card-text"><strong>{{ number_format($systemInfo['users_count']) }}</strong></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="card bg-warning text-white">
                                        <div class="card-body text-center">
                                            <h5 class="card-title">الحسابات</h5>
                                            <p class="card-text"><strong>{{ number_format($systemInfo['accounts_count']) }}</strong></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="card bg-danger text-white">
                                        <div class="card-body text-center">
                                            <h5 class="card-title">العملات</h5>
                                            <p class="card-text"><strong>{{ number_format($systemInfo['currencies_count']) }}</strong></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- أدوات الصيانة -->
                    <h4 class="mb-3"><i class="fas fa-toolbox text-primary"></i> أدوات الصيانة</h4>
                    <div class="row">
                        <!-- فحص صحة النظام -->
                        <div class="col-md-6 mb-3">
                            <div class="card border-primary">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <i class="fas fa-heartbeat fa-3x text-primary"></i>
                                    </div>
                                    <h5 class="card-title">فحص صحة النظام</h5>
                                    <p class="card-text">فحص شامل لجميع مكونات النظام والتحقق من سلامتها</p>
                                    <a href="{{ route('install.system_check') }}" class="btn btn-primary btn-block">
                                        <i class="fas fa-stethoscope mr-2"></i>بدء الفحص
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- النسخ الاحتياطية -->
                        <div class="col-md-6 mb-3">
                            <div class="card border-success">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <i class="fas fa-database fa-3x text-success"></i>
                                    </div>
                                    <h5 class="card-title">النسخ الاحتياطية</h5>
                                    <p class="card-text">إنشاء وإدارة النسخ الاحتياطية من قاعدة البيانات</p>
                                    <a href="{{ route('install.backup') }}" class="btn btn-success btn-block">
                                        <i class="fas fa-download mr-2"></i>إدارة النسخ
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- تحديث النظام -->
                        <div class="col-md-6 mb-3">
                            <div class="card border-warning">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <i class="fas fa-sync-alt fa-3x text-warning"></i>
                                    </div>
                                    <h5 class="card-title">تحديث النظام</h5>
                                    <p class="card-text">تحديث النظام وتشغيل الترحيلات الجديدة</p>
                                    <a href="{{ route('install.update') }}" class="btn btn-warning btn-block">
                                        <i class="fas fa-arrow-circle-up mr-2"></i>تحديث النظام
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- مسح الكاش -->
                        <div class="col-md-6 mb-3">
                            <div class="card border-danger">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <i class="fas fa-broom fa-3x text-danger"></i>
                                    </div>
                                    <h5 class="card-title">مسح الكاش</h5>
                                    <p class="card-text">مسح جميع ملفات الكاش لتحسين الأداء</p>
                                    <button onclick="clearCache()" class="btn btn-danger btn-block" id="clear-cache-btn">
                                        <i class="fas fa-trash mr-2"></i>مسح الكاش
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- أدوات إضافية -->
                    <h4 class="mb-3"><i class="fas fa-plus-circle text-primary"></i> أدوات إضافية</h4>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="card border-secondary">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <i class="fas fa-file-download fa-2x text-secondary"></i>
                                    </div>
                                    <h6 class="card-title">سجلات النظام</h6>
                                    <p class="card-text small">عرض سجلات النظام والأخطاء</p>
                                    <a href="{{ asset('storage/logs') }}" target="_blank" class="btn btn-secondary btn-sm">عرض السجلات</a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <div class="card border-secondary">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <i class="fas fa-info fa-2x text-secondary"></i>
                                    </div>
                                    <h6 class="card-title">معلومات PHP</h6>
                                    <p class="card-text small">عرض معلومات إعدادات PHP</p>
                                    <button onclick="window.open('/phpinfo', '_blank')" class="btn btn-secondary btn-sm">معلومات PHP</button>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <div class="card border-secondary">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <i class="fas fa-home fa-2x text-secondary"></i>
                                    </div>
                                    <h6 class="card-title">العودة للنظام</h6>
                                    <p class="card-text small">العودة إلى الصفحة الرئيسية</p>
                                    <a href="{{ url('/') }}" class="btn btn-secondary btn-sm">الصفحة الرئيسية</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function clearCache() {
    const btn = document.getElementById('clear-cache-btn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>جاري المسح...';
    
    fetch('{{ route("install.clear_cache") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        btn.disabled = false;
        if (data.success) {
            btn.innerHTML = '<i class="fas fa-check mr-2"></i>تم المسح بنجاح';
            btn.className = 'btn btn-success btn-block';
            setTimeout(() => {
                btn.innerHTML = '<i class="fas fa-trash mr-2"></i>مسح الكاش';
                btn.className = 'btn btn-danger btn-block';
            }, 3000);
        } else {
            btn.innerHTML = '<i class="fas fa-times mr-2"></i>فشل المسح';
            btn.className = 'btn btn-warning btn-block';
            alert('فشل في مسح الكاش: ' + data.message);
            setTimeout(() => {
                btn.innerHTML = '<i class="fas fa-trash mr-2"></i>مسح الكاش';
                btn.className = 'btn btn-danger btn-block';
            }, 3000);
        }
    })
    .catch(error => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-times mr-2"></i>خطأ في الشبكة';
        btn.className = 'btn btn-warning btn-block';
        console.error('Error:', error);
        setTimeout(() => {
            btn.innerHTML = '<i class="fas fa-trash mr-2"></i>مسح الكاش';
            btn.className = 'btn btn-danger btn-block';
        }, 3000);
    });
}
</script>
@endsection
