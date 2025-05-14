<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>System Installer</title>
    <link rel="icon" href="{{ asset('assets/logo.png') }}" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <style>
        body { background: #f8fafc; min-height: 100vh; }
        .card { border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); }
        .btn-primary, .btn-success, .btn-info { border-radius: 6px; font-weight: bold; }
        .install-header { background: linear-gradient(90deg, #007bff 0%, #0056b3 100%); color: #fff; padding: 2rem 0; text-align: center; }
    </style>
</head>
<body>
    <div class="install-header" style="background: linear-gradient(90deg, #2196f3 0%, #111 100%); color: #fff; padding: 2rem 0; text-align: center;">
        <img src="{{ asset('assets/dist/img/logo4.png') }}" alt="AurSuite Logo" style="height:70px; background:transparent;">
        <h1 class="mt-2" style="font-weight:bold; letter-spacing:1px; color:#1565c0;">AurSuite</h1>
        <div style="font-size:1.1rem; color:#fff; margin-top:8px;">
            <a href="https://aursuite.com" target="_blank" style="color:#fff; text-decoration:underline;">aursuite.com</a> &nbsp;|&nbsp; 
            <a href="mailto:support@aursuite.com" target="_blank" style="color:#fff; text-decoration:underline;">Support</a>
        </div>
    </div>
    <main>
        @yield('content')
    </main>
    <footer class="text-center py-4 text-muted" style="background:#f8fafc; border-top:1px solid #e0e0e0;">
        <img src="{{ asset('assets/dist/img/logo4.png') }}" alt="AurSuite Logo" style="height:32px; vertical-align:middle; margin-right:8px;">
        <span style="font-weight:bold; color:#1565c0;">AurSuite</span> &copy; {{ date('Y') }}
        &nbsp;|&nbsp;
        <a href="https://aursuite.com" target="_blank" style="color:#1565c0; text-decoration:underline;">aursuite.com</a>
        &nbsp;|&nbsp;
        <a href="mailto:support@aursuite.com" target="_blank" style="color:#1565c0; text-decoration:underline;">Support</a>
    </footer>
</body>
</html> 