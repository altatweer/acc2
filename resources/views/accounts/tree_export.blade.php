@extends('layouts.app')

@section('title', 'تصدير شجرة الحسابات')

@section('content')
<div class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <i class="fas fa-sitemap text-primary"></i>
                        تصدير شجرة الحسابات
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('accounts.index') }}">الحسابات</a></li>
                        <li class="breadcrumb-item active">تصدير شجرة الحسابات</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            
            <!-- إحصائيات سريعة -->
            <div class="row mb-4">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3>{{ $stats['total_groups'] }}</h3>
                            <p>إجمالي المجموعات</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-folder"></i>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ $stats['total_accounts'] }}</h3>
                            <p>جميع الحسابات</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-secondary">
                        <div class="inner">
                            <h3>{{ array_sum($stats['by_type']) }}</h3>
                            <p>إجمالي العناصر</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-calculator"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- إحصائيات حسب النوع -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-chart-pie mr-1"></i>
                                توزيع الحسابات حسب النوع
                            </h3>
                        </div>
                        <div class="card-body">
                            @php
                                $typeNames = [
                                    'asset' => 'الأصول',
                                    'liability' => 'الخصوم', 
                                    'equity' => 'حقوق الملكية',
                                    'revenue' => 'الإيرادات',
                                    'expense' => 'المصروفات'
                                ];
                                $typeColors = [
                                    'asset' => 'success',
                                    'liability' => 'danger', 
                                    'equity' => 'purple',
                                    'revenue' => 'primary',
                                    'expense' => 'warning'
                                ];
                            @endphp
                            
                            @foreach($stats['by_type'] as $type => $count)
                                @php
                                    $typeName = $typeNames[$type] ?? $type;
                                    $color = $typeColors[$type] ?? 'secondary';
                                @endphp
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge badge-{{ $color }} badge-lg">{{ $typeName }}</span>
                                    <strong>{{ $count }} حساب</strong>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-info-circle mr-1"></i>
                                معلومات مهمة
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <h5><i class="icon fas fa-info"></i> حول شجرة الحسابات:</h5>
                                <ul class="mb-0">
                                    <li><strong>المستوى 1:</strong> الفئات الرئيسية (الأصول، الخصوم، إلخ)</li>
                                    <li><strong>المستوى 2:</strong> المجموعات الفرعية الأولى</li>
                                    <li><strong>المستوى 3:</strong> المجموعات الفرعية الثانوية</li>
                                    <li><strong>المستوى 4:</strong> الحسابات التفصيلية النهائية</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- خيارات التصدير -->
            <div class="row">
                <div class="col-md-8">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-download mr-1"></i>
                                خيارات التصدير
                            </h3>
                        </div>
                        <div class="card-body">
                            <form id="exportForm">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>ملاحظة:</strong> سيتم تصدير جميع الحسابات والمجموعات الموجودة في النظام.
                                </div>

                                <div class="row mt-4">
                                    <div class="col-md-6">
                                        <button type="button" class="btn btn-success btn-block" onclick="exportToExcel()">
                                            <i class="fas fa-file-excel mr-2"></i>
                                            تصدير إلى Excel
                                        </button>
                                    </div>
                                    <div class="col-md-6">
                                        <button type="button" class="btn btn-info btn-block" onclick="previewTree()">
                                            <i class="fas fa-eye mr-2"></i>
                                            معاينة في المتصفح
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <button type="button" class="btn btn-secondary btn-block" onclick="printTree()">
                                            <i class="fas fa-print mr-2"></i>
                                            طباعة
                                        </button>
                                    </div>
                                    <div class="col-md-6">
                                        <button type="button" class="btn btn-warning btn-block" onclick="getTreeJson()">
                                            <i class="fas fa-code mr-2"></i>
                                            تصدير JSON
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-question-circle mr-1"></i>
                                تعليمات
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-light">
                                <h6><i class="fas fa-lightbulb"></i> نصائح مهمة:</h6>
                                <ul class="small mb-0">
                                    <li>تصدير Excel يتضمن جميع التفاصيل والتنسيقات</li>
                                    <li>المعاينة تظهر البيانات كما ستظهر في Excel</li>
                                    <li>الطباعة مُحسنة للورق A4</li>
                                    <li>JSON مفيد للمطورين والأنظمة الخارجية</li>
                                    <li>الحسابات مرتبة حسب الكود</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>

@endsection

@section('scripts')
<script>
// الحصول على base URL الحالي
const baseUrl = window.location.origin;

function exportToExcel() {
    // عرض loading
    Swal.fire({
        title: 'جاري التصدير...',
        text: 'يرجى الانتظار حتى اكتمال تصدير الملف',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        }
    });
    
    // تنزيل الملف
    window.location.href = `${baseUrl}/accounts/tree/export`;
    
    // إغلاق loading بعد ثانيتين
    setTimeout(() => {
        Swal.close();
        Swal.fire({
            icon: 'success',
            title: 'تم التصدير بنجاح!',
            text: 'تم تنزيل ملف Excel بنجاح',
            timer: 2000,
            showConfirmButton: false
        });
    }, 2000);
}

function previewTree() {
    window.open(`${baseUrl}/accounts/tree/preview`, '_blank');
}

function printTree() {
    window.open(`${baseUrl}/accounts/tree/print`, '_blank');
}

function getTreeJson() {
    Swal.fire({
        title: 'جاري تحضير البيانات...',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        }
    });
    
    fetch(`${baseUrl}/accounts/tree/json`)
        .then(response => {
            if (!response.ok) {
                throw new Error('خطأ في الشبكة');
            }
            return response.json();
        })
        .then(data => {
            Swal.close();
            
            // تنزيل JSON
            const blob = new Blob([JSON.stringify(data, null, 2)], {type: 'application/json'});
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `شجرة_الحسابات_${new Date().toISOString().slice(0,10)}.json`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
            
            Swal.fire({
                icon: 'success',
                title: 'تم التصدير!',
                text: 'تم تنزيل ملف JSON بنجاح',
                timer: 2000,
                showConfirmButton: false
            });
        })
        .catch(error => {
            Swal.close();
            console.error('خطأ:', error);
            Swal.fire({
                icon: 'error',
                title: 'خطأ!',
                text: 'حدث خطأ أثناء تصدير البيانات: ' + error.message
            });
        });
}

// اختبار أن الأزرار تعمل
console.log('🔗 مسارات التصدير:');
console.log('تصدير Excel:', `${baseUrl}/accounts/tree/export`);
console.log('معاينة:', `${baseUrl}/accounts/tree/preview`);
console.log('طباعة:', `${baseUrl}/accounts/tree/print`);
console.log('JSON:', `${baseUrl}/accounts/tree/json`);
</script>
@endsection

@section('styles')
<style>
.small-box {
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.card {
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.badge-lg {
    font-size: 0.9em;
    padding: 0.5em 0.8em;
}

.btn-block {
    border-radius: 8px;
    font-weight: 600;
}
</style>
@endsection 