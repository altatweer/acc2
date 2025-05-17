@php
    use App\Models\Setting;
    $systemName = Setting::get('system_name', 'نظام المحاسبة');
    $companyName = Setting::get('company_name', '');
    $companyLogo = Setting::get('company_logo', '');
    $systemDescription = Setting::get('system_description', 'نظام محاسبة متكامل يساعدك على إدارة حساباتك بكل سهولة وفعالية');
    
    // إنشاء صورة SVG بديلة تحتوي على كلمة AUR بأحرف كبيرة
    $fallbackSvg = 'data:image/svg+xml;base64,'.base64_encode('<svg width="200" height="60" xmlns="http://www.w3.org/2000/svg"><text x="100" y="35" font-family="Arial" font-size="24" text-anchor="middle" fill="white">AUR</text></svg>');
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $systemName }} - تسجيل الدخول</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #1e40af;
            --accent-color: #3b82f6;
            --dark-color: #1e293b;
            --light-color: #f8fafc;
            --success-color: #16a34a;
            --error-color: #dc2626;
        }
        
        body {
            font-family: 'Tajawal', 'Segoe UI', sans-serif;
            background-color: #f8fafc;
            background-image: linear-gradient(120deg, #a1c4fd 0%, #c2e9fb 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
        }
        
        .login-container {
            width: 100%;
            max-width: 1000px;
            display: flex;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            overflow: hidden;
            background-color: white;
        }
        
        .login-image {
            width: 50%;
            background-color: var(--primary-color);
            background-image: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 40px;
            position: relative;
        }
        
        .login-image::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            background-image: url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiPjxkZWZzPjxwYXR0ZXJuIGlkPSJwYXR0ZXJuXyFKSTNqTTNaIiBwYXR0ZXJuVW5pdHM9InVzZXJTcGFjZU9uVXNlIiB3aWR0aD0iNDgiIGhlaWdodD0iNDgiIHBhdHRlcm5UcmFuc2Zvcm09InJvdGF0ZSgxMzUpIj48cmVjdCBpZD0icGF0dGVybl8xSkkzak0zWl9iZyIgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgZmlsbD0icmdiYSgzNywgOTksIDIzNSwgMCkiPjwvcmVjdD48cGF0aCBmaWxsPSJyZ2JhKDI1NSwgMjU1LCAyNTUsIDAuMDUpIiBkPSJNIDI0ICwgMjQgYSA4LDggMCAwIDEgLTgsOCBhIDgsOCAwIDAgMSAtOCwtOCBhIDgsOCAwIDAgMSA4LC04IGEgOCw4IDAgMCAxIDgsOCB6Ij48L3BhdGg+PC9wYXR0ZXJuPjwvZGVmcz48cmVjdCBmaWxsPSJ1cmwoI3BhdHRlcm5fIUpJM2pNM1opIiBoZWlnaHQ9IjEwMCUiIHdpZHRoPSIxMDAlIj48L3JlY3Q+PC9zdmc+');
            opacity: 0.3;
        }
        
        .login-image img {
            width: 180px;
            height: auto;
            margin-bottom: 30px;
            position: relative;
            z-index: 2;
            filter: brightness(0) invert(1);
        }
        
        .login-image h3 {
            color: white;
            font-size: 28px;
            margin-bottom: 20px;
            font-weight: 700;
            position: relative;
            z-index: 2;
        }
        
        .login-image p {
            color: rgba(255, 255, 255, 0.8);
            text-align: center;
            font-size: 16px;
            max-width: 80%;
            line-height: 1.6;
            position: relative;
            z-index: 2;
        }
        
        .login-form {
            width: 50%;
            padding: 60px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .login-form h2 {
            color: var(--dark-color);
            font-size: 28px;
            margin-bottom: 10px;
            font-weight: 700;
            text-align: center;
        }
        
        .login-form p {
            color: #64748b;
            font-size: 16px;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .form-floating {
            margin-bottom: 20px;
            position: relative;
        }
        
        .form-floating input {
            height: 60px;
            padding: 1rem 1rem 1rem 3rem;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background-color: #f8fafc;
            transition: all 0.3s;
        }
        
        .form-floating input:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
            background-color: white;
        }
        
        .form-floating label {
            padding: 1rem 0 0 3rem;
        }
        
        .input-icon {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            left: 16px;
            color: #94a3b8;
            z-index: 2;
        }
        
        .remember-me {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .remember-me input {
            margin-left: 10px;
            width: 16px;
            height: 16px;
        }
        
        .login-button {
            height: 50px;
            background-color: var(--primary-color);
            border: none;
            border-radius: 8px;
            color: white;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s;
            margin-bottom: 20px;
        }
        
        .login-button:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
        }
        
        .forgot-password {
            text-align: center;
            color: var(--primary-color);
            font-size: 14px;
            text-decoration: none;
            transition: all 0.3s;
            display: block;
        }
        
        .forgot-password:hover {
            text-decoration: underline;
            color: var(--secondary-color);
        }
        
        .login-footer {
            margin-top: 40px;
            text-align: center;
            color: #64748b;
            font-size: 14px;
        }
        
        /* RTL specific adjustments */
        html[dir="rtl"] .form-floating input {
            padding: 1rem 3rem 1rem 1rem;
        }
        
        html[dir="rtl"] .form-floating label {
            padding: 1rem 3rem 0 0;
        }
        
        html[dir="rtl"] .input-icon {
            left: auto;
            right: 16px;
        }
        
        html[dir="rtl"] .remember-me input {
            margin-left: 0;
            margin-right: 10px;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
                max-width: 400px;
            }
            
            .login-image, .login-form {
                width: 100%;
            }
            
            .login-image {
                padding: 30px;
            }
            
            .login-form {
                padding: 40px 30px;
            }
            
            .login-image img {
                width: 140px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="login-image">
                <img src="{{ asset('storage/logos/' . $companyLogo) }}" alt="{{ $companyName }}" onerror="this.src='{{ $fallbackSvg }}'">
                <h3>{{ $systemName }}</h3>
                <p>{{ $systemDescription }}</p>
            </div>
            <div class="login-form">
                <h2>مرحباً بك مجدداً</h2>
                <p>الرجاء تسجيل الدخول للمتابعة</p>
                
                @if(session('error'))
                    <div class="alert alert-danger mb-3">
                        {{ session('error') }}
                    </div>
                @endif
                
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    
                    <div class="form-floating">
                        <span class="input-icon">
                            <i class="fa-regular fa-envelope"></i>
                        </span>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="البريد الإلكتروني" value="{{ old('email') }}" required autofocus>
                        <label for="email">البريد الإلكتروني</label>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-floating">
                        <span class="input-icon">
                            <i class="fa-solid fa-lock"></i>
                        </span>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="كلمة المرور" required>
                        <label for="password">كلمة المرور</label>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="remember-me">
                        <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label for="remember">تذكرني</label>
                    </div>
                    
                    <button type="submit" class="btn login-button w-100">
                        <i class="fa-solid fa-right-to-bracket me-2"></i>
                        دخول
                    </button>
                    
                    @if (Route::has('password.request'))
                        <a class="forgot-password" href="{{ route('password.request') }}">
                            نسيت كلمة المرور؟
                        </a>
                    @endif
                    
                    <div class="login-footer">
                        {{ $systemName }} &copy; {{ date('Y') }} - {{ $companyName }} جميع الحقوق محفوظة
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
