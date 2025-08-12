# 🛠️ مجلد أدوات التثبيت والصيانة

هذا المجلد يحتوي على جميع الأدوات المطلوبة لتثبيت وصيانة وتحديث نظام المحاسبة الاحترافي v2.2.1.

## 📁 محتويات المجلد

### 🚀 أدوات التثبيت الرئيسية
| الملف | الوصف | الاستخدام |
|-------|--------|----------|
| `installer.php` | مثبت النظام التلقائي الشامل | `php install/installer.php` |
| `updater.php` | أداة التحديث التلقائية | `php install/updater.php` |
| `system_check.php` | فحص شامل لصحة النظام | `php install/system_check.php` |

### 💾 أدوات النسخ الاحتياطي
| الملف | الوصف | الاستخدام |
|-------|--------|----------|
| `backup_database.php` | نسخة احتياطية سريعة من قاعدة البيانات | `php install/backup_database.php` |
| `backup_database.php --list` | عرض النسخ الاحتياطية المتاحة | `php install/backup_database.php --list` |

### 📖 أدلة التثبيت
| الملف | الوصف |
|-------|--------|
| `INSTALLATION_GUIDE.md` | دليل التثبيت الشامل والمفصل |
| `QUICK_INSTALL.md` | دليل التثبيت السريع (5 دقائق) |
| `README.md` | هذا الملف |

### 📂 مجلدات إضافية
- `backups/` - مجلد النسخ الاحتياطية التلقائية
- `database/` - ملفات قاعدة البيانات الإضافية
- `config/` - ملفات إعدادات إضافية
- `scripts/` - سكريبتات مساعدة
- `assets/` - ملفات ثابتة للتثبيت

## 🚀 التثبيت السريع

### للمستخدمين الجدد:
```bash
php install/installer.php
```

### لتحديث نسخة موجودة:
```bash
php install/updater.php
```

### للتحقق من صحة النظام:
```bash
php install/system_check.php
```

## 🆘 استكشاف الأخطاء

### مشكلة في الأذونات؟
```bash
chmod +x install/*.php
sudo chown -R www-data:www-data storage bootstrap/cache
```

### مشكلة في قاعدة البيانات؟
```bash
php install/system_check.php
php artisan migrate:fresh --seed
```

### مشكلة في الإعدادات؟
```bash
cp .env.example .env
php artisan key:generate
```

## 📞 الحصول على المساعدة

إذا واجهت أي مشاكل:

1. **اقرأ الدليل الشامل:** `install/INSTALLATION_GUIDE.md`
2. **شغل فحص النظام:** `php install/system_check.php`
3. **راجع سجل الأخطاء:** `storage/logs/laravel.log`
4. **اتصل بالدعم:** support@altatweer.com

## ⚡ نصائح سريعة

- 🔄 **قبل أي تحديث:** اعمل نسخة احتياطية بـ `php install/backup_database.php`
- 🔍 **بعد كل تغيير:** شغل `php install/system_check.php`
- 🧹 **للصيانة الدورية:** احذف النسخ الاحتياطية القديمة من مجلد `backups/`
- 📊 **لمراقبة الأداء:** راجع ملفات logs في `storage/logs/`

## 🎯 الدعم والمساهمة

هذه الأدوات مطورة ومحدثة باستمرار. للمساهمة أو الإبلاغ عن مشاكل:

- **GitHub:** https://github.com/altatweer/acc2
- **البريد:** support@altatweer.com
- **الوثائق:** https://docs.altatweer.com

---

<div align="center">

**🏢 نظام المحاسبة الاحترافي v2.2.1**  
*مطور بواسطة شركة الأطوار للتكنولوجيا*

</div>
