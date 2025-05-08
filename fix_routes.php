<?php
/**
 * This script updates all Blade templates to use the new localized route system
 * Run it with: php fix_routes.php
 */

$viewsDir = __DIR__ . '/resources/views';
$count = 0;
processDirectory($viewsDir);

echo "Fixed $count route calls in templates.\n";

function processDirectory($dir) {
    global $count;
    
    $files = scandir($dir);
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;
        
        $path = $dir . '/' . $file;
        if (is_dir($path)) {
            processDirectory($path);
        } else if (preg_match('/\.blade\.php$/', $file)) {
            $content = file_get_contents($path);
            
            // Pattern to match route() with lang parameter for replacement
            $pattern = '/route\(\s*([\'"])([^\'"\s]+)\1\s*,\s*\[([^\]]*?[\'"]lang[\'"](?:\s*=>\s*app\(\)->getLocale\(\)|[\'"]ar[\'"]\s*|[\'"]en[\'"]\s*).*?)\]\s*\)/s';
            
            // Replace with Route::localizedRoute() and remove the lang parameter
            $newContent = preg_replace_callback($pattern, function($matches) use (&$count) {
                $count++;
                
                $routeName = $matches[2];
                $params = $matches[3];
                
                // Remove the lang parameter from the parameters list
                $params = preg_replace('/[\'"]lang[\'"](?:\s*=>\s*app\(\)->getLocale\(\)|[\'"]ar[\'"]\s*|[\'"]en[\'"]\s*),?\s*/', '', $params);
                $params = preg_replace('/,\s*\]$/', ']', $params);
                
                // If we have other parameters, keep them
                if (trim($params) !== '') {
                    return "Route::localizedRoute('{$routeName}', [{$params}])";
                } else {
                    return "Route::localizedRoute('{$routeName}')";
                }
            }, $content);
            
            if ($newContent !== $content) {
                file_put_contents($path, $newContent);
                echo "Updated: $path - Fixed $count calls\n";
            }
        }
    }
} 