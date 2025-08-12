<?php
/**
 * 🔍 فحص شامل لصحة النظام
 * نظام المحاسبة الاحترافي v2.2.1
 */

require_once 'vendor/autoload.php';

class SystemHealthCheck
{
    private $checks = [];
    private $warnings = [];
    private $errors = [];
    
    public function __construct()
    {
        $this->displayHeader();
    }
    
    private function displayHeader()
    {
        echo "\n";
        echo "🔍 ============================================== 🔍\n";
        echo "            فحص صحة النظام الشامل\n";
        echo "               نظام المحاسبة v2.2.1\n";
        echo "🔍 ============================================== 🔍\n\n";
    }
    
    public function runAllChecks()
    {
        $this->log('بدء فحص صحة النظام...', 'info');
        echo "\n";
        
        $checks = [
            'checkPHPVersion' => 'فحص إصدار PHP',
            'checkPHPExtensions' => 'فحص إضافات PHP',
            'checkFilePermissions' => 'فحص أذونات الملفات',
            'checkDatabaseConnection' => 'فحص اتصال قاعدة البيانات',
            'checkLaravelConfig' => 'فحص إعدادات Laravel',
            'checkStorageLinks' => 'فحص روابط التخزين',
            'checkAccountsData' => 'فحص بيانات الحسابات',
            'checkCurrenciesData' => 'فحص بيانات العملات',
            'checkMigrations' => 'فحص ترحيل قاعدة البيانات',
            'checkSecurity' => 'فحص إعدادات الأمان'
        ];
        
        $passed = 0;
        $total = count($checks);
        
        foreach ($checks as $method => $description) {
            $this->log("📋 $description...", 'step');
            
            $result = $this->$method();
            
            if ($result) {
                $this->log("✅ نجح: $description", 'success');
                $passed++;
            } else {
                $this->log("❌ فشل: $description", 'error');
            }
            
            echo "\n";
        }
        
        $this->displaySummary($passed, $total);
    }
    
    private function checkPHPVersion()
    {
        $minVersion = '8.1';
        $currentVersion = PHP_VERSION;
        
        if (version_compare($currentVersion, $minVersion, '>=')) {
            $this->addCheck('PHP Version', "✓ $currentVersion (المطلوب: $minVersion+)", 'success');
            return true;
        } else {
            $this->addCheck('PHP Version', "✗ $currentVersion (المطلوب: $minVersion+)", 'error');
            return false;
        }
    }
    
    private function checkPHPExtensions()
    {
        $required = [
            'pdo', 'pdo_mysql', 'mbstring', 'tokenizer', 
            'xml', 'ctype', 'json', 'bcmath', 'fileinfo', 'openssl'
        ];
        
        $missing = [];
        
        foreach ($required as $extension) {
            if (!extension_loaded($extension)) {
                $missing[] = $extension;
            }
        }
        
        if (empty($missing)) {
            $this->addCheck('PHP Extensions', '✓ جميع الإضافات المطلوبة موجودة', 'success');
            return true;
        } else {
            $this->addCheck('PHP Extensions', '✗ إضافات مفقودة: ' . implode(', ', $missing), 'error');
            return false;
        }
    }
    
    private function checkFilePermissions()
    {
        $folders = [
            'storage' => 'مجلد التخزين',
            'bootstrap/cache' => 'مجلد كاش Bootstrap',
            'public/storage' => 'رابط التخزين العام'
        ];
        
        $issues = [];
        
        foreach ($folders as $folder => $description) {
            if (!is_dir($folder)) {
                $issues[] = "$description غير موجود";
                continue;
            }
            
            if (!is_writable($folder)) {
                $issues[] = "$description غير قابل للكتابة";
            }
        }
        
        if (empty($issues)) {
            $this->addCheck('File Permissions', '✓ جميع أذونات الملفات صحيحة', 'success');
            return true;
        } else {
            $this->addCheck('File Permissions', '✗ مشاكل: ' . implode(', ', $issues), 'error');
            return false;
        }
    }
    
    private function checkDatabaseConnection()
    {
        try {
            // محاولة تحميل Laravel
            $app = require_once 'bootstrap/app.php';
            $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
            
            // اختبار اتصال قاعدة البيانات
            $pdo = DB::connection()->getPdo();
            
            if ($pdo) {
                $dbName = DB::connection()->getDatabaseName();
                $this->addCheck('Database Connection', "✓ متصل بقاعدة البيانات: $dbName", 'success');
                return true;
            }
        } catch (Exception $e) {
            $this->addCheck('Database Connection', "✗ فشل الاتصال: " . $e->getMessage(), 'error');
        }
        
        return false;
    }
    
    private function checkLaravelConfig()
    {
        try {
            if (!file_exists('.env')) {
                $this->addCheck('Laravel Config', '✗ ملف .env غير موجود', 'error');
                return false;
            }
            
            $env = file_get_contents('.env');
            $issues = [];
            
            if (!preg_match('/APP_KEY=.{32,}/', $env)) {
                $issues[] = 'مفتاح التطبيق غير موجود أو قصير';
            }
            
            if (strpos($env, 'APP_ENV=local') !== false) {
                $this->addWarning('التطبيق في وضع التطوير (local)');
            }
            
            if (strpos($env, 'APP_DEBUG=true') !== false) {
                $this->addWarning('وضع التشخيص مفعل');
            }
            
            if (empty($issues)) {
                $this->addCheck('Laravel Config', '✓ إعدادات Laravel صحيحة', 'success');
                return true;
            } else {
                $this->addCheck('Laravel Config', '✗ مشاكل: ' . implode(', ', $issues), 'error');
                return false;
            }
        } catch (Exception $e) {
            $this->addCheck('Laravel Config', '✗ خطأ في القراءة: ' . $e->getMessage(), 'error');
            return false;
        }
    }
    
    private function checkStorageLinks()
    {
        $storageLink = 'public/storage';
        
        if (is_link($storageLink) || is_dir($storageLink)) {
            $this->addCheck('Storage Links', '✓ رابط التخزين موجود', 'success');
            return true;
        } else {
            $this->addCheck('Storage Links', '✗ رابط التخزين غير موجود', 'error');
            return false;
        }
    }
    
    private function checkAccountsData()
    {
        try {
            $app = require_once 'bootstrap/app.php';
            $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
            
            $accountsCount = \App\Models\Account::count();
            $groupsCount = \App\Models\Account::where('is_group', 1)->count();
            
            if ($accountsCount > 0) {
                $this->addCheck('Accounts Data', "✓ يحتوي على $accountsCount حساب ($groupsCount مجموعة)", 'success');
                return true;
            } else {
                $this->addCheck('Accounts Data', '⚠ لا توجد حسابات (قاعدة بيانات فارغة)', 'warning');
                return true; // ليس خطأ، قد تكون قاعدة بيانات جديدة
            }
        } catch (Exception $e) {
            $this->addCheck('Accounts Data', '✗ خطأ في قراءة الحسابات: ' . $e->getMessage(), 'error');
            return false;
        }
    }
    
    private function checkCurrenciesData()
    {
        try {
            $app = require_once 'bootstrap/app.php';
            $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
            
            $currenciesCount = \App\Models\Currency::count();
            
            if ($currenciesCount > 0) {
                $defaultCurrency = \App\Models\Currency::where('is_default', 1)->first();
                $defaultName = $defaultCurrency ? $defaultCurrency->code : 'غير محدد';
                
                $this->addCheck('Currencies Data', "✓ يحتوي على $currenciesCount عملة (افتراضية: $defaultName)", 'success');
                return true;
            } else {
                $this->addCheck('Currencies Data', '⚠ لا توجد عملات', 'warning');
                return true;
            }
        } catch (Exception $e) {
            $this->addCheck('Currencies Data', '✗ خطأ في قراءة العملات: ' . $e->getMessage(), 'error');
            return false;
        }
    }
    
    private function checkMigrations()
    {
        try {
            $app = require_once 'bootstrap/app.php';
            $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
            
            // فحص آخر ترحيل
            $lastMigration = DB::table('migrations')->orderBy('id', 'desc')->first();
            
            if ($lastMigration) {
                $this->addCheck('Database Migrations', '✓ ترحيل قاعدة البيانات مكتمل', 'success');
                return true;
            } else {
                $this->addCheck('Database Migrations', '✗ لم يتم تشغيل أي ترحيل', 'error');
                return false;
            }
        } catch (Exception $e) {
            $this->addCheck('Database Migrations', '✗ خطأ في فحص الترحيل: ' . $e->getMessage(), 'error');
            return false;
        }
    }
    
    private function checkSecurity()
    {
        $issues = [];
        
        // فحص وجود ملفات حساسة
        $sensitiveFiles = ['.env', 'config/database.php'];
        
        foreach ($sensitiveFiles as $file) {
            if (file_exists("public/$file")) {
                $issues[] = "ملف حساس في مجلد public: $file";
            }
        }
        
        // فحص إعدادات الأمان في .env
        if (file_exists('.env')) {
            $env = file_get_contents('.env');
            
            if (strpos($env, 'APP_DEBUG=true') !== false && strpos($env, 'APP_ENV=production') !== false) {
                $issues[] = 'وضع التشخيص مفعل في بيئة الإنتاج';
            }
        }
        
        if (empty($issues)) {
            $this->addCheck('Security Settings', '✓ الإعدادات الأمنية سليمة', 'success');
            return true;
        } else {
            $this->addCheck('Security Settings', '⚠ تحذيرات أمنية: ' . implode(', ', $issues), 'warning');
            return true; // تحذيرات وليس أخطاء
        }
    }
    
    private function addCheck($name, $result, $status)
    {
        $this->checks[] = ['name' => $name, 'result' => $result, 'status' => $status];
        $this->log("   $result", $status);
    }
    
    private function addWarning($message)
    {
        $this->warnings[] = $message;
    }
    
    private function displaySummary($passed, $total)
    {
        echo "🎯 ============================================== 🎯\n";
        echo "                    ملخص النتائج\n";
        echo "🎯 ============================================== 🎯\n\n";
        
        $percentage = round(($passed / $total) * 100);
        
        echo "📊 النتيجة الإجمالية: $passed/$total ($percentage%)\n\n";
        
        if ($percentage >= 90) {
            $this->log('🎉 ممتاز! النظام في حالة صحية ممتازة', 'success');
        } elseif ($percentage >= 70) {
            $this->log('👍 جيد! النظام يعمل مع بعض التحسينات المقترحة', 'warning');
        } else {
            $this->log('⚠️ يحتاج إصلاح! هناك مشاكل تحتاج حل قبل الإنتاج', 'error');
        }
        
        echo "\n";
        
        if (!empty($this->warnings)) {
            echo "⚠️ تحذيرات:\n";
            foreach ($this->warnings as $warning) {
                echo "• $warning\n";
            }
            echo "\n";
        }
        
        echo "📋 تقرير مفصل:\n";
        echo "==============\n";
        foreach ($this->checks as $check) {
            $status = $check['status'];
            $icon = $status === 'success' ? '✅' : ($status === 'warning' ? '⚠️' : '❌');
            echo "$icon {$check['name']}: {$check['result']}\n";
        }
        
        echo "\n💡 نصائح:\n";
        echo "==========\n";
        echo "• قم بإصلاح الأخطاء قبل النشر في الإنتاج\n";
        echo "• راجع التحذيرات والنصائح الأمنية\n";
        echo "• اعمل نسخة احتياطية دورية من قاعدة البيانات\n";
        echo "• حدث النظام بانتظام\n\n";
    }
    
    private function log($message, $type = 'info')
    {
        $colors = [
            'info' => "\033[0;37m",
            'success' => "\033[0;32m",
            'warning' => "\033[0;33m",
            'error' => "\033[0;31m",
            'step' => "\033[1;36m",
        ];
        
        $reset = "\033[0m";
        $color = $colors[$type] ?? $colors['info'];
        
        echo $color . $message . $reset . "\n";
    }
}

// تشغيل فحص النظام
if (php_sapi_name() === 'cli') {
    $checker = new SystemHealthCheck();
    $checker->runAllChecks();
} else {
    echo "أداة فحص النظام تعمل من سطر الأوامر فقط.\n";
    echo "استخدم: php install/system_check.php\n";
}
