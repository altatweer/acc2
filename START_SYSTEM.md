# 🚀 دليل تشغيل نظام المحاسبة الاحترافي v2.2.1

## ⚡ تشغيل سريع

### 1. تشغيل النظام للمرة الأولى
```bash
# إذا كان النظام جديد - قم بالتثبيت
php install/installer.php

# بدء تشغيل الخادم المحلي
php artisan serve --host=127.0.0.1 --port=8000

# أو للوصول من الشبكة المحلية
php artisan serve --host=0.0.0.0 --port=8000
```

### 2. الوصول للنظام
```
🌐 الرابط: http://localhost:8000
👤 المدير: admin@example.com  
🔑 كلمة المرور: password
```

## 🔧 إعدادات أساسية

### بعد تسجيل الدخول:

1. **غيّر كلمة مرور المدير:**
   - اذهب إلى: الإعدادات → الملف الشخصي
   - استخدم كلمة مرور قوية

2. **حدّث بيانات الشركة:**
   - اذهب إلى: الإعدادات → بيانات الشركة
   - أدخل اسم الشركة والشعار والعنوان

3. **تأكد من العملات:**
   - اذهب إلى: الإعدادات → العملات
   - تأكد من وجود العملة الافتراضية (IQD)

4. **راجع شجرة الحسابات:**
   - اذهب إلى: الحسابات → عرض الحسابات
   - أضف حسابات جديدة حسب الحاجة

## 🏢 إعداد بيئة الإنتاج

### لخادم Apache:
```apache
<VirtualHost *:80>
    ServerName your-domain.com
    DocumentRoot /path/to/accounting-system/public
    
    <Directory /path/to/accounting-system/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

### لخادم Nginx:
```nginx
server {
    listen 80;
    server_name your-domain.com;
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

### إعدادات بيئة الإنتاج في .env:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# قاعدة البيانات الفعلية
DB_DATABASE=your_production_db
DB_USERNAME=your_db_user
DB_PASSWORD=your_secure_password
```

## 🛠️ أوامر الصيانة

### صيانة يومية:
```bash
# فحص صحة النظام
php install/system_check.php

# نسخة احتياطية من قاعدة البيانات
php install/backup_database.php
```

### صيانة أسبوعية:
```bash
# مسح الكاش
php artisan cache:clear
php artisan view:clear
php artisan config:clear

# تحسين الأداء
php artisan optimize
```

### صيانة شهرية:
```bash
# تحديث المكتبات
composer update --no-dev

# فحص الأمان
php artisan config:cache
php artisan route:cache
```

## 🔍 مراقبة النظام

### سجلات مهمة:
- **سجل النظام:** `storage/logs/laravel.log`
- **سجل Apache:** `/var/log/apache2/error.log`
- **سجل قاعدة البيانات:** `/var/log/mysql/error.log`

### مراقبة الأداء:
```bash
# مراقبة استخدام القرص الصلب
df -h

# مراقبة استخدام الذاكرة
free -h

# مراقبة العمليات
htop
```

## 🚨 التعامل مع الطوارئ

### إذا توقف النظام:
```bash
# 1. فحص سجلات الأخطاء
tail -f storage/logs/laravel.log

# 2. إعادة تشغيل الخدمات
sudo systemctl restart apache2
sudo systemctl restart mysql

# 3. فحص صحة النظام
php install/system_check.php
```

### استعادة من نسخة احتياطية:
```bash
# عرض النسخ المتاحة
php install/backup_database.php --list

# استعادة من نسخة احتياطية
mysql -u username -p database_name < install/backups/backup_file.sql
```

## 📱 الوصول من الهاتف المحمول

النظام متوافق مع الهواتف المحمولة:
- ✅ تصميم متجاوب
- ✅ يعمل على جميع المتصفحات
- ✅ سرعة تحميل محسنة

## 🔐 الأمان والحماية

### نصائح أمنية:
1. **استخدم HTTPS في الإنتاج**
2. **غيّر كلمات المرور الافتراضية**
3. **حدّث النظام باستمرار**
4. **اعمل نسخ احتياطية دورية**
5. **راقب سجلات الدخول**

### تفعيل HTTPS:
```bash
# استخدم Let's Encrypt (مجاني)
sudo certbot --apache -d your-domain.com
```

## 🎓 التدريب والدعم

### موارد التعلم:
- **دليل المستخدم:** `docs/user_guide.md`
- **دليل الإدارة:** `docs/admin_guide.md`
- **فيديوهات تعليمية:** [قريباً]

### الدعم الفني:
- **البريد:** support@altatweer.com
- **الهاتف:** [رقم الدعم]
- **ساعات الدعم:** 9 صباحاً - 5 مساءً

## 📈 إضافة ميزات جديدة

النظام قابل للتوسع والتطوير. لإضافة ميزات جديدة:

1. راجع `docs/developer_guide.md`
2. اتصل بفريق التطوير
3. طلب تطوير مخصص

## 🎉 مبروك!

تم إعداد نظام المحاسبة بنجاح! 🎊

الآن يمكنك:
- ✅ إدارة الحسابات والمعاملات
- ✅ إنشاء الفواتير والسندات  
- ✅ إدارة المرتبات والموظفين
- ✅ إنتاج التقارير المالية
- ✅ تصدير وطباعة المستندات

---

<div align="center">

**🏢 نظام المحاسبة الاحترافي**  
*النجاح يبدأ بمحاسبة دقيقة*

**📞 للدعم: support@altatweer.com**

</div>
