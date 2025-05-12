<?php
// Laravel Installer Directory & Permission Fixer
// Place this file in public/ and run it once after upload

$dirs = [
    '../storage',
    '../storage/app',
    '../storage/app/public',
    '../storage/app/private',
    '../storage/app/public/logos',
    '../storage/framework',
    '../storage/framework/cache',
    '../storage/framework/sessions',
    '../storage/framework/views',
    '../storage/framework/testing',
    '../storage/logs',
    '../storage/fonts',
    '../storage/fonts/mpdf',
    '../storage/fonts/mpdf/ttfontdata',
    '../bootstrap/cache',
];

$errors = [];
foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        if (!mkdir($dir, 0777, true)) {
            $errors[] = "<b>Could not create:</b> $dir";
        }
    }
    if (!chmod($dir, 0777)) {
        $errors[] = "<b>Could not set permissions (0777):</b> $dir";
    }
}

?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Laravel Installer Directory Check</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f8fafc; padding: 40px; }
        .box { background: #fff; border-radius: 8px; box-shadow: 0 2px 8px #0001; max-width: 600px; margin: auto; padding: 32px; }
        .success { color: #155724; background: #d4edda; border: 1px solid #c3e6cb; padding: 16px; border-radius: 6px; margin-bottom: 20px; }
        .error { color: #721c24; background: #f8d7da; border: 1px solid #f5c6cb; padding: 16px; border-radius: 6px; margin-bottom: 20px; }
        ul { margin: 0; padding-left: 20px; }
    </style>
</head>
<body>
<div class="box">
    <h2>Laravel Installer Directory & Permission Check</h2>
    <?php if (empty($errors)): ?>
        <div class="success">
            <b>All required directories exist and permissions set to 0777.</b><br>
            You can now safely delete this file (<code>public/installer_check.php</code>) and start the web installer from your browser.
        </div>
    <?php else: ?>
        <div class="error">
            <b>Some directories could not be created or permissions not set automatically:</b>
            <ul>
                <?php foreach ($errors as $e) echo "<li>$e</li>"; ?>
            </ul>
            <br>
            <b>Please create these directories and set permissions to 0777 manually via your hosting control panel (File Manager).</b>
        </div>
    <?php endif; ?>
    <hr>
    <div style="font-size:13px;color:#888;">This script is safe to delete after running once.</div>
</div>
</body>
</html> 