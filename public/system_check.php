<?php
// فحص سريع لحالة النظام - للتشخيص السريع
?>
<!DOCTYPE html>
<html dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>فحص حالة النظام</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 800px; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .check { padding: 10px; margin: 5px 0; border-radius: 5px; }
        .pass { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .fail { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .warning { background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
        h1 { color: #333; }
        .details { font-size: 12px; color: #666; margin-top: 5px; }
        pre { background: #f8f9fa; padding: 10px; border-radius: 5px; font-size: 12px; overflow-x: auto; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 فحص حالة النظام</h1>
        
        <div class="check <?= version_compare(PHP_VERSION, '8.0', '>=') ? 'pass' : 'fail' ?>">
            <strong>PHP Version:</strong> <?= PHP_VERSION ?>
            <div class="details">المطلوب: PHP 8.0 أو أحدث</div>
        </div>
        
        <div class="check <?= extension_loaded('pdo') ? 'pass' : 'fail' ?>">
            <strong>PDO Extension:</strong> <?= extension_loaded('pdo') ? 'متوفر' : 'غير متوفر' ?>
        </div>
        
        <div class="check <?= extension_loaded('mbstring') ? 'pass' : 'fail' ?>">
            <strong>Mbstring Extension:</strong> <?= extension_loaded('mbstring') ? 'متوفر' : 'غير متوفر' ?>
        </div>
        
        <div class="check <?= extension_loaded('openssl') ? 'pass' : 'fail' ?>">
            <strong>OpenSSL Extension:</strong> <?= extension_loaded('openssl') ? 'متوفر' : 'غير متوفر' ?>
        </div>
        
        <div class="check <?= is_writable(__DIR__ . '/../storage') ? 'pass' : 'fail' ?>">
            <strong>Storage Writable:</strong> <?= is_writable(__DIR__ . '/../storage') ? 'قابل للكتابة' : 'غير قابل للكتابة' ?>
            <div class="details">المسار: <?= realpath(__DIR__ . '/../storage') ?></div>
        </div>
        
        <div class="check <?= file_exists(__DIR__ . '/../.env') ? 'pass' : 'warning' ?>">
            <strong>.env File:</strong> <?= file_exists(__DIR__ . '/../.env') ? 'موجود' : 'غير موجود' ?>
            <div class="details">المسار: <?= __DIR__ . '/../.env' ?></div>
        </div>
        
        <div class="check <?= file_exists(__DIR__ . '/../storage/app/install.lock') ? 'warning' : 'pass' ?>">
            <strong>Installation Lock:</strong> <?= file_exists(__DIR__ . '/../storage/app/install.lock') ? 'النظام مثبت' : 'جاهز للتثبيت' ?>
        </div>
        
        <?php
        // Session test
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }
        $_SESSION['test'] = 'working';
        $sessionWorks = isset($_SESSION['test']) && $_SESSION['test'] === 'working';
        ?>
        
        <div class="check <?= $sessionWorks ? 'pass' : 'fail' ?>">
            <strong>Session Support:</strong> <?= $sessionWorks ? 'يعمل' : 'لا يعمل' ?>
            <div class="details">Session ID: <?= session_id() ?: 'غير متوفر' ?></div>
        </div>
        
        <h2>🌐 معلومات الخادم</h2>
        <pre>
Server Software: <?= $_SERVER['SERVER_SOFTWARE'] ?? 'غير محدد' ?>
Document Root: <?= $_SERVER['DOCUMENT_ROOT'] ?? 'غير محدد' ?>
HTTP Host: <?= $_SERVER['HTTP_HOST'] ?? 'غير محدد' ?>
Request URI: <?= $_SERVER['REQUEST_URI'] ?? 'غير محدد' ?>
PHP SAPI: <?= php_sapi_name() ?>
System: <?= php_uname() ?>
</pre>

        <h2>🔗 روابط سريعة</h2>
        <div style="margin: 20px 0;">
            <a href="/install/" style="display: inline-block; background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px;">
                🔧 بدء التثبيت
            </a>
            <a href="/" style="display: inline-block; background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px;">
                🏠 الصفحة الرئيسية
            </a>
            <?php if (file_exists(__DIR__ . '/../storage/app/install.lock')): ?>
            <a href="/login" style="display: inline-block; background: #17a2b8; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px;">
                🔐 تسجيل الدخول
            </a>
            <?php endif; ?>
        </div>
        
        <div style="margin-top: 30px; padding: 15px; background: #e9ecef; border-radius: 5px; font-size: 14px; color: #495057;">
            <strong>📝 ملاحظة:</strong> هذا الملف للتشخيص فقط. يمكن حذفه بعد التأكد من عمل النظام.
        </div>
    </div>
</body>
</html>
