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
            background: linear-gradient(180deg, #23272b 0%, #343a40 100%);
            box-shadow: 2px 0 8px rgba(0,0,0,0.04);
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
        .nav-sidebar .nav-link.active {
            background: linear-gradient(90deg, #007bff 0%, #0056b3 100%);
            color: #fff !important;
            border-radius: 6px;
        }
        .nav-sidebar .nav-link {
            color: #c2c7d0;
            transition: background 0.2s, color 0.2s;
        }
        .nav-sidebar .nav-link:hover {
            background: #007bff22;
            color: #007bff;
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
        .content-wrapper, .main-footer, .main-header {
            margin-right: 250px;
            margin-left: 0;
        }
        @else
        /* LTR specific styles */
        .sidebar { left: 0; right: auto; }
        .main-sidebar { left: 0; right: auto; }
        .content-wrapper, .main-footer, .main-header {
            margin-left: 250px;
            margin-right: 0;
        }
        @endif
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

@php
    $isSuperAdmin = auth()->check() && auth()->user()->isSuperAdmin();
    use App\Models\Setting;
    $systemName = Setting::get('system_name', 'نظام الحسابات');
    $companyLogo = Setting::get('company_logo');
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
        <!-- Language Switcher -->
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="languageDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                {{ App::getLocale() == 'ar' ? 'العربية' : 'English' }}
            </a>
            <div class="dropdown-menu" aria-labelledby="languageDropdown">
                <a class="dropdown-item" href="{{ url('/language/ar') }}">العربية</a>
                <a class="dropdown-item" href="{{ url('/language/en') }}">English</a>
            </div>
        </li>
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
        @if($companyLogo)
            <img src="{{ asset('storage/'.$companyLogo) }}" alt="Logo" class="mb-2">
        @else
            <img src="{{ asset('assets/logo.png') }}" alt="Logo" class="mb-2">
        @endif
        <span class="brand-text font-weight-light">{{ $systemName }}</span>
    </a>

    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

                <li class="nav-header">@lang('sidebar.dashboard')</li>
                @if($isSuperAdmin || auth()->user()->can('عرض لوحة التحكم'))
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ Request::routeIs('dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-home"></i>
                        <p>@lang('sidebar.home')</p>
                    </a>
                </li>
                @endif

                @if($isSuperAdmin || auth()->user()->can('عرض الحسابات'))
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

                @if($isSuperAdmin || auth()->user()->can('عرض السندات'))
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

                @if($isSuperAdmin || auth()->user()->can('عرض الحركات المالية'))
                <li class="nav-header">@lang('sidebar.transactions')</li>
                <li class="nav-item">
                    <a href="{{ route('transactions.index') }}" class="nav-link {{ Request::routeIs('transactions.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-exchange-alt"></i>
                        <p>@lang('sidebar.transactions_management')</p>
                    </a>
                </li>
                @endif

                @if($isSuperAdmin || auth()->user()->can('عرض العملات'))
                <li class="nav-header">@lang('sidebar.currencies')</li>
                <li class="nav-item">
                    <a href="{{ route('currencies.index') }}" class="nav-link {{ Request::routeIs('currencies.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-coins"></i>
                        <p>@lang('sidebar.currencies_management')</p>
                    </a>
                </li>
                @endif

                @if($isSuperAdmin || auth()->user()->can('عرض الفواتير'))
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

                @if($isSuperAdmin || auth()->user()->can('عرض العملاء'))
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

                @if($isSuperAdmin || auth()->user()->can('عرض العناصر'))
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

                @if($isSuperAdmin || auth()->user()->can('عرض القيود المحاسبية'))
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

                @if($isSuperAdmin || auth()->user()->can('عرض دفعات الرواتب'))
                <li class="nav-item">
                    <a href="{{ route('salary-payments.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-money-check-alt"></i>
                        <p>@lang('sidebar.salary_payments')</p>
                    </a>
                </li>
                @endif

                @if($isSuperAdmin || auth()->user()->can('عرض الموظفين'))
                <li class="nav-header">@lang('sidebar.hr')</li>
                <li class="nav-item">
                    <a href="{{ route('employees.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-user-tie"></i>
                        <p>@lang('sidebar.employees')</p>
                    </a>
                </li>
                @endif
                @if($isSuperAdmin || auth()->user()->can('عرض الرواتب'))
                <li class="nav-item">
                    <a href="{{ route('salaries.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-money-bill-wave"></i>
                        <p>@lang('sidebar.salaries')</p>
                    </a>
                </li>
                @endif
                @if($isSuperAdmin || auth()->user()->can('عرض دفعات الرواتب'))
                <li class="nav-item">
                    <a href="{{ route('salary-payments.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-money-check-alt"></i>
                        <p>@lang('sidebar.salary_payments')</p>
                    </a>
                </li>
                @endif
                @if($isSuperAdmin || auth()->user()->can('عرض كشوف الرواتب'))
                <li class="nav-item">
                    <a href="{{ route('salary-batches.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-file-invoice-dollar"></i>
                        <p>@lang('sidebar.salary_sheets')</p>
                    </a>
                </li>
                @endif

                @if($isSuperAdmin || auth()->user()->can('عرض الأدوار'))
                <li class="nav-header">@lang('sidebar.system_settings')</li>
                <li class="nav-item">
                    <a href="{{ route('admin.roles.index') }}" class="nav-link {{ Request::routeIs('admin.roles.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user-shield"></i>
                        <p>@lang('sidebar.roles')</p>
                    </a>
                </li>
                @endif
                @if($isSuperAdmin || auth()->user()->can('عرض الصلاحيات'))
                <li class="nav-item">
                    <a href="{{ route('admin.permissions.index') }}" class="nav-link {{ Request::routeIs('admin.permissions.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-key"></i>
                        <p>@lang('sidebar.permissions')</p>
                    </a>
                </li>
                @endif
                @if($isSuperAdmin || auth()->user()->can('عرض أدوار المستخدمين'))
                <li class="nav-item">
                    <a href="{{ route('admin.user-roles.index') }}" class="nav-link {{ Request::routeIs('admin.user-roles.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-users-cog"></i>
                        <p>@lang('sidebar.user_roles')</p>
                    </a>
                </li>
                @endif
                @if($isSuperAdmin || auth()->user()->can('عرض المستخدمين'))
                <li class="nav-item">
                    <a href="{{ route('admin.users.index') }}" class="nav-link {{ Request::routeIs('admin.users.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-users"></i>
                        <p>@lang('sidebar.users')</p>
                    </a>
                </li>
                @endif
                @if($isSuperAdmin || auth()->user()->can('إدارة إعدادات النظام'))
                <li class="nav-item">
                    <a href="{{ route('accounting-settings.edit') }}" class="nav-link {{ Request::routeIs('accounting-settings.edit') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-cogs"></i>
                        <p>@lang('sidebar.accounting_settings')</p>
                    </a>
                </li>
                @endif
                @if($isSuperAdmin || auth()->user()->can('إدارة إعدادات النظام'))
                <li class="nav-item">
                    <a href="{{ route('settings.system.edit') }}" class="nav-link {{ Request::routeIs('settings.system.edit') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-cog"></i>
                        <p>@lang('sidebar.system_settings_page')</p>
                    </a>
                </li>
                @endif

                @if($isSuperAdmin || auth()->user()->can('عرض التقارير'))
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
<footer class="main-footer text-center">
    <strong>{{ $systemName }} © {{ date('Y') }}</strong> | <span>{{ $companyName }}</span>
</footer>

</div>

<!-- AdminLTE Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
@stack('scripts')
</body>
</html>
