<?php
/**
 * 🛠️ أداة تشخيص مشاكل الخادم السحابي
 * رفع هذا الملف للخادم وتشغيله لمعرفة المشاكل
 */

echo "<!DOCTYPE html>\n<html><head><meta charset='UTF-8'><title>تشخيص الخادم السحابي</title>";
echo "<style>body{font-family:Arial;margin:20px;direction:rtl;} .ok{color:green;} .error{color:red;} .warning{color:orange;} h2{background:#f0f0f0;padding:10px;}</style></head><body>";

echo "<h1>🛠️ تشخيص مشاكل الخادم السحابي</h1>";

// معلومات أساسية
echo "<h2>📊 معلومات أساسية</h2>";
echo "<p><strong>PHP Version:</strong> " . PHP_VERSION . "</p>";
echo "<p><strong>Server:</strong> " . $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown' . "</p>";
echo "<p><strong>Document Root:</strong> " . $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown' . "</p>";
echo "<p><strong>Current Directory:</strong> " . __DIR__ . "</p>";

// فحص أذونات المجلدات المهمة
echo "<h2>📁 فحص أذونات المجلدات</h2>";
$directories = [
    'storage' => 'storage',
    'storage/app' => 'storage/app',
    'storage/app/private' => 'storage/app/private',
    'storage/framework' => 'storage/framework',
    'storage/framework/sessions' => 'storage/framework/sessions',
    'storage/logs' => 'storage/logs',
    'bootstrap/cache' => 'bootstrap/cache',
];

foreach ($directories as $name => $path) {
    if (is_dir($path)) {
        $perms = substr(sprintf('%o', fileperms($path)), -4);
        $writable = is_writable($path);
        $class = $writable ? 'ok' : 'error';
        echo "<p class='$class'>✓ $name: موجود، أذونات: $perms، قابل للكتابة: " . ($writable ? 'نعم' : 'لا') . "</p>";
    } else {
        echo "<p class='error'>✗ $name: غير موجود</p>";
        
        // محاولة إنشاء المجلد
        if (mkdir($path, 0755, true)) {
            echo "<p class='ok'>✓ تم إنشاء $name بنجاح</p>";
        } else {
            echo "<p class='error'>✗ فشل في إنشاء $name</p>";
        }
    }
}

// فحص ملف .env
echo "<h2>⚙️ فحص ملف .env</h2>";
if (file_exists('.env')) {
    echo "<p class='ok'>✓ ملف .env موجود</p>";
    
    // فحص إعدادات مهمة
    $envContent = file_get_contents('.env');
    $envLines = explode("\n", $envContent);
    $envSettings = [];
    
    foreach ($envLines as $line) {
        if (strpos($line, '=') !== false && !str_starts_with(trim($line), '#')) {
            [$key, $value] = explode('=', $line, 2);
            $envSettings[trim($key)] = trim($value);
        }
    }
    
    $importantSettings = [
        'APP_ENV' => 'بيئة التطبيق',
        'APP_DEBUG' => 'وضع التطبيق',
        'APP_URL' => 'رابط التطبيق',
        'DB_CONNECTION' => 'نوع قاعدة البيانات',
        'DB_HOST' => 'خادم قاعدة البيانات',
        'DB_DATABASE' => 'اسم قاعدة البيانات',
        'SESSION_DRIVER' => 'طريقة حفظ الجلسات',
    ];
    
    foreach ($importantSettings as $key => $description) {
        $value = $envSettings[$key] ?? 'غير محدد';
        echo "<p>• <strong>$description ($key):</strong> $value</p>";
    }
} else {
    echo "<p class='error'>✗ ملف .env غير موجود</p>";
}

// فحص قاعدة البيانات
echo "<h2>🗃️ فحص قاعدة البيانات</h2>";
if (file_exists('.env')) {
    try {
        // قراءة إعدادات قاعدة البيانات من .env
        $envContent = file_get_contents('.env');
        preg_match('/DB_HOST=(.+)/', $envContent, $dbHost);
        preg_match('/DB_DATABASE=(.+)/', $envContent, $dbDatabase);
        preg_match('/DB_USERNAME=(.+)/', $envContent, $dbUsername);
        preg_match('/DB_PASSWORD=(.+)/', $envContent, $dbPassword);
        
        if (!empty($dbHost[1])) {
            $host = trim($dbHost[1]);
            $database = trim($dbDatabase[1] ?? '');
            $username = trim($dbUsername[1] ?? '');
            $password = trim($dbPassword[1] ?? '');
            
            $dsn = "mysql:host=$host;dbname=$database";
            $pdo = new PDO($dsn, $username, $password);
            echo "<p class='ok'>✓ الاتصال بقاعدة البيانات نجح</p>";
            
            // فحص جدول licenses
            $stmt = $pdo->query("SHOW TABLES LIKE 'licenses'");
            if ($stmt->rowCount() > 0) {
                echo "<p class='ok'>✓ جدول licenses موجود</p>";
                
                $stmt = $pdo->query("SELECT COUNT(*) as count FROM licenses");
                $count = $stmt->fetch()['count'];
                echo "<p class='ok'>✓ عدد الرخص في الجدول: $count</p>";
            } else {
                echo "<p class='warning'>⚠️ جدول licenses غير موجود - يحتاج تشغيل migrations</p>";
            }
            
        } else {
            echo "<p class='error'>✗ إعدادات قاعدة البيانات غير مكتملة في .env</p>";
        }
    } catch (Exception $e) {
        echo "<p class='error'>✗ فشل الاتصال بقاعدة البيانات: " . $e->getMessage() . "</p>";
    }
}

// فحص إضافات PHP المطلوبة
echo "<h2>🔧 فحص إضافات PHP</h2>";
$requiredExtensions = [
    'pdo' => 'PDO',
    'pdo_mysql' => 'PDO MySQL',
    'mbstring' => 'Multibyte String',
    'openssl' => 'OpenSSL',
    'fileinfo' => 'File Info',
    'json' => 'JSON',
    'tokenizer' => 'Tokenizer',
    'xml' => 'XML',
    'ctype' => 'Character Type',
    'bcmath' => 'BC Math',
];

foreach ($requiredExtensions as $ext => $name) {
    $loaded = extension_loaded($ext);
    $class = $loaded ? 'ok' : 'error';
    echo "<p class='$class'>" . ($loaded ? '✓' : '✗') . " $name ($ext)</p>";
}

// فحص composer وautoload
echo "<h2>📦 فحص Composer</h2>";
if (file_exists('vendor/autoload.php')) {
    echo "<p class='ok'>✓ ملف vendor/autoload.php موجود</p>";
} else {
    echo "<p class='error'>✗ ملف vendor/autoload.php غير موجود - يحتاج تشغيل composer install</p>";
}

if (file_exists('composer.json')) {
    echo "<p class='ok'>✓ ملف composer.json موجود</p>";
} else {
    echo "<p class='error'>✗ ملف composer.json غير موجود</p>";
}

// اختبار كتابة ملف
echo "<h2>✍️ اختبار كتابة الملفات</h2>";
$testFile = 'storage/app/test_write.txt';
if (is_dir('storage/app')) {
    if (file_put_contents($testFile, 'test content')) {
        echo "<p class='ok'>✓ يمكن كتابة الملفات في storage/app</p>";
        unlink($testFile); // حذف الملف التجريبي
    } else {
        echo "<p class='error'>✗ لا يمكن كتابة الملفات في storage/app</p>";
    }
} else {
    echo "<p class='error'>✗ مجلد storage/app غير موجود</p>";
}

// اختبار sessions
echo "<h2>🔐 اختبار Sessions</h2>";
if (!session_id()) {
    session_start();
}
$_SESSION['test'] = 'working';
if (isset($_SESSION['test']) && $_SESSION['test'] === 'working') {
    echo "<p class='ok'>✓ Sessions تعمل بشكل صحيح</p>";
} else {
    echo "<p class='error'>✗ مشكلة في Sessions</p>";
}

// نصائح لحل المشاكل
echo "<h2>💡 نصائح لحل المشاكل</h2>";
echo "<ul>";
echo "<li><strong>أذونات المجلدات:</strong> تأكد أن مجلدات storage و bootstrap/cache لها أذونات 755 أو 777</li>";
echo "<li><strong>قاعدة البيانات:</strong> تأكد من صحة بيانات الاتصال في ملف .env</li>";
echo "<li><strong>Composer:</strong> شغّل 'composer install --no-dev' في Terminal</li>";
echo "<li><strong>Migrations:</strong> شغّل 'php artisan migrate --force' لإنشاء الجداول</li>";
echo "<li><strong>Cache:</strong> شغّل 'php artisan cache:clear' و 'php artisan config:clear'</li>";
echo "<li><strong>تفعيل Debug:</strong> اضبط APP_DEBUG=true في .env لرؤية الأخطاء</li>";
echo "</ul>";

echo "<hr><p style='text-align:center;color:#666;'>تم إنشاؤه بواسطة نظام التشخيص التلقائي</p>";
echo "</body></html>";
?>
