<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class InstallController extends Controller
{
    public function __construct()
    {
        // Protect all install routes except finish and maintenance tools
        $this->middleware(function ($request, $next) {
            $allowedRoutes = ['install.finish', 'install.maintenance', 'install.system_check', 'install.backup'];
            
            if (!in_array($request->route()->getName(), $allowedRoutes)) {
                if (file_exists(storage_path('app/install.lock'))) {
                    // إذا تم التثبيت، فقط اسمح بأدوات الصيانة
                    if (in_array($request->route()->getName(), $allowedRoutes)) {
                        return $next($request);
                    }
                    abort(403, 'تم تثبيت النظام بالفعل. للصيانة، استخدم أدوات الصيانة المتاحة.');
                }
            }
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        // Check required folders and permissions
        $requiredDirs = [
            storage_path(),
            storage_path('app'),
            storage_path('app/public'),
            storage_path('app/private'),
            storage_path('app/public/logos'),
            storage_path('framework'),
            storage_path('framework/cache'),
            storage_path('framework/sessions'),
            storage_path('framework/views'),
            storage_path('framework/testing'),
            storage_path('logs'),
            storage_path('fonts'),
            storage_path('fonts/mpdf'),
            storage_path('fonts/mpdf/ttfontdata'),
            base_path('bootstrap/cache'),
        ];
        $installerErrors = [];
        foreach ($requiredDirs as $dir) {
            if (!is_dir($dir)) {
                if (!@mkdir($dir, 0777, true)) {
                    $installerErrors[] = "<b>تعذر إنشاء المجلد:</b> $dir";
                }
            }
            if (!@chmod($dir, 0777)) {
                $installerErrors[] = "<b>تعذر ضبط الصلاحيات (0777):</b> $dir";
            }
        }
        $permissions = [
            'storage' => is_writable(storage_path()),
            'env' => is_writable(base_path('.env')),
        ];
        foreach ($requiredDirs as $dir) {
            if (!is_dir($dir) || !is_writable($dir)) {
                $permissions['storage'] = false;
                break;
            }
        }
        $requirements = [
            'php' => [
                'required' => '8.0',
                'current' => PHP_VERSION,
                'ok' => version_compare(PHP_VERSION, '8.0', '>=')
            ],
            'extensions' => [
                'pdo' => extension_loaded('pdo'),
                'mbstring' => extension_loaded('mbstring'),
                'openssl' => extension_loaded('openssl'),
                'fileinfo' => extension_loaded('fileinfo'),
                'gd' => extension_loaded('gd'),
            ],
            'permissions' => $permissions,
            'installer_dirs' => $installerErrors,
        ];
        return view('install.welcome', compact('requirements'));
    }

    public function processStep(Request $request)
    {
        try {
            $licenseKey = trim($request->input('license_key', ''));
            
            if (empty($licenseKey)) {
                return back()->withInput()->with('license_error', 'مفتاح الترخيص مطلوب');
            }
            
            // التحقق من مفاتيح التطوير
            $isDevKey = in_array($licenseKey, ['DEV-2025-INTERNAL', 'DEV-2025-TESTING']) 
                       || preg_match('/^DEV-\d{4}-[A-Z0-9]{4,}$/i', $licenseKey);
            
            if ($isDevKey) {
                // حفظ في الجلسة
                session([
                    'license_key' => $licenseKey,
                    'license_verified' => true,
                    'install_step' => 'database'
                ]);
                
                return redirect()->route('install.database');
            }
            
            // للمفاتيح الأخرى، استخدام LicenseService
            $licenseService = app(\App\Services\LicenseService::class);
            $validation = $licenseService->validateLicenseKey($licenseKey);
            
            if ($validation['valid']) {
                session([
                    'license_key' => $licenseKey,
                    'license_verified' => true,
                    'install_step' => 'database'
                ]);
                
                return redirect()->route('install.database');
            }
            
            return back()->withInput()->with('license_error', $validation['message'] ?? 'مفتاح ترخيص غير صالح');
            
        } catch (\Exception $e) {
            return back()->withInput()->with('license_error', 'خطأ في معالجة مفتاح الترخيص: ' . $e->getMessage());
        }
    }

    public function database(Request $request)
    {
        // التحقق من التحقق من الترخيص
        if (!session('license_verified')) {
            return redirect()->route('install.index')->with('install_notice', 'يجب التحقق من مفتاح الترخيص أولاً');
        }
        
        return view('install.database');
    }

    public function migrate(Request $request)
    {
        // التحقق من اجتياز الخطوات السابقة
        if (!session('license_verified') || session('install_step') !== 'migrate') {
            return redirect()->route('install.index')->with('install_notice', 'يجب إكمال الخطوات السابقة أولاً');
        }
        
        return view('install.migrate');
    }

    public function runMigrate(Request $request)
    {
        // Check if any tables exist in the database
        try {
            $tables = \DB::select('SHOW TABLES');
            if (count($tables) > 0) {
                return redirect()->route('install.migrate')->with('migrate_error', 'The selected database is not empty. Please use a new or empty database for installation.');
            }
        } catch (\Exception $e) {
            return redirect()->route('install.migrate')->with('migrate_error', 'Could not check database tables: ' . $e->getMessage());
        }
        try {
            \Artisan::call('migrate:fresh', ['--force' => true]);
            

            
            // تحديث خطوة التثبيت
            session(['install_step' => 'admin']);
            
            // وضع رسالة نجاح في الجلسة
            Session::flash('success', 'تم ترحيل قاعدة البيانات بنجاح.');
            return redirect()->route('install.admin');
        } catch (\Exception $e) {
            return redirect()->route('install.migrate')->with('migrate_error', $e->getMessage());
        }
    }

    public function saveDatabase(Request $request)
    {
        $request->validate([
            'db_host' => 'required',
            'db_database' => 'required',
            'db_username' => 'required',
            'db_password' => 'nullable',
        ]);

        $envData = [
            'DB_HOST' => $request->db_host,
            'DB_DATABASE' => $request->db_database,
            'DB_USERNAME' => $request->db_username,
            'DB_PASSWORD' => $request->db_password,
        ];

        // اختبار الاتصال
        try {
            \DB::purge('mysql');
            config([
                'database.connections.mysql.host' => $envData['DB_HOST'],
                'database.connections.mysql.database' => $envData['DB_DATABASE'],
                'database.connections.mysql.username' => $envData['DB_USERNAME'],
                'database.connections.mysql.password' => $envData['DB_PASSWORD'],
            ]);
            \DB::connection()->getPdo();
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['db_error' => 'فشل الاتصال بقاعدة البيانات: ' . $e->getMessage()]);
        }

        // تحديث ملف .env
        foreach ($envData as $key => $value) {
            $this->setEnvValue($key, $value);
        }

        // تحديث خطوة التثبيت
        session(['install_step' => 'migrate']);

        return redirect()->route('install.migrate');
    }

    protected function setEnvValue($key, $value)
    {
        $envPath = base_path('.env');
        $content = file_get_contents($envPath);
        $pattern = "/^{$key}=.*$/m";
        $replacement = $key . '=' . $value;
        if (preg_match($pattern, $content)) {
            $content = preg_replace($pattern, $replacement, $content);
        } else {
            $content .= "\n{$replacement}";
        }
        file_put_contents($envPath, $content);
    }

    public function admin(Request $request)
    {
        // التحقق من اجتياز الخطوات السابقة
        if (!session('license_verified') || session('install_step') !== 'admin') {
            return redirect()->route('install.index')->with('install_notice', 'يجب إكمال الخطوات السابقة أولاً');
        }
        
        return view('install.admin');
    }

    public function saveAdmin(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Check if there are any users already
        if (\App\Models\User::count() > 0) {
            return redirect()->route('install.admin')
                ->with('error', 'يوجد بالفعل مستخدمين في النظام!');
        }

        // Create the admin user
        $user = new \App\Models\User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->save();

        // Assign super-admin role (or create it if it doesn't exist)
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(
            ['name' => 'super-admin'],
            ['guard_name' => 'web']
        );
        
        $user->assignRole($adminRole);

        // تحديث خطوة التثبيت
        session(['install_step' => 'currencies']);

        // Redirect to next step
        return redirect()->route('install.currencies');
    }

    public function currencies(Request $request)
    {
        // التحقق من اجتياز الخطوات السابقة
        if (!session('license_verified') || session('install_step') !== 'currencies') {
            return redirect()->route('install.index')->with('install_notice', 'يجب إكمال الخطوات السابقة أولاً');
        }
        
        return view('install.currencies');
    }

    public function saveCurrencies(Request $request)
    {
        $request->validate([
            'currencies' => 'required|array|min:1',
        ]);
        $allCurrencies = [
            'USD' => 'دولار أمريكي',
            'EUR' => 'يورو',
            'SAR' => 'ريال سعودي',
            'AED' => 'درهم إماراتي',
            'EGP' => 'جنيه مصري',
            'JOD' => 'دينار أردني',
            'KWD' => 'دينار كويتي',
            'QAR' => 'ريال قطري',
            'OMR' => 'ريال عماني',
            'BHD' => 'دينار بحريني',
            'IQD' => 'دينار عراقي',
            'TRY' => 'ليرة تركية',
            'GBP' => 'جنيه إسترليني',
        ];
        $currencies = $request->currencies;
        // Determine default currency
        if (count($currencies) == 1) {
            $defaultCurrency = $currencies[0];
        } else {
            $request->validate([
                'default_currency' => 'required|in:' . implode(',', $currencies),
            ]);
            $defaultCurrency = $request->default_currency;
        }
        session(['install_currencies' => $currencies]);
        session(['install_default_currency' => $defaultCurrency]);
        try {
            foreach ($currencies as $code) {
                \App\Models\Currency::firstOrCreate([
                    'code' => $code
                ], [
                    'name' => $allCurrencies[$code] ?? $code,
                    'symbol' => $code,
                    'is_active' => true,
                    'is_default' => $code == $defaultCurrency,
                ]);
            }
            // Ensure only one default
            \App\Models\Currency::where('code', '!=', $defaultCurrency)->update(['is_default' => false]);
        } catch (\Exception $e) {
            return back()->withInput()->with('currencies_error', 'فشل حفظ العملات: ' . $e->getMessage());
        }
        
        // تحديث خطوة التثبيت
        session(['install_step' => 'chart']);
        
        // انتقل مباشرة إلى صفحة شجرة الحسابات
        return redirect()->route('install.chart');
    }

    public function chart(Request $request)
    {
        // التحقق من اجتياز الخطوات السابقة
        if (!session('license_verified') || session('install_step') !== 'chart') {
            return redirect()->route('install.index')->with('install_notice', 'يجب إكمال الخطوات السابقة أولاً');
        }
        
        return view('install.chart');
    }

    public function importChart(Request $request)
    {
        $request->validate([
            'chart_type' => 'required|in:ar,en',
        ]);
        $currencies = session('install_currencies', ['USD']);
        if ($request->has('currencies')) {
            $currencies = $request->input('currencies');
            session(['install_currencies' => $currencies]);
        }
        try {
            foreach ($currencies as $currency) {
                $chart = $request->chart_type == 'ar'
                    ? \App\Helpers\ChartOfAccounts::getArabicChart($currency)
                    : \App\Helpers\ChartOfAccounts::getEnglishChart($currency);
                $codeToId = [];
                // إنشاء الحسابات
                foreach ($chart as $row) {
                    $data = $row;
                    unset($data['parent_code']);
                    $data['parent_id'] = null;
                    $account = \App\Models\Account::create($data);
                    $codeToId[$row['code']] = $account->id;
                }
                // تحديث parent_id
                foreach ($chart as $row) {
                    if ($row['parent_code']) {
                        $account = \App\Models\Account::where('code', $row['code'])->where('default_currency', $currency)->first();
                        if ($account && isset($codeToId[$row['parent_code']])) {
                            $account->parent_id = $codeToId[$row['parent_code']];
                            $account->save();
                        }
                    }
                }
                // ربط الحسابات الافتراضية تلقائيًا
                $defaultAccounts = [
                    'default_sales_account' => '4100',
                    'default_purchases_account' => '5110',
                    'default_customers_account' => '1301',
                    'default_suppliers_account' => '2101',
                    'salary_expense_account' => '5101',
                    'employee_payables_account' => '2106',
                    'deductions_account' => '2201',
                    'tax_account' => '5300',
                    'inventory_account' => '1401',
                    'main_bank_account' => '1201',
                    'main_cash_account' => '1101',
                ];
                $missing = [];
                foreach ($defaultAccounts as $settingKey => $accountCode) {
                    $account = \App\Models\Account::where('code', $accountCode)->where('default_currency', $currency)->first();
                    if ($account) {
                        \App\Models\AccountingSetting::updateOrCreate(
                            ['key' => $settingKey, 'currency' => $currency],
                            ['value' => $account->id]
                        );
                    } else {
                        $missing[] = $accountCode;
                    }
                }
                if (count($missing) > 0) {
                    $missingDetails = [];
                    foreach ($missing as $code) {
                        $name = array_search($code, $defaultAccounts);
                        $missingDetails[] = $code . ($name ? ' (' . $name . ')' : '');
                    }
                    return back()->with('chart_error', 
                        'لم يتم العثور على بعض الحسابات الافتراضية المطلوبة في الشجرة: ' . 
                        implode(", ", $missingDetails) . 
                        '. تحقق من أن شجرة الحسابات تحتوي على هذه الأرقام أو قم بإضافتها يدوياً بعد التثبيت.');
                }
            }
        } catch (\Exception $e) {
            return back()->with('chart_error', 'فشل استيراد الشجرة أو ربط الحسابات الافتراضية: ' . $e->getMessage());
        }
        
        // تحديث خطوة التثبيت
        session(['install_step' => 'finish']);
        
        // انتقل مباشرة إلى صفحة النهاية
        return redirect()->route('install.finish');
    }

    public function finish(Request $request)
    {
        // التحقق من اجتياز جميع خطوات التثبيت
        if (!session('license_verified') || session('install_step') !== 'finish') {
            return redirect()->route('install.index')->with('install_notice', 'يجب إكمال جميع خطوات التثبيت أولاً');
        }
        
        $lockPath = storage_path('app/install.lock');
        if (!file_exists($lockPath)) {
            // إنشاء مجلد app إذا لم يكن موجود
            if (!is_dir(storage_path('app'))) {
                mkdir(storage_path('app'), 0755, true);
            }
            
            file_put_contents($lockPath, date('Y-m-d H:i:s') . " - Installation completed");
            
            // Seed permissions after install
            try {
                \Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\PermissionSeeder', '--force' => true]);
                \Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\LicenseSeeder', '--force' => true]);
            } catch (\Exception $e) {
                \Log::warning('Failed to run seeders during installation: ' . $e->getMessage());
            }
        }
        
        // مسح بيانات التثبيت من الجلسة
        session()->forget(['license_key', 'license_verified', 'install_step']);
        
        return view('install.finish');
    }

    // حماية جميع خطوات التثبيت
    private function checkInstallLock()
    {
        if (file_exists(storage_path('app/install.lock'))) {
            abort(403, 'تم تثبيت النظام بالفعل.');
        }
    }

    // ============== أدوات الصيانة والإدارة ==============
    
    /**
     * صفحة أدوات الصيانة الرئيسية
     */
    public function maintenance(Request $request)
    {
        // التحقق من أن النظام مثبت
        if (!file_exists(storage_path('app/install.lock'))) {
            return redirect()->route('install.index')->with('install_notice', 'يجب تثبيت النظام أولاً');
        }

        $systemInfo = $this->getSystemInfo();
        return view('install.maintenance', compact('systemInfo'));
    }

    /**
     * فحص صحة النظام
     */
    public function systemCheck(Request $request)
    {
        if (!file_exists(storage_path('app/install.lock'))) {
            return redirect()->route('install.index');
        }

        $checks = $this->performSystemChecks();
        return view('install.system-check', compact('checks'));
    }

    /**
     * إنشاء نسخة احتياطية
     */
    public function backup(Request $request)
    {
        if (!file_exists(storage_path('app/install.lock'))) {
            return redirect()->route('install.index');
        }

        if ($request->isMethod('post')) {
            $result = $this->createDatabaseBackup();
            return response()->json($result);
        }

        $backups = $this->getAvailableBackups();
        return view('install.backup', compact('backups'));
    }

    /**
     * تحديث النظام
     */
    public function update(Request $request)
    {
        if (!file_exists(storage_path('app/install.lock'))) {
            return redirect()->route('install.index');
        }

        if ($request->isMethod('post')) {
            $result = $this->runSystemUpdate();
            return response()->json($result);
        }

        $updateInfo = $this->getUpdateInfo();
        return view('install.update', compact('updateInfo'));
    }

    /**
     * مسح الكاش
     */
    public function clearCache(Request $request)
    {
        try {
            \Artisan::call('cache:clear');
            \Artisan::call('config:clear');
            \Artisan::call('view:clear');
            \Artisan::call('route:clear');

            return response()->json([
                'success' => true,
                'message' => 'تم مسح جميع ملفات الكاش بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل في مسح الكاش: ' . $e->getMessage()
            ]);
        }
    }

    // ============== دوال المساعدة ==============

    private function getSystemInfo()
    {
        return [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'database_connection' => $this->testDatabaseConnection(),
            'storage_writable' => is_writable(storage_path()),
            'install_date' => file_exists(storage_path('app/install.lock')) 
                ? date('Y-m-d H:i:s', filemtime(storage_path('app/install.lock'))) 
                : null,
            'accounts_count' => Schema::hasTable('accounts') ? DB::table('accounts')->count() : 0,
            'users_count' => Schema::hasTable('users') ? DB::table('users')->count() : 0,
            'currencies_count' => Schema::hasTable('currencies') ? DB::table('currencies')->count() : 0,
        ];
    }

    private function performSystemChecks()
    {
        $checks = [
            'php_version' => [
                'name' => 'إصدار PHP',
                'status' => version_compare(PHP_VERSION, '8.1', '>='),
                'message' => 'PHP ' . PHP_VERSION . ' (المطلوب: 8.1+)'
            ],
            'database_connection' => [
                'name' => 'اتصال قاعدة البيانات',
                'status' => $this->testDatabaseConnection(),
                'message' => $this->testDatabaseConnection() ? 'متصل بنجاح' : 'فشل الاتصال'
            ],
            'storage_writable' => [
                'name' => 'أذونات التخزين',
                'status' => is_writable(storage_path()),
                'message' => is_writable(storage_path()) ? 'قابل للكتابة' : 'غير قابل للكتابة'
            ],
            'env_file' => [
                'name' => 'ملف الإعدادات',
                'status' => file_exists(base_path('.env')),
                'message' => file_exists(base_path('.env')) ? 'موجود' : 'غير موجود'
            ],
            'install_lock' => [
                'name' => 'حالة التثبيت',
                'status' => file_exists(storage_path('app/install.lock')),
                'message' => file_exists(storage_path('app/install.lock')) ? 'مثبت' : 'غير مثبت'
            ]
        ];

        // فحص الإضافات المطلوبة
        $requiredExtensions = ['pdo', 'pdo_mysql', 'mbstring', 'tokenizer', 'xml', 'ctype', 'json', 'bcmath', 'fileinfo', 'openssl'];
        foreach ($requiredExtensions as $extension) {
            $checks["ext_$extension"] = [
                'name' => "إضافة $extension",
                'status' => extension_loaded($extension),
                'message' => extension_loaded($extension) ? 'متاحة' : 'مفقودة'
            ];
        }

        return $checks;
    }

    private function testDatabaseConnection()
    {
        try {
            DB::connection()->getPdo();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function createDatabaseBackup()
    {
        try {
            $backupFile = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
            $backupPath = storage_path('app/backups/' . $backupFile);
            
            // إنشاء مجلد النسخ الاحتياطية إذا لم يكن موجوداً
            if (!is_dir(dirname($backupPath))) {
                mkdir(dirname($backupPath), 0755, true);
            }

            $dbConfig = config('database.connections.' . config('database.default'));
            $command = "mysqldump -h{$dbConfig['host']} -P{$dbConfig['port']} -u{$dbConfig['username']}";
            
            if (!empty($dbConfig['password'])) {
                $command .= " -p'{$dbConfig['password']}'";
            }
            
            $command .= " {$dbConfig['database']} > $backupPath 2>/dev/null";
            
            exec($command, $output, $returnCode);
            
            if ($returnCode === 0 && file_exists($backupPath) && filesize($backupPath) > 0) {
                return [
                    'success' => true,
                    'message' => 'تم إنشاء النسخة الاحتياطية بنجاح',
                    'file' => $backupFile,
                    'size' => $this->formatBytes(filesize($backupPath))
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'فشل في إنشاء النسخة الاحتياطية'
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'خطأ: ' . $e->getMessage()
            ];
        }
    }

    private function getAvailableBackups()
    {
        $backupsPath = storage_path('app/backups');
        if (!is_dir($backupsPath)) {
            return [];
        }

        $backups = [];
        $files = glob($backupsPath . '/backup_*.sql');
        
        foreach ($files as $file) {
            $backups[] = [
                'name' => basename($file),
                'date' => date('Y-m-d H:i:s', filemtime($file)),
                'size' => $this->formatBytes(filesize($file))
            ];
        }

        // ترتيب حسب التاريخ (الأحدث أولاً)
        usort($backups, function($a, $b) {
            return strcmp($b['date'], $a['date']);
        });

        return $backups;
    }

    private function runSystemUpdate()
    {
        try {
            // تشغيل الترحيلات الجديدة
            \Artisan::call('migrate', ['--force' => true]);
            
            // مسح الكاش
            \Artisan::call('cache:clear');
            \Artisan::call('config:clear');
            \Artisan::call('view:clear');
            \Artisan::call('route:clear');

            return [
                'success' => true,
                'message' => 'تم تحديث النظام بنجاح'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'فشل في تحديث النظام: ' . $e->getMessage()
            ];
        }
    }

    private function getUpdateInfo()
    {
        return [
            'current_version' => config('app.version', '2.2.1'),
            'has_pending_migrations' => $this->hasPendingMigrations(),
            'last_update' => file_exists(storage_path('app/last_update')) 
                ? file_get_contents(storage_path('app/last_update'))
                : null
        ];
    }

    private function hasPendingMigrations()
    {
        try {
            $output = \Artisan::call('migrate:status');
            return strpos(\Artisan::output(), 'Pending') !== false;
        } catch (\Exception $e) {
            return false;
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
} 