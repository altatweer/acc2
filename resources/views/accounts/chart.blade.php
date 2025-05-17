@extends('layouts.app')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">
                    <i class="fas fa-sitemap mr-2 text-primary"></i>@lang('messages.account_chart_title')
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fas fa-home"></i> @lang('messages.dashboard_title')</a></li>
                    <li class="breadcrumb-item active">@lang('messages.account_chart_title')</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-primary card-outline shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-sitemap mr-1"></i> @lang('messages.account_chart_title')
                        </h3>
                        <div class="card-tools">
                            <div class="input-group input-group-sm" style="width: 250px;">
                                <input type="text" id="account-search" class="form-control" placeholder="@lang('messages.search')...">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-primary">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="btn-group account-type-filter">
                                    <button type="button" class="btn btn-sm btn-outline-primary active" data-type="all">@lang('messages.all')</button>
                                    <button type="button" class="btn btn-sm btn-outline-success" data-type="asset">@lang('messages.type_asset')</button>
                                    <button type="button" class="btn btn-sm btn-outline-danger" data-type="liability">@lang('messages.type_liability')</button>
                                    <button type="button" class="btn btn-sm btn-outline-info" data-type="equity">@lang('messages.type_equity')</button>
                                    <button type="button" class="btn btn-sm btn-outline-warning" data-type="revenue">@lang('messages.type_revenue')</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" data-type="expense">@lang('messages.type_expense')</button>
                                </div>
                                <div class="float-right">
                                    <button type="button" id="expand-all" class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-expand-arrows-alt"></i> @lang('messages.expand_all')
                                    </button>
                                    <button type="button" id="collapse-all" class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-compress-arrows-alt"></i> @lang('messages.collapse_all')
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="account-tree-container">
                            <div class="alert alert-info mb-3">
                                <i class="fas fa-info-circle mr-1"></i> @lang('messages.click_account_for_details')
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div id="accountTree" class="custom-jstree"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="card card-info card-outline shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-info-circle mr-1"></i> @lang('messages.account_info')
                        </h3>
                        <div class="card-tools">
                            <span class="badge badge-light instruction-badge">
                                <i class="fas fa-mouse-pointer mr-1"></i> @lang('messages.select_account_in_tree')
                            </span>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-bordered mb-0" id="account-details">
                            <tbody>
                                <tr>
                                    <th style="width: 150px;">@lang('messages.account_name')</th>
                                    <td id="account-name">-</td>
                                </tr>
                                <tr>
                                    <th>@lang('messages.account_code')</th>
                                    <td id="account-code">-</td>
                                </tr>
                                <tr>
                                    <th>@lang('messages.account_type')</th>
                                    <td id="account-type">-</td>
                                </tr>
                                <tr>
                                    <th>@lang('messages.account_nature')</th>
                                    <td id="account-nature">-</td>
                                </tr>
                                <tr>
                                    <th>@lang('messages.currency')</th>
                                    <td id="account-currency">-</td>
                                </tr>
                                <tr>
                                    <th>@lang('messages.is_cash_box')</th>
                                    <td id="account-is-cash-box">-</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer bg-light" id="account-actions" style="display: none;">
                        <a href="#" id="view-account-link" class="btn btn-sm btn-info">
                            <i class="fas fa-eye"></i> @lang('messages.view_details')
                        </a>
                        <a href="#" id="edit-account-link" class="btn btn-sm btn-primary">
                            <i class="fas fa-edit"></i> @lang('messages.edit')
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card card-warning card-outline shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-pie mr-1"></i> @lang('messages.account_structure')
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-box bg-gradient-success">
                                    <span class="info-box-icon"><i class="fas fa-money-bill-wave"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">@lang('messages.type_asset')</span>
                                        <span class="info-box-number" id="asset-accounts-count">-</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-box bg-gradient-danger">
                                    <span class="info-box-icon"><i class="fas fa-file-invoice-dollar"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">@lang('messages.type_liability')</span>
                                        <span class="info-box-number" id="liability-accounts-count">-</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-box bg-gradient-warning">
                                    <span class="info-box-icon"><i class="fas fa-chart-line"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">@lang('messages.type_revenue')</span>
                                        <span class="info-box-number" id="revenue-accounts-count">-</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-box bg-gradient-info">
                                    <span class="info-box-icon"><i class="fas fa-shopping-cart"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">@lang('messages.type_expense')</span>
                                        <span class="info-box-number" id="expense-accounts-count">-</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-center mt-3">
                            <a href="{{ route('accounts.index') }}" class="btn btn-primary">
                                <i class="fas fa-list"></i> @lang('messages.manage_accounts')
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- jsTree CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.12/themes/default/style.min.css" />

<style>
.account-tree-container {
    max-height: 600px;
    overflow-y: auto;
    border: 1px solid #dee2e6;
    border-radius: 5px;
    padding: 15px;
    background-color: #fdfdfd;
}

.custom-jstree {
    direction: {{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }};
    font-size: 16px;
}

.jstree-default .jstree-anchor {
    font-weight: 500;
    border-radius: 4px;
    transition: all 0.2s;
}

.jstree-default .jstree-hovered {
    background-color: #e9f5ff;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.1);
}

.jstree-default .jstree-clicked {
    background-color: #ddf4ff;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

/* Customize icons by account type */
.account-asset > .jstree-anchor > .jstree-themeicon {
    color: #28a745 !important;
}

.account-liability > .jstree-anchor > .jstree-themeicon {
    color: #dc3545 !important;
}

.account-equity > .jstree-anchor > .jstree-themeicon {
    color: #6f42c1 !important;
}

.account-revenue > .jstree-anchor > .jstree-themeicon {
    color: #ffc107 !important;
}

.account-expense > .jstree-anchor > .jstree-themeicon {
    color: #17a2b8 !important;
}

/* Filter buttons */
.account-type-filter .btn {
    border-radius: 20px;
    padding: 5px 12px;
}

.account-type-filter .btn.active {
    font-weight: bold;
}

/* Animation for selected account info */
.bg-light-pulse {
    animation: bgPulse 1s ease-in-out;
}

@keyframes bgPulse {
    0% { background-color: #ffffff; }
    50% { background-color: #e1f5fe; }
    100% { background-color: #ffffff; }
}

.node-highlight {
    animation: nodeHighlight 1s ease-in-out;
}

@keyframes nodeHighlight {
    0% { background-color: #ddf4ff; }
    50% { background-color: #aed7ff; }
    100% { background-color: #ddf4ff; }
}

/* Fix table appearance */
#account-details {
    font-size: 15px;
}

#account-details th {
    background-color: #f8f9fa;
}

#account-details td {
    vertical-align: middle;
}

/* Make table rows look better on hover */
#account-details tr:hover {
    background-color: #f8f9fa;
}

/* Custom tooltip to help users */
.hover-help {
    position: relative;
    cursor: help;
}

.hover-help:after {
    content: '?';
    display: inline-block;
    width: 16px;
    height: 16px;
    line-height: 16px;
    text-align: center;
    font-size: 12px;
    font-weight: bold;
    border-radius: 50%;
    background-color: #f8f9fa;
    color: #6c757d;
    margin-left: 5px;
}

.hover-help:hover:before {
    content: attr(data-help);
    position: absolute;
    bottom: 100%;
    left: 0;
    background-color: #343a40;
    color: white;
    text-align: center;
    border-radius: 6px;
    padding: 5px 10px;
    width: 220px;
    font-size: 12px;
    z-index: 1;
}

.instruction-badge {
    font-size: 12px;
    padding: 5px 10px;
    background-color: #f8f9fa;
    border: 1px dashed #dee2e6;
}

.alert-info {
    border-left: 4px solid #17a2b8;
}

/* Pulsating animation for instruction badge */
@keyframes pulsate {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

.instruction-badge {
    animation: pulsate 2s infinite;
}
</style>

@endsection

@push('scripts')
<!-- jQuery & jsTree JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.12/jstree.min.js"></script>

<script>
    // Original accounts data from backend
    const accountsData = @json($accounts);
    
    // Counter for different account types
    let accountCounts = {
        asset: 0,
        liability: 0,
        equity: 0,
        revenue: 0,
        expense: 0,
        total: 0
    };
    
    // Count different account types from the data
    function countAccountTypes(nodes) {
        nodes.forEach(node => {
            accountCounts.total++;
            
            if (node.type) {
                accountCounts[node.type]++;
            }
            
            if (node.children_recursive && node.children_recursive.length > 0) {
                countAccountTypes(node.children_recursive);
            }
        });
    }
    
    // Convert account data to jsTree format with added classes for account types
    function buildTree(nodes) {
        return nodes.map(node => {
            // Add CSS class based on account type
            let typeClass = node.type ? `account-${node.type}` : '';
            
            let item = {
                id: node.id,
                text: node.name + (node.code ? ' <span style="color:#888;font-size:12px">['+node.code+']</span>' : ''),
                children: node.children_recursive && node.children_recursive.length > 0 ? buildTree(node.children_recursive) : [],
                icon: node.is_group ? 'fas fa-folder' : 'fas fa-file-alt',
                state: { opened: true },
                a_attr: {
                    class: typeClass,
                    'data-type': node.type || ''
                },
                li_attr: {
                    class: typeClass,
                    'data-type': node.type || ''
                },
                // Store all account data for reference
                data: node
            };
            return item;
        });
    }

    $(function() {
        // Count different types of accounts
        countAccountTypes(accountsData);
        
        // Update account count display
        $('#asset-accounts-count').text(accountCounts.asset);
        $('#liability-accounts-count').text(accountCounts.liability);
        $('#revenue-accounts-count').text(accountCounts.revenue);
        $('#expense-accounts-count').text(accountCounts.expense);
        
        // Initialize the jsTree
        const $tree = $('#accountTree').jstree({
            'core' : {
                'data' : buildTree(accountsData),
                'themes': { 
                    'variant': 'large',
                    'responsive': true 
                },
                'check_callback': true
            },
            'plugins' : [ 'wholerow', 'search', 'types', 'state' ],
            'types' : {
                'default' : { 'icon' : 'fas fa-folder' },
                'file' : { 'icon' : 'fas fa-file-alt' }
            },
            'search': {
                'case_insensitive': true,
                'show_only_matches': true
            },
            'state': {
                'key': 'accounting-chart-state'
            }
        });
        
        // Handle node selection to show account details
        $tree.on('select_node.jstree', function(e, data) {
            const node = data.node.original.data;
            
            if (node) {
                // Update account info panel
                $('#account-name').text(node.name || '-');
                $('#account-code').text(node.code || '-');
                
                // Set account type correctly with appropriate styling
                let accountType = '-';
                let typeClass = 'secondary';
                
                if (node.type) {
                    if (node.type === 'asset') {
                        accountType = '@lang('messages.type_asset')';
                        typeClass = 'success';
                    }
                    else if (node.type === 'liability') {
                        accountType = '@lang('messages.type_liability')';
                        typeClass = 'danger';
                    }
                    else if (node.type === 'equity') {
                        accountType = '@lang('messages.type_equity')';
                        typeClass = 'primary';
                    }
                    else if (node.type === 'revenue') {
                        accountType = '@lang('messages.type_revenue')';
                        typeClass = 'warning';
                    }
                    else if (node.type === 'expense') {
                        accountType = '@lang('messages.type_expense')';
                        typeClass = 'info';
                    }
                    $('#account-type').html(`<span class="badge badge-${typeClass}">${accountType}</span>`);
                } else {
                    $('#account-type').text('-');
                }
                
                // Set account nature with styling
                if (node.nature) {
                    const natureClass = node.nature === 'debit' ? 'info' : 'warning';
                    const natureText = node.nature === 'debit' ? '@lang('messages.debit_nature')' : '@lang('messages.credit_nature')';
                    $('#account-nature').html(`<span class="badge badge-${natureClass}">${natureText}</span>`);
                } else {
                    $('#account-nature').text('-');
                }
                
                // Set currency and cash box status
                $('#account-currency').text(node.currency || '-');
                
                if (node.is_cash_box !== undefined) {
                    const cashBoxClass = node.is_cash_box ? 'success' : 'secondary';
                    const cashBoxText = node.is_cash_box ? '@lang('messages.yes')' : '@lang('messages.no')';
                    $('#account-is-cash-box').html(`<span class="badge badge-${cashBoxClass}">${cashBoxText}</span>`);
                } else {
                    $('#account-is-cash-box').text('-');
                }
                
                // Add animations to highlight the changes
                $('#account-details tr').addClass('bg-light-pulse');
                setTimeout(() => {
                    $('#account-details tr').removeClass('bg-light-pulse');
                }, 1000);
                
                // Show account actions if it's not a group
                if (!node.is_group) {
                    $('#account-actions').show();
                    $('#view-account-link').attr('href', '{{ url("accounts") }}/' + node.id);
                    $('#edit-account-link').attr('href', '{{ url("accounts") }}/' + node.id + '/edit');
                } else {
                    $('#account-actions').hide();
                }
                
                // Highlight the selected node visually
                $('.jstree-clicked').addClass('node-highlight');
                setTimeout(() => {
                    $('.jstree-clicked').removeClass('node-highlight');
                }, 1000);
            }
        });
        
        // Search functionality
        let searchTimeout;
        $('#account-search').keyup(function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                const searchText = $('#account-search').val();
                $tree.jstree('search', searchText);
            }, 250);
        });
        
        // Filter by account type
        $('.account-type-filter button').click(function() {
            const type = $(this).data('type');
            
            // Update active button
            $('.account-type-filter button').removeClass('active');
            $(this).addClass('active');
            
            if (type === 'all') {
                // Show all nodes
                $tree.jstree('show_all');
            } else {
                // Search by attribute
                $('#accountTree li').hide();
                $('#accountTree li[data-type="' + type + '"]').show();
                $('#accountTree li').each(function() {
                    if ($(this).find('li[data-type="' + type + '"]').length) {
                        $(this).show();
                    }
                });
            }
        });
        
        // Expand all nodes
        $('#expand-all').click(function() {
            $tree.jstree('open_all');
        });
        
        // Collapse all nodes
        $('#collapse-all').click(function() {
            $tree.jstree('close_all');
        });
    });
</script>
@endpush
