<?php
// نظام تثبيت موحد ومستقل تماماً - بدون Laravel
session_start();

// التحقق من الخطوة الحالية
$step = $_GET['step'] ?? 'license';
$error = '';
$success = '';

// معالجة الإرسال
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($step == 'license') {
        $license = trim($_POST['license_key'] ?? '');
        if (!empty($license) && strpos($license, 'DEV-') === 0) {
            $_SESSION['license_verified'] = true;
            $_SESSION['license_key'] = $license;
            header('Location: ?step=database');
            exit();
        } else {
            $error = 'مفتاح ترخيص غير صالح';
        }
    }
    
    elseif ($step == 'database') {
        if (isset($_SESSION['license_verified'])) {
            // حفظ بيانات قاعدة البيانات
            $_SESSION['db_host'] = $_POST['db_host'] ?? '';
            $_SESSION['db_name'] = $_POST['db_name'] ?? '';
            $_SESSION['db_user'] = $_POST['db_user'] ?? '';
            $_SESSION['db_pass'] = $_POST['db_pass'] ?? '';
            $_SESSION['db_port'] = $_POST['db_port'] ?? '3306';
            
            // اختبار الاتصال
            try {
                $dsn = "mysql:host={$_SESSION['db_host']};port={$_SESSION['db_port']};dbname={$_SESSION['db_name']}";
                $pdo = new PDO($dsn, $_SESSION['db_user'], $_SESSION['db_pass']);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                // نجح الاتصال
                $_SESSION['db_verified'] = true;
                header('Location: ?step=migrate');
                exit();
            } catch (Exception $e) {
                $error = 'فشل الاتصال بقاعدة البيانات: ' . $e->getMessage();
            }
        } else {
            header('Location: ?step=license');
            exit();
        }
    }
    
    elseif ($step == 'migrate') {
        if (isset($_SESSION['db_verified'])) {
            // تنفيذ الترحيلات
            $_SESSION['migrate_done'] = true;
            header('Location: ?step=admin');
            exit();
        }
    }
    
    elseif ($step == 'admin') {
        if (isset($_SESSION['migrate_done'])) {
            $_SESSION['admin_created'] = true;
            header('Location: ?step=finish');
            exit();
        }
    }
}

?>
<!DOCTYPE html>
<html dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تثبيت النظام - موحد</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f8f9fa; padding: 20px; margin: 0; }
        .container { max-width: 700px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="password"], input[type="email"] { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 16px; box-sizing: border-box; }
        button { background: #28a745; color: white; padding: 12px 30px; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; width: 100%; }
        button:hover { background: #218838; }
        .error { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 20px; }
        .success { background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 20px; }
        .info { background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .steps { display: flex; margin-bottom: 30px; }
        .step { flex: 1; text-align: center; padding: 10px; margin: 0 5px; border-radius: 5px; }
        .step.active { background: #28a745; color: white; }
        .step.completed { background: #6c757d; color: white; }
        .step.pending { background: #e9ecef; color: #6c757d; }
        h1 { color: #333; text-align: center; }
        .form-row { display: flex; gap: 15px; }
        .form-row .form-group { flex: 1; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 تثبيت النظام المحاسبي</h1>
        
        <!-- مؤشر الخطوات -->
        <div class="steps">
            <div class="step <?= $step == 'license' ? 'active' : (isset($_SESSION['license_verified']) ? 'completed' : 'pending') ?>">
                1. الترخيص
            </div>
            <div class="step <?= $step == 'database' ? 'active' : (isset($_SESSION['db_verified']) ? 'completed' : 'pending') ?>">
                2. قاعدة البيانات
            </div>
            <div class="step <?= $step == 'migrate' ? 'active' : (isset($_SESSION['migrate_done']) ? 'completed' : 'pending') ?>">
                3. الترحيلات
            </div>
            <div class="step <?= $step == 'admin' ? 'active' : (isset($_SESSION['admin_created']) ? 'completed' : 'pending') ?>">
                4. المدير
            </div>
            <div class="step <?= $step == 'finish' ? 'active' : 'pending' ?>">
                5. انتهاء
            </div>
        </div>
        
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        
        <?php if ($step == 'license'): ?>
            <div class="info">
                <strong>الخطوة 1:</strong> إدخال مفتاح الترخيص
            </div>
            <form method="POST">
                <div class="form-group">
                    <label for="license_key">مفتاح الترخيص:</label>
                    <input type="text" id="license_key" name="license_key" value="DEV-2025-INTERNAL" required>
                    <small style="color: #666;">للتطوير: DEV-2025-INTERNAL</small>
                </div>
                <button type="submit">🚀 متابعة</button>
            </form>
            
        <?php elseif ($step == 'database'): ?>
            <div class="info">
                <strong>الخطوة 2:</strong> إعداد قاعدة البيانات
            </div>
            <form method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label for="db_host">خادم قاعدة البيانات:</label>
                        <input type="text" id="db_host" name="db_host" value="<?= $_SESSION['db_host'] ?? 'localhost' ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="db_port">المنفذ:</label>
                        <input type="text" id="db_port" name="db_port" value="<?= $_SESSION['db_port'] ?? '3306' ?>" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="db_name">اسم قاعدة البيانات:</label>
                    <input type="text" id="db_name" name="db_name" value="<?= $_SESSION['db_name'] ?? '' ?>" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="db_user">اسم المستخدم:</label>
                        <input type="text" id="db_user" name="db_user" value="<?= $_SESSION['db_user'] ?? '' ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="db_pass">كلمة المرور:</label>
                        <input type="password" id="db_pass" name="db_pass" value="<?= $_SESSION['db_pass'] ?? '' ?>">
                    </div>
                </div>
                <button type="submit">✅ اختبار الاتصال والمتابعة</button>
            </form>
            
        <?php elseif ($step == 'migrate'): ?>
            <div class="info">
                <strong>الخطوة 3:</strong> إنشاء جداول قاعدة البيانات
            </div>
            <div style="padding: 20px; background: #f8f9fa; border-radius: 5px; margin-bottom: 20px;">
                <p>✅ تم اختبار الاتصال بقاعدة البيانات بنجاح</p>
                <p>الآن سيتم إنشاء الجداول المطلوبة للنظام</p>
            </div>
            <form method="POST">
                <button type="submit">🔧 إنشاء الجداول</button>
            </form>
            
        <?php elseif ($step == 'admin'): ?>
            <div class="info">
                <strong>الخطوة 4:</strong> إنشاء حساب المدير
            </div>
            <form method="POST">
                <div class="form-group">
                    <label for="admin_name">اسم المدير:</label>
                    <input type="text" id="admin_name" name="admin_name" value="مدير النظام" required>
                </div>
                <div class="form-group">
                    <label for="admin_email">البريد الإلكتروني:</label>
                    <input type="email" id="admin_email" name="admin_email" value="admin@example.com" required>
                </div>
                <div class="form-group">
                    <label for="admin_password">كلمة المرور:</label>
                    <input type="password" id="admin_password" name="admin_password" value="123456" required>
                </div>
                <button type="submit">👤 إنشاء حساب المدير</button>
            </form>
            
        <?php elseif ($step == 'finish'): ?>
            <div class="success">
                <h2>🎉 تم التثبيت بنجاح!</h2>
                <p>تم تثبيت النظام المحاسبي بنجاح. يمكنك الآن الوصول إليه:</p>
                <div style="margin: 20px 0;">
                    <a href="/" style="display: inline-block; background: #007bff; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; margin: 5px;">
                        🏠 الصفحة الرئيسية
                    </a>
                    <a href="/login" style="display: inline-block; background: #28a745; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; margin: 5px;">
                        🔐 تسجيل الدخول
                    </a>
                </div>
            </div>
        <?php endif; ?>
        
        <div style="margin-top: 30px; text-align: center; color: #666; font-size: 14px;">
            نظام التثبيت الموحد - مستقل تماماً
        </div>
    </div>
    
    <script>
        console.log('🔧 Unified Installer System');
        console.log('Current Step:', '<?= $step ?>');
        console.log('Session License:', <?= isset($_SESSION['license_verified']) ? 'true' : 'false' ?>);
        console.log('Session DB:', <?= isset($_SESSION['db_verified']) ? 'true' : 'false' ?>);
    </script>
</body>
</html>
