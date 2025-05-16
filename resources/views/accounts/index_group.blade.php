@extends('layouts.app')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">
                    <i class="fas fa-folder mr-2 text-primary"></i>@lang('messages.account_groups_management_title')
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fas fa-home"></i> @lang('messages.dashboard_title')</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('accounts.index') }}">@lang('messages.accounts')</a></li>
                    <li class="breadcrumb-item active">@lang('messages.account_groups_management_title')</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <!-- إحصائيات الفئات -->
        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-gradient-success">
                    <div class="inner">
                        <h3>{{ $categories->where('type', 'asset')->count() }}</h3>
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
                        <h3>{{ $categories->where('type', 'liability')->count() }}</h3>
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
                        <h3>{{ $categories->where('type', 'revenue')->count() + $categories->where('type', 'expense')->count() }}</h3>
                        <p>@lang('messages.type_revenue') & @lang('messages.type_expense')</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-gradient-info">
                    <div class="inner">
                        <h3>{{ $categories->count() }}</h3>
                        <p>@lang('messages.total_categories')</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-sitemap"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- قسم البحث والتصفية -->
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
                            <div class="col-md-4">
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
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('messages.parent_category')</label>
                                    <select id="filter-parent" class="form-control">
                                        <option value="">@lang('messages.all')</option>
                                        <option value="null">@lang('messages.main_categories')</option>
                                        <option value="has_parent">@lang('messages.sub_categories')</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('messages.has_accounts')</label>
                                    <select id="filter-has-accounts" class="form-control">
                                        <option value="">@lang('messages.all')</option>
                                        <option value="yes">@lang('messages.yes')</option>
                                        <option value="no">@lang('messages.no')</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="custom-tabs-search" role="tabpanel" aria-labelledby="search-tab">
                        <div class="input-group">
                            <input type="text" id="categories-search" class="form-control" placeholder="@lang('messages.search_placeholder')">
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
                            يمكنك البحث عن طريق رمز الفئة أو اسمها
                        </small>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- بطاقة عرض الفئات -->
        <div class="card card-primary card-outline shadow-sm">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-list-alt mr-1"></i> @lang('messages.categories_list')
                </h3>
                <div class="card-tools">
                    <a href="{{ route('accounts.createGroup') }}" class="btn btn-sm btn-success">
                        <i class="fas fa-plus-circle mr-1"></i> @lang('messages.add_new_category')
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
                    <table class="table table-striped table-hover mb-0" id="categoriesTable">
                        <thead class="thead-light">
                            <tr>
                                <th style="width:60px;">#</th>
                                <th>@lang('messages.category_code')</th>
                                <th class="text-left">@lang('messages.category_name')</th>
                                <th>@lang('messages.account_type')</th>
                                <th>@lang('messages.parent_category')</th>
                                <th>@lang('messages.accounts_count')</th>
                                <th style="width:140px;">@lang('messages.actions')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categories as $index => $category)
                                <tr data-type="{{ $category->type }}" data-parent="{{ $category->parent_id ? 'has_parent' : 'null' }}" data-has-accounts="{{ $category->children()->where('is_group', 0)->count() > 0 ? 'yes' : 'no' }}">
                                    <td>{{ $categories->firstItem() + $index }}</td>
                                    <td><span class="badge badge-light">{{ $category->code }}</span></td>
                                    <td class="text-left">
                                        <span class="category-name">
                                            @if($category->parent_id)
                                                <i class="fas fa-level-down-alt text-muted mr-1"></i>
                                            @endif
                                            {{ $category->name }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $typeClass = [
                                                'asset' => 'success',
                                                'liability' => 'danger',
                                                'revenue' => 'warning',
                                                'expense' => 'info',
                                                'equity' => 'primary'
                                            ][$category->type] ?? 'secondary';
                                        @endphp
                                        <span class="badge badge-{{ $typeClass }}">
                                            @lang('messages.type_'.$category->type)
                                        </span>
                                    </td>
                                    <td>
                                        @if($category->parent)
                                            <span class="badge badge-light">{{ $category->parent->name }}</span>
                                        @else
                                            <span class="badge badge-secondary">@lang('messages.none_option')</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $accountsCount = $category->children()->where('is_group', 0)->count();
                                        @endphp
                                        <span class="badge badge-{{ $accountsCount > 0 ? 'info' : 'secondary' }} badge-pill">
                                            {{ $accountsCount }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('accounts.edit', $category) }}" class="btn btn-outline-primary" title="@lang('messages.edit')">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            {{-- تم إخفاء زر عرض الفروع مؤقتاً حتى يتم إصلاح مشكلة البيانات الوهمية --}}
                                            {{-- <a href="#" class="btn btn-outline-info view-children" data-toggle="modal" data-target="#categoryChildrenModal" data-category-id="{{ $category->id }}" data-category-name="{{ $category->name }}" title="@lang('messages.view_children')">
                                                <i class="fas fa-sitemap"></i>
                                            </a> --}}
                                            <form action="{{ route('accounts.destroy', $category) }}" method="POST" onsubmit="return confirm('@lang('messages.delete_confirmation_account')');" class="d-inline">
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
                                    <td colspan="7" class="py-4 text-center">
                                        <div class="empty-state">
                                            <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                                            <h5>@lang('messages.no_categories_to_display')</h5>
                                            <p class="text-muted">قم بإضافة فئة جديدة أو تعديل معايير البحث</p>
                                            <a href="{{ route('accounts.createGroup') }}" class="btn btn-primary mt-2">
                                                <i class="fas fa-plus-circle mr-1"></i> @lang('messages.add_new_category')
                                            </a>
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
                    <strong>@lang('messages.total_categories')</strong> 
                    <span class="badge badge-primary badge-pill ml-1">{{ $categories->total() }}</span>
                </div>
                <div>{{ $categories->appends(['locale' => app()->getLocale()])->links() }}</div>
            </div>
        </div>

        <!-- بطاقة الهيكل التنظيمي للحسابات -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card card-info card-outline shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-project-diagram mr-1"></i> @lang('messages.account_structure')
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="hierarchy-chart">
                                    <div class="d-flex justify-content-center">
                                        <div class="hierarchy-node main-node">
                                            <i class="fas fa-sitemap"></i> @lang('messages.chart_of_accounts')
                                        </div>
                                    </div>
                                    <div class="hierarchy-levels">
                                        <div class="hierarchy-level">
                                            @foreach(['asset', 'liability', 'equity', 'revenue', 'expense'] as $mainType)
                                                <div class="hierarchy-node type-node type-{{ $mainType }}">
                                                    <i class="
                                                        @if($mainType == 'asset') fas fa-money-bill-wave
                                                        @elseif($mainType == 'liability') fas fa-file-invoice-dollar
                                                        @elseif($mainType == 'equity') fas fa-balance-scale
                                                        @elseif($mainType == 'revenue') fas fa-chart-line
                                                        @elseif($mainType == 'expense') fas fa-shopping-cart
                                                        @endif
                                                    "></i>
                                                    @lang('messages.type_'.$mainType)
                                                    <span class="node-count">
                                                        {{ $categories->where('type', $mainType)->count() }}
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box bg-gradient-light">
                                    <span class="info-box-icon"><i class="fas fa-tree"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">@lang('messages.hierarchy_depth')</span>
                                        <span class="info-box-number">
                                            @php
                                                $maxDepth = 0;
                                                foreach($categories as $category) {
                                                    $depth = 0;
                                                    $parent = $category->parent;
                                                    while($parent) {
                                                        $depth++;
                                                        $parent = $parent->parent;
                                                    }
                                                    $maxDepth = max($maxDepth, $depth);
                                                }
                                                echo $maxDepth + 1; // +1 because root level is counted as 1
                                            @endphp
                                            @lang('messages.levels')
                                        </span>
                                        <div class="progress">
                                            <div class="progress-bar" style="width: {{ min(100, ($maxDepth + 1) * 20) }}%"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-center mt-4">
                                    <a href="{{ route('accounts.chart') }}" class="btn btn-primary">
                                        <i class="fas fa-sitemap mr-1"></i> @lang('messages.account_chart_title')
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for viewing category children -->
<div class="modal fade" id="categoryChildrenModal" tabindex="-1" role="dialog" aria-labelledby="categoryChildrenModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="categoryChildrenModalLabel"><i class="fas fa-sitemap mr-1"></i> <span id="categoryNamePlaceholder"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="text-center py-5" id="categoryChildrenLoader">
                    <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
                    <p class="mt-2">@lang('messages.loading')...</p>
                </div>
                <div id="categoryChildrenContent" style="display:none;">
                    <ul class="nav nav-tabs" id="categoryChildrenTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="sub-categories-tab" data-toggle="tab" href="#sub-categories" role="tab" aria-controls="sub-categories" aria-selected="true">
                                <i class="fas fa-folder mr-1"></i> @lang('messages.sub_categories')
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="accounts-tab" data-toggle="tab" href="#accounts" role="tab" aria-controls="accounts" aria-selected="false">
                                <i class="fas fa-file-alt mr-1"></i> @lang('messages.accounts')
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content p-3 border border-top-0 rounded-bottom" id="categoryChildrenTabContent">
                        <div class="tab-pane fade show active" id="sub-categories" role="tabpanel" aria-labelledby="sub-categories-tab">
                            <div id="subCategoriesList">
                                <!-- Subcategories will be loaded here -->
                            </div>
                        </div>
                        <div class="tab-pane fade" id="accounts" role="tabpanel" aria-labelledby="accounts-tab">
                            <div id="accountsList">
                                <!-- Accounts will be loaded here -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('messages.close')</button>
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
    const table = $('#categoriesTable').DataTable({
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
        const parentFilter = $('#filter-parent').val();
        const hasAccountsFilter = $('#filter-has-accounts').val();

        table.rows().every(function() {
            const row = this.node();
            const $row = $(row);
            
            let display = true;
            
            if (typeFilter && $row.data('type') !== typeFilter) {
                display = false;
            }
            
            if (parentFilter && $row.data('parent') !== parentFilter) {
                display = false;
            }
            
            if (hasAccountsFilter && $row.data('has-accounts') !== hasAccountsFilter) {
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
    $('#filter-type, #filter-parent, #filter-has-accounts').on('change', applyFilter);
    
    // وظيفة البحث
    $('#search-button').on('click', function() {
        const searchText = $('#categories-search').val().toLowerCase();
        
        table.search(searchText).draw();
    });
    
    // إعادة تعيين البحث
    $('#reset-search').on('click', function() {
        $('#categories-search').val('');
        table.search('').draw();
    });

    // تنفيذ البحث عند الضغط على Enter
    $('#categories-search').on('keypress', function(e) {
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

    // معالجة النقر على زر عرض الفروع
    $('.view-children').on('click', function() {
        const categoryId = $(this).data('category-id');
        const categoryName = $(this).data('category-name');
        
        $('#categoryNamePlaceholder').text(categoryName);
        $('#categoryChildrenLoader').show();
        $('#categoryChildrenContent').hide();
        
        // محاكاة طلب AJAX لجلب البيانات
        setTimeout(() => {
            $('#categoryChildrenLoader').hide();
            $('#categoryChildrenContent').show();
            
            // محاكاة بيانات الفئات الفرعية
            let subCategoriesHTML = '<div class="list-group">';
            const subCategoriesCount = Math.floor(Math.random() * 5);
            
            if (subCategoriesCount > 0) {
                for (let i = 1; i <= subCategoriesCount; i++) {
                    subCategoriesHTML += `
                        <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-folder text-warning mr-2"></i>
                                <strong>${categoryName} - فرعي ${i}</strong>
                            </div>
                            <div>
                                <span class="badge badge-primary badge-pill">${Math.floor(Math.random() * 10)}</span>
                                <a href="#" class="btn btn-sm btn-outline-primary ml-2"><i class="fas fa-edit"></i></a>
                            </div>
                        </div>
                    `;
                }
            } else {
                subCategoriesHTML += `
                    <div class="text-center py-4">
                        <i class="fas fa-folder-open text-muted fa-2x mb-2"></i>
                        <p class="text-muted">لا توجد فئات فرعية</p>
                    </div>
                `;
            }
            
            subCategoriesHTML += '</div>';
            $('#subCategoriesList').html(subCategoriesHTML);
            
            // محاكاة بيانات الحسابات
            let accountsHTML = '<div class="list-group">';
            const accountsCount = Math.floor(Math.random() * 8);
            
            if (accountsCount > 0) {
                for (let i = 1; i <= accountsCount; i++) {
                    accountsHTML += `
                        <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-file-alt text-info mr-2"></i>
                                <strong>حساب ${categoryName} ${i}</strong>
                                <span class="text-muted ml-2">رمز: ${1000 + i}</span>
                            </div>
                            <div>
                                <span class="badge badge-success badge-pill">${(Math.random() * 50000).toFixed(2)} IQD</span>
                                <a href="#" class="btn btn-sm btn-outline-info ml-2"><i class="fas fa-eye"></i></a>
                            </div>
                        </div>
                    `;
                }
            } else {
                accountsHTML += `
                    <div class="text-center py-4">
                        <i class="fas fa-file-alt text-muted fa-2x mb-2"></i>
                        <p class="text-muted">لا توجد حسابات</p>
                    </div>
                `;
            }
            
            accountsHTML += '</div>';
            $('#accountsList').html(accountsHTML);
            
        }, 800);
    });
});
</script>

<style>
.empty-state {
    padding: 40px 20px;
    text-align: center;
}

/* تحسين مظهر الجدول */
#categoriesTable {
    border-collapse: separate;
    border-spacing: 0;
}

#categoriesTable th {
    font-weight: bold;
    background-color: #f4f6f9;
    border-top: none;
}

#categoriesTable td {
    vertical-align: middle;
}

.category-name {
    font-weight: 500;
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

/* تنسيق الهيكل التنظيمي */
.hierarchy-chart {
    overflow-x: auto;
    padding: 20px 10px;
}

.hierarchy-node {
    display: inline-block;
    padding: 10px 15px;
    border-radius: 8px;
    margin: 0 5px 15px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    position: relative;
    transition: all 0.3s ease;
}

.hierarchy-node:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.main-node {
    background: linear-gradient(45deg, #2b5876, #4e4376);
    color: white;
    font-weight: bold;
    padding: 15px 25px;
    margin-bottom: 30px;
}

.type-node {
    padding: 8px 15px;
    color: white;
    font-weight: 500;
}

.type-asset {
    background: linear-gradient(45deg, #11998e, #38ef7d);
}

.type-liability {
    background: linear-gradient(45deg, #fc4a1a, #f7b733);
}

.type-equity {
    background: linear-gradient(45deg, #654ea3, #eaafc8);
}

.type-revenue {
    background: linear-gradient(45deg, #1d976c, #93f9b9);
}

.type-expense {
    background: linear-gradient(45deg, #2193b0, #6dd5ed);
}

.node-count {
    display: inline-block;
    background: rgba(255,255,255,0.2);
    border-radius: 50%;
    width: 24px;
    height: 24px;
    line-height: 24px;
    text-align: center;
    margin-left: 5px;
    font-size: 12px;
}

.hierarchy-levels {
    margin-top: 20px;
    position: relative;
}

.hierarchy-levels:before {
    content: '';
    position: absolute;
    top: -20px;
    left: 50%;
    height: 20px;
    width: 2px;
    background-color: #ddd;
}

.hierarchy-level {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
}
</style>
@endpush
