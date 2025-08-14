<?php
// حل مباشر - بدون Laravel routing تماماً
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $license = trim($_POST['license_key'] ?? '');
    
    if (!empty($license) && strpos($license, 'DEV-') === 0) {
        $_SESSION['license_verified'] = true;
        $_SESSION['license_key'] = $license;
        
        // انتقال مباشر
        header('Location: /install/database');
        exit();
    } else {
        $error = 'مفتاح ترخيص غير صالح';
    }
}
?>
<!DOCTYPE html>
<html dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تثبيت النظام - مباشر</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f8f9fa; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"] { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 16px; }
        button { background: #28a745; color: white; padding: 12px 30px; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; width: 100%; }
        button:hover { background: #218838; }
        .error { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 20px; }
        .info { background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 تثبيت النظام - مباشر</h1>
        
        <div class="info">
            <strong>حل مباشر:</strong> هذا الملف يعمل مباشرة بدون Laravel routing
        </div>
        
        <?php if (isset($error)): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="license_key">مفتاح الترخيص:</label>
                <input type="text" id="license_key" name="license_key" value="DEV-2025-INTERNAL" required>
            </div>
            <button type="submit">🚀 متابعة التثبيت</button>
        </form>
        
        <div style="margin-top: 20px; font-size: 14px; color: #666;">
            <strong>للتطوير:</strong> DEV-2025-INTERNAL<br>
            <strong>للاختبار:</strong> DEV-2025-TESTING
        </div>
    </div>
    
    <script>
        console.log('🔧 Direct Install System');
        console.log('Current URL:', window.location.href);
        console.log('Form method: POST, no JavaScript interference');
    </script>
</body>
</html>
