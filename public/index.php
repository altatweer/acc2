<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

// --- Laravel Installer Directory & Permission Bootstrap ---
$dirs = [
    __DIR__.'/../storage',
    __DIR__.'/../storage/app',
    __DIR__.'/../storage/app/public',
    __DIR__.'/../storage/app/private',
    __DIR__.'/../storage/app/public/logos',
    __DIR__.'/../storage/framework',
    __DIR__.'/../storage/framework/cache',
    __DIR__.'/../storage/framework/sessions',
    __DIR__.'/../storage/framework/views',
    __DIR__.'/../storage/framework/testing',
    __DIR__.'/../storage/logs',
    __DIR__.'/../storage/fonts',
    __DIR__.'/../storage/fonts/mpdf',
    __DIR__.'/../storage/fonts/mpdf/ttfontdata',
    __DIR__.'/../bootstrap/cache',
];
$errors = [];
foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        if (!@mkdir($dir, 0777, true)) {
            $errors[] = "<b>تعذر إنشاء المجلد:</b> $dir";
        }
    }
    if (!@chmod($dir, 0777)) {
        $errors[] = "<b>تعذر ضبط الصلاحيات (0777):</b> $dir";
    }
}
if (!empty($errors)) {
    echo "<!DOCTYPE html><html lang='ar'><head><meta charset='UTF-8'><title>فحص متطلبات التثبيت</title><style>body{font-family:Tahoma,Arial,sans-serif;background:#f8fafc;padding:40px;} .box{background:#fff;border-radius:8px;box-shadow:0 2px 8px #0001;max-width:600px;margin:auto;padding:32px;} .error{color:#721c24;background:#f8d7da;border:1px solid #f5c6cb;padding:16px;border-radius:6px;margin-bottom:20px;} ul{margin:0;padding-left:20px;}</style></head><body><div class='box'><h2>فحص متطلبات التثبيت</h2><div class='error'><b>بعض المجلدات لم يتم إنشاؤها أو لم يتم ضبط الصلاحيات تلقائيًا:</b><ul>";
    foreach ($errors as $e) echo "<li>$e</li>";
    echo "</ul><br><b>يرجى إنشاء هذه المجلدات وضبط الصلاحيات إلى 0777 يدويًا عبر لوحة تحكم الاستضافة (File Manager).</b></div><hr><div style='font-size:13px;color:#888;'>لن يعمل النظام حتى يتم حل هذه المشاكل.</div></div></body></html>";
    exit;
}

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
