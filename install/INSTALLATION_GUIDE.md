# 🏢 دليل التثبيت والتحديث - نظام المحاسبة الاحترافي v2.2.1

## 📋 جدول المحتويات

1. [متطلبات النظام](#متطلبات-النظام)
2. [التثبيت الجديد](#التثبيت-الجديد)
3. [تحديث النظام](#تحديث-النظام)
4. [إعداد قاعدة البيانات](#إعداد-قاعدة-البيانات)
5. [إعدادات الأمان](#إعدادات-الأمان)
6. [استكشاف الأخطاء](#استكشاف-الأخطاء)
7. [الصيانة الدورية](#الصيانة-الدورية)

---

## 🔧 متطلبات النظام

### متطلبات الخادم الأساسية:
- **PHP:** الإصدار 8.1 أو أحدث
- **MySQL:** الإصدار 8.0 أو أحدث
- **Apache/Nginx:** أي إصدار حديث
- **Composer:** لإدارة مكتبات PHP
- **Node.js & NPM:** لبناء الملفات الثابتة

### إضافات PHP المطلوبة:
```
- pdo
- pdo_mysql
- mbstring
- tokenizer
- xml
- ctype
- json
- bcmath
- fileinfo
- openssl
- zip
- gd (اختيارية - لمعالجة الصور)
```

### مساحة التخزين:
- **المساحة الأساسية:** 500MB
- **قاعدة البيانات:** 100MB (تبدأ فارغة)
- **مجلد التحميلات:** حسب الاستخدام

---

## 🚀 التثبيت الجديد

### الطريقة الأولى: التثبيت التلقائي (موصى بها)

```bash
# 1. استنساخ المشروع
git clone https://github.com/altatweer/acc2.git accounting-system
cd accounting-system

# 2. تشغيل المثبت التلقائي
php install/installer.php
```

### الطريقة الثانية: التثبيت اليدوي

#### 1. تحضير الملفات
```bash
# استنساخ المشروع
git clone https://github.com/altatweer/acc2.git accounting-system
cd accounting-system

# تثبيت مكتبات PHP
composer install --optimize-autoloader --no-dev

# تثبيت مكتبات JavaScript (اختياري)
npm install
npm run build
```

#### 2. إعداد ملف البيئة
```bash
# نسخ ملف الإعدادات
cp .env.example .env

# تعديل إعدادات قاعدة البيانات
nano .env
```

**إعدادات قاعدة البيانات في .env:**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=accounting_system
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

#### 3. إعداد Laravel
```bash
# توليد مفتاح التطبيق
php artisan key:generate

# تشغيل ترحيل قاعدة البيانات
php artisan migrate

# تعبئة البيانات الأساسية
php artisan db:seed

# ربط مجلد التخزين
php artisan storage:link
```

#### 4. تعيين الأذونات
```bash
# Linux/macOS
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# أو حسب المستخدم
sudo chown -R $USER:$USER storage bootstrap/cache
```

---

## 🔄 تحديث النظام

### التحديث التلقائي (موصى به)
```bash
# الانتقال لمجلد المشروع
cd /path/to/accounting-system

# تشغيل أداة التحديث
php install/updater.php
```

### التحديث اليدوي

#### 1. إنشاء نسخة احتياطية
```bash
# نسخة احتياطية من قاعدة البيانات
mysqldump -u username -p database_name > backup_$(date +%Y%m%d_%H%M%S).sql

# نسخة احتياطية من الملفات
tar -czf backup_files_$(date +%Y%m%d_%H%M%S).tar.gz .
```

#### 2. تحديث الكود
```bash
# سحب أحدث التحديثات
git pull origin main

# تحديث المكتبات
composer install --optimize-autoloader --no-dev
npm install && npm run build
```

#### 3. تحديث قاعدة البيانات
```bash
# تشغيل ترحيلات قاعدة البيانات الجديدة
php artisan migrate

# مسح الكاش
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

---

## 🗄️ إعداد قاعدة البيانات

### إنشاء قاعدة البيانات
```sql
-- اتصل بـ MySQL كمدير
mysql -u root -p

-- إنشاء قاعدة البيانات
CREATE DATABASE accounting_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- إنشاء مستخدم (اختياري)
CREATE USER 'acc_user'@'localhost' IDENTIFIED BY 'strong_password';
GRANT ALL PRIVILEGES ON accounting_system.* TO 'acc_user'@'localhost';
FLUSH PRIVILEGES;
```

### استيراد بيانات جاهزة
```bash
# إذا كان لديك ملف SQL جاهز
mysql -u username -p accounting_system < database_dump.sql
```

---

## 🔒 إعدادات الأمان

### 1. إعدادات الخادم
```apache
# Apache - إضافة إلى .htaccess
<Files ".env">
    Require all denied
</Files>

<Files "*.php">
    <RequireAll>
        Require all granted
        Require not ip 192.0.2.0/24
    </RequireAll>
</Files>
```

### 2. إعدادات قاعدة البيانات
- استخدم كلمات مرور قوية
- قم بتقييد الوصول للمنافذ
- فعّل SSL/TLS للاتصالات

### 3. إعدادات Laravel
```env
# في .env للإنتاج
APP_ENV=production
APP_DEBUG=false
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
```

---

## 🔧 إعداد الخادم

### Apache
```apache
<VirtualHost *:80>
    ServerName accounting.example.com
    DocumentRoot /path/to/accounting-system/public
    
    <Directory /path/to/accounting-system/public>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/accounting_error.log
    CustomLog ${APACHE_LOG_DIR}/accounting_access.log combined
</VirtualHost>
```

### Nginx
```nginx
server {
    listen 80;
    server_name accounting.example.com;
    root /path/to/accounting-system/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

---

## ❗ استكشاف الأخطاء

### مشاكل شائعة وحلولها:

#### 1. خطأ "Class not found"
```bash
# تحديث الـ autoloader
composer dump-autoload
```

#### 2. خطأ أذونات الملفات
```bash
# إصلاح أذونات storage
chmod -R 775 storage bootstrap/cache
```

#### 3. خطأ اتصال قاعدة البيانات
- تحقق من إعدادات .env
- تأكد من تشغيل خدمة MySQL
- تحقق من صحة بيانات الاتصال

#### 4. مشاكل العملات والحسابات
```bash
# إعادة تشغيل ترحيل العملات
php artisan migrate:fresh --seed
```

### فحص صحة النظام:
```bash
# فحص حالة النظام
php artisan inspire  # للتأكد من عمل Laravel

# فحص اتصال قاعدة البيانات
php artisan tinker
>>> DB::connection()->getPdo()

# فحص الأذونات
ls -la storage/ bootstrap/cache/
```

---

## 🔄 الصيانة الدورية

### يومياً:
```bash
# نسخة احتياطية من قاعدة البيانات
./backup_daily.sh
```

### أسبوعياً:
```bash
# مسح اللوجات القديمة
php artisan log:clear

# تحسين قاعدة البيانات
php artisan optimize:clear
php artisan optimize
```

### شهرياً:
```bash
# تحديث المكتبات
composer update
npm update

# فحص الأمان
php artisan security:check
```

---

## 📞 الدعم والمساعدة

### الدعم التقني:
- **البريد الإلكتروني:** support@altatweer.com
- **الموقع:** https://altatweer.com
- **GitHub Issues:** https://github.com/altatweer/acc2/issues

### موارد إضافية:
- **دليل المستخدم:** `/docs/user_guide.md`
- **دليل المطور:** `/docs/developer_guide.md`
- **سجل التغييرات:** `/CHANGELOG.md`

---

## 📝 ملاحظات مهمة

1. **قم دائماً بإنشاء نسخة احتياطية قبل أي تحديث**
2. **اختبر التحديثات في بيئة التطوير أولاً**
3. **راجع سجل التغييرات قبل التحديث**
4. **حافظ على تحديث PHP ومكتباته**
5. **فعّل HTTPS في بيئة الإنتاج**

---

<div align="center">

**🏢 نظام المحاسبة الاحترافي v2.2.1**  
*مطور بواسطة شركة الأطوار للتكنولوجيا*

[![التحديث](https://img.shields.io/badge/آخر_تحديث-2025--01--23-blue.svg)](https://github.com/altatweer/acc2)
[![الإصدار](https://img.shields.io/badge/الإصدار-2.2.1-green.svg)](https://github.com/altatweer/acc2/releases)
[![الدعم](https://img.shields.io/badge/الدعم-نشط-brightgreen.svg)](mailto:support@altatweer.com)

</div>
