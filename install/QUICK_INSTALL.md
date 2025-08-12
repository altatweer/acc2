# ⚡ التثبيت السريع - نظام المحاسبة v2.2.1

## 🚀 تثبيت جديد (5 دقائق)

### 1. تحميل وإعداد الملفات
```bash
# استنساخ المشروع
git clone https://github.com/altatweer/acc2.git accounting-system
cd accounting-system

# تثبيت التبعيات
composer install --no-dev --optimize-autoloader
```

### 2. إعداد قاعدة البيانات
```bash
# نسخ وتعديل إعدادات البيئة
cp .env.example .env

# تعديل هذه الإعدادات في .env:
# DB_DATABASE=accounting_system
# DB_USERNAME=your_username  
# DB_PASSWORD=your_password
```

### 3. تشغيل المثبت التلقائي
```bash
php install/installer.php
```

### 4. الوصول للنظام
```
الرابط: http://localhost/accounting-system/public
المدير: admin@example.com
المرور: password
```

---

## 🔄 تحديث سريع (3 دقائق)

### لأنظمة موجودة:
```bash
# 1. نسخة احتياطية سريعة
php install/backup_database.php

# 2. تحديث الكود  
git pull origin main
composer install --no-dev --optimize-autoloader

# 3. تشغيل التحديث
php install/updater.php
```

---

## 🛠️ أوامر مفيدة

```bash
# مسح الكاش
php artisan cache:clear && php artisan view:clear

# إصلاح أذونات الملفات
chmod -R 755 storage bootstrap/cache

# إنشاء رابط التخزين
php artisan storage:link

# فحص حالة النظام
php artisan tinker --execute="echo 'النظام يعمل: ' . (DB::connection()->getPdo() ? 'نعم' : 'لا');"
```

---

## ❗ حل المشاكل السريع

### مشكلة الأذونات:
```bash
sudo chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache  
```

### مشكلة قاعدة البيانات:
```bash
php artisan migrate:fresh --seed
```

### مشكلة الكاش:
```bash
php artisan optimize:clear
```

---

## 📞 دعم سريع

**مشكلة عاجلة؟**  
واتساب: [رقم الدعم]  
بريد: support@altatweer.com

**موارد:**  
📖 الدليل الشامل: `install/INSTALLATION_GUIDE.md`  
🐛 تقرير مشاكل: https://github.com/altatweer/acc2/issues
