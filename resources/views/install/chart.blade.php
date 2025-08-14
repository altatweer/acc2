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
                        <h6><i class="fas fa-info-circle"></i> الشجرة المحاسبية الشاملة - أكثر من 100 حساب:</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="mb-0">
                                    <li><strong>الأصول المتداولة:</strong> النقدية (3 صناديق)، البنوك (5 بنوك)، العملاء (محليون، دوليون، حكوميون)، المخزون المفصل</li>
                                    <li><strong>الأصول الثابتة:</strong> الأراضي، المباني، المعدات، الأثاث، المركبات + الإهلاك المجمع</li>
                                    <li><strong>الخصوم المتداولة:</strong> الموردون (محليون، دوليون)، الرواتب والمزايا، خصومات الموظفين</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="mb-0">
                                    <li><strong>حقوق الملكية:</strong> رأس المال المفصل، الاحتياطيات، الأرباح المحتجزة</li>
                                    <li><strong>الإيرادات:</strong> المبيعات (محلية، تصديرية)، الخدمات، الإيرادات الأخرى</li>
                                    <li><strong>المصروفات:</strong> تكلفة البضاعة، الرواتب المفصلة، التشغيلية، الإهلاك، المالية، الضرائب</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-success">
                        <h6><i class="fas fa-link"></i> الحسابات الافتراضية التي سيتم ربطها تلقائياً (محدثة):</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="mb-0">
                                    <li>حساب المبيعات المحلية (4101)</li>
                                    <li>حساب مشتريات البضاعة (5101)</li>
                                    <li>حساب العملاء المحليون (1201)</li>
                                    <li>حساب الموردون المحليون (2101)</li>
                                    <li>حساب الرواتب الأساسية (5201)</li>
                                    <li>حساب رواتب مستحقة الدفع (2201)</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="mb-0">
                                    <li>حساب سلف الموظفين (2301)</li>
                                    <li>حساب ضريبة القيمة المضافة (5602)</li>
                                    <li>حساب بضاعة جاهزة للبيع (1301)</li>
                                    <li>حساب البنك المركزي العراقي (1110)</li>
                                    <li>حساب الصندوق الرئيسي (1101)</li>
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