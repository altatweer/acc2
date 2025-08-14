@extends('layouts.install')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg">
                <div class="card-header bg-info text-white text-center">
                    <h2>استيراد شجرة الحسابات</h2>
                    <p class="mb-0">يمكنك استيراد شجرة حسابات جاهزة باللغة العربية أو الإنجليزية <b>لكل عملة محددة</b>.<br>
                        <span class="text-warning font-weight-bold">هذه الخطوة مطلوبة. يجب استيراد الشجرة لمتابعة التثبيت.</span>
                    </p>
                </div>
                <div class="card-body">
                    @if(session('chart_error'))
                        <div class="alert alert-danger text-center">{{ session('chart_error') }}</div>
                    @endif
                    <div class="mb-3">
                        <strong>العملات المحددة:</strong>
                        <ul>
                            @php $currencies = session('install_currencies', ['USD']); @endphp
                            @foreach($currencies as $cur)
                                <li>{{ $cur }}</li>
                            @endforeach
                        </ul>
                    </div>
                    
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle"></i> ماذا سيتم استيراده:</h6>
                        <ul class="mb-0">
                            <li><strong>الأصول:</strong> النقدية، البنوك، العملاء، المخزون، الأصول الثابتة</li>
                            <li><strong>الخصوم:</strong> الموردون، القروض، الرواتب المستحقة</li>
                            <li><strong>رأس المال:</strong> رأس المال، الأرباح المحتجزة</li>
                            <li><strong>الإيرادات:</strong> المبيعات، إيرادات الخدمات</li>
                            <li><strong>المصروفات:</strong> تكلفة البضاعة، المشتريات، الرواتب، الإيجار، المصروفات التشغيلية</li>
                        </ul>
                    </div>
                    
                    <div class="alert alert-success">
                        <h6><i class="fas fa-link"></i> الحسابات الافتراضية التي سيتم ربطها تلقائياً:</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="mb-0">
                                    <li>حساب المبيعات الافتراضي (4100)</li>
                                    <li>حساب المشتريات الافتراضي (5110)</li>
                                    <li>حساب العملاء (1301)</li>
                                    <li>حساب الموردين (2101)</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="mb-0">
                                    <li>حساب مصروفات الرواتب (5101)</li>
                                    <li>حساب الصندوق الرئيسي (1101)</li>
                                    <li>حساب البنك الرئيسي (1201)</li>
                                    <li>حساب المخزون (1401)</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <form method="POST" action="{{ route('install.importChart') }}">
                        @csrf
                        <div class="form-group mb-4">
                            <label class="form-label"><strong>اختر لغة شجرة الحسابات:</strong></label>
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="radio" name="chart_type" id="chart_ar" value="ar" checked>
                                <label class="form-check-label" for="chart_ar">
                                    <strong>العربية</strong> - شجرة الحسابات باللغة العربية
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="chart_type" id="chart_en" value="en">
                                <label class="form-check-label" for="chart_en">
                                    <strong>English</strong> - Chart of Accounts in English
                                </label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success btn-lg btn-block">
                            <i class="fas fa-download me-2"></i>استيراد الشجرة لجميع العملات
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 