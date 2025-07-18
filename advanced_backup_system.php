<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdvancedBackupSystem extends Command
{
    protected $signature = 'backup:advanced 
                            {--type=full : Type of backup (full|accounts|transactions)}
                            {--verify : Verify backup integrity after creation}';

    protected $description = 'نظام نسخ احتياطية متقدم للدمج الآمن';

    public function handle()
    {
        $backupType = $this->option('type');
        $verify = $this->option('verify');

        $this->info('🛡️ بدء عملية النسخ الاحتياطي المتقدم');

        // 1. التحقق من المساحة المتاحة
        $this->checkDiskSpace();

        // 2. إنشاء النسخة الاحتياطية
        $backupPath = $this->createBackup($backupType);

        // 3. التحقق من سلامة النسخة
        if ($verify) {
            $this->verifyBackup($backupPath);
        }

        // 4. حفظ معلومات النسخة الاحتياطية
        $this->logBackupInfo($backupPath, $backupType);

        $this->info("✅ تم إنشاء النسخة الاحتياطية بنجاح: {$backupPath}");
    }

    private function checkDiskSpace(): void
    {
        $requiredSpace = 500 * 1024 * 1024; // 500 MB
        $freeSpace = disk_free_space(storage_path());

        if ($freeSpace < $requiredSpace) {
            $this->error('❌ مساحة القرص غير كافية للنسخ الاحتياطي');
            $this->line("المطلوب: " . number_format($requiredSpace / 1024 / 1024) . " MB");
            $this->line("المتاح: " . number_format($freeSpace / 1024 / 1024) . " MB");
            exit(1);
        }

        $this->info("✅ مساحة القرص كافية: " . number_format($freeSpace / 1024 / 1024) . " MB");
    }

    private function createBackup($type): string
    {
        $timestamp = date('Y_m_d_H_i_s');
        $backupDir = storage_path("app/backups/merge_safety_{$timestamp}");
        
        if (!file_exists($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        switch ($type) {
            case 'full':
                return $this->createFullBackup($backupDir, $timestamp);
            case 'accounts':
                return $this->createAccountsBackup($backupDir, $timestamp);
            case 'transactions':
                return $this->createTransactionsBackup($backupDir, $timestamp);
            default:
                return $this->createFullBackup($backupDir, $timestamp);
        }
    }

    private function createFullBackup($backupDir, $timestamp): string
    {
        $this->info('📦 إنشاء نسخة احتياطية كاملة...');

        // 1. قاعدة البيانات
        $this->createDatabaseDump($backupDir);

        // 2. ملفات التكوين
        $this->backupConfigFiles($backupDir);

        // 3. معلومات النظام
        $this->createSystemSnapshot($backupDir);

        // 4. ضغط الملفات
        $zipFile = storage_path("app/backups/full_backup_{$timestamp}.zip");
        $this->createZipArchive($backupDir, $zipFile);

        return $zipFile;
    }

    private function createDatabaseDump($backupDir): void
    {
        $this->line('   🗄️ نسخ احتياطية لقاعدة البيانات...');

        $tables = [
            'accounts', 'journal_entry_lines', 'journal_entries',
            'transactions', 'vouchers', 'account_balances',
            'currencies', 'users', 'customers', 'employees'
        ];

        foreach ($tables as $table) {
            $sqlFile = "{$backupDir}/{$table}_backup.sql";
            
            $command = sprintf(
                'mysqldump --host=%s --user=%s --password=%s --single-transaction --routines --triggers %s %s > %s',
                config('database.connections.mysql.host'),
                config('database.connections.mysql.username'),
                config('database.connections.mysql.password'),
                config('database.connections.mysql.database'),
                $table,
                $sqlFile
            );

            exec($command, $output, $returnCode);
            
            if ($returnCode !== 0) {
                $this->error("❌ فشل في نسخ جدول: {$table}");
                throw new \Exception("Database backup failed for table: {$table}");
            }

            $this->line("      ✅ {$table}");
        }
    }

    private function backupConfigFiles($backupDir): void
    {
        $this->line('   ⚙️ نسخ ملفات التكوين...');

        $configFiles = [
            'config/app.php',
            'config/database.php', 
            '.env'
        ];

        foreach ($configFiles as $file) {
            if (file_exists(base_path($file))) {
                copy(base_path($file), "{$backupDir}/" . basename($file));
                $this->line("      ✅ " . basename($file));
            }
        }
    }

    private function createSystemSnapshot($backupDir): void
    {
        $this->line('   📊 إنشاء لقطة النظام...');

        $snapshot = [
            'backup_time' => now(),
            'laravel_version' => app()->version(),
            'php_version' => PHP_VERSION,
            'database_stats' => $this->getDatabaseStats(),
            'disk_usage' => $this->getDiskUsage(),
        ];

        file_put_contents(
            "{$backupDir}/system_snapshot.json",
            json_encode($snapshot, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
    }

    private function getDatabaseStats(): array
    {
        $stats = [];
        
        $tables = ['accounts', 'journal_entry_lines', 'vouchers', 'transactions'];
        
        foreach ($tables as $table) {
            $count = DB::table($table)->count();
            $stats[$table] = $count;
        }

        return $stats;
    }

    private function getDiskUsage(): array
    {
        return [
            'total_space' => disk_total_space(storage_path()),
            'free_space' => disk_free_space(storage_path()),
            'used_space' => disk_total_space(storage_path()) - disk_free_space(storage_path())
        ];
    }

    private function createZipArchive($sourceDir, $zipFile): void
    {
        $this->line('   📦 ضغط الملفات...');

        $zip = new \ZipArchive();
        
        if ($zip->open($zipFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== TRUE) {
            throw new \Exception("Cannot create zip file: {$zipFile}");
        }

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($sourceDir),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($sourceDir) + 1);
                $zip->addFile($filePath, $relativePath);
            }
        }

        $zip->close();
        
        // حذف المجلد المؤقت
        $this->deleteDirectory($sourceDir);
    }

    private function verifyBackup($backupPath): void
    {
        $this->info('🔍 التحقق من سلامة النسخة الاحتياطية...');

        // 1. التحقق من وجود الملف
        if (!file_exists($backupPath)) {
            throw new \Exception("Backup file not found: {$backupPath}");
        }

        // 2. التحقق من حجم الملف
        $fileSize = filesize($backupPath);
        if ($fileSize < 1024) { // أقل من 1KB مشبوه
            throw new \Exception("Backup file too small: {$fileSize} bytes");
        }

        // 3. التحقق من إمكانية قراءة الملف
        if (pathinfo($backupPath, PATHINFO_EXTENSION) === 'zip') {
            $zip = new \ZipArchive();
            if ($zip->open($backupPath) !== TRUE) {
                throw new \Exception("Cannot read backup zip file");
            }
            $zip->close();
        }

        $this->info("✅ النسخة الاحتياطية سليمة: " . number_format($fileSize / 1024 / 1024, 2) . " MB");
    }

    private function logBackupInfo($backupPath, $type): void
    {
        $logEntry = [
            'timestamp' => now(),
            'type' => $type,
            'file_path' => $backupPath,
            'file_size' => filesize($backupPath),
            'created_by' => auth()->user()->name ?? 'System',
            'purpose' => 'Pre-merge safety backup'
        ];

        Storage::append('backup_log.json', json_encode($logEntry) . "\n");
    }

    private function deleteDirectory($dir): void
    {
        if (!is_dir($dir)) return;

        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = "$dir/$file";
            is_dir($path) ? $this->deleteDirectory($path) : unlink($path);
        }
        rmdir($dir);
    }
} 