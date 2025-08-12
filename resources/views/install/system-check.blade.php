@extends('layouts.install')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white text-center">
                    <h2><i class="fas fa-heartbeat mr-2"></i>فحص صحة النظام</h2>
                    <p class="mb-0">فحص شامل لجميع مكونات النظام AurSuite</p>
                </div>
                <div class="card-body">
                    <!-- أزرار التحكم -->
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-primary" onclick="refreshChecks()">
                                    <i class="fas fa-sync-alt"></i> تحديث الفحص
                                </button>
                                <a href="{{ route('install.maintenance') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> العودة للصيانة
                                </a>
                            </div>
                        </div>
                        <div class="col-md-4 text-right">
                            @php
                                $totalChecks = count($checks);
                                $passedChecks = collect($checks)->where('status', true)->count();
                                $percentage = $totalChecks > 0 ? round(($passedChecks / $totalChecks) * 100) : 0;
                            @endphp
                            <div class="progress">
                                <div class="progress-bar 
                                    @if($percentage >= 90) bg-success 
                                    @elseif($percentage >= 70) bg-warning 
                                    @else bg-danger 
                                    @endif" 
                                    role="progressbar" 
                                    style="width: {{ $percentage }}%" 
                                    aria-valuenow="{{ $percentage }}" 
                                    aria-valuemin="0" 
                                    aria-valuemax="100">
                                    {{ $percentage }}%
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- نتائج الفحص -->
                    <div class="row">
                        <div class="col-12">
                            <h4 class="mb-3">
                                <i class="fas fa-list-check text-primary"></i> نتائج الفحص
                                <span class="badge 
                                    @if($percentage >= 90) badge-success 
                                    @elseif($percentage >= 70) badge-warning 
                                    @else badge-danger 
                                    @endif">
                                    {{ $passedChecks }}/{{ $totalChecks }}
                                </span>
                            </h4>
                        </div>
                    </div>

                    <div class="row">
                        @php
                            $categories = [
                                'system' => ['name' => 'النظام الأساسي', 'icon' => 'server', 'color' => 'primary'],
                            ];
                        @endphp

                        <div class="col-12">
                            <div class="accordion" id="checksAccordion">
                                <!-- فحوصات النظام الأساسي -->
                                <div class="card">
                                    <div class="card-header" id="systemHeader">
                                        <h5 class="mb-0">
                                            <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#systemChecks" aria-expanded="true">
                                                <i class="fas fa-server text-primary mr-2"></i>
                                                فحوصات النظام الأساسي
                                                @php
                                                    $systemChecks = collect($checks)->filter(function($check, $key) {
                                                        return !str_starts_with($key, 'ext_');
                                                    });
                                                    $systemPassed = $systemChecks->where('status', true)->count();
                                                    $systemTotal = $systemChecks->count();
                                                @endphp
                                                <span class="badge badge-{{ $systemPassed == $systemTotal ? 'success' : 'warning' }} ml-2">
                                                    {{ $systemPassed }}/{{ $systemTotal }}
                                                </span>
                                            </button>
                                        </h5>
                                    </div>
                                    <div id="systemChecks" class="collapse show" data-parent="#checksAccordion">
                                        <div class="card-body">
                                            <div class="list-group list-group-flush">
                                                @foreach($checks as $key => $check)
                                                    @if(!str_starts_with($key, 'ext_'))
                                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                                            <div class="d-flex align-items-center">
                                                                <i class="fas fa-{{ $check['status'] ? 'check-circle text-success' : 'times-circle text-danger' }} mr-3"></i>
                                                                <div>
                                                                    <h6 class="mb-0">{{ $check['name'] }}</h6>
                                                                    <small class="text-muted">{{ $check['message'] }}</small>
                                                                </div>
                                                            </div>
                                                            <span class="badge badge-{{ $check['status'] ? 'success' : 'danger' }}">
                                                                {{ $check['status'] ? 'نجح' : 'فشل' }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- فحوصات إضافات PHP -->
                                <div class="card">
                                    <div class="card-header" id="extensionsHeader">
                                        <h5 class="mb-0">
                                            <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#extensionsChecks">
                                                <i class="fas fa-puzzle-piece text-info mr-2"></i>
                                                إضافات PHP
                                                @php
                                                    $extensionsChecks = collect($checks)->filter(function($check, $key) {
                                                        return str_starts_with($key, 'ext_');
                                                    });
                                                    $extensionsPassed = $extensionsChecks->where('status', true)->count();
                                                    $extensionsTotal = $extensionsChecks->count();
                                                @endphp
                                                <span class="badge badge-{{ $extensionsPassed == $extensionsTotal ? 'success' : 'warning' }} ml-2">
                                                    {{ $extensionsPassed }}/{{ $extensionsTotal }}
                                                </span>
                                            </button>
                                        </h5>
                                    </div>
                                    <div id="extensionsChecks" class="collapse" data-parent="#checksAccordion">
                                        <div class="card-body">
                                            <div class="row">
                                                @foreach($checks as $key => $check)
                                                    @if(str_starts_with($key, 'ext_'))
                                                        <div class="col-md-6 mb-2">
                                                            <div class="d-flex align-items-center p-2 border rounded {{ $check['status'] ? 'border-success bg-light' : 'border-danger' }}">
                                                                <i class="fas fa-{{ $check['status'] ? 'check text-success' : 'times text-danger' }} mr-2"></i>
                                                                <span class="small">{{ $check['name'] }}</span>
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- ملخص الحالة -->
                    <div class="row">
                        <div class="col-12">
                            <div class="alert 
                                @if($percentage >= 90) alert-success 
                                @elseif($percentage >= 70) alert-warning 
                                @else alert-danger 
                                @endif" role="alert">
                                <h5 class="alert-heading">
                                    @if($percentage >= 90)
                                        <i class="fas fa-check-circle"></i> ممتاز! النظام في حالة صحية ممتازة
                                    @elseif($percentage >= 70)
                                        <i class="fas fa-exclamation-triangle"></i> جيد! النظام يعمل مع بعض التحسينات المقترحة
                                    @else
                                        <i class="fas fa-times-circle"></i> تحذير! يحتاج النظام إلى إصلاحات قبل الاستخدام في الإنتاج
                                    @endif
                                </h5>
                                
                                @if($percentage < 100)
                                    <hr>
                                    <h6>النصائح والتوصيات:</h6>
                                    <ul class="mb-0">
                                        @foreach($checks as $check)
                                            @if(!$check['status'])
                                                <li>إصلاح مشكلة: {{ $check['name'] }} - {{ $check['message'] }}</li>
                                            @endif
                                        @endforeach
                                    </ul>
                                @else
                                    <hr>
                                    <p class="mb-0">جميع الفحوصات نجحت! النظام جاهز للاستخدام في بيئة الإنتاج.</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- أدوات إضافية -->
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card border-info">
                                <div class="card-body text-center">
                                    <i class="fas fa-download fa-2x text-info mb-2"></i>
                                    <h6>تصدير النتائج</h6>
                                    <button class="btn btn-info btn-sm" onclick="exportResults()">
                                        تصدير JSON
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-warning">
                                <div class="card-body text-center">
                                    <i class="fas fa-print fa-2x text-warning mb-2"></i>
                                    <h6>طباعة التقرير</h6>
                                    <button class="btn btn-warning btn-sm" onclick="window.print()">
                                        طباعة
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-success">
                                <div class="card-body text-center">
                                    <i class="fas fa-envelope fa-2x text-success mb-2"></i>
                                    <h6>إرسال للدعم</h6>
                                    <button class="btn btn-success btn-sm" onclick="sendToSupport()">
                                        إرسال التقرير
                                    </button>
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
function refreshChecks() {
    location.reload();
}

function exportResults() {
    const results = @json($checks);
    const blob = new Blob([JSON.stringify(results, null, 2)], {type: 'application/json'});
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `system-check-${new Date().toISOString().slice(0,10)}.json`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
}

function sendToSupport() {
    const results = @json($checks);
    const subject = 'تقرير فحص النظام - AurSuite';
    const body = `تقرير فحص النظام:
    
النسبة المئوية للنجاح: {{ $percentage }}%
الفحوصات الناجحة: {{ $passedChecks }}/{{ $totalChecks }}
التاريخ: ${new Date().toLocaleDateString('ar')}

النتائج التفصيلية:
${JSON.stringify(results, null, 2)}`;
    
    const mailtoUrl = `mailto:support@aursuite.com?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`;
    window.location.href = mailtoUrl;
}
</script>

<style>
@media print {
    .btn, .card-header button {
        display: none !important;
    }
    .collapse {
        display: block !important;
    }
    .card {
        break-inside: avoid;
    }
}
</style>
@endsection
