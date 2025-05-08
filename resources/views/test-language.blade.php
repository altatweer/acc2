<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>{{ app()->getLocale() == 'ar' ? 'اختبار اللغة' : 'Language Test' }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            direction: {{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }};
            text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }};
            padding: 20px;
        }
        .card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin: 20px auto;
            max-width: 800px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
        }
        .info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-right: 10px;
        }
        .env-notice {
            margin-top: 20px;
            padding: 15px;
            background-color: #ffe6e6;
            border-radius: 6px;
            color: #ff0000;
        }
    </style>
</head>
<body>
    <div class="card">
        <h1>{{ app()->getLocale() == 'ar' ? 'اختبار تغيير اللغة' : 'Language Switching Test' }}</h1>
        
        <div class="info">
            <h2>{{ app()->getLocale() == 'ar' ? 'معلومات الجلسة الحالية' : 'Current Session Info' }}</h2>
            <p><strong>{{ app()->getLocale() == 'ar' ? 'اللغة الحالية' : 'Current Locale' }}:</strong> {{ app()->getLocale() }}</p>
            <p><strong>{{ app()->getLocale() == 'ar' ? 'اتجاه الصفحة' : 'Page Direction' }}:</strong> {{ app()->getLocale() == 'ar' ? 'RTL' : 'LTR' }}</p>
            <p><strong>{{ app()->getLocale() == 'ar' ? 'جلسة اللغة' : 'Session Locale' }}:</strong> {{ session('locale') }}</p>
        </div>

        <div class="info">
            <h2>{{ app()->getLocale() == 'ar' ? 'معلومات إضافية' : 'Additional Info' }}</h2>
            <p><strong>{{ app()->getLocale() == 'ar' ? 'لغة متصفح المستخدم' : 'Browser Languages' }}:</strong> 
                <span id="browserLanguages">JavaScript required to detect</span>
            </p>
            <p><strong>{{ app()->getLocale() == 'ar' ? 'رقم الجلسة' : 'Session ID' }}:</strong> {{ session()->getId() }}</p>
            <p><strong>{{ app()->getLocale() == 'ar' ? 'وقت التحميل' : 'Page Load Time' }}:</strong> {{ now() }}</p>
            <p><strong>URL Parameters:</strong> {{ request()->getQueryString() ?: 'None' }}</p>
        </div>

        <div>
            <h2>{{ app()->getLocale() == 'ar' ? 'طرق تغيير اللغة' : 'Language Change Methods' }}</h2>
            
            <div style="margin-bottom: 20px">
                <h3>{{ app()->getLocale() == 'ar' ? 'الطريقة العادية' : 'Normal Method' }}</h3>
                <a href="{{ route('lang.switch', 'ar') }}" class="button">عربي</a>
                <a href="{{ route('lang.switch', 'en') }}" class="button">English</a>
            </div>
            
            <div style="margin-bottom: 20px">
                <h3>{{ app()->getLocale() == 'ar' ? 'الطريقة المباشرة (الموثوقة)' : 'Direct Method (Reliable)' }}</h3>
                <a href="{{ url('/set-language/ar') }}" class="button">{{ app()->getLocale() == 'ar' ? 'العربية' : 'Switch to Arabic' }}</a>
                <a href="{{ url('/set-language/en') }}" class="button">{{ app()->getLocale() == 'ar' ? 'الإنجليزية' : 'Switch to English' }}</a>
            </div>
            
            <div>
                <h3>{{ app()->getLocale() == 'ar' ? 'الطرق الأخرى للاختبار' : 'Other Test Methods' }}</h3>
                <a href="{{ url('/test-language/force/ar') }}" class="button">{{ app()->getLocale() == 'ar' ? 'اختبار العربية' : 'Test Arabic' }}</a>
                <a href="{{ url('/test-language/force/en') }}" class="button">{{ app()->getLocale() == 'ar' ? 'اختبار الإنجليزية' : 'Test English' }}</a>
            </div>
        </div>

        @if(app()->environment('local'))
        <div class="env-notice">
            <p><strong>{{ app()->getLocale() == 'ar' ? 'ملاحظة مهمة' : 'Important Notice' }}:</strong> 
            {{ app()->getLocale() == 'ar' ? 'التطبيق يعمل في بيئة التطوير المحلية. قد تحتاج إلى تعطيل ذاكرة التخزين المؤقت للمتصفح.' : 'Application is running in local environment. You may need to disable browser caching.' }}</p>
        </div>
        @endif
    </div>

    <script>
    // Display browser language settings
    document.getElementById('browserLanguages').textContent = navigator.languages 
        ? navigator.languages.join(', ') 
        : (navigator.language || navigator.userLanguage);
    </script>
</body>
</html> 