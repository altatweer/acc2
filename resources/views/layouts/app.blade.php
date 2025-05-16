<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>@lang('messages.accounting_system')</title>
    <link rel="icon" href="{{ asset('assets/logo.png') }}" type="image/png">
    <!-- Google Fonts -->
    @if(app()->getLocale() == 'ar')
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
    @else
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    @endif
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">
    <!-- AdminLTE -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <!-- RTL support (only for Arabic) -->
    @if(app()->getLocale() == 'ar')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-rtl@4.6.0-1/dist/css/bootstrap-rtl.min.css">
    @endif

    <!-- ملف التنسيق الموحد المخصص -->
    <link rel="stylesheet" href="{{ asset('resources/css/custom.css') }}">

    <style>
        body {
            font-family: {{ app()->getLocale() == 'ar' ? "'Tajawal'" : "'Roboto'" }}, sans-serif;
            direction: {{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }};
            text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }};
            background: linear-gradient(135deg, #f8fafc 0%, #e0e7ef 100%);
            min-height: 100vh;
        }
        .main-header {
            background: linear-gradient(90deg, #007bff 0%, #0056b3 100%);
            color: #fff !important;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }
        .main-header .navbar-nav .nav-link, .main-header .navbar-nav .nav-link i {
            color: #fff !important;
        }
        .main-sidebar {
            background: #181f2a;
            width: 260px !important;
            border-right: 1px solid #232b3b;
            transition: width 0.2s;
        }
        .brand-link {
            background: #fff;
            border-bottom: 1px solid #eee;
            padding: 1.2rem 1rem;
        }
        .brand-link img {
            height: 40px;
            margin-bottom: 5px;
        }
        .brand-text {
            font-weight: bold;
            color: #007bff;
            font-size: 1.3rem;
        }
        .sidebar {
            padding-top: 1.2rem;
        }
        .nav-sidebar .nav-link {
            color: #b8c2cc;
            font-size: 0.96rem;
            padding: 0.48rem 1rem;
            margin-bottom: 3px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            transition: background 0.18s, color 0.18s, box-shadow 0.18s;
            font-family: 'Roboto', 'Tajawal', sans-serif;
            font-weight: 500;
            letter-spacing: 0.1px;
            position: relative;
        }
        .nav-sidebar .nav-link i.nav-icon {
            font-size: 1.13rem;
            margin-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 0.7rem;
            color: #6ea8fe !important;
            background: transparent;
            border-radius: 50%;
            min-width: 26px;
            min-height: 26px;
            text-align: center;
            transition: background 0.18s, color 0.18s;
        }
        .nav-sidebar .nav-link.active {
            background: linear-gradient(90deg, #1e3a8a 0%, #0a2540 100%);
            color: #fff !important;
            font-weight: bold;
            box-shadow: 0 2px 12px #1e3a8a22;
        }
        .nav-sidebar .nav-link.active i.nav-icon {
            color: #fff !important;
        }
        .nav-sidebar .nav-link:hover {
            background: #232b3b;
            color: #6ea8fe;
            box-shadow: 0 2px 8px #1e3a8a11;
        }
        .nav-sidebar .nav-link:hover i.nav-icon {
            color: #6ea8fe !important;
        }
        /* فاصل لوني واضح بين الأقسام */
        .nav-section-divider {
            border-top: 1.5px solid #232b3b;
            margin: 0.7rem 0 0.7rem 0;
        }
        /* توحيد لون أيقونات العناصر السفلية (التقارير) */
        .nav-sidebar .nav-link .fa-balance-scale,
        .nav-sidebar .nav-link .fa-file-invoice-dollar,
        .nav-sidebar .nav-link .fa-chart-line,
        .nav-sidebar .nav-link .fa-money-check-alt,
        .nav-sidebar .nav-link .fa-receipt {
            color: #6ea8fe !important;
        }
        .nav-header {
            color: #6ea8fe;
            font-size: 0.89rem;
            font-weight: 600;
            margin: 1.1rem 0 0.3rem 0;
            letter-spacing: 0.3px;
            padding-left: 0.7rem;
            padding-right: 0.7rem;
            border-bottom: 1px solid #232b3b;
            padding-bottom: 0.3rem;
        }
        .nav-sidebar .nav-item {
            border-bottom: none;
        }
        .sidebar-collapse .main-sidebar { width: 60px !important; }
        .sidebar-collapse .nav-sidebar .nav-link span, .sidebar-collapse .nav-header { display: none !important; }
        .sidebar-collapse .nav-sidebar .nav-link i.nav-icon {
            margin: 0 auto;
            display: block;
        }
        .sidebar-collapse .nav-sidebar .nav-link {
            justify-content: center;
        }
        @media (max-width: 900px) {
            .main-sidebar { width: 60px !important; }
            .nav-sidebar .nav-link span, .nav-header { display: none !important; }
            .nav-sidebar .nav-link i.nav-icon {
                margin: 0 auto;
                display: block;
            }
            .nav-sidebar .nav-link {
                justify-content: center;
            }
        }
        /* Tooltip عند التصغير */
        .sidebar-collapse .nav-sidebar .nav-link[title]:hover:after {
            content: attr(title);
            position: absolute;
            left: 60px;
            top: 50%;
            transform: translateY(-50%);
            background: #232b3b;
            color: #fff;
            padding: 4px 12px;
            border-radius: 6px;
            font-size: 0.95rem;
            white-space: nowrap;
            z-index: 9999;
            box-shadow: 0 2px 8px #0002;
        }
        .main-footer {
            background: #fff;
            border-top: 1px solid #eee;
            color: #888;
            font-size: 1rem;
            padding: 1rem 0;
        }
        .content-wrapper {
            background: #f8fafc;
            min-height: 100vh;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }
        .btn-primary, .btn-success, .btn-info {
            border-radius: 6px;
            font-weight: bold;
        }
        .btn-primary {
            background: linear-gradient(90deg, #007bff 0%, #0056b3 100%);
            border: none;
        }
        .btn-success {
            background: linear-gradient(90deg, #28a745 0%, #218838 100%);
            border: none;
        }
        .btn-info {
            background: linear-gradient(90deg, #17a2b8 0%, #117a8b 100%);
            border: none;
        }
        
        @if(app()->getLocale() == 'ar')
        /* RTL specific styles */
        .sidebar { right: 0; left: auto; }
        .main-sidebar { right: 0; left: auto; }
        .content-wrapper, .main-footer {
            margin-right: 260px;
            margin-left: 0;
        }
        .main-header {
            margin: 0 !important;
        }
        body, .wrapper, .content-wrapper {
            padding-left: 0 !important;
            padding-right: 0 !important;
            margin-left: 0 !important;
        }
        @else
        /* LTR specific styles */
        .sidebar { left: 0; right: auto; }
        .main-sidebar { left: 0; right: auto; }
        .content-wrapper, .main-footer {
            margin-left: 260px;
            margin-right: 0;
        }
        .main-header {
            margin: 0 !important;
        }
        @endif
        /* تخصيص صفحة تسجيل الدخول فقط */
        @if (request()->routeIs('login'))
        .wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0a2540 0%, #1e3a8a 100%) !important;
        }
        @endif
        /* توحيد لون جميع الأيقونات في الـ Sidebar */
        .nav-sidebar .nav-link i {
            color: #6ea8fe !important;
            transition: color 0.18s;
        }
        .nav-sidebar .nav-link.active i {
            color: #fff !important;
        }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

@php
    $isSuperAdmin = auth()->check() && auth()->user()->isSuperAdmin();
    use App\Models\Setting;
    $systemName = Setting::get('system_name', 'AurSuite');
    $companyLogo = Setting::get('company_logo', '');
    $companyName = Setting::get('company_name', '');
@endphp

@auth
<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav mr-auto">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
        </li>
    </ul>
    {{-- Logout form and link --}}
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
        @csrf
    </form>
    <ul class="navbar-nav ml-auto">
        {{-- حذف زر تبديل اللغة بالكامل --}}
        <li class="nav-item">
            <a href="#" class="nav-link text-danger" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt"></i> @lang('sidebar.logout')
            </a>
        </li>
    </ul>
</nav>

<!-- Sidebar -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="{{ route('dashboard') }}" class="brand-link text-center">
        <span class="brand-text font-weight-light">{{ $systemName }}</span>
    </a>

    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

                <li class="nav-header">@lang('sidebar.dashboard')</li>
                @if($isSuperAdmin || auth()->user()->can('view_dashboard'))
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ Request::routeIs('dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-home"></i>
                        <p>@lang('sidebar.home')</p>
                    </a>
                </li>
                @endif

                @if($isSuperAdmin || auth()->user()->can('view_accounts'))
                <li class="nav-header">@lang('sidebar.accounts')</li>
                <li class="nav-item has-treeview {{ Request::routeIs('accounts.*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ Request::routeIs('accounts.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-folder-open"></i>
                        <p>
                            @lang('sidebar.accounts_management')
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('accounts.index') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>@lang('sidebar.categories_list')</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('accounts.real') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>@lang('sidebar.accounts_list')</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('accounts.createGroup') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>@lang('sidebar.add_category')</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('accounts.createAccount') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>@lang('sidebar.add_account')</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('accounts.chart') }}" class="nav-link {{ Request::routeIs('accounts.chart') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>@lang('sidebar.chart_of_accounts')</p>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif

                @if($isSuperAdmin || auth()->user()->can('view_vouchers'))
                <li class="nav-header">@lang('sidebar.vouchers')</li>
                <li class="nav-item has-treeview {{ Request::routeIs('vouchers.*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ Request::routeIs('vouchers.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-file-invoice"></i>
                        <p>
                            @lang('sidebar.vouchers_management')
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ Route::localizedRoute('vouchers.index', ['type' => 'receipt']) }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>@lang('sidebar.receipt_vouchers')</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ Route::localizedRoute('vouchers.index', ['type' => 'payment']) }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>@lang('sidebar.payment_vouchers')</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ Route::localizedRoute('vouchers.index', ['type' => 'transfer']) }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>@lang('sidebar.transfer_vouchers')</p>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif

                @if($isSuperAdmin || auth()->user()->can('view_transactions'))
                <li class="nav-header">@lang('sidebar.transactions')</li>
                <li class="nav-item">
                    <a href="{{ route('transactions.index') }}" class="nav-link {{ Request::routeIs('transactions.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-exchange-alt"></i>
                        <p>@lang('sidebar.transactions_management')</p>
                    </a>
                </li>
                @endif

                @if($isSuperAdmin || auth()->user()->can('view_currencies'))
                <li class="nav-header">@lang('sidebar.currencies')</li>
                <li class="nav-item">
                    <a href="{{ route('currencies.index') }}" class="nav-link {{ Request::routeIs('currencies.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-coins"></i>
                        <p>@lang('sidebar.currencies_management')</p>
                    </a>
                </li>
                @endif

                @if($isSuperAdmin || auth()->user()->can('view_invoices'))
                <li class="nav-header">@lang('sidebar.invoices')</li>
                <li class="nav-item has-treeview {{ Request::routeIs('invoices.*') || Request::routeIs('invoice-payments.*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ Request::routeIs('invoices.*') || Request::routeIs('invoice-payments.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-receipt"></i>
                        <p>
                            @lang('sidebar.invoices_management')
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('invoices.index') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>@lang('sidebar.invoices_list')</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('invoices.create') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>@lang('sidebar.new_invoice')</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('invoice-payments.create') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>@lang('sidebar.pay_invoice')</p>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif

                @if($isSuperAdmin || auth()->user()->can('view_customers'))
                <li class="nav-header">@lang('sidebar.customers')</li>
                <li class="nav-item has-treeview {{ Request::routeIs('customers.*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ Request::routeIs('customers.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-users"></i>
                        <p>@lang('sidebar.customers_management')<i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('customers.index') }}" class="nav-link {{ Request::routeIs('customers.index') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>@lang('sidebar.customers_list')</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('customers.create') }}" class="nav-link {{ Request::routeIs('customers.create') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>@lang('sidebar.new_customer')</p>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif

                @if($isSuperAdmin || auth()->user()->can('view_items'))
                <li class="nav-header">@lang('sidebar.items')</li>
                <li class="nav-item has-treeview {{ Request::routeIs('items.*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ Request::routeIs('items.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-box-open"></i>
                        <p>@lang('sidebar.items_management')<i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('items.index') }}" class="nav-link {{ Request::routeIs('items.index') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>@lang('sidebar.items_list')</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('items.create') }}" class="nav-link {{ Request::routeIs('items.create') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>@lang('sidebar.new_item')</p>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif

                @if($isSuperAdmin || auth()->user()->can('view_journal_entries'))
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('journal-entries.index') }}">
                        <i class="fas fa-book"></i>
                        <span>@lang('sidebar.accounting_entries')</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('ledger.index') }}">
                        <i class="fas fa-book-open"></i>
                        <span>@lang('sidebar.ledger')</span>
                    </a>
                </li>
                @endif

                @if($isSuperAdmin || auth()->user()->can('view_salary_payments'))
                <li class="nav-item">
                    <a href="{{ route('salary-payments.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-money-check-alt"></i>
                        <p>@lang('sidebar.salary_payments')</p>
                    </a>
                </li>
                @endif

                @if($isSuperAdmin || auth()->user()->can('view_employees'))
                <li class="nav-header">@lang('sidebar.hr')</li>
                <li class="nav-item">
                    <a href="{{ route('employees.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-user-tie"></i>
                        <p>@lang('sidebar.employees')</p>
                    </a>
                </li>
                @endif
                @if($isSuperAdmin || auth()->user()->can('view_salaries'))
                <li class="nav-item">
                    <a href="{{ route('salaries.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-money-bill-wave"></i>
                        <p>@lang('sidebar.salaries')</p>
                    </a>
                </li>
                @endif
                @if($isSuperAdmin || auth()->user()->can('view_salary_payments'))
                <li class="nav-item">
                    <a href="{{ route('salary-payments.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-money-check-alt"></i>
                        <p>@lang('sidebar.salary_payments')</p>
                    </a>
                </li>
                @endif
                @if($isSuperAdmin || auth()->user()->can('view_salary_batches'))
                <li class="nav-item">
                    <a href="{{ route('salary-batches.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-file-invoice-dollar"></i>
                        <p>@lang('sidebar.salary_sheets')</p>
                    </a>
                </li>
                @endif

                @if($isSuperAdmin || auth()->user()->can('view_roles'))
                <li class="nav-header">@lang('sidebar.system_settings')</li>
                <li class="nav-item">
                    <a href="{{ route('admin.roles.index') }}" class="nav-link {{ Request::routeIs('admin.roles.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user-shield"></i>
                        <p>@lang('sidebar.roles')</p>
                    </a>
                </li>
                @endif
                @if($isSuperAdmin || auth()->user()->can('view_permissions'))
                <li class="nav-item">
                    <a href="{{ route('admin.permissions.index') }}" class="nav-link {{ Request::routeIs('admin.permissions.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-key"></i>
                        <p>@lang('sidebar.permissions')</p>
                    </a>
                </li>
                @endif
                @if($isSuperAdmin || auth()->user()->can('view_user_roles'))
                <li class="nav-item">
                    <a href="{{ route('admin.user-roles.index') }}" class="nav-link {{ Request::routeIs('admin.user-roles.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-users-cog"></i>
                        <p>@lang('sidebar.user_roles')</p>
                    </a>
                </li>
                @endif
                @if($isSuperAdmin || auth()->user()->can('view_users'))
                <li class="nav-item">
                    <a href="{{ route('admin.users.index') }}" class="nav-link {{ Request::routeIs('admin.users.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-users"></i>
                        <p>@lang('sidebar.users')</p>
                    </a>
                </li>
                @endif
                @if($isSuperAdmin || auth()->user()->can('manage_system_settings'))
                <li class="nav-item">
                    <a href="{{ route('accounting-settings.edit') }}" class="nav-link {{ Request::routeIs('accounting-settings.edit') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-cogs"></i>
                        <p>@lang('sidebar.accounting_settings')</p>
                    </a>
                </li>
                @endif
                @if($isSuperAdmin || auth()->user()->can('عرض الصلاحيات'))
                <li class="nav-item">
                    <a href="{{ route('settings.system.edit') }}" class="nav-link {{ Request::routeIs('settings.system.edit') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-cog"></i>
                        <p>@lang('sidebar.system_settings_page')</p>
                    </a>
                </li>
                @endif
                @if($isSuperAdmin)
                <li class="nav-item">
                    <a href="{{ route('languages.index') }}" class="nav-link {{ Request::routeIs('languages.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-language"></i>
                        <p>@lang('sidebar.languages_management')</p>
                    </a>
                </li>
                @endif

                @if($isSuperAdmin || auth()->user()->can('view_reports'))
                <li class="nav-header">@lang('sidebar.reports')</li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('reports.trial-balance') }}">
                        <i class="fas fa-balance-scale"></i>
                        <span>@lang('sidebar.trial_balance')</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('reports.balance-sheet') }}">
                        <i class="fas fa-file-invoice-dollar"></i>
                        <span>@lang('sidebar.balance_sheet')</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('reports.income-statement') }}">
                        <i class="fas fa-chart-line"></i>
                        <span>@lang('sidebar.income_statement')</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('reports.payroll') }}">
                        <i class="fas fa-money-check-alt"></i>
                        <span>@lang('sidebar.payroll_report')</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('reports.expenses-revenues') }}">
                        <i class="fas fa-receipt"></i>
                        <span>@lang('sidebar.expenses_revenues')</span>
                    </a>
                </li>
                @endif

            </ul>
        </nav>
    </div>
</aside>
@endauth

<!-- Content Wrapper -->
<div class="content-wrapper">
    <section class="content">
        <div class="container-fluid pt-3">
            @yield('content')
        </div>
    </section>
</div>

<!-- Footer -->
@if (!request()->routeIs('login'))
<footer class="main-footer text-center">
    <strong>{{ $systemName }} © {{ date('Y') }}</strong> | <span>{{ $companyName }}</span>
</footer>
@endif

</div>

<!-- AdminLTE Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
@stack('scripts')
</body>
</html>
