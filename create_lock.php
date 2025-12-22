<?php
// إنشاء ملف install.lock يدوياً
$lockPath = __DIR__ . '/storage/app/install.lock';

if (!is_dir(__DIR__ . '/storage/app')) {
    mkdir(__DIR__ . '/storage/app', 0755, true);
}

file_put_contents($lockPath, date('Y-m-d H:i:s') . " - Installation completed manually");

echo "تم إنشاء ملف install.lock بنجاح!<br>";
echo "يمكنك الآن حذف هذا الملف (create_lock.php) لأسباب أمنية.";
