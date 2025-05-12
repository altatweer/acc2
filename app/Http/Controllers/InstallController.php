<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InstallController extends Controller
{
    public function __construct()
    {
        // Protect all install routes except finish
        $this->middleware(function ($request, $next) {
            if (!in_array($request->route()->getName(), ['install.finish'])) {
                if (file_exists(storage_path('app/install.lock'))) {
                    abort(403, 'تم تثبيت النظام بالفعل. لا يمكنك إعادة التثبيت.');
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
        ];
        return view('install.welcome', compact('requirements'));
    }

    public function processStep(Request $request)
    {
        // بعد تحقق المتطلبات، انتقل لخطوة إعداد قاعدة البيانات
        return redirect()->route('install.database');
    }

    public function database(Request $request)
    {
        return view('install.database');
    }

    public function migrate(Request $request)
    {
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
            \Artisan::call('migrate', ['--force' => true]);
        } catch (\Exception $e) {
            return redirect()->route('install.migrate')->with('migrate_error', $e->getMessage());
        }
        // Proceed to next step (admin creation)
        return redirect()->route('install.admin');
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
        return view('install.admin');
    }

    public function saveAdmin(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed|min:6',
        ]);

        try {
            $user = new \App\Models\User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = bcrypt($request->password);
            $user->is_super_admin = true;
            $user->save();
            // إذا كان هناك علاقة roles أو صلاحيات، أضفها هنا
            // مثال: $user->assignRole('super-admin');
        } catch (\Exception $e) {
            return back()->withInput()->with('admin_error', 'فشل إنشاء السوبر أدمن: ' . $e->getMessage());
        }
        // انتقل مباشرة إلى صفحة العملات
        return redirect()->route('install.currencies');
    }

    public function currencies(Request $request)
    {
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
        // انتقل مباشرة إلى صفحة شجرة الحسابات
        return redirect()->route('install.chart');
    }

    public function chart(Request $request)
    {
        return view('install.chart');
    }

    public function importChart(Request $request)
    {
        $request->validate([
            'chart_type' => 'required|in:ar,en',
        ]);
        $currencies = session('install_currencies', ['USD']); // استرجع العملات المختارة من الجلسة أو USD افتراضي
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
                // أولاً أنشئ كل المجموعات والحسابات بدون parent_id
                foreach ($chart as $row) {
                    $data = $row;
                    unset($data['parent_code']);
                    $data['parent_id'] = null;
                    $account = \App\Models\Account::create($data);
                    $codeToId[$row['code']] = $account->id;
                }
                // ثم حدث parent_id لكل حساب بناءً على parent_code
                foreach ($chart as $row) {
                    if ($row['parent_code']) {
                        $account = \App\Models\Account::where('code', $row['code'])->where('currency', $currency)->first();
                        if ($account && isset($codeToId[$row['parent_code']])) {
                            $account->parent_id = $codeToId[$row['parent_code']];
                            $account->save();
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            return back()->with('chart_error', 'فشل استيراد الشجرة: ' . $e->getMessage());
        }
        // انتقل مباشرة إلى صفحة النهاية
        return redirect()->route('install.finish');
    }

    public function skipChart(Request $request)
    {
        // انتقل مباشرة إلى صفحة النهاية
        return redirect()->route('install.finish');
    }

    public function finish(Request $request)
    {
        $lockPath = storage_path('app/install.lock');
        if (!file_exists($lockPath)) {
            file_put_contents($lockPath, 'installed');
        }
        return view('install.finish');
    }

    // حماية جميع خطوات التثبيت
    private function checkInstallLock()
    {
        if (file_exists(storage_path('app/install.lock'))) {
            abort(403, 'تم تثبيت النظام بالفعل.');
        }
    }
} 