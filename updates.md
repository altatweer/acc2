# تحديثات نظام المحاسبة

تاريخ التحديث: 2024-05-10

## التحديثات المنفذة

### 1. إصلاح مشكلة الـ Route::localizedRoute
- تم استبدال جميع استخدامات `Route::localizedRoute()` بـ `route()` في ملف dashboard.blade.php
- تم حل مشكلة الخطأ "Attribute [localizedRoute] does not exist"
- تم تنظيف الكاش بعد التعديل لضمان تطبيق التغييرات

### 2. تحسينات سابقة
- تم تصحيح مشكلة المنطقة الزمنية (تغيير من UTC إلى Asia/Baghdad)
- تم إضافة ترجمات عربية مفقودة
- تم تحسين طباعة المستندات وإصلاح مشكلة ظهور الهيدر المشوه
- تم تعديل موضع التوقيعات في المستندات المطبوعة

## تفاصيل التعديلات الفنية
- تم تعديل ملف `resources/views/dashboard.blade.php` لإصلاح خطأ الـ Route::localizedRoute
- تم تنفيذ أوامر لتنظيف الكاش: `php artisan config:clear && php artisan cache:clear && php artisan view:clear` 