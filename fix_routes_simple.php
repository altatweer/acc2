<?php

/**
 * سكريبت لإصلاح مشاكل المسارات المتبقية في المشروع بعد تغيير المسارات للطريقة الجديدة
 * 
 * هذا السكريبت يمسح جميع ملفات blade ويصلح أي استخدام للمسارات التي تحتاج إلى locale
 * Run: php fix_routes_simple.php
 */

$baseDir = __DIR__;
$viewsDir = $baseDir . '/resources/views';
$bladeFiles = [];

// البحث عن جميع ملفات blade
function findBladeFiles($dir, &$files) {
    if (!is_dir($dir)) {
        echo "Directory does not exist: $dir\n";
        return;
    }
    
    $items = scandir($dir);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') {
            continue;
        }
        
        $path = $dir . DIRECTORY_SEPARATOR . $item;
        if (is_dir($path)) {
            findBladeFiles($path, $files);
        } elseif (substr($item, -10) === '.blade.php') {
            $files[] = $path;
        }
    }
}

echo "Finding blade files...\n";
findBladeFiles($viewsDir, $bladeFiles);
echo "Found " . count($bladeFiles) . " blade files.\n";

// قائمة بأنماط القديمة التي يجب استبدالها
$patterns = [
    // صيغة route مع locale
    '/route\([\'"]([^\'"]*)[\'"]\s*,\s*\[\s*[\'"]locale[\'"]\s*=>\s*([^\]]*)\]\)/' => 'route(\'$1\')',
    // صيغة route مع voucher and locale
    '/route\([\'"]([^\'"]*)[\'"]\s*,\s*\[\s*[\'"]voucher[\'"]\s*=>\s*([^,\]]*)\s*,\s*[\'"]locale[\'"]\s*=>\s*([^\]]*)\]\)/' => 'route(\'$1\', [\'voucher\' => $2])',
    // صيغة route مع item and locale
    '/route\([\'"]([^\'"]*)[\'"]\s*,\s*\[\s*[\'"]([^\'"]*)[\'"]\s*=>\s*([^,\]]*)\s*,\s*[\'"]locale[\'"]\s*=>\s*([^\]]*)\]\)/' => 'route(\'$1\', [\'$2\' => $3])',
    // تحويل Route::localizedRoute إلى route عادي
    '/Route::localizedRoute\([\'"]([^\'"]*)[\'"]\s*(?:,\s*\[\s*[\'"]([^\'"]*)[\'"]\s*=>\s*([^,\]]*)\s*\])?\)/' => 'route(\'$1\'$2$3)',
];

$totalFixed = 0;

foreach ($bladeFiles as $file) {
    $content = file_get_contents($file);
    $originalContent = $content;
    $modified = false;
    
    foreach ($patterns as $pattern => $replacement) {
        $newContent = preg_replace($pattern, $replacement, $content);
        
        if ($newContent !== $content) {
            $content = $newContent;
            $modified = true;
        }
    }
    
    if ($modified) {
        file_put_contents($file, $content);
        $totalFixed++;
        echo "Fixed: $file\n";
    }
}

echo "Total files fixed: $totalFixed\n";
echo "Done!\n"; 