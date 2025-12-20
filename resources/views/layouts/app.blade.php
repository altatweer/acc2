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

    @stack('styles')

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
        
        /* Main sidebar styling */
        .main-sidebar {
            background: #2c3e50;
            background: linear-gradient(to bottom, #2c3e50, #1a252f);
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            transition: width 0.3s ease;
            width: 250px;
            position: fixed;
            height: 100%;
            z-index: 1038;
            border-right: none;
        }
        
        .sidebar {
            height: calc(100% - 65px);
            overflow-y: auto;
            padding-top: 10px;
            scrollbar-width: thin;
            scrollbar-color: rgba(255,255,255,0.1) transparent;
            position: relative;
        }
        
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }
        
        .sidebar::-webkit-scrollbar-track {
            background: transparent;
        }
        
        .sidebar::-webkit-scrollbar-thumb {
            background-color: rgba(255,255,255,0.1);
            border-radius: 3px;
        }
        
        /* Nav sidebar styling */
        .nav-sidebar {
            padding: 0 0.5rem;
        }
        
        .nav-sidebar .nav-item {
            margin-bottom: 1px;
            position: relative;
        }
        
        .nav-sidebar .nav-link {
            color: #ecf0f1;
            border-radius: 6px;
            margin: 0 0.3rem;
            padding: 0.5rem 0.8rem;
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
            display: flex;
            align-items: center;
            overflow: hidden;
            font-size: {{ app()->getLocale() == 'ar' ? '0.92rem' : '0.85rem' }};
            letter-spacing: {{ app()->getLocale() == 'ar' ? '0' : '0.2px' }};
        }
        
        .nav-sidebar .nav-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: transparent;
            transition: all 0.3s ease;
        }
        
        .nav-sidebar .nav-link:hover {
            color: #ffffff;
            background: rgba(255,255,255,0.1);
            transform: translateX(3px);
        }
        
        .nav-sidebar .nav-link:hover::before {
            background: #3498db;
        }
        
        .nav-sidebar .nav-link.active {
            background: rgba(52, 152, 219, 0.2);
            color: #ffffff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }
        
        .nav-sidebar .nav-link.active::before {
            background: #3498db;
        }
        
        .nav-sidebar .nav-link i.nav-icon {
            font-size: {{ app()->getLocale() == 'ar' ? '1.05rem' : '1.05rem' }};
            margin-right: {{ app()->getLocale() == 'ar' ? '0.65rem' : '0.75rem' }};
            margin-left: {{ app()->getLocale() == 'ar' ? '0.2rem' : '0' }};
            width: 20px;
            text-align: center;
            color: #bdc3c7;
            transition: all 0.3s ease;
            opacity: 0.9;
        }
        
        .nav-sidebar .nav-link:hover i.nav-icon,
        .nav-sidebar .nav-link.active i.nav-icon {
            color: #3498db;
            opacity: 1;
            transform: scale(1.1);
        }
        
        .nav-sidebar .nav-treeview {
            display: none;
            padding-left: 14px;
            margin-top: 3px;
            margin-bottom: 3px;
            border-left: 1px dashed rgba(255, 255, 255, 0.2);
            margin-left: 8px;
        }
        
        .nav-sidebar .menu-open > .nav-treeview {
            display: block;
        }
        
        .nav-sidebar .nav-treeview .nav-item {
            margin-bottom: 1px;
        }
        
        .nav-sidebar .nav-treeview .nav-link {
            font-size: {{ app()->getLocale() == 'ar' ? '0.85rem' : '0.8rem' }};
            padding: 0.4rem 0.8rem;
            border-radius: 4px;
            color: rgba(255, 255, 255, 0.8);
            margin-left: 0;
            line-height: 1.4;
        }
        
        .nav-sidebar .nav-treeview .nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #ffffff;
        }
        
        .nav-sidebar .nav-treeview .nav-link.active {
            background: rgba(52, 152, 219, 0.2);
            color: #3498db;
        }
        
        .nav-sidebar .nav-link p {
            margin-bottom: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            font-size: {{ app()->getLocale() == 'ar' ? '0.85rem' : '0.8rem' }};
        }
        
        /* Angle icon animation */
        .nav-sidebar .nav-link .fa-angle-left {
            transition: transform 0.3s ease;
            position: absolute;
            right: 1rem;
        }
        
        .nav-sidebar .menu-open > .nav-link .fa-angle-left {
            transform: rotate(-90deg);
        }
        
        /* Nav headers styling */
        .nav-header {
            color: rgba(255, 255, 255, 0.7);
            font-size: {{ app()->getLocale() == 'ar' ? '0.75rem' : '0.7rem' }};
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: {{ app()->getLocale() == 'ar' ? '0' : '0.8px' }};
            padding: 0.7rem 1rem 0.3rem;
            margin-top: 0.4rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 0.4rem;
        }
        
        /* Content wrapper */
        .content-wrapper {
            background: var(--content-bg);
            min-height: 100vh;
            padding-bottom: 2rem;
            transition: margin-left 0.3s, margin-right 0.3s;
            margin-left: 0;
            margin-right: 0;
            width: calc(100% - 250px);
        }
        
        /* Cards styling */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.06);
            transition: transform 0.3s, box-shadow 0.3s;
            overflow: hidden;
        }
        
        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }
        
        .card-header {
            background: white;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 1.2rem 1.5rem;
            font-weight: 600;
        }
        
        .card-primary.card-outline {
            border-top: 3px solid var(--primary-color);
        }
        
        /* Buttons styling */
        .btn {
            font-weight: 500;
            border-radius: 6px;
            padding: 0.5rem 1rem;
            transition: all 0.2s;
            position: relative;
            overflow: hidden;
        }
        
        .btn::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 100%;
            height: 0;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            opacity: 0;
            transition: all 0.3s;
        }
        
        .btn:active::after {
            height: 300%;
            opacity: 1;
        }
        
        .btn-primary {
            background: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover, .btn-primary:focus {
            background: var(--primary-hover);
            border-color: var(--primary-hover);
            box-shadow: 0 4px 10px rgba(37, 99, 235, 0.3);
        }
        
        .btn-success {
            background: var(--success-color);
            border-color: var(--success-color);
        }
        
        .btn-success:hover, .btn-success:focus {
            background: #0ca678;
            border-color: #0ca678;
            box-shadow: 0 4px 10px rgba(16, 185, 129, 0.3);
        }
        
        .btn-danger {
            background: var(--danger-color);
            border-color: var(--danger-color);
        }
        
        .btn-danger:hover, .btn-danger:focus {
            background: #dc2626;
            border-color: #dc2626;
            box-shadow: 0 4px 10px rgba(239, 68, 68, 0.3);
        }
        
        /* Forms styling */
        .form-control {
            border-radius: 6px;
            border: 1px solid #e2e8f0;
            padding: 0.65rem 1rem;
            height: auto;
            font-size: 0.95rem;
            transition: all 0.2s;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        }
        
        /* Table styling */
        .table {
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
            border-radius: 8px;
            overflow: hidden;
        }
        
        .table th {
            background: #f8fafc;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.8rem;
            padding: 0.85rem 1rem;
            color: #475569;
            letter-spacing: 0.5px;
            border-top: none;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .table td {
            padding: 1rem;
            vertical-align: middle;
            border-top: none;
            border-bottom: 1px solid #f1f5f9;
            color: #1e293b;
            font-size: 0.95rem;
        }
        
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #fafafa;
        }
        
        .table-hover tbody tr:hover {
            background-color: #f1f5f9;
        }
        
        /* Footer styling */
        .main-footer {
            background: white;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
            padding: 1rem 0;
            font-size: 0.9rem;
            color: #64748b;
            margin-left: 0;
            margin-right: 0;
            width: calc(100% - 250px);
        }
        
        /* Badges styling */
        .badge {
            font-size: 75%;
            font-weight: 500;
            padding: 0.4em 0.65em;
            border-radius: 6px;
        }
        
        .badge-primary {
            background: var(--primary-light);
            color: var(--primary-color);
        }
        
        .badge-success {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success-color);
        }
        
        .badge-danger {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger-color);
        }
        
        .badge-warning {
            background: rgba(245, 158, 11, 0.1);
            color: var(--warning-color);
        }
        
        /* RTL specific styles */
        @if(app()->getLocale() == 'ar')
        .sidebar {
            right: 0;
            left: auto;
        }
        
        .main-sidebar {
            right: 0;
            left: auto;
        }
        
        /* Special adjustments for Arabic menu */
        .nav-sidebar .nav-link p {
            font-size: 0.9rem;
        }
        
        .nav-sidebar .menu-open > .nav-link .fa-angle-left {
            margin-top: 4px;
        }
        
        .content-wrapper {
            margin-right: 250px;
            margin-left: 0 !important;
            float: left;
        }
        
        .main-footer {
            margin-right: 250px;
            margin-left: 0 !important;
            float: left;
        }
        
        .sidebar-collapse .content-wrapper {
            margin-right: 4.6rem !important;
            margin-left: 0 !important;
        }
        
        .sidebar-collapse .main-footer {
            margin-right: 4.6rem !important;
            margin-left: 0 !important;
        }
        
        .main-header {
            margin-right: 250px !important;
            margin-left: 0 !important;
        }
        
        .sidebar-collapse .main-header {
            margin-right: 4.6rem !important;
        }
        
        .nav-sidebar .nav-link.active::before {
            right: -7px;
            left: auto;
            border-color: transparent transparent transparent var(--primary-color);
        }
        
        .nav-sidebar .nav-link i.nav-icon {
            margin-right: 0;
            margin-left: 0.7rem;
        }
        
        /* Fix for mobile view in RTL */
        @media (max-width: 767.98px) {
            .main-sidebar, .main-sidebar::before {
                box-shadow: none !important;
                margin-right: -250px;
                margin-left: 0;
            }
            
            .sidebar-open .main-sidebar, .sidebar-open .main-sidebar::before {
                margin-right: 0;
            }
            
            .content-wrapper,
            .main-footer,
            .main-header {
                margin-right: 0 !important;
            margin-left: 0 !important;
        }
        }
        
        @else
        .sidebar {
            left: 0;
            right: auto;
        }
        
        .main-sidebar {
            left: 0;
            right: auto;
        }
        
        .content-wrapper {
            margin-left: 250px;
            margin-right: 0;
            float: right;
        }
        
        .main-footer {
            margin-left: 250px;
            margin-right: 0;
            float: right;
        }
        
        .sidebar-collapse .content-wrapper {
            margin-left: 4.6rem;
            margin-right: 0;
        }
        
        .sidebar-collapse .main-footer {
            margin-left: 4.6rem;
            margin-right: 0;
        }
        
        .main-header {
            margin-left: 250px;
            margin-right: 0;
        }
        
        .sidebar-collapse .main-header {
            margin-left: 4.6rem;
        }
        
        /* Fix for mobile view in LTR */
        @media (max-width: 767.98px) {
            .main-sidebar, .main-sidebar::before {
                box-shadow: none !important;
                margin-left: -250px;
                margin-right: 0;
            }
            
            .sidebar-open .main-sidebar, .sidebar-open .main-sidebar::before {
                margin-left: 0;
            }
            
            .content-wrapper,
            .main-footer,
            .main-header {
                margin-left: 0 !important;
                margin-right: 0 !important;
            }
        }
        @endif
        
        /* تخصيص صفحة تسجيل الدخول فقط */
        @if (request()->routeIs('login'))
        .wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%) !important;
        }
        
        .login-box {
            width: 400px;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        
        .login-box .card {
            box-shadow: none;
            margin-bottom: 0;
        }
        
        .login-logo {
            font-weight: 700;
            font-size: 1.8rem;
            margin-bottom: 0;
            padding: 2rem 1rem 1rem;
            text-align: center;
            color: #1e293b;
        }
        @endif
        
        /* Dashboard cards */
        .info-box {
            display: flex;
            min-height: 100px;
            background: #fff;
            width: 100%;
            border-radius: 12px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.06);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .info-box:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }
        
        .info-box-icon {
            width: 90px;
            background-color: rgba(0, 0, 0, 0.04);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.875rem;
            color: var(--primary-color);
        }
        
        .info-box-content {
            padding: 15px 10px;
            flex: 1;
        }
        
        .info-box-text {
            display: block;
            font-size: 1rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            font-weight: 600;
            color: #475569;
        }
        
        .info-box-number {
            display: block;
            font-weight: 700;
            font-size: 1.5rem;
            color: #1e293b;
        }
        
        /* Animation */
        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(37, 99, 235, 0.5);
            }
            70% {
                box-shadow: 0 0 0 10px rgba(37, 99, 235, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(37, 99, 235, 0);
            }
        }
        
        .pulse-animation {
            animation: pulse 2s infinite;
        }
        
        /* Media queries for responsive layout */
        @media (max-width: 991.98px) {
            .content-wrapper, .main-footer {
                width: 100%;
            }
            
            .main-header {
                width: 100%;
            }
        }
        
        /* تحسينات خاصة للخط العربي */
        @if(app()->getLocale() == 'ar')
        * {
            font-family: 'Tajawal', sans-serif !important;
            text-rendering: optimizeLegibility;
            -webkit-font-smoothing: antialiased;
        }
        
        /* استثناء الأيقونات من خط Tajawal */
        .fa, .fas, .far, .fab, .fal, .fad,
        i[class*=" fa-"],
        i.nav-icon,
        .nav-icon:before,
        .nav-icon:after {
            font-family: "Font Awesome 5 Free" !important;
        }
        
        .far, i.far {
            font-family: "Font Awesome 5 Regular" !important;
        }
        
        .fab, i.fab {
            font-family: "Font Awesome 5 Brands" !important;
        }
        
        .nav-sidebar .nav-link p {
            font-weight: 500;
            letter-spacing: 0.2px;
            font-size: 0.92rem !important;
            padding-right: 2px;
        }
        
        .nav-sidebar .nav-link.active p {
            font-weight: 600;
        }
        
        .nav-sidebar .nav-header {
            font-weight: 700;
            letter-spacing: 0.3px;
            font-size: 0.85rem !important;
            color: rgba(255, 255, 255, 0.85);
        }
        
        .nav-sidebar .nav-treeview .nav-link {
            font-weight: 400;
            font-size: 0.88rem !important;
        }
        
        .brand-text {
            font-weight: 700;
            letter-spacing: 0.5px;
            font-size: 1.3rem;
        }
        
        .user-name {
            font-weight: 500;
            font-size: 0.95rem;
        }
        
        .nav-sidebar .nav-link:hover p {
            color: white;
        }
        @endif

        /* تصميم زر تسجيل الخروج الثابت في أسفل القائمة */
        .sidebar-logout {
            position: sticky;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 10px;
            background: rgba(220, 53, 69, 0.8);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            z-index: 10;
            margin-top: 20px;
        }

        .sidebar-logout a {
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px;
            border-radius: 5px;
            transition: all 0.3s;
            font-weight: bold;
        }

        .sidebar-logout a:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }

        .sidebar-logout i {
            margin-right: 10px;
            font-size: 1.2em;
        }

        /* RTL adjustments for logout button */
        html[dir="rtl"] .sidebar-logout i {
            margin-right: 0;
            margin-left: 10px;
        }

        /* تخصيص شكل الزر عند طي القائمة */
        .sidebar-collapse .sidebar-logout span {
            display: none;
        }

        .sidebar-collapse .sidebar-logout {
            padding: 5px;
        }

        .sidebar-collapse .sidebar-logout a {
            justify-content: center;
        }

        .sidebar-collapse .sidebar-logout i {
            margin-right: 0;
            margin-left: 0;
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
    <a href="{{ route('dashboard') }}" class="brand-link">
        @if($companyLogo)
        <img src="{{ asset('storage/logos/'.$companyLogo) }}" alt="Logo" class="brand-image">
        @endif
        <span class="brand-text">{{ $systemName }}</span>
    </a>

    <div class="sidebar">
        <!-- ملف تعريف المستخدم -->
        <div class="user-profile">
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
            </div>
        </div>

        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

                <li class="nav-header">@lang('sidebar.dashboard')</li>
                @if($isSuperAdmin || auth()->user()->can('view_dashboard'))
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ Request::routeIs('dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>@lang('sidebar.home')</p>
                    </a>
                </li>
                @endif

                @if($isSuperAdmin || auth()->user()->can('view_accounts'))
                <li class="nav-header">@lang('sidebar.accounts')</li>
                <li class="nav-item has-treeview {{ Request::routeIs('accounts.*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ Request::routeIs('accounts.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-wallet"></i>
                        <p>
                            @lang('sidebar.accounts_management')
                            <i class="right fas fa-angle-left"></i>
                        </p>
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
                        <li class="nav-item">
                            <a href="{{ route('accounts.tree.index') }}" class="nav-link {{ Request::routeIs('accounts.tree.*') ? 'active' : '' }}">
                                <i class="fas fa-sitemap nav-icon"></i>
                                <p>تصدير شجرة الحسابات</p>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif

                @if($isSuperAdmin || auth()->user()->can('view_vouchers'))
                <li class="nav-header">@lang('sidebar.vouchers')</li>
                <li class="nav-item has-treeview {{ Request::routeIs('vouchers.*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ Request::routeIs('vouchers.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-file-invoice-dollar"></i>
                        <p>
                            @lang('sidebar.vouchers_management')
                            <i class="right fas fa-angle-left"></i>
                        </p>
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
                @if($isSuperAdmin || auth()->user()->can('manage_system_settings'))
                <li class="nav-item">
                    <a href="{{ route('print-settings.edit') }}" class="nav-link {{ Request::routeIs('print-settings.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-print"></i>
                        <p>إعدادات الطباعة المخصصة</p>
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
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('reports.currency-comparison') }}">
                        <i class="fas fa-chart-pie"></i>
                        <span>مقارنة العملات</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('reports.cash-flow') }}">
                        <i class="fas fa-money-bill-wave"></i>
                        <span>التدفقات النقدية</span>
                    </a>
                </li>
                @endif

            </ul>
        </nav>

        {{-- إضافة زر تسجيل الخروج الثابت في أسفل القائمة --}}
        <div class="sidebar-logout">
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt"></i>
                <span>@lang('sidebar.logout')</span>
            </a>
        </div>
    </div>
</aside>
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
</body>
</html>
