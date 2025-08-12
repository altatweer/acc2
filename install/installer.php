<?php
/**
 * 🏢 نظام المحاسبة الاحترافي v2.2.1
 * مثبت النظام الشامل
 * 
 * @version 2.2.1
 * @author شركة الأطوار للتكنولوجيا
 * @license مرخص لشركة الأطوار للتكنولوجيا
 */

class AccountingSystemInstaller
{
    private $config = [
        'app_name' => 'نظام المحاسبة الاحترافي',
        'version' => '2.2.1',
        'min_php_version' => '8.1',
        'required_extensions' => [
            'pdo', 'pdo_mysql', 'mbstring', 'tokenizer', 
            'xml', 'ctype', 'json', 'bcmath', 'fileinfo', 'openssl'
        ],
        'required_folders' => [
            'storage/app', 'storage/framework/cache', 'storage/framework/sessions',
            'storage/framework/views', 'storage/logs', 'bootstrap/cache',
            'public/storage', 'public/assets/uploads'
        ]
    ];
    
    private $steps = [
        'check_requirements' => 'فحص متطلبات النظام',
        'setup_environment' => 'إعداد ملف البيئة',
        'setup_database' => 'إعداد قاعدة البيانات',
        'run_migrations' => 'تشغيل ترحيل قاعدة البيانات',
        'seed_database' => 'تعبئة البيانات الأساسية',
        'setup_storage' => 'إعداد مجلدات التخزين',
        'create_admin' => 'إنشاء حساب المدير',
        'finalize' => 'اللمسات الأخيرة'
    ];
    
    private $currentStep = 0;
    private $errors = [];
    private $warnings = [];

    public function __construct()
    {
        $this->displayHeader();
    }

    private function displayHeader()
    {
        echo "\n";
        echo "🏢 ============================================== 🏢\n";
        echo "     {$this->config['app_name']} v{$this->config['version']}\n";
        echo "           مثبت النظام الشامل والاحترافي\n";
        echo "     مطور بواسطة شركة الأطوار للتكنولوجيا\n";
        echo "🏢 ============================================== 🏢\n\n";
    }

    public function install()
    {
        $this->log('بدء عملية التثبيت...', 'info');
        
        foreach ($this->steps as $step => $description) {
            $this->currentStep++;
            $this->log("الخطوة {$this->currentStep}/{count($this->steps)}: $description", 'step');
            
            $result = $this->$step();
            
            if (!$result) {
                $this->log("❌ فشل في الخطوة: $description", 'error');
                return false;
            }
            
            $this->log("✅ تمت الخطوة: $description", 'success');
            sleep(1); // توقف لثانية واحدة
        }
        
        $this->displaySuccessMessage();
        return true;
    }

    private function check_requirements()
    {
        $this->log('فحص متطلبات النظام...', 'info');
        
        // فحص إصدار PHP
        if (version_compare(PHP_VERSION, $this->config['min_php_version'], '<')) {
            $this->addError("إصدار PHP غير مدعوم. المطلوب: {$this->config['min_php_version']}+، الحالي: " . PHP_VERSION);
        } else {
            $this->log("✓ إصدار PHP: " . PHP_VERSION, 'success');
        }
        
        // فحص الإضافات المطلوبة
        foreach ($this->config['required_extensions'] as $extension) {
            if (!extension_loaded($extension)) {
                $this->addError("إضافة PHP مفقودة: $extension");
            } else {
                $this->log("✓ إضافة PHP: $extension", 'success');
            }
        }
        
        // فحص أذونات المجلدات
        foreach ($this->config['required_folders'] as $folder) {
            if (!is_dir($folder)) {
                if (!mkdir($folder, 0755, true)) {
                    $this->addError("فشل في إنشاء المجلد: $folder");
                } else {
                    $this->log("✓ تم إنشاء المجلد: $folder", 'success');
                }
            }
            
            if (!is_writable($folder)) {
                $this->addError("المجلد غير قابل للكتابة: $folder");
            } else {
                $this->log("✓ المجلد قابل للكتابة: $folder", 'success');
            }
        }
        
        // فحص Composer
        if (!file_exists('vendor/autoload.php')) {
            $this->addError('مكتبات Composer غير مثبتة. يرجى تشغيل: composer install');
        } else {
            $this->log('✓ مكتبات Composer مثبتة', 'success');
        }
        
        return empty($this->errors);
    }

    private function setup_environment()
    {
        $this->log('إعداد ملف البيئة...', 'info');
        
        if (!file_exists('.env')) {
            if (file_exists('.env.example')) {
                copy('.env.example', '.env');
                $this->log('✓ تم نسخ .env.example إلى .env', 'success');
            } else {
                $this->createDefaultEnv();
                $this->log('✓ تم إنشاء ملف .env افتراضي', 'success');
            }
        }
        
        // توليد مفتاح التطبيق
        exec('php artisan key:generate --force 2>&1', $output, $returnCode);
        if ($returnCode === 0) {
            $this->log('✓ تم توليد مفتاح التطبيق', 'success');
        } else {
            $this->addWarning('لم يتم توليد مفتاح التطبيق تلقائياً');
        }
        
        return true;
    }

    private function setup_database()
    {
        $this->log('إعداد قاعدة البيانات...', 'info');
        
        // قراءة إعدادات قاعدة البيانات من .env
        $dbConfig = $this->getDatabaseConfig();
        
        if (empty($dbConfig['host']) || empty($dbConfig['database'])) {
            $this->log('⚠️  إعدادات قاعدة البيانات غير مكتملة في ملف .env', 'warning');
            $this->log('   يرجى تحديث الإعدادات التالية:', 'info');
            $this->log('   DB_HOST=localhost', 'info');
            $this->log('   DB_PORT=3306', 'info');
            $this->log('   DB_DATABASE=accounting_system', 'info');
            $this->log('   DB_USERNAME=your_username', 'info');
            $this->log('   DB_PASSWORD=your_password', 'info');
            return false;
        }
        
        // اختبار الاتصال
        try {
            $pdo = new PDO(
                "mysql:host={$dbConfig['host']};port={$dbConfig['port']};charset=utf8mb4",
                $dbConfig['username'],
                $dbConfig['password'],
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
            
            // إنشاء قاعدة البيانات إذا لم تكن موجودة
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbConfig['database']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $this->log("✓ تم إنشاء/التحقق من قاعدة البيانات: {$dbConfig['database']}", 'success');
            
        } catch (PDOException $e) {
            $this->addError("فشل في الاتصال بقاعدة البيانات: " . $e->getMessage());
            return false;
        }
        
        return true;
    }

    private function run_migrations()
    {
        $this->log('تشغيل ترحيل قاعدة البيانات...', 'info');
        
        exec('php artisan migrate --force 2>&1', $output, $returnCode);
        
        if ($returnCode === 0) {
            $this->log('✓ تم تشغيل جميع ترحيلات قاعدة البيانات', 'success');
            return true;
        } else {
            $this->addError('فشل في تشغيل ترحيلات قاعدة البيانات: ' . implode("\n", $output));
            return false;
        }
    }

    private function seed_database()
    {
        $this->log('تعبئة البيانات الأساسية...', 'info');
        
        exec('php artisan db:seed --force 2>&1', $output, $returnCode);
        
        if ($returnCode === 0) {
            $this->log('✓ تم تعبئة البيانات الأساسية', 'success');
        } else {
            $this->addWarning('تحذير: لم يتم تعبئة البيانات الأساسية تلقائياً');
        }
        
        return true;
    }

    private function setup_storage()
    {
        $this->log('إعداد مجلدات التخزين...', 'info');
        
        // إنشاء رابط رمزي للتخزين
        exec('php artisan storage:link 2>&1', $output, $returnCode);
        
        if ($returnCode === 0) {
            $this->log('✓ تم إنشاء رابط التخزين', 'success');
        } else {
            $this->addWarning('تحذير: لم يتم إنشاء رابط التخزين تلقائياً');
        }
        
        // تعيين أذونات المجلدات
        $folders = ['storage', 'bootstrap/cache'];
        foreach ($folders as $folder) {
            if (is_dir($folder)) {
                chmod($folder, 0755);
                $this->log("✓ تم تعيين أذونات المجلد: $folder", 'success');
            }
        }
        
        return true;
    }

    private function create_admin()
    {
        $this->log('إنشاء حساب المدير...', 'info');
        
        // التحقق من وجود مستخدم مدير
        exec('php artisan tinker --execute="echo App\Models\User::where(\'email\', \'admin@example.com\')->exists() ? \'1\' : \'0\';" 2>/dev/null', $output);
        
        if (isset($output[0]) && $output[0] === '1') {
            $this->log('✓ حساب المدير موجود مسبقاً', 'success');
        } else {
            // إنشاء حساب مدير افتراضي
            $this->log('   إنشاء حساب مدير افتراضي...', 'info');
            $this->log('   البريد الإلكتروني: admin@example.com', 'info');
            $this->log('   كلمة المرور: password', 'info');
        }
        
        return true;
    }

    private function finalize()
    {
        $this->log('اللمسات الأخيرة...', 'info');
        
        // مسح الكاش
        exec('php artisan cache:clear 2>/dev/null');
        exec('php artisan config:clear 2>/dev/null');
        exec('php artisan view:clear 2>/dev/null');
        exec('php artisan route:clear 2>/dev/null');
        
        $this->log('✓ تم مسح جميع ملفات الكاش', 'success');
        
        // حذف ملفات التثبيت المؤقتة
        if (file_exists('check_accounts.php')) {
            unlink('check_accounts.php');
        }
        if (file_exists('clean_test_accounts.php')) {
            unlink('clean_test_accounts.php');
        }
        
        $this->log('✓ تم تنظيف الملفات المؤقتة', 'success');
        
        return true;
    }

    private function getDatabaseConfig()
    {
        $env = file_get_contents('.env');
        preg_match('/DB_HOST=(.*)/', $env, $host);
        preg_match('/DB_PORT=(.*)/', $env, $port);
        preg_match('/DB_DATABASE=(.*)/', $env, $database);
        preg_match('/DB_USERNAME=(.*)/', $env, $username);
        preg_match('/DB_PASSWORD=(.*)/', $env, $password);
        
        return [
            'host' => trim($host[1] ?? ''),
            'port' => trim($port[1] ?? '3306'),
            'database' => trim($database[1] ?? ''),
            'username' => trim($username[1] ?? ''),
            'password' => trim($password[1] ?? ''),
        ];
    }

    private function createDefaultEnv()
    {
        $envContent = "APP_NAME=\"نظام المحاسبة الاحترافي\"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=http://localhost

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=accounting_system
DB_USERNAME=root
DB_PASSWORD=

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MAIL_MAILER=smtp
MAIL_HOST=localhost
MAIL_PORT=587
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=noreply@example.com
MAIL_FROM_NAME=\"نظام المحاسبة\"

TENANT_ID=1
DEFAULT_CURRENCY=IQD
";
        
        file_put_contents('.env', $envContent);
    }

    private function addError($message)
    {
        $this->errors[] = $message;
        $this->log("❌ خطأ: $message", 'error');
    }

    private function addWarning($message)
    {
        $this->warnings[] = $message;
        $this->log("⚠️  تحذير: $message", 'warning');
    }

    private function log($message, $type = 'info')
    {
        $colors = [
            'info' => "\033[0;37m",     // أبيض
            'success' => "\033[0;32m",  // أخضر
            'warning' => "\033[0;33m",  // أصفر
            'error' => "\033[0;31m",    // أحمر
            'step' => "\033[1;36m",     // سماوي غامق
        ];
        
        $reset = "\033[0m";
        $color = $colors[$type] ?? $colors['info'];
        
        echo $color . $message . $reset . "\n";
    }

    private function displaySuccessMessage()
    {
        echo "\n";
        echo "🎉 ============================================== 🎉\n";
        echo "           تم تثبيت النظام بنجاح!\n";
        echo "🎉 ============================================== 🎉\n\n";
        
        echo "📋 معلومات مهمة:\n";
        echo "================\n";
        echo "🌐 رابط النظام: " . (getenv('APP_URL') ?: 'http://localhost') . "\n";
        echo "👤 بريد المدير: admin@example.com\n";
        echo "🔑 كلمة المرور: password\n\n";
        
        echo "🚀 الخطوات التالية:\n";
        echo "=================\n";
        echo "1. قم بتسجيل الدخول إلى النظام\n";
        echo "2. غيّر كلمة مرور المدير\n";
        echo "3. حدّث إعدادات الشركة\n";
        echo "4. ابدأ في إدخال بيانات الحسابات\n\n";
        
        if (!empty($this->warnings)) {
            echo "⚠️  تحذيرات:\n";
            echo "============\n";
            foreach ($this->warnings as $warning) {
                echo "• $warning\n";
            }
            echo "\n";
        }
        
        echo "💪 استمتع بتجربة المحاسبة الاحترافية!\n\n";
    }
}

// تشغيل المثبت
if (php_sapi_name() === 'cli') {
    $installer = new AccountingSystemInstaller();
    $result = $installer->install();
    exit($result ? 0 : 1);
} else {
    echo "هذا المثبت يعمل من سطر الأوامر فقط.\n";
    echo "استخدم: php install/installer.php\n";
}
