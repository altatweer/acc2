# إنشاء ملف install.lock يدوياً

إذا كان النظام مثبتاً بالفعل ولكن صفحة التثبيت تظهر دائماً، يجب إنشاء ملف `install.lock` يدوياً.

## الطريقة 1: عبر SSH/Terminal

```bash
# الاتصال بالسيرفر عبر SSH
ssh user@your-server.com

# الانتقال إلى مجلد المشروع
cd /path/to/your/project

# إنشاء ملف install.lock
touch storage/app/install.lock
echo "$(date '+%Y-%m-%d %H:%M:%S') - Installation completed" > storage/app/install.lock

# التأكد من الصلاحيات
chmod 644 storage/app/install.lock
```

## الطريقة 2: عبر cPanel File Manager

1. افتح cPanel File Manager
2. اذهب إلى مجلد المشروع
3. افتح مجلد `storage/app`
4. أنشئ ملف جديد باسم `install.lock`
5. أضف المحتوى: `2025-12-22 00:00:00 - Installation completed`

## الطريقة 3: عبر PHP Script

أنشئ ملف `create_lock.php` في المجلد الرئيسي:

```php
<?php
$lockPath = __DIR__ . '/storage/app/install.lock';
if (!file_exists($lockPath)) {
    if (!is_dir(__DIR__ . '/storage/app')) {
        mkdir(__DIR__ . '/storage/app', 0755, true);
    }
    file_put_contents($lockPath, date('Y-m-d H:i:s') . " - Installation completed");
    echo "تم إنشاء ملف install.lock بنجاح";
} else {
    echo "الملف موجود بالفعل";
}
```

ثم افتح: `https://your-domain.com/create_lock.php`

**ملاحظة:** احذف الملف بعد الاستخدام لأسباب أمنية.

