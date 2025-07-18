<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@lang('messages.accounting_system')</title>
    <link rel="icon" href="{{ asset('assets/logo.png') }}" type="image/png">
    <!-- Google Fonts -->
    @if(app()->getLocale() == 'ar')
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700&display=swap" rel="stylesheet">
    @else
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @endif
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- AdminLTE -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <!-- RTL support (only for Arabic) -->
    @if(app()->getLocale() == 'ar')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-rtl@4.6.0-1/dist/css/bootstrap-rtl.min.css">
    @endif

    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.5/dist/sweetalert2.min.css">
    
    <!-- Account Currency Display Enhancement -->
    <link rel="stylesheet" href="{{ asset('assets/css/account-currency-display.css') }}">

    <!-- Enhanced Sidebar Styles -->
    <link rel="stylesheet" href="{{ asset('assets/css/enhanced-sidebar.css') }}">
    
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">

    <style>
        /* Dark & Light mode variables */
        :root {
            --primary-color: #3498db;
            --primary-hover: #2980b9;
            --primary-light: #d6eaf8;
            --secondary-color: #2c3e50;
            --success-color: #2ecc71;
            --danger-color: #e74c3c;
            --warning-color: #f39c12;
            --info-color: #3498db;
            --dark-blue: #2c3e50;
            --sidebar-bg: #2c3e50;
            --sidebar-item: #ecf0f1;
            --sidebar-active: #3498db;
            --content-bg: #f5f7fa;
            --card-shadow: rgba(0, 0, 0, 0.08);
            --text-color: #2c3e50;
            --text-muted: #7f8c8d;
            --border-color: #ecf0f1;
            --header-bg: #fff;
        }
        

        
        .dark-mode {
            --primary-color: #3498db;
            --primary-hover: #2980b9;
            --primary-light: #1f618d;
            --secondary-color: #2c3e50;
            --content-bg: #1e272e;
            --text-color: #ecf0f1;
            --text-muted: #bdc3c7;
            --border-color: #34495e;
            --card-shadow: rgba(0, 0, 0, 0.2);
            --header-bg: #2c3e50;
        }
        
        /* Mode toggle button */
        .mode-toggle {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            transition: all 0.3s;
        }
        
        .mode-toggle:hover {
            transform: scale(1.1);
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.2);
        }
        
        .mode-toggle i {
            font-size: 1.3rem;
        }
        
        /* Dark mode specific styles */
        .dark-mode body {
            background: var(--content-bg);
            color: var(--text-color);
        }
        
        .dark-mode .main-header {
            background: var(--header-bg);
            border-bottom: 1px solid var(--border-color);
        }
        
        .dark-mode .content-wrapper {
            background: var(--content-bg);
        }
        
        .dark-mode .card {
            background: #2c3e50;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.15);
        }
        
        .dark-mode .card-header {
            background: #2c3e50;
            border-bottom: 1px solid #34495e;
        }
        
        .dark-mode .table th {
            background: #2c3e50;
            color: #bdc3c7;
            border-bottom: 1px solid #34495e;
        }
        
        .dark-mode .table td {
            border-bottom: 1px solid #34495e;
            color: #ecf0f1;
        }
        
        .dark-mode .table-striped tbody tr:nth-of-type(odd) {
            background-color: #1e272e;
        }
        
        .dark-mode .main-footer {
            background: #2c3e50;
            border-top: 1px solid #34495e;
            color: #bdc3c7;
        }
        
        .dark-mode .navbar-light .navbar-nav .nav-link {
            color: #e2e8f0;
        }
        
        .dark-mode .navbar-light .navbar-nav .nav-link:hover {
            color: var(--primary-color);
        }
        
        .dark-mode .form-control {
            background-color: #1e293b;
            border-color: #334155;
            color: #e2e8f0;
        }
        
        .dark-mode .form-control:focus {
            border-color: var(--primary-color);
            background-color: #1e293b;
        }
        
        body {
            font-family: {{ app()->getLocale() == 'ar' ? "'Tajawal'" : "'Poppins'" }}, sans-serif;
            direction: {{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }};
            text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }};
            background: var(--content-bg);
            min-height: 100vh;
            overflow-x: hidden;
            margin: 0;
            padding: 0;
            font-size: {{ app()->getLocale() == 'ar' ? '0.92rem' : '0.85rem' }};
            @if(app()->getLocale() == 'ar')
            text-rendering: optimizeLegibility;
            -webkit-font-smoothing: antialiased;
            @endif
        }
        
        /* Wrapper & container fixes */
        .wrapper {
            width: 100%;
            overflow-x: hidden;
        }
        
        .container-fluid {
            width: 100%;
            padding-right: 15px;
            padding-left: 15px;
            margin-right: auto;
            margin-left: auto;
        }
        
        .main-header {
            background: #fff;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            border: none;
            width: 100%;
        }
        
        .navbar-light .navbar-nav .nav-link {
            color: var(--dark-blue);
            font-weight: 500;
            transition: color 0.2s;
        }
        
        .navbar-light .navbar-nav .nav-link:hover {
            color: var(--primary-color);
        }
        
        /* Logo area styling */
        .brand-link {
            display: flex;
            align-items: center;
            justify-content: center;
            background: #1a252f;
            color: #ffffff !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
            height: 65px;
            padding: 0.8rem 1rem;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }
        
        .brand-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(52, 152, 219, 0.2) 0%, rgba(41, 128, 185, 0.2) 100%);
            opacity: 0;
            transition: opacity 0.3s;
        }
        
        .brand-link:hover {
            background: #1c2833;
        }
        
        .brand-link:hover::before {
            opacity: 1;
        }
        
        .brand-image {
            height: 42px;
            width: auto;
            margin-right: 0.7rem;
            transition: transform 0.3s;
            filter: brightness(1.1);
        }
        
        .brand-link:hover .brand-image {
            transform: scale(1.05);
        }
        
        .brand-text {
            font-weight: 700;
            color: #ffffff;
            font-size: 1.25rem;
            white-space: nowrap;
            letter-spacing: 0.5px;
            text-shadow: 0 1px 2px rgba(0,0,0,0.2);
        }
        
        /* User profile in sidebar */
        .user-profile {
            padding: 1.2rem 1rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            color: #ffffff;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            background: rgba(0, 0, 0, 0.15);
        }
        
        .user-profile-content {
            display: flex;
            align-items: center;
        }

        .user-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: #3498db;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.3rem;
            margin-right: 0.8rem;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.3);
        }
        
        .user-info {
            flex: 1;
            overflow: hidden;
        }
        
        .user-name {
            font-weight: 600;
            font-size: 1rem;
            margin-bottom: 0.3rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            color: #ecf0f1;
        }
        
        .user-role {
            font-size: 0.8rem;
            color: rgba(255, 255, 255, 0.7);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .user-status {
            display: flex;
            align-items: center;
            margin-top: 0.5rem;
        }

        .status-indicator {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background-color: var(--success-color);
            margin-right: 0.5rem;
        }

        .status-text {
            font-size: 0.75rem;
            color: rgba(255, 255, 255, 0.8);
        }
        
        /* Main sidebar styling */
        .main-sidebar {
            background: linear-gradient(180deg, #2c3e50 0%, #34495e 100%);
            box-shadow: 0 0 30px rgba(0, 0, 0, 0.2);
        }
        
        .brand-link {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%) !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }
        
        .brand-link:hover {
            background: linear-gradient(135deg, #2980b9 0%, #3498db 100%) !important;
            transform: scale(1.02);
        }
        
        .user-profile {
            background: rgba(0, 0, 0, 0.2);
            border-radius: 0 0 15px 15px;
            margin: 10px;
            padding: 20px 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }
        
        .user-profile:hover {
            background: rgba(52, 152, 219, 0.1);
        }
        
        .user-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.4rem;
            margin-left: 12px;
            box-shadow: 0 4px 12px rgba(52, 152, 219, 0.4);
            border: 3px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }
        
        .user-profile:hover .user-avatar {
            transform: scale(1.1);
            box-shadow: 0 6px 20px rgba(52, 152, 219, 0.6);
        }
        
        .user-info {
            flex: 1;
            overflow: hidden;
        }
        
        .user-name {
            font-weight: 600;
            font-size: 1.1rem;
            color: #ecf0f1;
            margin-bottom: 4px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .user-role {
            font-size: 0.85rem;
            color: #bdc3c7;
            background: rgba(52, 152, 219, 0.2);
            padding: 2px 8px;
            border-radius: 12px;
            display: inline-block;
            border: 1px solid rgba(52, 152, 219, 0.3);
        }
        
        .sidebar-nav {
            height: calc(100vh - 250px);
            overflow-y: auto;
            padding-bottom: 120px; /* Space for footer */
        }
        
        .nav-header {
            color: #bdc3c7;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 20px 15px 10px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 0.4rem;
            display: flex;
            align-items: center;
        }

        .nav-header i {
            margin-right: 0.5rem;
            font-size: 1.1rem;
        }
        
        .nav-link {
            color: #ecf0f1 !important;
            border-radius: 12px;
            margin: 2px 5px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .nav-link:hover {
            background: linear-gradient(90deg, rgba(52, 152, 219, 0.1) 0%, rgba(52, 152, 219, 0.05) 100%) !important;
            color: #ecf0f1 !important;
            transform: translateX(5px);
            border-left: 3px solid #3498db;
        }
        
        .nav-link.active {
            background: linear-gradient(135deg, rgba(52, 152, 219, 0.2) 0%, rgba(41, 128, 185, 0.2) 100%) !important;
            color: white !important;
            border-left: 4px solid #3498db;
            box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
        }
        
        .nav-icon {
            width: 20px;
            margin-left: 12px;
            transition: all 0.3s ease;
        }
        
        .nav-link:hover .nav-icon {
            color: #3498db;
            transform: scale(1.2);
        }
        
        .nav-badge {
            position: absolute;
            top: 8px;
            left: 25px;
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
            font-size: 0.7rem;
            padding: 2px 6px;
            border-radius: 10px;
            min-width: 18px;
            text-align: center;
        }
        
        /* Sidebar collapsed state adjustments */
        .sidebar-collapse .user-profile {
            padding: 15px 5px;
            text-align: center;
            margin-left: 0;
        }

        /* Enhanced Sidebar Search */
        .sidebar-search {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .search-container {
            position: relative;
        }

        .search-input {
            width: 100%;
            padding: 0.75rem 2.5rem 0.75rem 1rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 6px;
            background-color: rgba(255, 255, 255, 0.1);
            color: #ecf0f1;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .search-input:focus {
            border-color: var(--primary-color);
            background-color: rgba(255, 255, 255, 0.2);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .search-icon {
            position: absolute;
            top: 50%;
            left: 1rem;
            transform: translateY(-50%);
            color: #bdc3c7;
            font-size: 1rem;
        }

        .search-results {
            position: absolute;
            top: 100%;
            left: 0;
            width: 100%;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            max-height: 200px;
            overflow-y: auto;
            display: none; /* Hidden by default */
        }

        .search-results.show {
            display: block;
        }

        .search-results .search-item {
            padding: 0.75rem 1rem;
            cursor: pointer;
            transition: background-color 0.2s ease;
            display: flex;
            align-items: center;
        }

        .search-results .search-item:hover {
            background-color: #f1f5f9;
        }

        .search-results .search-item i {
            margin-right: 0.75rem;
            font-size: 0.9rem;
            color: #3498db;
        }

        .search-results .search-item p {
            margin-bottom: 0;
            font-size: 0.9rem;
            color: #475569;
        }

        .search-results .search-item .badge {
            margin-left: 0.5rem;
        }

        /* Enhanced Sidebar Footer */
        .sidebar-footer {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 1rem 1.5rem;
            background: rgba(0, 0, 0, 0.3);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            text-align: center;
            backdrop-filter: blur(10px);
        }

        .system-info {
            color: #bdc3c7;
            font-size: 0.75rem;
            margin-bottom: 8px;
        }

        .logout-btn {
            width: 100%;
            padding: 10px;
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            cursor: pointer;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .logout-btn:hover {
            background: linear-gradient(135deg, #c0392b, #a93226);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(231, 76, 60, 0.4);
            color: white;
            text-decoration: none;
        }

        .logout-btn i {
            margin-right: 0.5rem;
        }

        /* Mobile Sidebar Backdrop */
        .sidebar-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1037;
            display: none; /* Hidden by default */
        }

        .sidebar-open .sidebar-backdrop {
            display: block;
        }

        /* Collapsed sidebar adjustments */
        .sidebar-collapse .user-info,
        .sidebar-collapse .sidebar-search,
        .sidebar-collapse .nav-header,
        .sidebar-collapse .nav-link p {
            display: none;
        }

        .sidebar-collapse .nav-link {
            justify-content: center;
            padding: 12px;
        }

        .sidebar-collapse .nav-icon {
            margin: 0;
        }

        .sidebar-collapse .user-avatar {
            margin: 0;
        }
    </style>

    <script>
        // Sidebar toggle function
        function toggleSidebar() {
            const sidebar = document.querySelector('.main-sidebar');
            const sidebarBackdrop = document.querySelector('.sidebar-backdrop');
            
            if (window.innerWidth <= 768) {
                document.body.classList.toggle('sidebar-open');
            } else {
                document.body.classList.toggle('sidebar-collapse');
            }
        }
    </script>
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
<nav class="main-header navbar navbar-expand navbar-light">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
        </li>
    </ul>
    {{-- Logout form and link --}}
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
        @csrf
    </form>
    <ul class="navbar-nav {{ app()->getLocale() == 'ar' ? 'mr-auto' : 'ml-auto' }}">
        {{-- Notifications dropdown --}}
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#" aria-expanded="false">
                <i class="far fa-bell"></i>
                <span class="badge badge-primary navbar-badge">3</span>
            </a>
            <div class="dropdown-menu dropdown-menu-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}">
                <span class="dropdown-header">3 @lang('messages.notifications')</span>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                    <i class="fas fa-envelope mr-2"></i> @lang('messages.new_invoice')
                    <span class="float-right text-muted text-sm">3 @lang('messages.mins')</span>
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                    <i class="fas fa-file mr-2"></i> @lang('messages.new_report')
                    <span class="float-right text-muted text-sm">2 @lang('messages.days')</span>
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                    <i class="fas fa-check-circle mr-2"></i> @lang('messages.payment_received')
                    <span class="float-right text-muted text-sm">1 @lang('messages.week')</span>
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item dropdown-footer">@lang('messages.see_all_notifications')</a>
            </div>
        </li>
        {{-- User dropdown --}}
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="fas fa-user-circle"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}">
                <span class="dropdown-header">{{ auth()->user()->name }}</span>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                    <i class="fas fa-user mr-2"></i> @lang('messages.profile')
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt {{ app()->getLocale() == 'ar' ? 'ml-2' : 'mr-2' }}"></i> @lang('sidebar.logout')
                </a>
            </div>
        </li>
    </ul>
</nav>

<!-- Sidebar -->
<aside class="main-sidebar elevation-4">
    <!-- Enhanced Brand Section -->
    <a href="{{ route('dashboard') }}" class="brand-link">
        @if($companyLogo)
        <img src="{{ asset('storage/logos/'.$companyLogo) }}" alt="Logo" class="brand-image">
        @endif
        <span class="brand-text">{{ $systemName }}</span>
    </a>

    <div class="sidebar">
        <!-- Enhanced User Profile -->
        <div class="user-profile">
            <div class="user-profile-content">
                <div class="user-avatar">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
                <div class="user-info">
                    <div class="user-name">{{ auth()->user()->name }}</div>
                    <div class="user-role">
                        @if($isSuperAdmin)
                            @lang('messages.super_admin')
                        @else
                            {{ auth()->user()->roles->first()->name ?? __('messages.user') }}
                        @endif
                    </div>
                    <div class="user-status">
                        <div class="status-indicator"></div>
                        <span class="status-text">متصل</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Search Section -->
        <div class="sidebar-search">
            <div class="search-container">
                <input type="text" class="search-input" placeholder="البحث في القائمة..." id="sidebarSearch">
                <i class="fas fa-search search-icon"></i>
                <div class="search-results" id="searchResults"></div>
            </div>
        </div>



        <!-- Enhanced Navigation -->
        <nav class="sidebar-nav">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

                <!-- Dashboard Section -->
                <li class="nav-header">
                    <i class="fas fa-tachometer-alt"></i>
                    @lang('sidebar.dashboard')
                </li>
                @if($isSuperAdmin || auth()->user()->can('view_dashboard'))
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ Request::routeIs('dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>@lang('sidebar.home')</p>
                    </a>
                </li>
                @endif

                <!-- Accounts Section -->
                @if($isSuperAdmin || auth()->user()->can('view_accounts'))
                <li class="nav-header">
                    <i class="fas fa-chart-pie"></i>
                    @lang('sidebar.accounts')
                </li>
                <li class="nav-item has-treeview {{ Request::routeIs('accounts.*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ Request::routeIs('accounts.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-wallet"></i>
                        <p>@lang('sidebar.accounts_management')</p>
                        @if(Request::routeIs('accounts.*'))
                            <span class="nav-badge">{{ \App\Models\Account::count() }}</span>
                        @endif
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('accounts.index') }}" class="nav-link {{ Request::routeIs('accounts.index') ? 'active' : '' }}">
                                <i class="far fa-folder nav-icon"></i>
                                <p>@lang('sidebar.categories_list')</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('accounts.real') }}" class="nav-link {{ Request::routeIs('accounts.real') ? 'active' : '' }}">
                                <i class="far fa-list-alt nav-icon"></i>
                                <p>@lang('sidebar.accounts_list')</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('accounts.createGroup') }}" class="nav-link {{ Request::routeIs('accounts.createGroup') ? 'active' : '' }}">
                                <i class="far fa-folder-open nav-icon"></i>
                                <p>@lang('sidebar.add_category')</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('accounts.createAccount') }}" class="nav-link {{ Request::routeIs('accounts.createAccount') ? 'active' : '' }}">
                                <i class="far fa-plus-square nav-icon"></i>
                                <p>@lang('sidebar.add_account')</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('accounts.chart') }}" class="nav-link {{ Request::routeIs('accounts.chart') ? 'active' : '' }}">
                                <i class="far fa-chart-bar nav-icon"></i>
                                <p>@lang('sidebar.chart_of_accounts')</p>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif

                <!-- Vouchers Section -->
                @if($isSuperAdmin || auth()->user()->can('view_vouchers'))
                <li class="nav-header">
                    <i class="fas fa-file-invoice-dollar"></i>
                    @lang('sidebar.vouchers')
                </li>
                <li class="nav-item has-treeview {{ Request::routeIs('vouchers.*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ Request::routeIs('vouchers.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-file-invoice-dollar"></i>
                        <p>@lang('sidebar.vouchers_management')</p>
                        @php
                            $todayVouchers = \App\Models\Voucher::whereDate('created_at', today())->count();
                        @endphp
                        @if($todayVouchers > 0)
                            <span class="nav-badge">{{ $todayVouchers }}</span>
                        @endif
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ Route::localizedRoute('vouchers.index', ['type' => 'receipt']) }}" class="nav-link {{ request('type') == 'receipt' ? 'active' : '' }}">
                                <i class="far fa-arrow-alt-circle-down nav-icon"></i>
                                <p>@lang('sidebar.receipt_vouchers')</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ Route::localizedRoute('vouchers.index', ['type' => 'payment']) }}" class="nav-link {{ request('type') == 'payment' ? 'active' : '' }}">
                                <i class="far fa-arrow-alt-circle-up nav-icon"></i>
                                <p>@lang('sidebar.payment_vouchers')</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ Route::localizedRoute('vouchers.index', ['type' => 'transfer']) }}" class="nav-link {{ request('type') == 'transfer' ? 'active' : '' }}">
                                <i class="fas fa-exchange-alt nav-icon"></i>
                                <p>@lang('sidebar.transfer_vouchers')</p>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif

                <!-- Journal Entries -->
                @if($isSuperAdmin || auth()->user()->can('view_journal_entries'))
                <li class="nav-item">
                    <a class="nav-link {{ Request::routeIs('journal-entries.*') ? 'active' : '' }}" href="{{ route('journal-entries.index') }}">
                        <i class="nav-icon fas fa-book"></i>
                        <p>@lang('sidebar.accounting_entries')</p>
                        @php
                            $todayEntries = \App\Models\JournalEntry::whereDate('created_at', today())->count();
                        @endphp
                        @if($todayEntries > 0)
                            <span class="nav-badge">{{ $todayEntries }}</span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::routeIs('ledger.*') ? 'active' : '' }}" href="{{ route('ledger.index') }}">
                        <i class="nav-icon fas fa-book-open"></i>
                        <p>@lang('sidebar.ledger')</p>
                    </a>
                </li>
                @endif

                <!-- Transactions -->
                @if($isSuperAdmin || auth()->user()->can('view_transactions'))
                <li class="nav-header">
                    <i class="fas fa-exchange-alt"></i>
                    @lang('sidebar.transactions')
                </li>
                <li class="nav-item">
                    <a href="{{ route('transactions.index') }}" class="nav-link {{ Request::routeIs('transactions.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-exchange-alt"></i>
                        <p>@lang('sidebar.transactions_management')</p>
                    </a>
                </li>
                @endif

                <!-- Currencies -->
                @if($isSuperAdmin || auth()->user()->can('view_currencies'))
                <li class="nav-header">
                    <i class="fas fa-coins"></i>
                    @lang('sidebar.currencies')
                </li>
                <li class="nav-item">
                    <a href="{{ route('currencies.index') }}" class="nav-link {{ Request::routeIs('currencies.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-coins"></i>
                        <p>@lang('sidebar.currencies_management')</p>
                    </a>
                </li>
                @endif

                <!-- Invoices -->
                @if($isSuperAdmin || auth()->user()->can('view_invoices'))
                <li class="nav-header">
                    <i class="fas fa-receipt"></i>
                    @lang('sidebar.invoices')
                </li>
                <li class="nav-item has-treeview {{ Request::routeIs('invoices.*') || Request::routeIs('invoice-payments.*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ Request::routeIs('invoices.*') || Request::routeIs('invoice-payments.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-receipt"></i>
                        <p>@lang('sidebar.invoices_management')</p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('invoices.index') }}" class="nav-link {{ Request::routeIs('invoices.index') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>@lang('sidebar.invoices_list')</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('invoices.create') }}" class="nav-link {{ Request::routeIs('invoices.create') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>@lang('sidebar.new_invoice')</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('invoice-payments.create') }}" class="nav-link {{ Request::routeIs('invoice-payments.create') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>@lang('sidebar.pay_invoice')</p>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif

                <!-- Customers -->
                @if($isSuperAdmin || auth()->user()->can('view_customers'))
                <li class="nav-header">
                    <i class="fas fa-users"></i>
                    @lang('sidebar.customers')
                </li>
                <li class="nav-item has-treeview {{ Request::routeIs('customers.*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ Request::routeIs('customers.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-users"></i>
                        <p>@lang('sidebar.customers_management')</p>
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

                <!-- Items -->
                @if($isSuperAdmin || auth()->user()->can('view_items'))
                <li class="nav-header">
                    <i class="fas fa-box-open"></i>
                    @lang('sidebar.items')
                </li>
                <li class="nav-item has-treeview {{ Request::routeIs('items.*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ Request::routeIs('items.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-box-open"></i>
                        <p>@lang('sidebar.items_management')</p>
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

                <!-- HR Section -->
                @if($isSuperAdmin || auth()->user()->can('view_employees') || auth()->user()->can('view_salaries') || auth()->user()->can('view_salary_payments'))
                <li class="nav-header">
                    <i class="fas fa-user-tie"></i>
                    @lang('sidebar.hr')
                </li>
                @if($isSuperAdmin || auth()->user()->can('view_employees'))
                <li class="nav-item">
                    <a href="{{ route('employees.index') }}" class="nav-link {{ Request::routeIs('employees.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user-tie"></i>
                        <p>@lang('sidebar.employees')</p>
                    </a>
                </li>
                @endif
                @if($isSuperAdmin || auth()->user()->can('view_salaries'))
                <li class="nav-item">
                    <a href="{{ route('salaries.index') }}" class="nav-link {{ Request::routeIs('salaries.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-money-bill-wave"></i>
                        <p>@lang('sidebar.salaries')</p>
                    </a>
                </li>
                @endif
                @if($isSuperAdmin || auth()->user()->can('view_salary_payments'))
                <li class="nav-item">
                    <a href="{{ route('salary-payments.index') }}" class="nav-link {{ Request::routeIs('salary-payments.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-money-check-alt"></i>
                        <p>@lang('sidebar.salary_payments')</p>
                    </a>
                </li>
                @endif
                @if($isSuperAdmin || auth()->user()->can('view_salary_batches'))
                <li class="nav-item">
                    <a href="{{ route('salary-batches.index') }}" class="nav-link {{ Request::routeIs('salary-batches.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-file-invoice-dollar"></i>
                        <p>@lang('sidebar.salary_sheets')</p>
                    </a>
                </li>
                @endif
                @endif

                <!-- System Settings -->
                @if($isSuperAdmin || auth()->user()->can('view_roles') || auth()->user()->can('view_permissions') || auth()->user()->can('view_users'))
                <li class="nav-header">
                    <i class="fas fa-cogs"></i>
                    @lang('sidebar.system_settings')
                </li>
                @if($isSuperAdmin || auth()->user()->can('view_roles'))
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
                @endif

                <!-- Reports Section -->
                @if($isSuperAdmin || auth()->user()->can('view_reports'))
                <li class="nav-header">
                    <i class="fas fa-chart-bar"></i>
                    @lang('sidebar.reports')
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::routeIs('reports.trial-balance') ? 'active' : '' }}" href="{{ route('reports.trial-balance') }}">
                        <i class="nav-icon fas fa-balance-scale"></i>
                        <p>@lang('sidebar.trial_balance')</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::routeIs('reports.balance-sheet') ? 'active' : '' }}" href="{{ route('reports.balance-sheet') }}">
                        <i class="nav-icon fas fa-file-invoice-dollar"></i>
                        <p>@lang('sidebar.balance_sheet')</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::routeIs('reports.income-statement') ? 'active' : '' }}" href="{{ route('reports.income-statement') }}">
                        <i class="nav-icon fas fa-chart-line"></i>
                        <p>@lang('sidebar.income_statement')</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::routeIs('reports.payroll') ? 'active' : '' }}" href="{{ route('reports.payroll') }}">
                        <i class="nav-icon fas fa-money-check-alt"></i>
                        <p>@lang('sidebar.payroll_report')</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::routeIs('reports.expenses-revenues') ? 'active' : '' }}" href="{{ route('reports.expenses-revenues') }}">
                        <i class="nav-icon fas fa-receipt"></i>
                        <p>@lang('sidebar.expenses_revenues')</p>
                    </a>
                </li>
                @endif

            </ul>
        </nav>

        <!-- Enhanced Sidebar Footer -->
        <div class="sidebar-footer">
            <div class="system-info">
                النظام المحاسبي الاحترافي v2.2.3
            </div>
            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                @csrf
                <button type="submit" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>@lang('sidebar.logout')</span>
                </button>
            </form>
        </div>
    </div>
</aside>

<!-- Mobile Sidebar Backdrop -->
<div class="sidebar-backdrop d-lg-none" onclick="toggleSidebar()"></div>
@endauth

<!-- Content Wrapper -->
<div class="content-wrapper">
    <section class="content">
        <div class="container-fluid pt-3">
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif
            
            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif
            
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
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.5/dist/sweetalert2.all.min.js"></script>

<!-- Account Currency Enhancement -->
<script src="{{ asset('assets/js/account-currency-enhancement.js') }}"></script>

<script>
    // إعادة تنشيط Tooltips وPopovers
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
        $('[data-toggle="popover"]').popover();
        
        // تعامل مع رسائل النجاح والخطأ بشكل أكثر جاذبية
        @if(session('success'))
        setTimeout(function() {
            $('.alert-success').fadeOut('slow');
        }, 5000);
        @endif
        
        @if(session('error'))
        setTimeout(function() {
            $('.alert-danger').fadeOut('slow');
        }, 5000);
        @endif
    });
</script>

@stack('scripts')

<script>
// حماية النماذج من إعادة الإرسال - مستقلة عن app.js للتأكد من عملها في جميع الصفحات
document.addEventListener('DOMContentLoaded', function() {
    // ابحث عن جميع النماذج وأضف حماية ضد التكرار
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function(e) {
            // تحقق مما إذا كان النموذج قد تم تقديمه بالفعل
            if (this.hasAttribute('data-submitted')) {
                e.preventDefault();
                return;
            }
            
            // تعطيل جميع أزرار التقديم
            this.querySelectorAll('button[type="submit"], input[type="submit"]').forEach(button => {
                button.disabled = true;
                
                // إضافة نص "جارٍ الحفظ..." للأزرار
                if (button.tagName === 'BUTTON') {
                    button.setAttribute('data-original-text', button.innerHTML);
                    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جارٍ الحفظ...';
                }
            });
            
            // وضع علامة على النموذج بأنه تم تقديمه
            this.setAttribute('data-submitted', 'true');
        });
    });
});
</script>

<!-- Enhanced Sidebar JavaScript -->
<script src="{{ asset('assets/js/enhanced-sidebar.js') }}"></script>
</body>
</html>
