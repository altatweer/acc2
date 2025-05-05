<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>نظام الحسابات</title>
    <link rel="icon" href="{{ asset('assets/logo.png') }}" type="image/png">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">
    <!-- AdminLTE -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <!-- Bootstrap RTL -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-rtl@4.6.0-1/dist/css/bootstrap-rtl.min.css">

    <!-- ملف التنسيق الموحد المخصص -->
    <link rel="stylesheet" href="{{ asset('resources/css/custom.css') }}">

    <style>
        body {
            font-family: 'Tajawal', sans-serif;
            direction: rtl;
            text-align: right;
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
        .sidebar { right: 0; left: auto; }
        .main-sidebar { right: 0; left: auto; }
        .content-wrapper, .main-footer, .main-header {
            margin-right: 250px;
            margin-left: 0;
        }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

@php
    $isSuperAdmin = auth()->check() && auth()->user()->isSuperAdmin();
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
        <li class="nav-item">
            <a href="#" class="nav-link text-danger" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt"></i> تسجيل خروج
            </a>
        </li>
    </ul>
</nav>

<!-- Sidebar -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="{{ route('dashboard') }}" class="brand-link text-center">
        <img src="{{ asset('assets/logo.png') }}" alt="Logo" class="mb-2">
        <span class="brand-text font-weight-light">نظام الحسابات</span>
    </a>

    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

                <li class="nav-header">لوحة التحكم</li>
                @if($isSuperAdmin || auth()->user()->can('عرض لوحة التحكم'))
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ Request::routeIs('dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-home"></i>
                        <p>الرئيسية</p>
                    </a>
                </li>
                @endif

                @if($isSuperAdmin || auth()->user()->can('عرض الحسابات'))
                <li class="nav-header">الحسابات</li>
                <li class="nav-item has-treeview {{ Request::routeIs('accounts.*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ Request::routeIs('accounts.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-folder-open"></i>
                        <p>
                            إدارة الحسابات
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('accounts.index') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>عرض الفئات</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('accounts.real') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>عرض الحسابات</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('accounts.createGroup') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>إضافة فئة جديدة</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('accounts.createAccount') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>إضافة حساب جديد</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('accounts.chart') }}" class="nav-link {{ Request::routeIs('accounts.chart') ? 'active' : '' }}">
                                <i class="fas fa-sitemap nav-icon"></i>
                                <p>شجرة الحسابات</p>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif

                @if($isSuperAdmin || auth()->user()->can('عرض السندات'))
                <li class="nav-header">السندات المالية</li>
                <li class="nav-item has-treeview {{ Request::routeIs('vouchers.*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ Request::routeIs('vouchers.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-file-invoice"></i>
                        <p>
                            إدارة السندات
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('vouchers.index', ['type' => 'receipt']) }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>سندات القبض</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('vouchers.index', ['type' => 'payment']) }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>سندات الصرف</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('vouchers.index', ['type' => 'transfer']) }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>سندات التحويل</p>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif

                @if($isSuperAdmin || auth()->user()->can('عرض الحركات المالية'))
                <li class="nav-header">الحركات المالية</li>
                <li class="nav-item">
                    <a href="{{ route('transactions.index') }}" class="nav-link {{ Request::routeIs('transactions.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-exchange-alt"></i>
                        <p>إدارة الحركات</p>
                    </a>
                </li>
                @endif

                @if($isSuperAdmin || auth()->user()->can('عرض العملات'))
                <li class="nav-header">العملات</li>
                <li class="nav-item">
                    <a href="{{ route('currencies.index') }}" class="nav-link {{ Request::routeIs('currencies.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-coins"></i>
                        <p>إدارة العملات</p>
                    </a>
                </li>
                @endif

                @if($isSuperAdmin || auth()->user()->can('عرض الفواتير'))
                <li class="nav-header">الفواتير</li>
                <li class="nav-item has-treeview {{ Request::routeIs('invoices.*') || Request::routeIs('invoice-payments.*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ Request::routeIs('invoices.*') || Request::routeIs('invoice-payments.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-receipt"></i>
                        <p>
                            إدارة الفواتير
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('invoices.index') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>قائمة الفواتير</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('invoices.create') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>فاتورة جديدة</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('invoice-payments.create') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>سداد فاتورة</p>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif

                @if($isSuperAdmin || auth()->user()->can('عرض العملاء'))
                <li class="nav-header">العملاء</li>
                <li class="nav-item has-treeview {{ Request::routeIs('customers.*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ Request::routeIs('customers.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-users"></i>
                        <p>إدارة العملاء<i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('customers.index') }}" class="nav-link {{ Request::routeIs('customers.index') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>قائمة العملاء</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('customers.create') }}" class="nav-link {{ Request::routeIs('customers.create') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>عميل جديد</p>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif

                @if($isSuperAdmin || auth()->user()->can('عرض العناصر'))
                <li class="nav-header">المنتجات/الخدمات</li>
                <li class="nav-item has-treeview {{ Request::routeIs('items.*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ Request::routeIs('items.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-box-open"></i>
                        <p>إدارة العناصر<i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('items.index') }}" class="nav-link {{ Request::routeIs('items.index') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>قائمة العناصر</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('items.create') }}" class="nav-link {{ Request::routeIs('items.create') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>عنصر جديد</p>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif

                @if($isSuperAdmin || auth()->user()->can('عرض القيود المحاسبية'))
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('journal-entries.index') }}">
                        <i class="fas fa-book"></i>
                        <span>القيود المحاسبية</span>
                    </a>
                </li>
                @endif

                @if($isSuperAdmin || auth()->user()->can('عرض دفعات الرواتب'))
                <li class="nav-item">
                    <a href="{{ route('salary-payments.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-money-check-alt"></i>
                        <p>دفعات الرواتب</p>
                    </a>
                </li>
                @endif

                @if($isSuperAdmin || auth()->user()->can('عرض الموظفين'))
                <li class="nav-header">الموارد البشرية</li>
                <li class="nav-item">
                    <a href="{{ route('employees.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-user-tie"></i>
                        <p>الموظفون</p>
                    </a>
                </li>
                @endif
                @if($isSuperAdmin || auth()->user()->can('عرض الرواتب'))
                <li class="nav-item">
                    <a href="{{ route('salaries.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-money-bill-wave"></i>
                        <p>الرواتب</p>
                    </a>
                </li>
                @endif
                @if($isSuperAdmin || auth()->user()->can('عرض دفعات الرواتب'))
                <li class="nav-item">
                    <a href="{{ route('salary-payments.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-money-check-alt"></i>
                        <p>دفعات الرواتب</p>
                    </a>
                </li>
                @endif
                @if($isSuperAdmin || auth()->user()->can('عرض كشوف الرواتب'))
                <li class="nav-item">
                    <a href="{{ route('salary-batches.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-file-invoice-dollar"></i>
                        <p>كشوف الرواتب</p>
                    </a>
                </li>
                @endif

                @if($isSuperAdmin || auth()->user()->can('عرض الأدوار'))
                <li class="nav-header">إعدادات النظام</li>
                <li class="nav-item">
                    <a href="{{ route('admin.roles.index') }}" class="nav-link {{ Request::routeIs('admin.roles.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user-shield"></i>
                        <p>إدارة الأدوار</p>
                    </a>
                </li>
                @endif
                @if($isSuperAdmin || auth()->user()->can('عرض الصلاحيات'))
                <li class="nav-item">
                    <a href="{{ route('admin.permissions.index') }}" class="nav-link {{ Request::routeIs('admin.permissions.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-key"></i>
                        <p>إدارة الصلاحيات</p>
                    </a>
                </li>
                @endif
                @if($isSuperAdmin || auth()->user()->can('عرض أدوار المستخدمين'))
                <li class="nav-item">
                    <a href="{{ route('admin.user-roles.index') }}" class="nav-link {{ Request::routeIs('admin.user-roles.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-users-cog"></i>
                        <p>أدوار المستخدمين</p>
                    </a>
                </li>
                @endif
                @if($isSuperAdmin || auth()->user()->can('عرض المستخدمين'))
                <li class="nav-item">
                    <a href="{{ route('admin.users.index') }}" class="nav-link {{ Request::routeIs('admin.users.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-users"></i>
                        <p>إدارة المستخدمين</p>
                    </a>
                </li>
                @endif
                @if($isSuperAdmin || auth()->user()->can('إدارة إعدادات النظام'))
                <li class="nav-item">
                    <a href="{{ route('accounting-settings.edit') }}" class="nav-link {{ Request::routeIs('accounting-settings.edit') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-cogs"></i>
                        <p>إعدادات الحسابات</p>
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
    <strong>نظام الحسابات © {{ date('Y') }}</strong> | تصميم وبرمجة <a href="https://yourcompany.com" target="_blank">YourCompany</a>
</footer>

</div>

<!-- AdminLTE Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
@stack('scripts')
</body>
</html>
