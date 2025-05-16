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
        <!-- إحصائيات الحسابات - تعرض الإجمالي للكل وليس فقط للصفحة الحالية -->
        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-gradient-success">
                    <div class="inner">
                        <h3>{{ \App\Models\Account::where('type', 'asset')->where('is_group', 0)->count() }}</h3>
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
                        <h3>{{ \App\Models\Account::where('type', 'liability')->where('is_group', 0)->count() }}</h3>
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
                        <h3>{{ \App\Models\Account::where('is_cash_box', true)->where('is_group', 0)->count() }}</h3>
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
                        <h3>{{ \App\Models\Account::where('is_group', 0)->count() }}</h3>
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
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('messages.account_type')</label>
                                    <select id="filter-type" class="form-control">
                                        <option value="">@lang('messages.all')</option>
                                        <option value="asset">@lang('messages.type_asset')</option>
                                        <option value="liability">@lang('messages.type_liability')</option>
                                        <option value="equity">@lang('messages.type_equity')</option>
                                        <option value="revenue">@lang('messages.type_revenue')</option>
                                        <option value="expense">@lang('messages.type_expense')</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('messages.account_nature')</label>
                                    <select id="filter-nature" class="form-control">
                                        <option value="">@lang('messages.all')</option>
                                        <option value="debit">@lang('messages.debit_nature')</option>
                                        <option value="credit">@lang('messages.credit_nature')</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('messages.is_cash_box')</label>
                                    <select id="filter-cash-box" class="form-control">
                                        <option value="">@lang('messages.all')</option>
                                        <option value="1">@lang('messages.yes')</option>
                                        <option value="0">@lang('messages.no')</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('messages.currency')</label>
                                    <select id="filter-currency" class="form-control">
                                        <option value="">@lang('messages.all')</option>
                                        @foreach(\App\Models\Account::where('is_group', 0)->pluck('currency')->unique()->filter() as $currency)
                                            <option value="{{ $currency }}">{{ $currency }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="custom-tabs-search" role="tabpanel" aria-labelledby="search-tab">
                        <div class="input-group">
                            <input type="text" id="accounts-search" class="form-control" placeholder="@lang('messages.search_placeholder')">
                            <div class="input-group-append">
                                <button class="btn btn-primary" id="search-button">
                                    <i class="fas fa-search"></i> @lang('messages.search_button')
                                </button>
                                <button class="btn btn-default" id="reset-search">
                                    <i class="fas fa-times"></i> @lang('messages.reset_button')
                                </button>
                            </div>
                        </div>
                        <small class="form-text text-muted">
                            يمكنك البحث عن طريق رمز الحساب أو اسمه
                        </small>
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
                </div>
                <div>{{ $accounts->appends(['locale' => app()->getLocale()])->links() }}</div>
            </div>
        </div>

        <!-- صناديق النقد والبنوك - للكل بدلاً من صفحة فقط -->
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
                                // استخدام جميع الصناديق النقدية وليس فقط الصفحة الحالية
                                $allCashBoxes = \App\Models\Account::where('is_cash_box', true)->get();
                                // أخذ أول 8 فقط للعرض
                                $cashBoxes = $allCashBoxes->take(8);
                            @endphp
                            
                            @forelse($cashBoxes as $cashBox)
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <div class="info-box bg-gradient-{{ $loop->iteration % 4 == 0 ? 'danger' : ($loop->iteration % 4 == 1 ? 'success' : ($loop->iteration % 4 == 2 ? 'info' : 'warning')) }}">
                                        <span class="info-box-icon"><i class="fas fa-wallet"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">{{ $cashBox->name }}</span>
                                            <span class="info-box-number">
                                                @php
                                                // حساب الرصيد الفعلي من قاعدة البيانات
                                                $balance = $cashBox->journalEntryLines()
                                                        ->where('currency', $cashBox->currency)
                                                        ->selectRaw('SUM(debit) - SUM(credit) as balance')
                                                        ->first()->balance ?? 0;
                                                @endphp
                                                {{ number_format($balance, 2) }} {{ $cashBox->currency ?? 'IQD' }}
                                            </span>
                                            <div class="progress">
                                                @php
                                                // حساب نسبة استخدام الصندوق (للاستخدام في progress bar)
                                                $maxBalance = $allCashBoxes->max(function($account) {
                                                    return abs($account->journalEntryLines()
                                                        ->where('currency', $account->currency)
                                                        ->selectRaw('SUM(debit) - SUM(credit) as balance')
                                                        ->first()->balance ?? 0);
                                                }) ?: 1; // تجنب القسمة على صفر
                                                $percentage = min(100, round((abs($balance) / $maxBalance) * 100));
                                                @endphp
                                                <div class="progress-bar" style="width: {{ $percentage }}%"></div>
                                            </div>
                                            <a href="{{ route('accounts.show', $cashBox) }}" class="small-box-footer">
                                                @lang('messages.details') <i class="fas fa-arrow-circle-right"></i>
                                            </a>
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
    // تهيئة جدول البيانات
    const table = $('#realAccountsTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/' + (document.documentElement.lang === 'ar' ? 'ar.json' : 'en.json')
        },
        order: [[0, 'asc']],
        pageLength: 25,
        lengthMenu: [10, 25, 50, 100],
        searching: true,
        responsive: true,
        dom: 'Bfrtip',
        buttons: [
            'copy', 'excel', 'pdf', 'print'
        ]
    });

    // وظيفة تصفية البيانات حسب المعايير المحددة
    function applyFilter() {
        const typeFilter = $('#filter-type').val();
        const natureFilter = $('#filter-nature').val();
        const cashFilter = $('#filter-cash-box').val();
        const currencyFilter = $('#filter-currency').val();

        table.rows().every(function() {
            const row = this.node();
            const $row = $(row);
            
            let display = true;
            
            if (typeFilter && $row.data('type') !== typeFilter) {
                display = false;
            }
            
            if (natureFilter && $row.data('nature') !== natureFilter) {
                display = false;
            }
            
            if (cashFilter !== '' && $row.data('cash') !== cashFilter) {
                display = false;
            }
            
            if (currencyFilter && $row.data('currency') !== currencyFilter) {
                display = false;
            }
            
            $(row).toggle(display);
        });
        
        // إعادة ترقيم الصفوف بعد الفلترة
        table.rows(':visible').nodes().each(function(row, index) {
            $(row).find('td:first').text(index + 1);
        });
    }

    // تطبيق الفلاتر عند التغيير
    $('#filter-type, #filter-nature, #filter-cash-box, #filter-currency').on('change', applyFilter);
    
    // وظيفة البحث
    $('#search-button').on('click', function() {
        const searchText = $('#accounts-search').val().toLowerCase();
        
        table.search(searchText).draw();
    });
    
    // إعادة تعيين البحث
    $('#reset-search').on('click', function() {
        $('#accounts-search').val('');
        table.search('').draw();
    });

    // تنفيذ البحث عند الضغط على Enter
    $('#accounts-search').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            $('#search-button').click();
        }
    });

    // إضافة تأثيرات حركية للصناديق الإحصائية
    $('.small-box').each(function(index) {
        $(this).css('opacity', '0');
        $(this).css('transform', 'translateY(20px)');
        
        setTimeout(() => {
            $(this).css('transition', 'all 0.5s ease');
            $(this).css('opacity', '1');
            $(this).css('transform', 'translateY(0)');
        }, 100 * index);
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