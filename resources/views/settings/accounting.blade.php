@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">@lang('messages.accounting_settings')</h1>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                <strong>ملاحظة:</strong> الحسابات المختارة هنا ستُستخدم للقيود التلقائية في جميع العملات. تأكد من اختيار حسابات تدعم العملات المتعددة.
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-cogs"></i>
                        الحسابات الافتراضية للقيود التلقائية
                    </h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('accounting-settings.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <!-- حسابات المبيعات والمشتريات -->
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="mb-0">
                                            <i class="fas fa-shopping-cart"></i>
                                            حسابات المبيعات والمشتريات
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="sales_account">
                                                <i class="fas fa-money-bill-wave text-success"></i>
                                                حساب المبيعات الافتراضي
                                            </label>
                                            <select name="sales_account_id" id="sales_account" class="form-control">
                                                <option value="">-- اختر حساب المبيعات --</option>
                                                @foreach($individualAccounts as $acc)
                                                    @if($acc->type == 'revenue')
                                                        <option value="{{ $acc->id }}" 
                                                            {{ ($settings['default_sales_account'] ?? '') == $acc->id ? 'selected' : '' }}>
                                                            {{ $acc->name }} ({{ $acc->code }})
                                                        </option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="purchases_account">
                                                <i class="fas fa-truck text-warning"></i>
                                                حساب المشتريات الافتراضي
                                            </label>
                                            <select name="purchases_account_id" id="purchases_account" class="form-control">
                                                <option value="">-- اختر حساب المشتريات --</option>
                                                @foreach($individualAccounts as $acc)
                                                    @if($acc->type == 'expense')
                                                        <option value="{{ $acc->id }}" 
                                                            {{ ($settings['default_purchases_account'] ?? '') == $acc->id ? 'selected' : '' }}>
                                                            {{ $acc->name }} ({{ $acc->code }})
                                                        </option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- حسابات العملاء والموردين -->
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header bg-info text-white">
                                        <h5 class="mb-0">
                                            <i class="fas fa-users"></i>
                                            حسابات العملاء والموردين
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="customers_account">
                                                <i class="fas fa-user-friends text-primary"></i>
                                                فئة حسابات العملاء
                                            </label>
                                            <select name="customers_account_id" id="customers_account" class="form-control">
                                                <option value="">-- اختر الفئة المناسبة للعملاء --</option>
                                                @foreach($accountGroups as $acc)
                                                    @php
                                                        $typeIcon = [
                                                            'asset' => '🏦',
                                                            'liability' => '💳', 
                                                            'equity' => '🏛️',
                                                            'revenue' => '💹',
                                                            'expense' => '💸'
                                                        ][$acc->type] ?? '📁';
                                                        
                                                        $typeNameAr = [
                                                            'asset' => 'أصول',
                                                            'liability' => 'خصوم', 
                                                            'equity' => 'حقوق ملكية',
                                                            'revenue' => 'إيرادات',
                                                            'expense' => 'مصروفات'
                                                        ][$acc->type] ?? $acc->type;
                                                    @endphp
                                                    <option value="{{ $acc->id }}" 
                                                        {{ ($settings['default_customers_account'] ?? '') == $acc->id ? 'selected' : '' }}>
                                                        {{ $typeIcon }} {{ $acc->name }} ({{ $acc->code }}) - {{ $typeNameAr }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <small class="form-text text-muted">
                                                <i class="fas fa-info-circle"></i>
                                                سيتم إنشاء حساب منفصل لكل عميل داخل الفئة المختارة تلقائياً
                                            </small>
                                        </div>

                                        <div class="form-group">
                                            <label for="suppliers_account">
                                                <i class="fas fa-industry text-secondary"></i>
                                                فئة حسابات الموردين
                                            </label>
                                            <select name="suppliers_account_id" id="suppliers_account" class="form-control">
                                                <option value="">-- اختر الفئة المناسبة للموردين --</option>
                                                @foreach($accountGroups as $acc)
                                                    @php
                                                        $typeIcon = [
                                                            'asset' => '🏦',
                                                            'liability' => '💳', 
                                                            'equity' => '🏛️',
                                                            'revenue' => '💹',
                                                            'expense' => '💸'
                                                        ][$acc->type] ?? '📁';
                                                        
                                                        $typeNameAr = [
                                                            'asset' => 'أصول',
                                                            'liability' => 'خصوم', 
                                                            'equity' => 'حقوق ملكية',
                                                            'revenue' => 'إيرادات',
                                                            'expense' => 'مصروفات'
                                                        ][$acc->type] ?? $acc->type;
                                                    @endphp
                                                    <option value="{{ $acc->id }}" 
                                                        {{ ($settings['default_suppliers_account'] ?? '') == $acc->id ? 'selected' : '' }}>
                                                        {{ $typeIcon }} {{ $acc->name }} ({{ $acc->code }}) - {{ $typeNameAr }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <small class="form-text text-muted">
                                                <i class="fas fa-info-circle"></i>
                                                سيتم إنشاء حساب منفصل لكل مورد داخل الفئة المختارة تلقائياً
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- حسابات الرواتب -->
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header bg-success text-white">
                                        <h5 class="mb-0">
                                            <i class="fas fa-money-check"></i>
                                            حسابات الرواتب والموظفين
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="salary_expense_account">
                                                        <i class="fas fa-receipt text-danger"></i>
                                                        حساب مصروف الرواتب
                                                    </label>
                                                    <select name="salary_expense_account_id" id="salary_expense_account" class="form-control">
                                                        <option value="">-- اختر حساب مصروف الرواتب --</option>
                                                        @foreach($individualAccounts as $acc)
                                                            @if($acc->type == 'expense')
                                                                <option value="{{ $acc->id }}" 
                                                                    {{ ($settings['salary_expense_account'] ?? '') == $acc->id ? 'selected' : '' }}>
                                                                    {{ $acc->name }} ({{ $acc->code }})
                                                                </option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="employee_payables_account">
                                                        <i class="fas fa-user-clock text-info"></i>
                                                        حساب مستحقات الموظفين
                                                    </label>
                                                    <select name="employee_payables_account_id" id="employee_payables_account" class="form-control">
                                                        <option value="">-- اختر حساب مستحقات الموظفين --</option>
                                                        @foreach($individualAccounts as $acc)
                                                            @if($acc->type == 'liability')
                                                                <option value="{{ $acc->id }}" 
                                                                    {{ ($settings['employee_payables_account'] ?? '') == $acc->id ? 'selected' : '' }}>
                                                                    {{ $acc->name }} ({{ $acc->code }})
                                                                </option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="deductions_account">
                                                        <i class="fas fa-minus-circle text-warning"></i>
                                                        حساب الاستقطاعات
                                                    </label>
                                                    <select name="deductions_account_id" id="deductions_account" class="form-control">
                                                        <option value="">-- اختر حساب الاستقطاعات --</option>
                                                        @foreach($individualAccounts as $acc)
                                                            @if($acc->type == 'liability')
                                                                <option value="{{ $acc->id }}" 
                                                                    {{ ($settings['deductions_account'] ?? '') == $acc->id ? 'selected' : '' }}>
                                                                    {{ $acc->name }} ({{ $acc->code }})
                                                                </option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- حساب الأرصدة الافتتاحية -->
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header bg-warning text-dark">
                                        <h5 class="mb-0">
                                            <i class="fas fa-balance-scale"></i>
                                            حساب الأرصدة الافتتاحية
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="opening_balance_account">
                                                        <i class="fas fa-hand-holding-usd text-primary"></i>
                                                        حساب الأرصدة الافتتاحية
                                                    </label>
                                                    <select name="opening_balance_account_id" id="opening_balance_account" class="form-control">
                                                        <option value="">-- اختر حساب الأرصدة الافتتاحية --</option>
                                                        @foreach($individualAccounts as $acc)
                                                            @if($acc->type == 'equity')
                                                                <option value="{{ $acc->id }}" 
                                                                    {{ ($settings['opening_balance_account'] ?? '') == $acc->id ? 'selected' : '' }}>
                                                                    {{ $acc->name }} ({{ $acc->code }})
                                                                </option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                    <small class="form-text text-muted">
                                                        <i class="fas fa-info-circle"></i>
                                                        هذا الحساب يُستخدم في القيود المحاسبية للأرصدة الافتتاحية عند إنشاء الحسابات الجديدة
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-save"></i>
                                @lang('messages.save_settings')
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/i18n/ar.js"></script>
<script>
$(function(){
    $('.form-control').select2({
        width: '100%',
        dir: "{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}",
        language: "{{ app()->getLocale() }}",
        placeholder: 'اختر حساب...',
        allowClear: true,
        templateResult: function(option) {
            if (!option.id) {
                return option.text;
            }
            
            var $option = $(
                '<span>' + option.text + '</span>'
            );
            
            return $option;
        }
    });
});
</script>
@endpush 