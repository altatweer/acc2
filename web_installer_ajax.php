<?php
/**
 * Ajax Handler للمثبت الشامل
 */

// منع الوصول إذا كان النظام مثبت مسبقاً
if (file_exists('storage/app/install.lock') && $_GET['action'] !== 'delete_installer') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'النظام مثبت مسبقاً']);
    exit;
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';

header('Content-Type: application/json');

try {
    switch ($action) {
        case 'check_requirements':
            echo json_encode(checkRequirements());
            break;
            
        case 'setup_database':
            echo json_encode(setupDatabase());
            break;
            
        case 'create_admin':
            echo json_encode(createAdmin());
            break;
            
        case 'delete_installer':
            echo json_encode(deleteInstallerFiles());
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'إجراء غير صحيح']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'خطأ: ' . $e->getMessage()]);
}

function checkRequirements() {
    $requirements = [
        'php_version' => [
            'name' => 'إصدار PHP (المطلوب: 8.1+)',
            'status' => version_compare(PHP_VERSION, '8.1', '>='),
            'current' => PHP_VERSION,
            'required' => '8.1+'
        ]
    ];
    
    // فحص الإضافات المطلوبة
    $extensions = [
        'pdo' => 'PDO Extension',
        'pdo_mysql' => 'PDO MySQL Driver',
        'mbstring' => 'Multibyte String Extension',
        'openssl' => 'OpenSSL Extension',
        'tokenizer' => 'Tokenizer Extension',
        'xml' => 'XML Extension',
        'ctype' => 'Ctype Extension',
        'json' => 'JSON Extension',
        'bcmath' => 'BCMath Extension',
        'fileinfo' => 'Fileinfo Extension',
        'zip' => 'ZIP Extension'
    ];
    
    foreach ($extensions as $ext => $name) {
        $requirements["ext_$ext"] = [
            'name' => $name,
            'status' => extension_loaded($ext),
            'current' => extension_loaded($ext) ? 'متاح' : 'مفقود',
            'required' => 'مطلوب'
        ];
    }
    
    // فحص الأذونات
    $directories = [
        'storage' => 'مجلد Storage',
        'storage/app' => 'مجلد Storage/App',
        'storage/framework' => 'مجلد Storage/Framework',
        'storage/logs' => 'مجلد Storage/Logs',
        'bootstrap/cache' => 'مجلد Bootstrap/Cache'
    ];
    
    foreach ($directories as $dir => $name) {
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        
        $requirements["dir_$dir"] = [
            'name' => "$name (قابل للكتابة)",
            'status' => is_writable($dir),
            'current' => is_writable($dir) ? 'قابل للكتابة' : 'غير قابل للكتابة',
            'required' => 'قابل للكتابة'
        ];
    }
    
    // فحص ملف .env
    $requirements['env_file'] = [
        'name' => 'ملف .env',
        'status' => file_exists('.env'),
        'current' => file_exists('.env') ? 'موجود' : 'غير موجود',
        'required' => 'موجود'
    ];
    
    // حساب النتائج
    $totalChecks = count($requirements);
    $passedChecks = array_filter($requirements, function($req) { return $req['status']; });
    $canContinue = count($passedChecks) === $totalChecks;
    
    // إنشاء HTML للعرض
    $html = '';
    foreach ($requirements as $key => $req) {
        $statusClass = $req['status'] ? 'requirement-ok' : 'requirement-error';
        $statusIcon = $req['status'] ? 'fas fa-check-circle text-success' : 'fas fa-times-circle text-danger';
        
        $html .= "<div class='requirement-item $statusClass'>";
        $html .= "<div>";
        $html .= "<strong>{$req['name']}</strong><br>";
        $html .= "<small>الحالي: {$req['current']} | المطلوب: {$req['required']}</small>";
        $html .= "</div>";
        $html .= "<i class='$statusIcon' style='font-size: 1.5rem;'></i>";
        $html .= "</div>";
    }
    
    if ($canContinue) {
        $html .= "<div class='alert alert-success mt-3'>";
        $html .= "<i class='fas fa-check-circle me-2'></i>";
        $html .= "ممتاز! جميع المتطلبات متوفرة. يمكنك متابعة التثبيت.";
        $html .= "</div>";
    } else {
        $html .= "<div class='alert alert-danger mt-3'>";
        $html .= "<i class='fas fa-exclamation-triangle me-2'></i>";
        $html .= "يرجى إصلاح المتطلبات المفقودة قبل المتابعة.";
        $html .= "</div>";
    }
    
    return [
        'success' => true,
        'html' => $html,
        'can_continue' => $canContinue,
        'total_checks' => $totalChecks,
        'passed_checks' => count($passedChecks)
    ];
}

function setupDatabase() {
    $host = $_POST['db_host'] ?? '';
    $name = $_POST['db_name'] ?? '';
    $user = $_POST['db_user'] ?? '';
    $password = $_POST['db_password'] ?? '';
    
    if (empty($host) || empty($name) || empty($user)) {
        return ['success' => false, 'message' => 'يرجى ملء جميع الحقول المطلوبة'];
    }
    
    // اختبار الاتصال
    try {
        $dsn = "mysql:host=$host;dbname=$name;charset=utf8mb4";
        $pdo = new PDO($dsn, $user, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        
        // حفظ إعدادات قاعدة البيانات في .env
        updateEnvFile([
            'DB_HOST' => $host,
            'DB_DATABASE' => $name,
            'DB_USERNAME' => $user,
            'DB_PASSWORD' => $password,
        ]);
        
        // تشغيل الترحيلات
        runMigrations();
        
        // تعبئة البيانات الأساسية
        seedBasicData();
        
        return [
            'success' => true,
            'message' => 'تم إعداد قاعدة البيانات بنجاح'
        ];
        
    } catch (PDOException $e) {
        return [
            'success' => false,
            'message' => 'فشل الاتصال بقاعدة البيانات: ' . $e->getMessage()
        ];
    }
}

function createAdmin() {
    $name = $_POST['admin_name'] ?? '';
    $email = $_POST['admin_email'] ?? '';
    $password = $_POST['admin_password'] ?? '';
    $confirmation = $_POST['admin_password_confirmation'] ?? '';
    
    if (empty($name) || empty($email) || empty($password)) {
        return ['success' => false, 'message' => 'يرجى ملء جميع الحقول'];
    }
    
    if ($password !== $confirmation) {
        return ['success' => false, 'message' => 'كلمة المرور وتأكيدها غير متطابقتين'];
    }
    
    if (strlen($password) < 6) {
        return ['success' => false, 'message' => 'كلمة المرور يجب أن تكون 6 أحرف على الأقل'];
    }
    
    try {
        // تحميل Laravel
        require_once 'vendor/autoload.php';
        $app = require_once 'bootstrap/app.php';
        $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
        
        // إنشاء المستخدم
        $user = new \App\Models\User();
        $user->name = $name;
        $user->email = $email;
        $user->password = bcrypt($password);
        $user->save();
        
        // إنشاء دور المدير وتعيينه
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(
            ['name' => 'super-admin'],
            ['guard_name' => 'web']
        );
        
        $user->assignRole($adminRole);
        
        // إنشاء ملف القفل
        file_put_contents('storage/app/install.lock', date('Y-m-d H:i:s'));
        
        return [
            'success' => true,
            'message' => 'تم إنشاء حساب المدير وإكمال التثبيت بنجاح'
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'فشل إنشاء المدير: ' . $e->getMessage()
        ];
    }
}

function deleteInstallerFiles() {
    try {
        $files = ['web_installer.php', 'web_installer_ajax.php'];
        $deleted = [];
        
        foreach ($files as $file) {
            if (file_exists($file)) {
                if (unlink($file)) {
                    $deleted[] = $file;
                }
            }
        }
        
        return [
            'success' => true,
            'message' => 'تم حذف ملفات التثبيت بنجاح: ' . implode(', ', $deleted)
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'فشل حذف ملفات التثبيت: ' . $e->getMessage()
        ];
    }
}

function updateEnvFile($data) {
    $envPath = '.env';
    $envContent = file_exists($envPath) ? file_get_contents($envPath) : '';
    
    foreach ($data as $key => $value) {
        $pattern = "/^{$key}=.*$/m";
        $replacement = "{$key}={$value}";
        
        if (preg_match($pattern, $envContent)) {
            $envContent = preg_replace($pattern, $replacement, $envContent);
        } else {
            $envContent .= "\n{$replacement}";
        }
    }
    
    file_put_contents($envPath, $envContent);
}

function runMigrations() {
    try {
        require_once 'vendor/autoload.php';
        $app = require_once 'bootstrap/app.php';
        
        // تشغيل الترحيلات
        \Artisan::call('migrate:fresh', ['--force' => true]);
        
        return true;
    } catch (Exception $e) {
        throw new Exception('فشل تشغيل ترحيلات قاعدة البيانات: ' . $e->getMessage());
    }
}

function seedBasicData() {
    try {
        // تعبئة العملات الأساسية
        $currencies = [
            ['code' => 'IQD', 'name' => 'دينار عراقي', 'symbol' => 'د.ع', 'is_default' => true],
            ['code' => 'USD', 'name' => 'دولار أمريكي', 'symbol' => '$', 'is_default' => false],
            ['code' => 'EUR', 'name' => 'يورو', 'symbol' => '€', 'is_default' => false],
        ];
        
        foreach ($currencies as $currency) {
            \App\Models\Currency::firstOrCreate(
                ['code' => $currency['code']],
                $currency
            );
        }
        
        // تعبئة شجرة الحسابات الأساسية
        $accounts = [
            // الأصول
            ['code' => '1000', 'name' => 'الأصول', 'type' => 'أصل', 'nature' => 'مدين', 'is_group' => true],
            ['code' => '1100', 'name' => 'الأصول المتداولة', 'type' => 'أصل', 'nature' => 'مدين', 'is_group' => true, 'parent_code' => '1000'],
            ['code' => '1101', 'name' => 'الصندوق', 'type' => 'أصل', 'nature' => 'مدين', 'is_group' => false, 'parent_code' => '1100'],
            ['code' => '1201', 'name' => 'البنك', 'type' => 'أصل', 'nature' => 'مدين', 'is_group' => false, 'parent_code' => '1100'],
            
            // الخصوم
            ['code' => '2000', 'name' => 'الخصوم', 'type' => 'خصم', 'nature' => 'دائن', 'is_group' => true],
            ['code' => '2100', 'name' => 'الخصوم المتداولة', 'type' => 'خصم', 'nature' => 'دائن', 'is_group' => true, 'parent_code' => '2000'],
            
            // حقوق الملكية
            ['code' => '3000', 'name' => 'حقوق الملكية', 'type' => 'ملكية', 'nature' => 'دائن', 'is_group' => true],
            ['code' => '3100', 'name' => 'رأس المال', 'type' => 'ملكية', 'nature' => 'دائن', 'is_group' => false, 'parent_code' => '3000'],
            
            // الإيرادات
            ['code' => '4000', 'name' => 'الإيرادات', 'type' => 'إيراد', 'nature' => 'دائن', 'is_group' => true],
            ['code' => '4100', 'name' => 'مبيعات', 'type' => 'إيراد', 'nature' => 'دائن', 'is_group' => false, 'parent_code' => '4000'],
            
            // المصروفات
            ['code' => '5000', 'name' => 'المصروفات', 'type' => 'مصروف', 'nature' => 'مدين', 'is_group' => true],
            ['code' => '5100', 'name' => 'مصروفات إدارية', 'type' => 'مصروف', 'nature' => 'مدين', 'is_group' => false, 'parent_code' => '5000'],
        ];
        
        $codeToId = [];
        
        // إنشاء الحسابات
        foreach ($accounts as $accountData) {
            $data = $accountData;
            unset($data['parent_code']);
            $data['parent_id'] = null;
            $data['default_currency'] = 'IQD';
            
            $account = \App\Models\Account::create($data);
            $codeToId[$accountData['code']] = $account->id;
        }
        
        // تحديث parent_id
        foreach ($accounts as $accountData) {
            if (isset($accountData['parent_code']) && isset($codeToId[$accountData['parent_code']])) {
                $account = \App\Models\Account::find($codeToId[$accountData['code']]);
                if ($account) {
                    $account->parent_id = $codeToId[$accountData['parent_code']];
                    $account->save();
                }
            }
        }
        
        return true;
    } catch (Exception $e) {
        throw new Exception('فشل تعبئة البيانات الأساسية: ' . $e->getMessage());
    }
}
