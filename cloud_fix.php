<?php
/**
 * 🔧 أداة إصلاح مشاكل الخادم السحابي
 * رفع هذا الملف للخادم وتشغيله لإصلاح المشاكل التلقائية
 */

echo "<!DOCTYPE html>\n<html><head><meta charset='UTF-8'><title>إصلاح الخادم السحابي</title>";
echo "<style>body{font-family:Arial;margin:20px;direction:rtl;} .ok{color:green;} .error{color:red;} .warning{color:orange;} h2{background:#f0f0f0;padding:10px;} .action{background:#e7f3ff;padding:10px;margin:10px 0;border-left:4px solid #2196F3;}</style></head><body>";

echo "<h1>🔧 إصلاح مشاكل الخادم السحابي</h1>";

$actions = [];

// إنشاء المجلدات المطلوبة
echo "<h2>📁 إنشاء المجلدات المطلوبة</h2>";
$requiredDirs = [
    'storage',
    'storage/app',
    'storage/app/private',
    'storage/framework',
    'storage/framework/cache',
    'storage/framework/sessions',
    'storage/framework/views',
    'storage/logs',
    'bootstrap/cache',
];

foreach ($requiredDirs as $dir) {
    if (!is_dir($dir)) {
        if (mkdir($dir, 0755, true)) {
            echo "<p class='ok'>✓ تم إنشاء مجلد: $dir</p>";
            $actions[] = "إنشاء مجلد $dir";
        } else {
            echo "<p class='error'>✗ فشل في إنشاء مجلد: $dir</p>";
        }
    } else {
        echo "<p class='ok'>✓ مجلد موجود: $dir</p>";
    }
    
    // ضبط الأذونات
    if (is_dir($dir)) {
        chmod($dir, 0755);
        echo "<p class='ok'>✓ تم ضبط أذونات: $dir</p>";
    }
}

// إنشاء ملف .env إذا لم يكن موجوداً
echo "<h2>⚙️ فحص ملف .env</h2>";
if (!file_exists('.env') && file_exists('env.example')) {
    if (copy('env.example', '.env')) {
        echo "<p class='ok'>✓ تم إنشاء ملف .env من env.example</p>";
        $actions[] = "إنشاء ملف .env";
    } else {
        echo "<p class='error'>✗ فشل في إنشاء ملف .env</p>";
    }
} elseif (file_exists('.env')) {
    echo "<p class='ok'>✓ ملف .env موجود</p>";
} else {
    echo "<p class='error'>✗ ملف .env غير موجود وملف env.example غير موجود أيضاً</p>";
}

// إنشاء ملف index.php إذا لم يكن موجوداً
echo "<h2>🌐 فحص ملف index.php</h2>";
if (!file_exists('index.php') && file_exists('public/index.php')) {
    // إنشاء ملف index.php في الجذر يشير إلى public
    $indexContent = '<?php
// Redirect to public folder
$publicPath = __DIR__ . "/public";
if (is_dir($publicPath)) {
    require_once $publicPath . "/index.php";
} else {
    echo "Public folder not found";
}
';
    if (file_put_contents('index.php', $indexContent)) {
        echo "<p class='ok'>✓ تم إنشاء ملف index.php للتوجيه</p>";
        $actions[] = "إنشاء ملف index.php";
    }
} elseif (file_exists('index.php')) {
    echo "<p class='ok'>✓ ملف index.php موجود</p>";
}

// تحديث SESSION_DRIVER في .env
echo "<h2>🔐 إصلاح إعدادات Sessions</h2>";
if (file_exists('.env')) {
    $envContent = file_get_contents('.env');
    
    // تحديث SESSION_DRIVER إلى file
    if (strpos($envContent, 'SESSION_DRIVER=') !== false) {
        $envContent = preg_replace('/SESSION_DRIVER=.*/', 'SESSION_DRIVER=file', $envContent);
    } else {
        $envContent .= "\nSESSION_DRIVER=file\n";
    }
    
    // تحديث APP_DEBUG إلى true للتشخيص
    if (strpos($envContent, 'APP_DEBUG=') !== false) {
        $envContent = preg_replace('/APP_DEBUG=.*/', 'APP_DEBUG=true', $envContent);
    } else {
        $envContent .= "\nAPP_DEBUG=true\n";
    }
    
    if (file_put_contents('.env', $envContent)) {
        echo "<p class='ok'>✓ تم تحديث إعدادات .env</p>";
        $actions[] = "تحديث إعدادات sessions و debug";
    }
}

// إنشاء ملف تشخيص بسيط للتثبيت
echo "<h2>🛠️ إنشاء ملف تشخيص التثبيت</h2>";
$installTestContent = '<?php
// اختبار سريع لعملية التثبيت
echo "<!DOCTYPE html><html><head><meta charset=\"UTF-8\"><title>اختبار التثبيت</title></head><body style=\"font-family:Arial;direction:rtl;padding:20px;\">";
echo "<h1>🧪 اختبار عملية التثبيت</h1>";

// تحقق من الملفات المطلوبة
$requiredFiles = [
    "vendor/autoload.php" => "Composer Autoload",
    ".env" => "Environment File",
    "app/Services/LicenseService.php" => "License Service",
    "app/Http/Controllers/InstallController.php" => "Install Controller"
];

foreach ($requiredFiles as $file => $description) {
    if (file_exists($file)) {
        echo "<p style=\"color:green;\">✓ $description: موجود</p>";
    } else {
        echo "<p style=\"color:red;\">✗ $description: غير موجود</p>";
    }
}

// اختبار نظام الترخيص
try {
    if (file_exists("vendor/autoload.php")) {
        require_once "vendor/autoload.php";
        
        $licenseService = new \App\Services\LicenseService();
        $validation = $licenseService->validateLicenseKey("DEV-2025-INTERNAL");
        
        if ($validation["valid"]) {
            echo "<p style=\"color:green;\">✓ نظام الترخيص يعمل: " . $validation["message"] . "</p>";
        } else {
            echo "<p style=\"color:red;\">✗ مشكلة في نظام الترخيص: " . $validation["message"] . "</p>";
        }
    }
} catch (Exception $e) {
    echo "<p style=\"color:red;\">✗ خطأ في اختبار الترخيص: " . $e->getMessage() . "</p>";
}

echo "<hr><h2>🔗 روابط مفيدة</h2>";
echo "<p><a href=\"install\">صفحة التثبيت</a></p>";
echo "<p><a href=\"cloud_debug.php\">تشخيص شامل</a></p>";
echo "</body></html>";
?>';

if (file_put_contents('install_test.php', $installTestContent)) {
    echo "<p class='ok'>✓ تم إنشاء ملف install_test.php</p>";
    $actions[] = "إنشاء ملف اختبار التثبيت";
}

// إنشاء .htaccess للتوجيه إذا لم يكن موجوداً
echo "<h2>🌐 إنشاء ملف .htaccess</h2>";
if (!file_exists('.htaccess')) {
    $htaccessContent = 'RewriteEngine On

# Handle Laravel routing
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^(.*)$ public/index.php [L]

# Deny access to .env files
<Files ".env">
    Order allow,deny
    Deny from all
</Files>
';
    
    if (file_put_contents('.htaccess', $htaccessContent)) {
        echo "<p class='ok'>✓ تم إنشاء ملف .htaccess</p>";
        $actions[] = "إنشاء ملف .htaccess";
    }
} else {
    echo "<p class='ok'>✓ ملف .htaccess موجود</p>";
}

// عرض ملخص الإجراءات
echo "<h2>📋 ملخص الإجراءات المتخذة</h2>";
if (empty($actions)) {
    echo "<p class='ok'>✓ جميع الإعدادات صحيحة، لا حاجة لإجراءات</p>";
} else {
    echo "<ul>";
    foreach ($actions as $action) {
        echo "<li>$action</li>";
    }
    echo "</ul>";
}

// خطوات ما بعد الإصلاح
echo "<div class='action'>";
echo "<h3>📝 الخطوات التالية:</h3>";
echo "<ol>";
echo "<li><strong>اختبار التثبيت:</strong> <a href='install_test.php'>اضغط هنا للاختبار</a></li>";
echo "<li><strong>تشغيل Composer:</strong> في Terminal شغّل: <code>composer install --no-dev</code></li>";
echo "<li><strong>تشغيل Migrations:</strong> في Terminal شغّل: <code>php artisan migrate --force</code></li>";
echo "<li><strong>مسح Cache:</strong> في Terminal شغّل: <code>php artisan cache:clear</code></li>";
echo "<li><strong>بدء التثبيت:</strong> <a href='install'>اذهب لصفحة التثبيت</a></li>";
echo "</ol>";
echo "</div>";

echo "<hr><p style='text-align:center;color:#666;'>تم الإصلاح بواسطة أداة الإصلاح التلقائي</p>";
echo "</body></html>";
?>
