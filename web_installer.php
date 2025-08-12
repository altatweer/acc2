<?php
/**
 * 🌐 Web Installer للنظام المحاسبي AurSuite v2.2.1
 * مثبت شامل يعمل من المتصفح مباشرة
 */

// منع الوصول إذا كان النظام مثبت مسبقاً
if (file_exists('storage/app/install.lock')) {
    header('Location: /');
    exit('تم تثبيت النظام بالفعل. <a href="/">انتقل للنظام</a>');
}

$step = $_GET['step'] ?? 'welcome';
$error = $_GET['error'] ?? '';
$success = $_GET['success'] ?? '';

?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مثبت نظام AurSuite للمحاسبة</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            min-height: 100vh; 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .installer-container {
            max-width: 900px;
            margin: 40px auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .installer-header {
            background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .installer-body { padding: 40px; }
        .step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
        }
        .step {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 10px;
            position: relative;
        }
        .step.active {
            background: #3498db;
            color: white;
        }
        .step.completed {
            background: #27ae60;
            color: white;
        }
        .step::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 60px;
            width: 40px;
            height: 2px;
            background: #e9ecef;
            z-index: -1;
        }
        .step:last-child::after { display: none; }
        .requirement-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            margin-bottom: 10px;
        }
        .requirement-ok { border-color: #27ae60; background: #d4edda; }
        .requirement-error { border-color: #e74c3c; background: #f8d7da; }
        .requirement-warning { border-color: #f39c12; background: #fff3cd; }
        .btn-primary {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
        }
        .btn-success {
            background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);
            border: none;
        }
        .card { border: none; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .card-header { 
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 12px 12px 0 0 !important;
        }
        .progress { height: 25px; border-radius: 12px; }
        .form-control, .form-select {
            border-radius: 8px;
            border: 2px solid #e9ecef;
            padding: 12px;
        }
        .form-control:focus, .form-select:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }
    </style>
</head>
<body>
    <div class="installer-container">
        <div class="installer-header">
            <h1><i class="fas fa-rocket me-3"></i>AurSuite للمحاسبة</h1>
            <p class="mb-0">نظام محاسبة احترافي شامل - الإصدار 2.2.1</p>
        </div>
        
        <div class="installer-body">
            <!-- Step Indicator -->
            <div class="step-indicator">
                <div class="step <?= $step == 'welcome' ? 'active' : ($step != 'welcome' ? 'completed' : '') ?>">1</div>
                <div class="step <?= $step == 'requirements' ? 'active' : (in_array($step, ['database', 'admin', 'final']) ? 'completed' : '') ?>">2</div>
                <div class="step <?= $step == 'database' ? 'active' : (in_array($step, ['admin', 'final']) ? 'completed' : '') ?>">3</div>
                <div class="step <?= $step == 'admin' ? 'active' : ($step == 'final' ? 'completed' : '') ?>">4</div>
                <div class="step <?= $step == 'final' ? 'active' : '' ?>">5</div>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success"><i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <?php if ($step == 'welcome'): ?>
                <!-- Welcome Step -->
                <div class="text-center">
                    <i class="fas fa-chart-line text-primary" style="font-size: 4rem; margin-bottom: 20px;"></i>
                    <h2 class="mb-4">مرحباً بك في نظام AurSuite للمحاسبة</h2>
                    <p class="lead text-muted mb-4">
                        نظام محاسبة شامل ومتطور يدعم العملات المتعددة ويوفر جميع احتياجاتك المحاسبية
                    </p>
                    
                    <div class="row text-start mb-4">
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5><i class="fas fa-star text-warning me-2"></i>الميزات الرئيسية</h5>
                                    <ul class="list-unstyled">
                                        <li><i class="fas fa-check text-success me-2"></i>شجرة الحسابات الشاملة</li>
                                        <li><i class="fas fa-check text-success me-2"></i>الفواتير والمبيعات</li>
                                        <li><i class="fas fa-check text-success me-2"></i>إدارة المخزون</li>
                                        <li><i class="fas fa-check text-success me-2"></i>كشوف الرواتب</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5><i class="fas fa-globe text-info me-2"></i>دعم العملات المتعددة</h5>
                                    <ul class="list-unstyled">
                                        <li><i class="fas fa-check text-success me-2"></i>أسعار صرف تلقائية</li>
                                        <li><i class="fas fa-check text-success me-2"></i>تقارير متعددة العملات</li>
                                        <li><i class="fas fa-check text-success me-2"></i>محاسبة دقيقة</li>
                                        <li><i class="fas fa-check text-success me-2"></i>تحويلات العملات</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <a href="?step=requirements" class="btn btn-primary btn-lg">
                        <i class="fas fa-rocket me-2"></i>ابدأ التثبيت
                    </a>
                </div>

            <?php elseif ($step == 'requirements'): ?>
                <!-- Requirements Check -->
                <h2 class="text-center mb-4"><i class="fas fa-tools me-2"></i>فحص متطلبات النظام</h2>
                
                <div id="requirements-container">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">جاري الفحص...</span>
                        </div>
                        <p class="mt-3">جاري فحص متطلبات النظام...</p>
                    </div>
                </div>

                <script>
                // Auto-check requirements
                setTimeout(() => {
                    fetch('web_installer_ajax.php?action=check_requirements')
                        .then(response => response.json())
                        .then(data => {
                            document.getElementById('requirements-container').innerHTML = data.html;
                            if (data.can_continue) {
                                document.getElementById('continue-btn').style.display = 'block';
                            }
                        })
                        .catch(error => {
                            document.getElementById('requirements-container').innerHTML = 
                                '<div class="alert alert-danger">خطأ في فحص المتطلبات: ' + error.message + '</div>';
                        });
                }, 1000);
                </script>

                <div class="text-center mt-4">
                    <a href="?step=database" class="btn btn-success btn-lg" id="continue-btn" style="display: none;">
                        <i class="fas fa-arrow-left me-2"></i>متابعة إعداد قاعدة البيانات
                    </a>
                </div>

            <?php elseif ($step == 'database'): ?>
                <!-- Database Configuration -->
                <h2 class="text-center mb-4"><i class="fas fa-database me-2"></i>إعداد قاعدة البيانات</h2>
                
                <form method="POST" action="web_installer_ajax.php" id="database-form">
                    <input type="hidden" name="action" value="setup_database">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><i class="fas fa-server me-2"></i>عنوان الخادم</label>
                                <input type="text" name="db_host" class="form-control" value="localhost" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><i class="fas fa-database me-2"></i>اسم قاعدة البيانات</label>
                                <input type="text" name="db_name" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><i class="fas fa-user me-2"></i>اسم المستخدم</label>
                                <input type="text" name="db_user" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><i class="fas fa-lock me-2"></i>كلمة المرور</label>
                                <input type="password" name="db_password" class="form-control">
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-check me-2"></i>اختبار الاتصال والمتابعة
                        </button>
                    </div>
                </form>

                <script>
                document.getElementById('database-form').addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const formData = new FormData(this);
                    const btn = this.querySelector('button[type="submit"]');
                    const originalText = btn.innerHTML;
                    
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>جاري الاختبار...';
                    btn.disabled = true;
                    
                    fetch('web_installer_ajax.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.href = '?step=admin&success=' + encodeURIComponent(data.message);
                        } else {
                            window.location.href = '?step=database&error=' + encodeURIComponent(data.message);
                        }
                    })
                    .catch(error => {
                        btn.innerHTML = originalText;
                        btn.disabled = false;
                        alert('خطأ: ' + error.message);
                    });
                });
                </script>

            <?php elseif ($step == 'admin'): ?>
                <!-- Admin User Creation -->
                <h2 class="text-center mb-4"><i class="fas fa-user-shield me-2"></i>إنشاء حساب المدير</h2>
                
                <form method="POST" action="web_installer_ajax.php" id="admin-form">
                    <input type="hidden" name="action" value="create_admin">
                    
                    <div class="row justify-content-center">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label"><i class="fas fa-user me-2"></i>الاسم الكامل</label>
                                <input type="text" name="admin_name" class="form-control" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label"><i class="fas fa-envelope me-2"></i>البريد الإلكتروني</label>
                                <input type="email" name="admin_email" class="form-control" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label"><i class="fas fa-lock me-2"></i>كلمة المرور</label>
                                <input type="password" name="admin_password" class="form-control" required minlength="6">
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label"><i class="fas fa-lock me-2"></i>تأكيد كلمة المرور</label>
                                <input type="password" name="admin_password_confirmation" class="form-control" required minlength="6">
                            </div>
                            
                            <div class="text-center">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fas fa-user-plus me-2"></i>إنشاء الحساب والمتابعة
                                </button>
                            </div>
                        </div>
                    </div>
                </form>

                <script>
                document.getElementById('admin-form').addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const password = this.admin_password.value;
                    const confirmation = this.admin_password_confirmation.value;
                    
                    if (password !== confirmation) {
                        alert('كلمة المرور وتأكيدها غير متطابقتين');
                        return;
                    }
                    
                    const formData = new FormData(this);
                    const btn = this.querySelector('button[type="submit"]');
                    const originalText = btn.innerHTML;
                    
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>جاري الإنشاء...';
                    btn.disabled = true;
                    
                    fetch('web_installer_ajax.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.href = '?step=final&success=' + encodeURIComponent(data.message);
                        } else {
                            window.location.href = '?step=admin&error=' + encodeURIComponent(data.message);
                        }
                    })
                    .catch(error => {
                        btn.innerHTML = originalText;
                        btn.disabled = false;
                        alert('خطأ: ' + error.message);
                    });
                });
                </script>

            <?php elseif ($step == 'final'): ?>
                <!-- Installation Complete -->
                <div class="text-center">
                    <div class="mb-4">
                        <i class="fas fa-check-circle text-success" style="font-size: 5rem;"></i>
                    </div>
                    
                    <h2 class="text-success mb-4">تم تثبيت النظام بنجاح!</h2>
                    
                    <div class="alert alert-success text-start">
                        <h5><i class="fas fa-info-circle me-2"></i>معلومات مهمة:</h5>
                        <ul class="mb-0">
                            <li>تم تثبيت جميع الجداول والبيانات الأساسية</li>
                            <li>تم إنشاء حساب المدير</li>
                            <li>تم تفعيل العملات الأساسية</li>
                            <li>تم إنشاء شجرة الحسابات المحاسبية</li>
                            <li>النظام جاهز للاستخدام الآن</li>
                        </ul>
                    </div>
                    
                    <div class="alert alert-warning text-start">
                        <h6><i class="fas fa-shield-alt me-2"></i>نصائح أمنية:</h6>
                        <ul class="mb-0">
                            <li>احذف ملف <code>web_installer.php</code> من الخادم</li>
                            <li>احذف ملف <code>web_installer_ajax.php</code> من الخادم</li>
                            <li>قم بتغيير كلمة مرور المدير من الإعدادات</li>
                        </ul>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                        <a href="/" class="btn btn-success btn-lg me-md-2">
                            <i class="fas fa-sign-in-alt me-2"></i>دخول النظام
                        </a>
                        <button onclick="deleteInstallerFiles()" class="btn btn-danger btn-lg">
                            <i class="fas fa-trash me-2"></i>حذف ملفات التثبيت
                        </button>
                    </div>
                </div>

                <script>
                function deleteInstallerFiles() {
                    if (confirm('هل أنت متأكد من حذف ملفات التثبيت؟ لن تتمكن من إعادة تشغيل المثبت مرة أخرى.')) {
                        fetch('web_installer_ajax.php?action=delete_installer')
                            .then(response => response.json())
                            .then(data => {
                                alert(data.message);
                                if (data.success) {
                                    window.location.href = '/';
                                }
                            });
                    }
                }
                </script>

            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
