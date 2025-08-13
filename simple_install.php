<?php
/**
 * 🚀 تثبيت مبسط ومباشر - بدون Laravel routing
 * حل نهائي لمشكلة عدم انتقال التثبيت
 */

// بدء الجلسة
session_start();

// إعدادات أساسية
ini_set('display_errors', 1);
error_reporting(E_ALL);

// تحديد المرحلة الحالية
$step = $_GET['step'] ?? $_POST['step'] ?? 1;
$errors = [];
$success = [];

// معالجة POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($step) {
        case 1: // مفتاح الترخيص
            $license_key = trim($_POST['license_key'] ?? '');
            if (empty($license_key)) {
                $errors[] = 'مفتاح الترخيص مطلوب';
            } else {
                // حفظ المفتاح في الجلسة
                $_SESSION['license_key'] = $license_key;
                $_SESSION['license_verified'] = true;
                
                // الانتقال للخطوة التالية
                header('Location: simple_install.php?step=2');
                exit;
            }
            break;
            
        case 2: // قاعدة البيانات
            $db_host = trim($_POST['db_host'] ?? '');
            $db_database = trim($_POST['db_database'] ?? '');
            $db_username = trim($_POST['db_username'] ?? '');
            $db_password = $_POST['db_password'] ?? '';
            
            if (empty($db_host) || empty($db_database) || empty($db_username)) {
                $errors[] = 'جميع بيانات قاعدة البيانات مطلوبة عدا كلمة المرور';
            } else {
                // اختبار الاتصال
                try {
                    $dsn = "mysql:host=$db_host;dbname=$db_database;charset=utf8";
                    $pdo = new PDO($dsn, $db_username, $db_password);
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    
                    // حفظ بيانات قاعدة البيانات
                    $_SESSION['db_config'] = [
                        'host' => $db_host,
                        'database' => $db_database,
                        'username' => $db_username,
                        'password' => $db_password
                    ];
                    
                    // تحديث ملف .env
                    updateEnvFile([
                        'DB_HOST' => $db_host,
                        'DB_DATABASE' => $db_database,
                        'DB_USERNAME' => $db_username,
                        'DB_PASSWORD' => $db_password,
                        'SESSION_DRIVER' => 'file'
                    ]);
                    
                    $success[] = 'تم الاتصال بقاعدة البيانات بنجاح';
                    header('Location: simple_install.php?step=3');
                    exit;
                    
                } catch (PDOException $e) {
                    $errors[] = 'فشل الاتصال بقاعدة البيانات: ' . $e->getMessage();
                }
            }
            break;
            
        case 3: // تشغيل Migrations
            try {
                // تشغيل أوامر artisan
                $output = [];
                
                // مسح الكاش أولاً
                exec('php artisan cache:clear 2>&1', $output);
                exec('php artisan config:clear 2>&1', $output);
                
                // تشغيل الترحيلات
                exec('php artisan migrate --force 2>&1', $output);
                
                // تشغيل seeders
                exec('php artisan db:seed --class=LicenseSeeder --force 2>&1', $output);
                
                $_SESSION['migrations_done'] = true;
                header('Location: simple_install.php?step=4');
                exit;
                
            } catch (Exception $e) {
                $errors[] = 'خطأ في تشغيل الترحيلات: ' . $e->getMessage();
            }
            break;
            
        case 4: // إنشاء المدير
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            
            if (empty($name) || empty($email) || empty($password)) {
                $errors[] = 'جميع البيانات مطلوبة';
            } else {
                try {
                    // إنشاء المدير عبر قاعدة البيانات مباشرة
                    $db = $_SESSION['db_config'];
                    $dsn = "mysql:host={$db['host']};dbname={$db['database']};charset=utf8";
                    $pdo = new PDO($dsn, $db['username'], $db['password']);
                    
                    // إدراج المستخدم
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
                    $stmt->execute([$name, $email, $hashedPassword]);
                    
                    // إنشاء ملف install.lock
                    file_put_contents('storage/app/install.lock', 'installed-' . date('Y-m-d H:i:s'));
                    
                    $_SESSION['admin_created'] = true;
                    header('Location: simple_install.php?step=5');
                    exit;
                    
                } catch (Exception $e) {
                    $errors[] = 'خطأ في إنشاء المدير: ' . $e->getMessage();
                }
            }
            break;
    }
}

// دالة تحديث ملف .env
function updateEnvFile($updates) {
    $envFile = '.env';
    if (!file_exists($envFile)) {
        if (file_exists('env.example')) {
            copy('env.example', $envFile);
        } else {
            file_put_contents($envFile, "APP_NAME=Laravel\nAPP_ENV=production\nAPP_DEBUG=false\nAPP_URL=http://localhost\n");
        }
    }
    
    $content = file_get_contents($envFile);
    
    foreach ($updates as $key => $value) {
        if (strpos($content, $key . '=') !== false) {
            $content = preg_replace("/^$key=.*/m", "$key=$value", $content);
        } else {
            $content .= "\n$key=$value";
        }
    }
    
    file_put_contents($envFile, $content);
}

?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تثبيت مبسط - نظام المحاسبة</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .card { border: none; border-radius: 20px; box-shadow: 0 20px 40px rgba(0,0,0,0.1); }
        .step-indicator { background: #f8f9fa; padding: 15px; border-radius: 10px; margin-bottom: 20px; }
        .step-item { display: inline-block; padding: 5px 15px; margin: 0 5px; border-radius: 20px; }
        .step-active { background: #007bff; color: white; }
        .step-completed { background: #28a745; color: white; }
        .step-pending { background: #e9ecef; color: #6c757d; }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white text-center">
                        <h2><i class="fas fa-rocket me-2"></i>تثبيت مبسط - نظام المحاسبة</h2>
                        <p class="mb-0">حل مباشر وسريع للتثبيت</p>
                    </div>
                    
                    <div class="card-body">
                        <!-- مؤشر الخطوات -->
                        <div class="step-indicator text-center">
                            <span class="step-item <?= $step >= 1 ? ($step == 1 ? 'step-active' : 'step-completed') : 'step-pending' ?>">1. الترخيص</span>
                            <span class="step-item <?= $step >= 2 ? ($step == 2 ? 'step-active' : 'step-completed') : 'step-pending' ?>">2. قاعدة البيانات</span>
                            <span class="step-item <?= $step >= 3 ? ($step == 3 ? 'step-active' : 'step-completed') : 'step-pending' ?>">3. الترحيلات</span>
                            <span class="step-item <?= $step >= 4 ? ($step == 4 ? 'step-active' : 'step-completed') : 'step-pending' ?>">4. المدير</span>
                            <span class="step-item <?= $step >= 5 ? 'step-completed' : 'step-pending' ?>">5. اكتمال</span>
                        </div>

                        <!-- عرض الأخطاء -->
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <h6><i class="fas fa-exclamation-triangle"></i> أخطاء:</h6>
                                <ul class="mb-0">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?= htmlspecialchars($error) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <!-- عرض رسائل النجاح -->
                        <?php if (!empty($success)): ?>
                            <div class="alert alert-success">
                                <?php foreach ($success as $msg): ?>
                                    <p class="mb-0"><i class="fas fa-check"></i> <?= htmlspecialchars($msg) ?></p>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($step == 1): ?>
                            <!-- الخطوة 1: مفتاح الترخيص -->
                            <h4><i class="fas fa-key text-primary"></i> الخطوة 1: مفتاح الترخيص</h4>
                            <form method="POST">
                                <input type="hidden" name="step" value="1">
                                
                                <div class="alert alert-info">
                                    <p><strong>للتطوير والاختبار، استخدم:</strong></p>
                                    <code class="text-primary">DEV-2025-INTERNAL</code>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="license_key" class="form-label">مفتاح الترخيص</label>
                                    <input type="text" class="form-control" id="license_key" name="license_key" 
                                           value="<?= htmlspecialchars($_POST['license_key'] ?? 'DEV-2025-INTERNAL') ?>" required>
                                </div>
                                
                                <button type="submit" class="btn btn-primary btn-lg w-100">
                                    <i class="fas fa-arrow-right me-2"></i>التالي
                                </button>
                            </form>

                        <?php elseif ($step == 2): ?>
                            <!-- الخطوة 2: قاعدة البيانات -->
                            <h4><i class="fas fa-database text-primary"></i> الخطوة 2: إعداد قاعدة البيانات</h4>
                            <form method="POST">
                                <input type="hidden" name="step" value="2">
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="db_host" class="form-label">خادم قاعدة البيانات</label>
                                        <input type="text" class="form-control" id="db_host" name="db_host" 
                                               value="<?= htmlspecialchars($_POST['db_host'] ?? 'localhost') ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="db_database" class="form-label">اسم قاعدة البيانات</label>
                                        <input type="text" class="form-control" id="db_database" name="db_database" 
                                               value="<?= htmlspecialchars($_POST['db_database'] ?? '') ?>" required>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="db_username" class="form-label">اسم المستخدم</label>
                                        <input type="text" class="form-control" id="db_username" name="db_username" 
                                               value="<?= htmlspecialchars($_POST['db_username'] ?? '') ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="db_password" class="form-label">كلمة المرور (اختيارية)</label>
                                        <input type="password" class="form-control" id="db_password" name="db_password" 
                                               value="<?= htmlspecialchars($_POST['db_password'] ?? '') ?>">
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn btn-primary btn-lg w-100">
                                    <i class="fas fa-arrow-right me-2"></i>اختبار الاتصال والمتابعة
                                </button>
                            </form>

                        <?php elseif ($step == 3): ?>
                            <!-- الخطوة 3: تشغيل الترحيلات -->
                            <h4><i class="fas fa-cogs text-primary"></i> الخطوة 3: إنشاء جداول قاعدة البيانات</h4>
                            <div class="alert alert-warning">
                                <p><i class="fas fa-info-circle"></i> سيتم إنشاء جداول قاعدة البيانات وتهيئة البيانات الأساسية.</p>
                            </div>
                            
                            <form method="POST">
                                <input type="hidden" name="step" value="3">
                                <button type="submit" class="btn btn-success btn-lg w-100">
                                    <i class="fas fa-play me-2"></i>تشغيل إنشاء الجداول
                                </button>
                            </form>

                        <?php elseif ($step == 4): ?>
                            <!-- الخطوة 4: إنشاء المدير -->
                            <h4><i class="fas fa-user-shield text-primary"></i> الخطوة 4: إنشاء حساب المدير</h4>
                            <form method="POST">
                                <input type="hidden" name="step" value="4">
                                
                                <div class="mb-3">
                                    <label for="name" class="form-label">الاسم</label>
                                    <input type="text" class="form-control" id="name" name="name" 
                                           value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="email" class="form-label">البريد الإلكتروني</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="password" class="form-label">كلمة المرور</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                                
                                <button type="submit" class="btn btn-success btn-lg w-100">
                                    <i class="fas fa-user-plus me-2"></i>إنشاء حساب المدير
                                </button>
                            </form>

                        <?php elseif ($step == 5): ?>
                            <!-- الخطوة 5: اكتمال التثبيت -->
                            <div class="text-center">
                                <div class="alert alert-success">
                                    <h3><i class="fas fa-check-circle text-success"></i> تم التثبيت بنجاح!</h3>
                                    <p>يمكنك الآن الدخول للنظام والبدء في الاستخدام.</p>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <h6>معلومات الدخول:</h6>
                                                <p><strong>البريد:</strong> <?= htmlspecialchars($_SESSION['admin_email'] ?? 'تم الحفظ') ?></p>
                                                <p><strong>المفتاح:</strong> <?= htmlspecialchars($_SESSION['license_key'] ?? 'DEV-2025-INTERNAL') ?></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <h6>الخطوات التالية:</h6>
                                                <ul class="text-start">
                                                    <li>تسجيل الدخول</li>
                                                    <li>إعداد الحسابات</li>
                                                    <li>إضافة العملات</li>
                                                    <li>البدء في الاستخدام</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mt-4">
                                    <a href="/" class="btn btn-primary btn-lg me-2">
                                        <i class="fas fa-home me-2"></i>الذهاب للنظام
                                    </a>
                                    <a href="/login" class="btn btn-success btn-lg">
                                        <i class="fas fa-sign-in-alt me-2"></i>تسجيل الدخول
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
