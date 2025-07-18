# تشخيص مشكلة الكاش - دليل شامل

## الخطوات للتشخيص:

### 1. تحقق من وجود ملف .env:
```bash
ls -la .env
```

### 2. تحقق من محتويات ملف .env:
```bash
cat .env | grep MULTI_TENANCY
```

### 3. تحقق من القيم بدون كاش:
```bash
php artisan config:clear
php artisan tinker
# داخل tinker:
config('app.multi_tenancy_enabled')
```

### 4. تحقق من القيم مع الكاش:
```bash
php artisan config:cache
php artisan tinker
# داخل tinker:
config('app.multi_tenancy_enabled')
```

## الحلول المحتملة:

### الحل 1: تأكد من ملف .env
```bash
# تأكد من وجود هذه الأسطر في ملف .env:
MULTI_TENANCY_ENABLED=true
TENANT_DEFAULT_ID=1
```

### الحل 2: تأكد من عدم وجود مسافات
```bash
# صحيح:
MULTI_TENANCY_ENABLED=true

# خطأ:
MULTI_TENANCY_ENABLED = true
MULTI_TENANCY_ENABLED=True
MULTI_TENANCY_ENABLED="true"
```

### الحل 3: تأكد من ترميز الملف
```bash
# تأكد من أن ملف .env مرمز بـ UTF-8
# وليس به أحرف خاصة أو مسافات في النهاية
```

## الاختبار النهائي:

```bash
# بعد أي تعديل على .env:
php artisan config:clear
php artisan config:cache
php artisan tinker

# داخل tinker:
config('app.multi_tenancy_enabled')  # يجب أن يكون true
```

## إذا استمرت المشكلة:
- استخدم النظام بدون كاش (يعمل بشكل مثالي)
- أو أنشئ ملف .env جديد من البداية 