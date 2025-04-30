<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>نظام الحسابات</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">
    <!-- AdminLTE -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <!-- Bootstrap RTL -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.rtl.min.css">

    <style>
        body {
            font-family: 'Tajawal', sans-serif;
            direction: rtl;
            text-align: right;
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

@auth
<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav mr-auto">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
        </li>
    </ul>
    <ul class="navbar-nav ml-auto">
        <li class="nav-item">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-danger btn-sm">تسجيل خروج</button>
            </form>
        </li>
    </ul>
</nav>

<!-- Sidebar -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="{{ route('dashboard') }}" class="brand-link text-center">
        <span class="brand-text font-weight-light">نظام الحسابات</span>
    </a>

    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" role="menu" data-accordion="false">

                <li class="nav-header">لوحة التحكم</li>
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link">
                        <i class="nav-icon fas fa-home"></i>
                        <p>الرئيسية</p>
                    </a>
                </li>

                <li class="nav-header">الحسابات</li>
                <li class="nav-item">
                    <a href="{{ route('accounts.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-folder"></i>
                        <p>عرض الفئات</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('accounts.real') }}" class="nav-link">
                        <i class="nav-icon fas fa-list"></i>
                        <p>عرض الحسابات الفعلية</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('accounts.createGroup') }}" class="nav-link">
                        <i class="nav-icon fas fa-folder-plus"></i>
                        <p>إضافة فئة جديدة</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('accounts.createAccount') }}" class="nav-link">
                        <i class="nav-icon fas fa-plus-circle"></i>
                        <p>إضافة حساب فعلي</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('accounts.chart') }}" class="nav-link">
                        <i class="nav-icon fas fa-sitemap"></i>
                        <p>دليل الحسابات</p>
                    </a>
                </li>

                <li class="nav-header">السندات المالية</li>
                <li class="nav-item">
                    <a href="{{ route('vouchers.index', ['type' => 'receipt']) }}" class="nav-link">
                        <i class="nav-icon fas fa-file-invoice"></i>
                        <p>سندات القبض</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('vouchers.index', ['type' => 'payment']) }}" class="nav-link">
                        <i class="nav-icon fas fa-file-invoice-dollar"></i>
                        <p>سندات الصرف</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('vouchers.index', ['type' => 'transfer']) }}" class="nav-link">
                        <i class="nav-icon fas fa-exchange-alt"></i>
                        <p>سندات التحويل</p>
                    </a>
                </li>

                <li class="nav-header">الحركات المالية</li>
                <li class="nav-item">
                    <a href="{{ route('transactions.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-random"></i>
                        <p>إدارة الحركات</p>
                    </a>
                </li>

            </ul>
        </nav>
    </div>
</aside>
@endauth

<!-- Content Wrapper -->
<div class="content-wrapper">
    @yield('content')
</div>

<!-- Footer -->
<footer class="main-footer text-center">
    <strong>نظام الحسابات © {{ date('Y') }}</strong>
</footer>

</div>

<!-- AdminLTE Scripts -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>
