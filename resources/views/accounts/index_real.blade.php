@php
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
@endphp

@extends('layouts.app')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">
                    <i class="fas fa-money-check-alt mr-2 text-primary"></i>@lang('messages.real_accounts_list')
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fas fa-home"></i> @lang('messages.dashboard_title')</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('accounts.index') }}">@lang('messages.accounts')</a></li>
                    <li class="breadcrumb-item active">@lang('messages.real_accounts_list')</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <!-- إحصائيات الحسابات -->
        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-gradient-success">
                    <div class="inner">
                        <h3>{{ $statistics['asset_accounts'] }}</h3>
                        <p>@lang('messages.type_asset')</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-gradient-danger">
                    <div class="inner">
                        <h3>{{ $statistics['liability_accounts'] }}</h3>
                        <p>@lang('messages.type_liability')</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-gradient-warning">
                    <div class="inner">
                        <h3>{{ $statistics['cash_box_accounts'] }}</h3>
                        <p>@lang('messages.is_cash_box')</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-cash-register"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-gradient-info">
                    <div class="inner">
                        <h3>{{ $statistics['total_accounts'] }}</h3>
                        <p>@lang('messages.total_accounts')</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-calculator"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- بطاقة البحث والفلترة -->
        <div class="card card-primary card-outline card-outline-tabs shadow-sm mb-4">
            <div class="card-header p-0 border-bottom-0">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="filter-tab" data-toggle="pill" href="#custom-tabs-filter" role="tab" aria-controls="custom-tabs-filter" aria-selected="true">
                            <i class="fas fa-filter mr-1"></i> @lang('messages.filter')
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="search-tab" data-toggle="pill" href="#custom-tabs-search" role="tab" aria-controls="custom-tabs-search" aria-selected="false">
                            <i class="fas fa-search mr-1"></i> @lang('messages.search')
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="custom-tabs-filter" role="tabpanel" aria-labelledby="filter-tab">
                        <form method="GET" action="{{ route('accounts.real') }}" id="filterForm">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>@lang('messages.account_type')</label>
                                        <select name="type" id="filter-type" class="form-control">
                                            <option value="">@lang('messages.all')</option>
                                            <option value="asset" {{ request('type') == 'asset' ? 'selected' : '' }}>@lang('messages.type_asset')</option>
                                            <option value="liability" {{ request('type') == 'liability' ? 'selected' : '' }}>@lang('messages.type_liability')</option>
                                            <option value="equity" {{ request('type') == 'equity' ? 'selected' : '' }}>@lang('messages.type_equity')</option>
                                            <option value="revenue" {{ request('type') == 'revenue' ? 'selected' : '' }}>@lang('messages.type_revenue')</option>
                                            <option value="expense" {{ request('type') == 'expense' ? 'selected' : '' }}>@lang('messages.type_expense')</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>@lang('messages.account_nature')</label>
                                        <select name="nature" id="filter-nature" class="form-control">
                                            <option value="">@lang('messages.all')</option>
                                            <option value="debit" {{ request('nature') == 'debit' ? 'selected' : '' }}>@lang('messages.debit_nature')</option>
                                            <option value="credit" {{ request('nature') == 'credit' ? 'selected' : '' }}>@lang('messages.credit_nature')</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>@lang('messages.is_cash_box')</label>
                                        <select name="is_cash_box" id="filter-cash-box" class="form-control">
                                            <option value="">@lang('messages.all')</option>
                                            <option value="1" {{ request('is_cash_box') == '1' ? 'selected' : '' }}>@lang('messages.yes')</option>
                                            <option value="0" {{ request('is_cash_box') == '0' ? 'selected' : '' }}>@lang('messages.no')</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>@lang('messages.currency')</label>
                                        <select name="currency" id="filter-currency" class="form-control">
                                            <option value="">@lang('messages.all')</option>
                                            @foreach($currencies as $currency)
                                                <option value="{{ $currency }}" {{ request('currency') == $currency ? 'selected' : '' }}>{{ $currency }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-filter mr-1"></i> تطبيق الفلتر
                                    </button>
                                    <a href="{{ route('accounts.real') }}" class="btn btn-default">
                                        <i class="fas fa-times mr-1"></i> إعادة تعيين
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="custom-tabs-search" role="tabpanel" aria-labelledby="search-tab">
                        <form method="GET" action="{{ route('accounts.real') }}" id="searchForm">
                            <!-- الاحتفاظ بالفلاتر السابقة في البحث -->
                            <input type="hidden" name="type" value="{{ request('type') }}">
                            <input type="hidden" name="nature" value="{{ request('nature') }}">
                            <input type="hidden" name="is_cash_box" value="{{ request('is_cash_box') }}">
                            <input type="hidden" name="currency" value="{{ request('currency') }}">
                            
                            <div class="input-group">
                                <input type="text" name="search" value="{{ request('search') }}" id="accounts-search" class="form-control" placeholder="@lang('messages.search_placeholder')">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-primary" id="search-button">
                                        <i class="fas fa-search"></i> @lang('messages.search_button')
                                    </button>
                                    <a href="{{ route('accounts.real') }}" class="btn btn-default" id="reset-search">
                                        <i class="fas fa-times"></i> @lang('messages.reset_button')
                                    </a>
                                </div>
                            </div>
                            <small class="form-text text-muted">
                                يمكنك البحث عن طريق رمز الحساب أو اسمه
                            </small>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- بطاقة عرض الحسابات -->
        <div class="card card-primary card-outline shadow-sm">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-list mr-1"></i> @lang('messages.real_accounts_list')
                </h3>
                <div class="card-tools">
                    <a href="{{ route('accounts.createAccount') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus-circle mr-1"></i> @lang('messages.add_new')
                    </a>
                    <div class="btn-group ml-1">
                        <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-download mr-1"></i> @lang('messages.export') 
                        </button>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="#">
                                <i class="fas fa-file-excel mr-1 text-success"></i> @lang('messages.export_excel')
                            </a>
                            <a class="dropdown-item" href="#">
                                <i class="fas fa-file-pdf mr-1 text-danger"></i> @lang('messages.export_pdf')
                            </a>
                            <a class="dropdown-item" href="#">
                                <i class="fas fa-print mr-1 text-primary"></i> @lang('messages.print')
                            </a>
                        </div>
                    </div>
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                        <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="@lang('messages.close')">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-striped table-hover text-center mb-0" id="realAccountsTable">
                        <thead class="thead-light">
                            <tr>
                                <th style="width:60px;">#</th>
                                <th>@lang('messages.account_code')</th>
                                <th class="text-left">@lang('messages.account_name')</th>
                                <th>@lang('messages.parent_category')</th>
                                <th>@lang('messages.account_nature')</th>
                                <th>@lang('messages.is_cash_box')</th>
                                <th>@lang('messages.currency')</th>
                                <th style="width:120px;">@lang('messages.actions')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($accounts as $i => $account)
                                <tr data-type="{{ $account->type }}" data-nature="{{ $account->nature }}" data-cash="{{ $account->is_cash_box ? '1' : '0' }}" data-currency="{{ $account->currency }}">
                                    <td>{{ $accounts->firstItem() + $i }}</td>
                                    <td>{{ $account->code }}</td>
                                    <td class="text-left">{{ $account->name }}</td>
                                    <td>{{ $account->parent->name ?? '-' }}</td>
                                    <td>
                                        @if($account->nature == 'debit')
                                            <span class="badge badge-info">@lang('messages.debit_nature')</span>
                                        @elseif($account->nature == 'credit')
                                            <span class="badge badge-warning">@lang('messages.credit_nature')</span>
                                        @else
                                            <span class="badge badge-secondary">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($account->is_cash_box)
                                            <span class="badge badge-success">@lang('messages.yes')</span>
                                        @else
                                            <span class="badge badge-secondary">@lang('messages.no')</span>
                                        @endif
                                    </td>
                                    <td>{{ $account->currency ?? '-' }}</td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('accounts.show', $account) }}" class="btn btn-outline-info" title="@lang('messages.details')">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('accounts.edit', $account) }}" class="btn btn-outline-primary" title="@lang('messages.edit')">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('accounts.destroy', $account) }}" method="POST" onsubmit="return confirm('@lang('messages.delete_confirmation_account')');" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger" title="@lang('messages.delete')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="py-4 text-center">
                                        <div class="empty-state">
                                            <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                            <h5>@lang('messages.no_accounts_to_display')</h5>
                                            <p class="text-muted">قم بإضافة حساب جديد أو تعديل معايير البحث</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer clearfix d-flex justify-content-between align-items-center bg-light">
                <div>
                    <strong>@lang('messages.total_accounts')</strong> 
                    <span class="badge badge-primary badge-pill ml-1">{{ $accounts->total() }}</span>
                    @if($accounts->total() != $statistics['total_accounts'])
                        <small class="text-muted">من أصل {{ $statistics['total_accounts'] }}</small>
                    @endif
                </div>
                <div>{{ $accounts->appends(['locale' => app()->getLocale()])->links() }}</div>
            </div>
        </div>

        <!-- صناديق النقد والبنوك -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card card-success card-outline shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-cash-register mr-1"></i> صناديق النقد والأرصدة المالية
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @php
                                $cashBoxes = $allCashBoxes->take(8);
                                $activeCurrencies = \App\Models\Currency::where('is_active', true)->pluck('code');
                                if ($activeCurrencies->isEmpty()) {
                                    $activeCurrencies = collect(['IQD', 'USD', 'EUR']);
                                }
                            @endphp
                            
                            @forelse($cashBoxes as $cashBox)
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card card-widget widget-user-2 shadow-lg">
                                        <!-- Widget user header -->
                                        <div class="widget-user-header bg-gradient-{{ $loop->iteration % 4 == 0 ? 'danger' : ($loop->iteration % 4 == 1 ? 'success' : ($loop->iteration % 4 == 2 ? 'info' : 'warning')) }}">
                                            <div class="widget-user-image">
                                                <i class="fas fa-wallet fa-2x"></i>
                                            </div>
                                            <h3 class="widget-user-username">{{ $cashBox->name }}</h3>
                                            <h5 class="widget-user-desc">
                                                <i class="fas fa-cash-register mr-1"></i> صندوق نقدي
                                            </h5>
                                        </div>
                                        <div class="card-footer p-0">
                                            <div class="row">
                                                @foreach($activeCurrencies as $index => $currency)
                                                    @php
                                                        $currencyBalance = $cashBox->balance($currency);
                                                        $isDefault = $currency == ($cashBox->default_currency ?? 'IQD');
                                                        $colorClass = $index % 3 == 0 ? 'primary' : ($index % 3 == 1 ? 'success' : 'info');
                                                    @endphp
                                                    <div class="col-4 border-right">
                                                        <div class="description-block @if($isDefault) border-bottom @endif">
                                                            <span class="description-percentage text-{{ $currencyBalance >= 0 ? 'success' : 'danger' }}">
                                                                <i class="fas fa-{{ $currencyBalance >= 0 ? 'plus' : 'minus' }}"></i>
                                                                @if($isDefault) <i class="fas fa-star text-warning ml-1"></i> @endif
                                                            </span>
                                                            <h5 class="description-header text-{{ $currencyBalance >= 0 ? 'success' : 'danger' }}">
                                                                {{ number_format(abs($currencyBalance), 0) }}
                                                            </h5>
                                                            <span class="description-text font-weight-bold text-{{ $colorClass }}">{{ $currency }}</span>
                                                        </div>
                                                        @if($isDefault)
                                                            <div class="bg-{{ $colorClass }} text-white text-center py-1">
                                                                <small><i class="fas fa-star mr-1"></i>افتراضي</small>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                            <!-- .row -->
                                            <div class="row">
                                                <div class="col-12 text-center p-2">
                                                    <a href="{{ route('accounts.show', $cashBox) }}" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-eye mr-1"></i> @lang('messages.details')
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <p class="text-center text-muted">لا توجد صناديق نقدية مسجلة</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css">
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>
<script>
$(function(){
    // تهيئة جدول البيانات (للعرض فقط - بدون فلترة)
    const table = $('#realAccountsTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/' + (document.documentElement.lang === 'ar' ? 'ar.json' : 'en.json')
        },
        order: [[0, 'asc']],
        pageLength: 25,
        lengthMenu: [10, 25, 50, 100],
        searching: false, // تعطيل البحث المحلي - نستخدم البحث على الخادم
        paging: false, // تعطيل التصفح المحلي - نستخدم Laravel pagination
        info: false, // تعطيل عرض المعلومات
        responsive: true,
        dom: 'Brt', // إزالة عناصر التحكم غير المطلوبة
        buttons: [
            'copy', 'excel', 'pdf', 'print'
        ]
    });

    // تطبيق التأثيرات البصرية فور تحميل الصفحة
    $('.small-box').each(function(index) {
        $(this).css('opacity', '0');
        $(this).css('transform', 'translateY(20px)');
        
        setTimeout(() => {
            $(this).css('transition', 'all 0.5s ease');
            $(this).css('opacity', '1');
            $(this).css('transform', 'translateY(0)');
        }, 100 * index);
    });

    // إضافة تأثير hover للصفوف
    $('#realAccountsTable tbody tr').hover(
        function() {
            $(this).addClass('table-active');
        },
        function() {
            $(this).removeClass('table-active');
        }
    );

    // تحسين تبديل التبويبات
    $('.nav-tabs a').on('click', function(e) {
        e.preventDefault();
        $(this).tab('show');
    });

    // إضافة مؤشر تحميل عند إرسال النماذج
    $('#filterForm, #searchForm').on('submit', function() {
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        
        submitBtn.prop('disabled', true)
                 .html('<i class="fas fa-spinner fa-spin mr-1"></i> جاري التحميل...');
                 
        // إعادة تعيين النص بعد فترة (في حالة عدم إعادة التحميل)
        setTimeout(() => {
            submitBtn.prop('disabled', false).html(originalText);
        }, 5000);
    });

    // تطبيق الفلتر تلقائياً عند تغيير القيم
    $('#filter-type, #filter-nature, #filter-cash-box, #filter-currency').on('change', function() {
        $('#filterForm').submit();
    });

    // البحث السريع عند الضغط على Enter
    $('#accounts-search').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            $('#searchForm').submit();
        }
    });
});
</script>

<style>
.empty-state {
    padding: 40px 20px;
    text-align: center;
}

/* تحسين مظهر الجدول */
#realAccountsTable {
    border-collapse: separate;
    border-spacing: 0;
}

#realAccountsTable th {
    font-weight: bold;
    background-color: #f4f6f9;
    border-top: none;
}

#realAccountsTable td {
    vertical-align: middle;
}

/* تأثيرات حركية */
.card {
    transition: all 0.3s ease;
}

.card:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

/* إصلاح مظهر الأزرار */
.btn-group-sm > .btn {
    padding: .25rem .5rem;
}

.btn-group-sm form {
    display: inline-block;
}

/* تحسين مظهر الإشارات */
.badge {
    padding: 0.4em 0.6em;
    font-weight: 500;
    font-size: 85%;
}
</style>
@endpush
