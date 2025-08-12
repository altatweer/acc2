<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 فحص البيانات مباشرة:\n";
echo "========================\n";

$defaultCurrency = \App\Models\Currency::getDefaultCode();
$account = \App\Models\Account::where('name', 'مبيعات عامة')->first();

if (!$account) {
    echo "❌ لا يوجد حساب مبيعات عامة\n";
    exit;
}

echo "📄 الحساب: {$account->name}\n";
echo "💰 العملة الافتراضية: {$account->default_currency}\n";

$lines = $account->journalEntryLines()->get();
echo "📝 عدد الخطوط: {$lines->count()}\n";

foreach ($lines as $line) {
    echo "   - العملة: {$line->currency} | المبلغ: {$line->credit} (دائن)\n";
}

$linesByCurrency = $lines->groupBy('currency');
echo "\n📊 تجميع حسب العملة:\n";

$rowsByCurrency = collect();

foreach ($linesByCurrency as $currency => $currencyLines) {
    $credit = $currencyLines->sum('credit');
    $actualCurrency = $currency ?: $defaultCurrency;
    
    echo "   {$actualCurrency}: {$credit}\n";
    
    if (!$rowsByCurrency->has($actualCurrency)) {
        $rowsByCurrency[$actualCurrency] = collect();
    }
    
    $rowsByCurrency[$actualCurrency]->push([
        'account' => $account,
        'credit' => $credit,
        'currency' => $actualCurrency
    ]);
}

echo "\n🎯 النتيجة النهائية:\n";
echo "العملات في rowsByCurrency: " . implode(', ', $rowsByCurrency->keys()->toArray()) . "\n";

if ($rowsByCurrency->has('USD')) {
    echo "✅ يظهر تحت USD\n";
} else {
    echo "❌ لا يظهر تحت USD\n";
}

if ($rowsByCurrency->has('IQD')) {
    echo "⚠️ يظهر أيضاً تحت IQD (مشكلة!)\n";
} else {
    echo "✅ لا يظهر تحت IQD\n";
} 