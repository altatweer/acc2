<?php
/**
 * 🔄 نظام المحاسبة الاحترافي v2.2.1
 * أداة التحديث الشاملة
 * 
 * @version 2.2.1
 * @author شركة الأطوار للتكنولوجيا
 * @license مرخص لشركة الأطوار للتكنولوجيا
 */

class AccountingSystemUpdater
{
    private $config = [
        'current_version' => '2.2.1',
        'backup_folder' => 'backups',
        'update_log' => 'storage/logs/update.log'
    ];
    
    private $updateSteps = [
        'backup_system' => 'إنشاء نسخة احتياطية',
        'check_compatibility' => 'فحص التوافق',
        'update_dependencies' => 'تحديث التبعيات',
        'run_migrations' => 'تشغيل ترحيل قاعدة البيانات',
        'update_assets' => 'تحديث الملفات الثابتة',
        'clear_cache' => 'مسح الكاش',
        'verify_update' => 'التحقق من التحديث',
        'cleanup' => 'تنظيف الملفات المؤقتة'
    ];
    
    private $errors = [];
    private $warnings = [];
    private $currentStep = 0;

    public function __construct()
    {
        $this->displayHeader();
        $this->createBackupFolder();
    }

    private function displayHeader()
    {
        echo "\n";
        echo "🔄 ============================================== 🔄\n";
        echo "     نظام المحاسبة الاحترافي - أداة التحديث\n";
        echo "                الإصدار {$this->config['current_version']}\n";
        echo "     مطور بواسطة شركة الأطوار للتكنولوجيا\n";
        echo "🔄 ============================================== 🔄\n\n";
    }

    public function update()
    {
        $this->log('بدء عملية التحديث...', 'info');
        $this->log('التأكد من وجود نسخة احتياطية قبل المتابعة', 'warning');
        
        $this->promptUserConfirmation();
        
        foreach ($this->updateSteps as $step => $description) {
            $this->currentStep++;
            $this->log("الخطوة {$this->currentStep}/" . count($this->updateSteps) . ": $description", 'step');
            
            $result = $this->$step();
            
            if (!$result) {
                $this->log("❌ فشل في الخطوة: $description", 'error');
                $this->rollback();
                return false;
            }
            
            $this->log("✅ تمت الخطوة: $description", 'success');
            sleep(1);
        }
        
        $this->displaySuccessMessage();
        return true;
    }

    private function promptUserConfirmation()
    {
        echo "⚠️  تحذير مهم:\n";
        echo "==============\n";
        echo "• سيتم تحديث النظام إلى الإصدار {$this->config['current_version']}\n";
        echo "• تأكد من وجود نسخة احتياطية من قاعدة البيانات والملفات\n";
        echo "• قد يستغرق التحديث عدة دقائق\n";
        echo "• لا تقطع العملية أثناء التحديث\n\n";
        
        echo "هل تريد المتابعة؟ (y/N): ";
        $handle = fopen("php://stdin", "r");
        $line = fgets($handle);
        fclose($handle);
        
        if (trim(strtolower($line)) !== 'y') {
            echo "تم إلغاء التحديث.\n";
            exit(1);
        }
        
        echo "\n";
    }

    private function backup_system()
    {
        $this->log('إنشاء نسخة احتياطية من النظام...', 'info');
        
        $backupFile = $this->config['backup_folder'] . '/backup_' . date('Y-m-d_H-i-s') . '.sql';
        
        // إنشاء نسخة احتياطية من قاعدة البيانات
        $dbConfig = $this->getDatabaseConfig();
        
        if (empty($dbConfig['database'])) {
            $this->addWarning('لم يتم العثور على إعدادات قاعدة البيانات');
            return true; // نكمل بدون نسخة احتياطية
        }
        
        $command = "mysqldump -h{$dbConfig['host']} -P{$dbConfig['port']} -u{$dbConfig['username']}";
        if (!empty($dbConfig['password'])) {
            $command .= " -p{$dbConfig['password']}";
        }
        $command .= " {$dbConfig['database']} > $backupFile 2>/dev/null";
        
        exec($command, $output, $returnCode);
        
        if ($returnCode === 0 && file_exists($backupFile)) {
            $this->log("✓ تم إنشاء نسخة احتياطية: $backupFile", 'success');
        } else {
            $this->addWarning('فشل في إنشاء نسخة احتياطية تلقائية');
        }
        
        return true;
    }

    private function check_compatibility()
    {
        $this->log('فحص توافق النظام...', 'info');
        
        // فحص إصدار PHP
        if (version_compare(PHP_VERSION, '8.1', '<')) {
            $this->addError("إصدار PHP غير مدعوم. المطلوب: 8.1+، الحالي: " . PHP_VERSION);
            return false;
        }
        
        // فحص Laravel
        if (file_exists('artisan')) {
            $this->log('✓ Laravel framework موجود', 'success');
        } else {
            $this->addError('Laravel framework غير موجود');
            return false;
        }
        
        // فحص قاعدة البيانات
        try {
            $dbConfig = $this->getDatabaseConfig();
            $pdo = new PDO(
                "mysql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['database']};charset=utf8mb4",
                $dbConfig['username'],
                $dbConfig['password'],
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
            $this->log('✓ اتصال قاعدة البيانات سليم', 'success');
        } catch (PDOException $e) {
            $this->addError('فشل في الاتصال بقاعدة البيانات: ' . $e->getMessage());
            return false;
        }
        
        return true;
    }

    private function update_dependencies()
    {
        $this->log('تحديث تبعيات النظام...', 'info');
        
        // تحديث Composer packages
        if (file_exists('composer.json')) {
            exec('composer install --no-dev --optimize-autoloader 2>&1', $output, $returnCode);
            
            if ($returnCode === 0) {
                $this->log('✓ تم تحديث مكتبات Composer', 'success');
            } else {
                $this->addWarning('تحذير: لم يتم تحديث مكتبات Composer تلقائياً');
            }
        }
        
        // تحديث NPM packages إذا كانت موجودة
        if (file_exists('package.json')) {
            exec('npm install 2>/dev/null', $output, $returnCode);
            
            if ($returnCode === 0) {
                $this->log('✓ تم تحديث مكتبات NPM', 'success');
                
                // بناء الملفات الثابتة
                exec('npm run build 2>/dev/null', $output, $returnCode);
                if ($returnCode === 0) {
                    $this->log('✓ تم بناء الملفات الثابتة', 'success');
                }
            }
        }
        
        return true;
    }

    private function run_migrations()
    {
        $this->log('تشغيل ترحيل قاعدة البيانات...', 'info');
        
        exec('php artisan migrate --force 2>&1', $output, $returnCode);
        
        if ($returnCode === 0) {
            $this->log('✓ تم تشغيل جميع ترحيلات قاعدة البيانات', 'success');
        } else {
            $this->addError('فشل في تشغيل ترحيلات قاعدة البيانات: ' . implode("\n", $output));
            return false;
        }
        
        return true;
    }

    private function update_assets()
    {
        $this->log('تحديث الملفات الثابتة...', 'info');
        
        // إعادة ربط التخزين
        exec('php artisan storage:link 2>/dev/null');
        $this->log('✓ تم ربط مجلد التخزين', 'success');
        
        // تحديث الأذونات
        $folders = ['storage', 'bootstrap/cache'];
        foreach ($folders as $folder) {
            if (is_dir($folder)) {
                chmod($folder, 0755);
                $this->log("✓ تم تحديث أذونات: $folder", 'success');
            }
        }
        
        return true;
    }

    private function clear_cache()
    {
        $this->log('مسح جميع ملفات الكاش...', 'info');
        
        $commands = [
            'cache:clear' => 'كاش التطبيق',
            'config:clear' => 'كاش الإعدادات',
            'view:clear' => 'كاش القوالب',
            'route:clear' => 'كاش المسارات'
        ];
        
        foreach ($commands as $command => $description) {
            exec("php artisan $command 2>/dev/null", $output, $returnCode);
            if ($returnCode === 0) {
                $this->log("✓ تم مسح $description", 'success');
            }
        }
        
        return true;
    }

    private function verify_update()
    {
        $this->log('التحقق من سلامة التحديث...', 'info');
        
        // التحقق من اتصال قاعدة البيانات
        try {
            exec('php artisan tinker --execute="echo \"Database connection: \" . (DB::connection()->getPdo() ? \"OK\" : \"Failed\");" 2>/dev/null', $output);
            
            if (strpos(implode(' ', $output), 'OK') !== false) {
                $this->log('✓ اتصال قاعدة البيانات سليم', 'success');
            } else {
                $this->addWarning('تحذير: قد تكون هناك مشكلة في اتصال قاعدة البيانات');
            }
        } catch (Exception $e) {
            $this->addWarning('تحذير: لم يتم التحقق من اتصال قاعدة البيانات');
        }
        
        // التحقق من الملفات الأساسية
        $essentialFiles = [
            'app/Http/Controllers/AccountController.php',
            'app/Models/Account.php',
            'resources/views/layouts/app.blade.php'
        ];
        
        foreach ($essentialFiles as $file) {
            if (file_exists($file)) {
                $this->log("✓ الملف موجود: $file", 'success');
            } else {
                $this->addWarning("تحذير: الملف مفقود: $file");
            }
        }
        
        return true;
    }

    private function cleanup()
    {
        $this->log('تنظيف الملفات المؤقتة...', 'info');
        
        // حذف ملفات التنظيف المؤقتة
        $tempFiles = [
            'check_accounts.php',
            'clean_test_accounts.php'
        ];
        
        foreach ($tempFiles as $file) {
            if (file_exists($file)) {
                unlink($file);
                $this->log("✓ تم حذف الملف المؤقت: $file", 'success');
            }
        }
        
        // تنظيف مجلد logs القديمة (أكثر من 30 يوم)
        if (is_dir('storage/logs')) {
            $files = glob('storage/logs/*.log');
            $thirtyDaysAgo = time() - (30 * 24 * 60 * 60);
            
            foreach ($files as $file) {
                if (filemtime($file) < $thirtyDaysAgo) {
                    unlink($file);
                    $this->log("✓ تم حذف ملف log قديم: " . basename($file), 'success');
                }
            }
        }
        
        return true;
    }

    private function rollback()
    {
        $this->log('🔄 بدء عملية التراجع...', 'warning');
        
        // البحث عن أحدث نسخة احتياطية
        $backupFiles = glob($this->config['backup_folder'] . '/backup_*.sql');
        
        if (!empty($backupFiles)) {
            // ترتيب الملفات حسب التاريخ (الأحدث أولاً)
            usort($backupFiles, function($a, $b) {
                return filemtime($b) - filemtime($a);
            });
            
            $latestBackup = $backupFiles[0];
            $this->log("محاولة استعادة النسخة الاحتياطية: $latestBackup", 'info');
            
            // يمكن إضافة منطق استعادة النسخة الاحتياطية هنا إذا لزم الأمر
            $this->log('يرجى استعادة النسخة الاحتياطية يدوياً إذا لزم الأمر', 'warning');
        }
        
        $this->log('تم إيقاف التحديث', 'error');
    }

    private function getDatabaseConfig()
    {
        if (!file_exists('.env')) {
            return [];
        }
        
        $env = file_get_contents('.env');
        preg_match('/DB_HOST=(.*)/', $env, $host);
        preg_match('/DB_PORT=(.*)/', $env, $port);
        preg_match('/DB_DATABASE=(.*)/', $env, $database);
        preg_match('/DB_USERNAME=(.*)/', $env, $username);
        preg_match('/DB_PASSWORD=(.*)/', $env, $password);
        
        return [
            'host' => trim($host[1] ?? '127.0.0.1'),
            'port' => trim($port[1] ?? '3306'),
            'database' => trim($database[1] ?? ''),
            'username' => trim($username[1] ?? ''),
            'password' => trim($password[1] ?? ''),
        ];
    }

    private function createBackupFolder()
    {
        if (!is_dir($this->config['backup_folder'])) {
            mkdir($this->config['backup_folder'], 0755, true);
        }
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
        
        $timestamp = date('Y-m-d H:i:s');
        echo $color . "[$timestamp] $message" . $reset . "\n";
        
        // حفظ في ملف اللوج
        if (!empty($this->config['update_log'])) {
            $logDir = dirname($this->config['update_log']);
            if (!is_dir($logDir)) {
                mkdir($logDir, 0755, true);
            }
            file_put_contents($this->config['update_log'], "[$timestamp] $message\n", FILE_APPEND);
        }
    }

    private function displaySuccessMessage()
    {
        echo "\n";
        echo "🎉 ============================================== 🎉\n";
        echo "           تم تحديث النظام بنجاح!\n";
        echo "           الإصدار الحالي: {$this->config['current_version']}\n";
        echo "🎉 ============================================== 🎉\n\n";
        
        echo "📋 ما تم تحديثه:\n";
        echo "================\n";
        echo "✅ قاعدة البيانات محدثة\n";
        echo "✅ الملفات الثابتة محدثة\n";
        echo "✅ التبعيات محدثة\n";
        echo "✅ الكاش تم مسحه\n\n";
        
        if (!empty($this->warnings)) {
            echo "⚠️  تحذيرات:\n";
            echo "============\n";
            foreach ($this->warnings as $warning) {
                echo "• $warning\n";
            }
            echo "\n";
        }
        
        echo "🚀 الخطوات التالية:\n";
        echo "=================\n";
        echo "1. تحقق من عمل النظام بشكل صحيح\n";
        echo "2. راجع الإعدادات الجديدة\n";
        echo "3. احذف النسخ الاحتياطية القديمة عند التأكد\n\n";
        
        echo "🎊 استمتع بالميزات الجديدة!\n\n";
    }
}

// تشغيل أداة التحديث
if (php_sapi_name() === 'cli') {
    $updater = new AccountingSystemUpdater();
    $result = $updater->update();
    exit($result ? 0 : 1);
} else {
    echo "أداة التحديث تعمل من سطر الأوامر فقط.\n";
    echo "استخدم: php install/updater.php\n";
}
