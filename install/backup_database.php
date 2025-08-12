<?php
/**
 * 💾 نسخة احتياطية سريعة من قاعدة البيانات
 * نظام المحاسبة الاحترافي v2.2.1
 */

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

class DatabaseBackup
{
    private $backupFolder = 'install/backups';
    
    public function __construct()
    {
        $this->createBackupFolder();
        $this->displayHeader();
    }
    
    private function displayHeader()
    {
        echo "\n";
        echo "💾 ============================================== 💾\n";
        echo "        نسخة احتياطية سريعة من قاعدة البيانات\n";
        echo "               نظام المحاسبة v2.2.1\n";
        echo "💾 ============================================== 💾\n\n";
    }
    
    public function createBackup()
    {
        $this->log('بدء إنشاء النسخة الاحتياطية...', 'info');
        
        $dbConfig = $this->getDatabaseConfig();
        
        if (empty($dbConfig['database'])) {
            $this->log('خطأ: لم يتم العثور على إعدادات قاعدة البيانات في .env', 'error');
            return false;
        }
        
        $timestamp = date('Y-m-d_H-i-s');
        $backupFile = $this->backupFolder . "/backup_database_{$timestamp}.sql";
        
        $this->log("إنشاء نسخة احتياطية من قاعدة البيانات: {$dbConfig['database']}", 'info');
        
        // بناء أمر mysqldump
        $command = "mysqldump";
        $command .= " -h{$dbConfig['host']}";
        $command .= " -P{$dbConfig['port']}";
        $command .= " -u{$dbConfig['username']}";
        
        if (!empty($dbConfig['password'])) {
            $command .= " -p'{$dbConfig['password']}'";
        }
        
        $command .= " {$dbConfig['database']}";
        $command .= " > $backupFile";
        $command .= " 2>/dev/null";
        
        exec($command, $output, $returnCode);
        
        if ($returnCode === 0 && file_exists($backupFile) && filesize($backupFile) > 0) {
            $fileSize = $this->formatBytes(filesize($backupFile));
            $this->log("✅ تم إنشاء النسخة الاحتياطية بنجاح!", 'success');
            $this->log("📁 مسار الملف: $backupFile", 'info');
            $this->log("📊 حجم الملف: $fileSize", 'info');
            
            // إنشاء ملف معلومات إضافي
            $this->createBackupInfo($backupFile, $dbConfig);
            
            return $backupFile;
        } else {
            $this->log("❌ فشل في إنشاء النسخة الاحتياطية", 'error');
            
            if ($returnCode === 127) {
                $this->log("تأكد من تثبيت mysqldump على النظام", 'warning');
            } elseif ($returnCode === 1) {
                $this->log("تحقق من بيانات الاتصال بقاعدة البيانات", 'warning');
            }
            
            return false;
        }
    }
    
    private function createBackupInfo($backupFile, $dbConfig)
    {
        $infoFile = $backupFile . '.info';
        
        $info = [
            'backup_date' => date('Y-m-d H:i:s'),
            'database_name' => $dbConfig['database'],
            'database_host' => $dbConfig['host'],
            'file_size' => filesize($backupFile),
            'file_size_formatted' => $this->formatBytes(filesize($backupFile)),
            'php_version' => PHP_VERSION,
            'mysql_version' => $this->getMySQLVersion(),
            'laravel_version' => $this->getLaravelVersion()
        ];
        
        file_put_contents($infoFile, json_encode($info, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        $this->log("📋 تم إنشاء ملف المعلومات: $infoFile", 'info');
    }
    
    public function listBackups()
    {
        $this->log('النسخ الاحتياطية المتاحة:', 'info');
        echo "========================\n";
        
        $backups = glob($this->backupFolder . '/backup_database_*.sql');
        
        if (empty($backups)) {
            echo "لا توجد نسخ احتياطية\n";
            return;
        }
        
        usort($backups, function($a, $b) {
            return filemtime($b) - filemtime($a);
        });
        
        foreach ($backups as $backup) {
            $filename = basename($backup);
            $filesize = $this->formatBytes(filesize($backup));
            $date = date('Y-m-d H:i:s', filemtime($backup));
            
            echo "📁 $filename\n";
            echo "   📅 التاريخ: $date\n";
            echo "   📊 الحجم: $filesize\n\n";
        }
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
    
    private function getMySQLVersion()
    {
        try {
            $dbConfig = $this->getDatabaseConfig();
            $pdo = new PDO(
                "mysql:host={$dbConfig['host']};port={$dbConfig['port']}",
                $dbConfig['username'],
                $dbConfig['password']
            );
            
            $result = $pdo->query('SELECT VERSION()')->fetchColumn();
            return $result;
        } catch (Exception $e) {
            return 'غير معروف';
        }
    }
    
    private function getLaravelVersion()
    {
        try {
            if (class_exists('\Illuminate\Foundation\Application')) {
                return app()->version();
            }
            return 'غير معروف';
        } catch (Exception $e) {
            return 'غير معروف';
        }
    }
    
    private function createBackupFolder()
    {
        if (!is_dir($this->backupFolder)) {
            mkdir($this->backupFolder, 0755, true);
        }
    }
    
    private function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
    
    private function log($message, $type = 'info')
    {
        $colors = [
            'info' => "\033[0;37m",
            'success' => "\033[0;32m",
            'warning' => "\033[0;33m",
            'error' => "\033[0;31m",
        ];
        
        $reset = "\033[0m";
        $color = $colors[$type] ?? $colors['info'];
        
        echo $color . $message . $reset . "\n";
    }
}

// تشغيل النسخة الاحتياطية
if (php_sapi_name() === 'cli') {
    $backup = new DatabaseBackup();
    
    if (isset($argv[1]) && $argv[1] === '--list') {
        $backup->listBackups();
    } else {
        $result = $backup->createBackup();
        
        if ($result) {
            echo "\n🎉 تمت العملية بنجاح!\n";
            echo "💡 نصيحة: احتفظ بالنسخة الاحتياطية في مكان آمن\n\n";
        } else {
            echo "\n❌ فشلت العملية!\n";
            echo "💡 تأكد من إعدادات قاعدة البيانات وتثبيت mysqldump\n\n";
            exit(1);
        }
    }
} else {
    echo "هذا السكريبت يعمل من سطر الأوامر فقط.\n";
    echo "الاستخدام: php install/backup_database.php\n";
    echo "عرض النسخ: php install/backup_database.php --list\n";
}
