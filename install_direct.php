<?php
/**
 * 🚀 مثبت مباشر للخادم السحابي - يتجاوز مشاكل Laravel routing
 * رفع هذا الملف والوصول إليه مباشرة لإكمال التثبيت
 */

// بدء الجلسة
session_start();

// إعداد التوجه العربي
echo "<!DOCTYPE html>\n<html dir='rtl' lang='ar'><head><meta charset='UTF-8'>";
echo "<title>التثبيت المباشر - نظام المحاسبة</title>";
echo "<link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>";
echo "<style>body{font-family:'Segoe UI',Tahoma,Arial;background:#f8f9fa;}";
echo ".card{border:none;box-shadow:0 4px 6px rgba(0,0,0,0.1);} .btn{border-radius:8px;}";
echo ".alert{border-radius:8px;} .form-control{border-radius:6px;}</style></head><body>";

echo "<div class='container py-5'><div class='row justify-content-center'>";
echo "<div class='col-md-8'><div class='card'>";
echo "<div class='card-header bg-primary text-white text-center'>";
echo "<h2>🚀 التثبيت المباشر للنظام</h2>";
echo "<p class='mb-0'>حل مباشر لتجاوز مشاكل التثبيت في الخادم السحابي</p>";
echo "</div><div class='card-body'>";

$step = $_GET['step'] ?? '1';
$error = '';
$success = '';

// الخطوة 1: التحقق من مفتاح الترخيص
if ($step == '1') {
    if ($_POST['license_key'] ?? '') {
        $licenseKey = $_POST['license_key'];
        
        // التحقق البسيط من مفتاح الترخيص
        if (preg_match('/^DEV-\d{4}-[A-Z0-9]{4,}$/i', $licenseKey)) {
            $_SESSION['license_key'] = $licenseKey;
            $_SESSION['license_valid'] = true;
            
            // حفظ معلومات الرخصة
            $licenseData = [
                'license_key' => $licenseKey,
                'domain' => $_SERVER['HTTP_HOST'],
                'activated_at' => date('Y-m-d H:i:s'),
                'type' => 'development'
            ];
            
            if (!is_dir('storage/app/private')) {
                mkdir('storage/app/private', 0755, true);
            }
            file_put_contents('storage/app/private/license.json', json_encode($licenseData));
            
            $success = "✅ تم التحقق من مفتاح الترخيص بنجاح";
            echo "<script>setTimeout(function(){ window.location.href='?step=2'; }, 2000);</script>";
        } else {
            $error = "❌ مفتاح الترخيص غير صحيح";
        }
    }
    
    echo "<h4>🔑 الخطوة 1: مفتاح الترخيص</h4>";
    
    if ($error) echo "<div class='alert alert-danger'>$error</div>";
    if ($success) echo "<div class='alert alert-success'>$success</div>";
    
    if (!$success) {
        echo "<form method='POST'>";
        echo "<div class='mb-3'>";
        echo "<label class='form-label'>مفتاح الترخيص:</label>";
        echo "<input type='text' name='license_key' class='form-control' value='DEV-2025-INTERNAL' required>";
        echo "<small class='text-muted'>استخدم: DEV-2025-INTERNAL للتطوير</small>";
        echo "</div>";
        echo "<button type='submit' class='btn btn-primary'>التحقق من المفتاح</button>";
        echo "</form>";
    }
}

// الخطوة 2: إعدادات قاعدة البيانات
elseif ($step == '2') {
    if ($_POST['db_host'] ?? '') {
        $dbData = [
            'DB_HOST' => $_POST['db_host'],
            'DB_DATABASE' => $_POST['db_database'],
            'DB_USERNAME' => $_POST['db_username'],
            'DB_PASSWORD' => $_POST['db_password']
        ];
        
        // اختبار الاتصال
        try {
            $dsn = "mysql:host={$dbData['DB_HOST']};dbname={$dbData['DB_DATABASE']}";
            $pdo = new PDO($dsn, $dbData['DB_USERNAME'], $dbData['DB_PASSWORD']);
            
            // تحديث ملف .env
            if (file_exists('.env')) {
                $envContent = file_get_contents('.env');
                foreach ($dbData as $key => $value) {
                    if (strpos($envContent, $key) !== false) {
                        $envContent = preg_replace("/^$key=.*/m", "$key=$value", $envContent);
                    } else {
                        $envContent .= "\n$key=$value\n";
                    }
                }
                file_put_contents('.env', $envContent);
            }
            
            $_SESSION['db_configured'] = true;
            $success = "✅ تم اختبار الاتصال بقاعدة البيانات بنجاح";
            echo "<script>setTimeout(function(){ window.location.href='?step=3'; }, 2000);</script>";
        } catch (Exception $e) {
            $error = "❌ فشل الاتصال بقاعدة البيانات: " . $e->getMessage();
        }
    }
    
    echo "<h4>🗃️ الخطوة 2: إعداد قاعدة البيانات</h4>";
    
    if ($error) echo "<div class='alert alert-danger'>$error</div>";
    if ($success) echo "<div class='alert alert-success'>$success</div>";
    
    if (!$success) {
        echo "<form method='POST'>";
        echo "<div class='row'>";
        echo "<div class='col-md-6 mb-3'>";
        echo "<label class='form-label'>خادم قاعدة البيانات:</label>";
        echo "<input type='text' name='db_host' class='form-control' value='localhost' required>";
        echo "</div>";
        echo "<div class='col-md-6 mb-3'>";
        echo "<label class='form-label'>اسم قاعدة البيانات:</label>";
        echo "<input type='text' name='db_database' class='form-control' required>";
        echo "</div>";
        echo "<div class='col-md-6 mb-3'>";
        echo "<label class='form-label'>اسم المستخدم:</label>";
        echo "<input type='text' name='db_username' class='form-control' required>";
        echo "</div>";
        echo "<div class='col-md-6 mb-3'>";
        echo "<label class='form-label'>كلمة المرور:</label>";
        echo "<input type='password' name='db_password' class='form-control'>";
        echo "</div>";
        echo "</div>";
        echo "<button type='submit' class='btn btn-primary'>اختبار الاتصال</button>";
        echo "</form>";
    }
}

// الخطوة 3: تشغيل Migrations
elseif ($step == '3') {
    echo "<h4>🔄 الخطوة 3: إنشاء جداول قاعدة البيانات</h4>";
    
    if ($_POST['run_migrations'] ?? '') {
        echo "<div class='alert alert-info'>جاري تشغيل Migrations...</div>";
        echo "<div style='background:#000;color:#0f0;padding:15px;border-radius:5px;font-family:monospace;'>";
        
        // تشغيل migrations باستخدام النظام
        $output = [];
        $return_var = 0;
        
        // محاولة تشغيل artisan migrate
        exec('php artisan migrate --force 2>&1', $output, $return_var);
        
        foreach ($output as $line) {
            echo htmlspecialchars($line) . "<br>";
        }
        
        if ($return_var === 0) {
            echo "</div><div class='alert alert-success mt-3'>✅ تم إنشاء جداول قاعدة البيانات بنجاح</div>";
            echo "<a href='?step=4' class='btn btn-success'>الانتقال للخطوة التالية</a>";
        } else {
            echo "</div><div class='alert alert-danger mt-3'>❌ فشل في تشغيل Migrations</div>";
            echo "<div class='alert alert-warning'>جرب تشغيل الأمر يدوياً في Terminal:</div>";
            echo "<code>php artisan migrate --force</code>";
        }
    } else {
        echo "<div class='alert alert-warning'>";
        echo "<strong>ملاحظة:</strong> سيتم إنشاء جداول قاعدة البيانات المطلوبة للنظام.";
        echo "</div>";
        
        echo "<form method='POST'>";
        echo "<button type='submit' name='run_migrations' value='1' class='btn btn-primary'>تشغيل إنشاء الجداول</button>";
        echo "</form>";
    }
}

// الخطوة 4: إنشاء المدير
elseif ($step == '4') {
    if ($_POST['admin_name'] ?? '') {
        // إنشاء المدير باستخدام SQL مباشر
        try {
            // قراءة إعدادات قاعدة البيانات من .env
            $envContent = file_get_contents('.env');
            preg_match('/DB_HOST=(.+)/', $envContent, $dbHost);
            preg_match('/DB_DATABASE=(.+)/', $envContent, $dbDatabase);
            preg_match('/DB_USERNAME=(.+)/', $envContent, $dbUsername);
            preg_match('/DB_PASSWORD=(.+)/', $envContent, $dbPassword);
            
            $host = trim($dbHost[1]);
            $database = trim($dbDatabase[1]);
            $username = trim($dbUsername[1]);
            $password = trim($dbPassword[1]);
            
            $dsn = "mysql:host=$host;dbname=$database";
            $pdo = new PDO($dsn, $username, $password);
            
            // إنشاء المستخدم
            $adminName = $_POST['admin_name'];
            $adminEmail = $_POST['admin_email'];
            $adminPassword = password_hash($_POST['admin_password'], PASSWORD_BCRYPT);
            $now = date('Y-m-d H:i:s');
            
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, created_at, updated_at) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$adminName, $adminEmail, $adminPassword, $now, $now]);
            
            $_SESSION['admin_created'] = true;
            $success = "✅ تم إنشاء حساب المدير بنجاح";
            echo "<script>setTimeout(function(){ window.location.href='?step=5'; }, 2000);</script>";
        } catch (Exception $e) {
            $error = "❌ فشل في إنشاء المدير: " . $e->getMessage();
        }
    }
    
    echo "<h4>👤 الخطوة 4: إنشاء حساب المدير</h4>";
    
    if ($error) echo "<div class='alert alert-danger'>$error</div>";
    if ($success) echo "<div class='alert alert-success'>$success</div>";
    
    if (!$success) {
        echo "<form method='POST'>";
        echo "<div class='mb-3'>";
        echo "<label class='form-label'>اسم المدير:</label>";
        echo "<input type='text' name='admin_name' class='form-control' required>";
        echo "</div>";
        echo "<div class='mb-3'>";
        echo "<label class='form-label'>البريد الإلكتروني:</label>";
        echo "<input type='email' name='admin_email' class='form-control' required>";
        echo "</div>";
        echo "<div class='mb-3'>";
        echo "<label class='form-label'>كلمة المرور:</label>";
        echo "<input type='password' name='admin_password' class='form-control' required minlength='6'>";
        echo "</div>";
        echo "<button type='submit' class='btn btn-primary'>إنشاء المدير</button>";
        echo "</form>";
    }
}

// الخطوة 5: اكتمال التثبيت
elseif ($step == '5') {
    echo "<h4>🎉 اكتمل التثبيت بنجاح!</h4>";
    
    // إنشاء ملف install.lock
    file_put_contents('storage/app/install.lock', 'installed');
    
    echo "<div class='alert alert-success'>";
    echo "<h5>✅ تم تثبيت النظام بنجاح!</h5>";
    echo "<p>يمكنك الآن الدخول للنظام واستخدامه.</p>";
    echo "</div>";
    
    echo "<div class='row mt-4'>";
    echo "<div class='col-md-6'>";
    echo "<div class='card bg-light'>";
    echo "<div class='card-body'>";
    echo "<h6>📊 معلومات التثبيت:</h6>";
    echo "<ul class='list-unstyled'>";
    echo "<li><strong>المفتاح:</strong> " . ($_SESSION['license_key'] ?? 'غير محدد') . "</li>";
    echo "<li><strong>النطاق:</strong> " . $_SERVER['HTTP_HOST'] . "</li>";
    echo "<li><strong>التاريخ:</strong> " . date('Y-m-d H:i:s') . "</li>";
    echo "</ul>";
    echo "</div></div></div>";
    
    echo "<div class='col-md-6'>";
    echo "<div class='card bg-light'>";
    echo "<div class='card-body'>";
    echo "<h6>🚀 الخطوات التالية:</h6>";
    echo "<ul>";
    echo "<li>اذهب لصفحة تسجيل الدخول</li>";
    echo "<li>ادخل بيانات المدير التي أنشأتها</li>";
    echo "<li>ابدأ في إعداد النظام</li>";
    echo "</ul>";
    echo "</div></div></div>";
    echo "</div>";
    
    echo "<div class='text-center mt-4'>";
    echo "<a href='/login' class='btn btn-success btn-lg'>الدخول للنظام</a>";
    echo "</div>";
    
    // تنظيف الجلسة
    session_destroy();
}

echo "</div></div></div></div>";
echo "<script src='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js'></script>";
echo "</body></html>";
?>
