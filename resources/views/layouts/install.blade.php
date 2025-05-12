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
    <div class="install-header">
        <img src="{{ asset('assets/logo.png') }}" alt="Logo" style="height:60px;">
        <h1 class="mt-2">Accounting System Installer</h1>
    </div>
    <main>
        @yield('content')
    </main>
    <footer class="text-center py-4 text-muted">
        &copy; {{ date('Y') }} Accounting System. All rights reserved.
    </footer>
</body>
</html> 