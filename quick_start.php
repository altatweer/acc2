<?php
/**
 * 🚀 بداية سريعة - تشخيص وتوجيه
 */

$checks = [];
$suggestions = [];

// فحص PHP
$checks['php'] = version_compare(PHP_VERSION, '8.0', '>=');
if (!$checks['php']) {
    $suggestions[] = 'قم بترقية PHP إلى الإصدار 8.0 أو أحدث';
}

// فحص المجلدات المطلوبة
$required_dirs = ['storage', 'storage/app', 'storage/framework', 'storage/logs', 'bootstrap/cache'];
$checks['directories'] = true;
foreach ($required_dirs as $dir) {
    if (!is_dir($dir) || !is_writable($dir)) {
        $checks['directories'] = false;
        $suggestions[] = "تأكد من وجود مجلد $dir وأن له صلاحيات الكتابة";
    }
}

// فحص ملف .env
$checks['env'] = file_exists('.env');
if (!$checks['env']) {
    $suggestions[] = 'أنشئ ملف .env من env.example';
}

// فحص composer
$checks['composer'] = file_exists('vendor/autoload.php');
if (!$checks['composer']) {
    $suggestions[] = 'شغّل composer install --no-dev';
}

// فحص قاعدة البيانات إذا كان .env موجود
$checks['database'] = false;
if ($checks['env']) {
    $env = file_get_contents('.env');
    preg_match('/DB_HOST=(.+)/', $env, $host);
    preg_match('/DB_DATABASE=(.+)/', $env, $database);
    preg_match('/DB_USERNAME=(.+)/', $env, $username);
    preg_match('/DB_PASSWORD=(.+)/', $env, $password);
    
    if (!empty($host[1]) && !empty($database[1]) && !empty($username[1])) {
        try {
            $dsn = "mysql:host=" . trim($host[1]) . ";dbname=" . trim($database[1]);
            $pdo = new PDO($dsn, trim($username[1]), trim($password[1] ?? ''));
            $checks['database'] = true;
        } catch (PDOException $e) {
            $suggestions[] = 'تحقق من إعدادات قاعدة البيانات في .env: ' . $e->getMessage();
        }
    }
}

$all_good = array_reduce($checks, function($carry, $check) { return $carry && $check; }, true);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بداية سريعة - نظام المحاسبة</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; }
        .card { border: none; border-radius: 20px; box-shadow: 0 20px 40px rgba(0,0,0,0.1); }
        .check-item { padding: 10px; margin: 5px 0; border-radius: 8px; }
        .check-ok { background: #d4edda; color: #155724; }
        .check-error { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white text-center">
                        <h2><i class="fas fa-tachometer-alt me-2"></i>بداية سريعة</h2>
                        <p class="mb-0">تشخيص سريع وتوجيه للتثبيت</p>
                    </div>
                    
                    <div class="card-body">
                        <h4><i class="fas fa-clipboard-check text-primary"></i> فحص النظام</h4>
                        
                        <div class="check-item <?= $checks['php'] ? 'check-ok' : 'check-error' ?>">
                            <i class="fas fa-<?= $checks['php'] ? 'check' : 'times' ?>"></i>
                            PHP <?= PHP_VERSION ?> <?= $checks['php'] ? '(مقبول)' : '(غير مقبول - مطلوب 8.0+)' ?>
                        </div>
                        
                        <div class="check-item <?= $checks['directories'] ? 'check-ok' : 'check-error' ?>">
                            <i class="fas fa-<?= $checks['directories'] ? 'check' : 'times' ?>"></i>
                            أذونات المجلدات <?= $checks['directories'] ? '(صحيحة)' : '(تحتاج إصلاح)' ?>
                        </div>
                        
                        <div class="check-item <?= $checks['env'] ? 'check-ok' : 'check-error' ?>">
                            <i class="fas fa-<?= $checks['env'] ? 'check' : 'times' ?>"></i>
                            ملف .env <?= $checks['env'] ? '(موجود)' : '(غير موجود)' ?>
                        </div>
                        
                        <div class="check-item <?= $checks['composer'] ? 'check-ok' : 'check-error' ?>">
                            <i class="fas fa-<?= $checks['composer'] ? 'check' : 'times' ?>"></i>
                            Composer <?= $checks['composer'] ? '(مثبت)' : '(غير مثبت)' ?>
                        </div>
                        
                        <div class="check-item <?= $checks['database'] ? 'check-ok' : 'check-error' ?>">
                            <i class="fas fa-<?= $checks['database'] ? 'check' : 'times' ?>"></i>
                            قاعدة البيانات <?= $checks['database'] ? '(متصلة)' : '(غير متصلة)' ?>
                        </div>
                        
                        <?php if (!empty($suggestions)): ?>
                            <div class="alert alert-warning mt-4">
                                <h6><i class="fas fa-exclamation-triangle"></i> إجراءات مطلوبة:</h6>
                                <ul class="mb-0">
                                    <?php foreach ($suggestions as $suggestion): ?>
                                        <li><?= $suggestion ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        
                        <div class="mt-4 text-center">
                            <?php if ($all_good): ?>
                                <div class="alert alert-success">
                                    <h5><i class="fas fa-check-circle"></i> النظام جاهز للتثبيت!</h5>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <a href="simple_install.php" class="btn btn-success btn-lg w-100">
                                            <i class="fas fa-rocket me-2"></i>التثبيت المبسط
                                        </a>
                                        <small class="text-muted d-block">الطريقة الأسهل والأسرع</small>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <a href="install" class="btn btn-primary btn-lg w-100">
                                            <i class="fas fa-cog me-2"></i>التثبيت التقليدي
                                        </a>
                                        <small class="text-muted d-block">عبر Laravel</small>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-warning">
                                    <h5><i class="fas fa-tools"></i> يجب إصلاح المشاكل أولاً</h5>
                                </div>
                                
                                <a href="cloud_fix.php" class="btn btn-warning btn-lg">
                                    <i class="fas fa-wrench me-2"></i>أداة الإصلاح التلقائي
                                </a>
                            <?php endif; ?>
                        </div>
                        
                        <hr>
                        
                        <div class="row text-center">
                            <div class="col-md-4">
                                <a href="cloud_debug.php" class="btn btn-outline-info w-100">
                                    <i class="fas fa-search me-2"></i>تشخيص شامل
                                </a>
                            </div>
                            <div class="col-md-4">
                                <a href="cloud_fix.php" class="btn btn-outline-warning w-100">
                                    <i class="fas fa-tools me-2"></i>إصلاح تلقائي
                                </a>
                            </div>
                            <div class="col-md-4">
                                <button onclick="location.reload()" class="btn btn-outline-secondary w-100">
                                    <i class="fas fa-redo me-2"></i>إعادة فحص
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
