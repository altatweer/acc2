<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>انتهت صلاحية الرخصة</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        .license-icon {
            font-size: 5rem;
            background: linear-gradient(45deg, #ff6b6b, #ee5a24);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-8 col-lg-6">
                <div class="card">
                    <div class="card-body text-center p-5">
                        <div class="license-icon mb-4">
                            <i class="fas fa-key"></i>
                        </div>
                        
                        <h1 class="h2 text-danger mb-3">انتهت صلاحية الرخصة</h1>
                        
                        <div class="alert alert-danger" role="alert">
                            <strong><i class="fas fa-exclamation-triangle me-2"></i>{{ $license['message'] }}</strong>
                        </div>

                        @if($license['type'] === 'development')
                            <div class="alert alert-info" role="alert">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>رخصة التطوير:</strong> هذه رخصة تطوير انتهت صلاحيتها.
                                <br>للمتابعة، يرجى تجديد الرخصة أو الحصول على رخصة إنتاج.
                            </div>
                        @endif

                        <div class="row text-start mt-4">
                            <div class="col-md-6">
                                <h6 class="fw-bold">معلومات الرخصة:</h6>
                                <ul class="list-unstyled">
                                    @if(isset($license['license_key']))
                                        <li><strong>المفتاح:</strong> {{ substr($license['license_key'], 0, 15) }}...</li>
                                    @endif
                                    <li><strong>النوع:</strong> {{ $license['type'] === 'development' ? 'تطوير' : $license['type'] }}</li>
                                    <li><strong>الحالة:</strong> 
                                        <span class="badge bg-danger">{{ $license['status'] }}</span>
                                    </li>
                                    @if(isset($license['expires_at']))
                                        <li><strong>انتهت في:</strong> {{ $license['expires_at']->format('Y-m-d H:i') }}</li>
                                    @endif
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6 class="fw-bold">الحلول المتاحة:</h6>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-arrow-left text-primary me-2"></i>تجديد الرخصة</li>
                                    <li><i class="fas fa-arrow-left text-primary me-2"></i>الحصول على رخصة جديدة</li>
                                    <li><i class="fas fa-arrow-left text-primary me-2"></i>التواصل مع الدعم</li>
                                </ul>
                            </div>
                        </div>

                        <div class="mt-4">
                            <a href="{{ route('install.maintenance') }}" class="btn btn-primary btn-lg me-2">
                                <i class="fas fa-tools me-2"></i>أدوات الصيانة
                            </a>
                            
                            @if($license['type'] === 'development')
                                <a href="{{ route('install.index') }}" class="btn btn-secondary btn-lg">
                                    <i class="fas fa-redo me-2"></i>إعادة التثبيت
                                </a>
                            @endif
                        </div>

                        <div class="mt-4 text-muted">
                            <small>
                                <i class="fas fa-envelope me-1"></i>
                                للمساعدة: support@yourdomain.com
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
