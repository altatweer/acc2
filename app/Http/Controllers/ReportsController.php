<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BalanceSheetExport;
use App\Exports\IncomeStatementExport;
use App\Exports\ExpensesRevenuesExport;
use App\Exports\PayrollExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Mpdf\Mpdf;
use App\Models\Setting;

class ReportsController extends Controller
{
    public function trialBalance(Request $request)
    {
        $from = $request->input('from');
        $to = $request->input('to');
        $selectedCurrency = $request->input('currency');
        $displayCurrency = $request->input('display_currency');
        
        // جلب العملات المتاحة للفلترة
        $currencies = \App\Models\Currency::all();
        $defaultCurrency = \App\Models\Currency::getDefaultCode();
        
        // جلب الحسابات الفعلية
        $query = \App\Models\Account::where('is_group', false);
        
        // فلترة الحسابات حسب العملة إذا تم اختيارها
        if ($selectedCurrency) {
            $query->where('default_currency', $selectedCurrency);
        }
        
        $accounts = $query->orderBy('code')->get();
        
        $rows = [];
        $totalDebit = 0;
        $totalCredit = 0;
        $totalBalance = 0;
        
        foreach ($accounts as $account) {
            $query = $account->journalEntryLines();
            if ($from) {
                $query->whereHas('journalEntry', function($q) use ($from) {
                    $q->where('date', '>=', $from);
                });
            }
            if ($to) {
                $query->whereHas('journalEntry', function($q) use ($to) {
                    $q->where('date', '<=', $to);
                });
            }
            
            // جلب جميع الخطوط مع عملاتها الفعلية
            $lines = $query->get();
            
            // تجميع حسب العملة الفعلية للمعاملة
            $linesByCurrency = $lines->groupBy('currency');
            
            foreach ($linesByCurrency as $currency => $currencyLines) {
                $debit = $currencyLines->sum('debit');
                $credit = $currencyLines->sum('credit');
                // استخدام helper method لحساب الرصيد بناءً على nature الحساب
                $balance = $this->calculateAccountBalance($account, $debit, $credit);
                
                // تجاهل الحسابات التي ليس لديها أي حركة إذا كان الرصيد صفر
                if ($debit == 0 && $credit == 0 && $balance == 0) {
                    continue;
                }
                
                $rows[] = [
                    'account' => $account,
                    'debit' => $debit,
                    'credit' => $credit,
                    'balance' => $balance,
                    'currency' => $currency ?: $defaultCurrency, // العملة الفعلية للمعاملة
                ];
                
                $totalDebit += $debit;
                $totalCredit += $credit;
                $totalBalance += $balance;
            }
        }
        
        // تجميع الصفوف حسب العملة الفعلية للمعاملات
        $rowsByCurrency = collect($rows)->groupBy('currency');
        
        // حساب المجاميع حسب كل عملة
        $totalsByCurrency = [];
        $grandTotalInDefaultCurrency = [
            'debit' => 0,
            'credit' => 0,
            'balance' => 0,
        ];
        
        // الحصول على أسعار الصرف الحالية مع تفاصيل التحويل
        $exchangeRateDetails = [];
        $currentDate = now()->format('Y-m-d');
        
        foreach ($rowsByCurrency as $currencyCode => $currencyRows) {
            // الحصول على سعر الصرف مع التفاصيل
            $currency = \App\Models\Currency::where('code', $currencyCode)->first();
            $exchangeRate = $currency ? $currency->exchange_rate : 1;
            
            // الحصول على تاريخ آخر تحديث لسعر الصرف
            $lastRateUpdate = \App\Models\Currency::where('code', $currencyCode)
                ->value('updated_at');
                
            $exchangeRateDetails[$currencyCode] = [
                'rate' => $exchangeRate,
                'symbol' => $currency ? $currency->symbol : $currencyCode,
                'name' => $currency ? $currency->name : $currencyCode,
                'last_updated' => $lastRateUpdate ? \Carbon\Carbon::parse($lastRateUpdate)->format('Y-m-d H:i') : 'غير محدد',
                'is_default' => ($currencyCode === $defaultCurrency),
            ];
            
            $debit = $currencyRows->sum('debit');
            $credit = $currencyRows->sum('credit');
            $balance = $currencyRows->sum('balance');
            
            $totalsByCurrency[$currencyCode] = [
                'debit' => $debit,
                'credit' => $credit,
                'balance' => $balance,
                'count' => $currencyRows->count(),
                'exchange_rate' => $exchangeRate,
                'last_updated' => $exchangeRateDetails[$currencyCode]['last_updated'],
            ];
            
            // تحويل للعملة الافتراضية للمجموع الكلي
            if ($currencyCode !== $defaultCurrency) {
                $grandTotalInDefaultCurrency['debit'] += ($debit * $exchangeRate);
                $grandTotalInDefaultCurrency['credit'] += ($credit * $exchangeRate);
                $grandTotalInDefaultCurrency['balance'] += ($balance * $exchangeRate);
            } else {
                $grandTotalInDefaultCurrency['debit'] += $debit;
                $grandTotalInDefaultCurrency['credit'] += $credit;
                $grandTotalInDefaultCurrency['balance'] += $balance;
            }
        }
        
        // المجموع الكلي معروض بكل العملات المتاحة
        $grandTotalInAllCurrencies = [];
        
        foreach ($totalsByCurrency as $currencyCode => $totals) {
            $grandTotalInAllCurrencies[$currencyCode] = $totals;
        }
        
        // إعداد البيانات للعرض بعملة واحدة مع تفاصيل التحويل
        $allRowsInDisplayCurrency = [];
        $conversionDetails = [];
        $displayCurrencyTotals = [
            'debit' => 0,
            'credit' => 0,
            'balance' => 0,
        ];
        
        if ($displayCurrency) {
            // استخدام تاريخ الفترة للتحويل (أو تاريخ اليوم إذا لم يكن محدداً)
            $conversionDate = $to ?: ($from ?: now()->format('Y-m-d'));
            
            foreach ($rows as $row) {
                // استخدام العملة الفعلية للمعاملة من $row['currency'] وليس من الحساب
                $originalCurrency = $row['currency'] ?? $defaultCurrency;
                
                // تحويل القيم إلى العملة المختارة باستخدام السعر التاريخي المثبت في النظام
                $convertedDebit = ($originalCurrency != $displayCurrency) ? 
                    \App\Helpers\CurrencyHelper::convertWithHistoricalRate($row['debit'], $originalCurrency, $displayCurrency, $conversionDate) : 
                    $row['debit'];
                
                $convertedCredit = ($originalCurrency != $displayCurrency) ? 
                    \App\Helpers\CurrencyHelper::convertWithHistoricalRate($row['credit'], $originalCurrency, $displayCurrency, $conversionDate) : 
                    $row['credit'];
                
                $convertedBalance = ($originalCurrency != $displayCurrency) ? 
                    \App\Helpers\CurrencyHelper::convertWithHistoricalRate($row['balance'], $originalCurrency, $displayCurrency, $conversionDate) : 
                    $row['balance'];
                
                // حساب سعر الصرف المستخدم
                $exchangeRateUsed = 1;
                if ($originalCurrency != $displayCurrency) {
                    $exchangeRateUsed = \App\Helpers\CurrencyHelper::convertWithHistoricalRate(1, $originalCurrency, $displayCurrency, $conversionDate);
                }
                
                $allRowsInDisplayCurrency[] = [
                    'account' => $row['account'],
                    'original_currency' => $originalCurrency,
                    'debit' => $convertedDebit,
                    'credit' => $convertedCredit,
                    'balance' => $convertedBalance,
                    'exchange_rate_used' => $exchangeRateUsed,
                ];
                
                // تجميع المجاميع
                $displayCurrencyTotals['debit'] += $convertedDebit;
                $displayCurrencyTotals['credit'] += $convertedCredit;
                $displayCurrencyTotals['balance'] += $convertedBalance;
                
                // تجميع تفاصيل التحويل
                if ($originalCurrency != $displayCurrency && !isset($conversionDetails[$originalCurrency])) {
                    $conversionDetails[$originalCurrency] = [
                        'from' => $originalCurrency,
                        'to' => $displayCurrency,
                        'rate' => $exchangeRateUsed,
                        'date' => $conversionDate,
                    ];
                }
            }
        }
        
        $balanceType = $request->input('balance_type');
        if ($balanceType === 'positive') {
            $rows = array_filter($rows, fn($row) => $row['balance'] > 0);
            $rowsByCurrency = $rowsByCurrency->map(function($currencyRows) {
                return $currencyRows->filter(fn($row) => $row['balance'] > 0);
            })->filter(function($currencyRows) {
                return $currencyRows->count() > 0;
            });
            
            if ($displayCurrency) {
                $allRowsInDisplayCurrency = array_filter($allRowsInDisplayCurrency, fn($row) => $row['balance'] > 0);
            }
        } elseif ($balanceType === 'negative') {
            $rows = array_filter($rows, fn($row) => $row['balance'] < 0);
            $rowsByCurrency = $rowsByCurrency->map(function($currencyRows) {
                return $currencyRows->filter(fn($row) => $row['balance'] < 0);
            })->filter(function($currencyRows) {
                return $currencyRows->count() > 0;
            });
            
            if ($displayCurrency) {
                $allRowsInDisplayCurrency = array_filter($allRowsInDisplayCurrency, fn($row) => $row['balance'] < 0);
            }
        }
        
        return view('reports.trial_balance', compact(
            'rows', 
            'rowsByCurrency',
            'totalsByCurrency',
            'grandTotalInDefaultCurrency',
            'grandTotalInAllCurrencies',
            'allRowsInDisplayCurrency',
            'displayCurrencyTotals',
            'exchangeRateDetails',
            'conversionDetails',
            'from', 
            'to', 
            'totalDebit', 
            'totalCredit', 
            'totalBalance',
            'currencies',
            'selectedCurrency',
            'displayCurrency',
            'defaultCurrency'
        ));
    }
    public function balanceSheet(Request $request)
    {
        $from = $request->input('from');
        $to = $request->input('to');
        $selectedCurrency = $request->input('currency');
        $displayCurrency = $request->input('display_currency');
        
        // جلب العملات المتاحة للفلترة
        $currencies = \App\Models\Currency::all();
        $defaultCurrency = \App\Models\Currency::getDefaultCode();
        
        $types = [
            ['ar' => 'أصل', 'en' => 'asset'],
            ['ar' => 'خصم', 'en' => 'liability'],
            ['ar' => 'حقوق ملكية', 'en' => 'equity'],
        ];
        
        $sections = [];
        $sectionsByCurrency = [];
        $sectionTotalsByCurrency = [];
        
        foreach ($types as $typeArr) {
            $query = \App\Models\Account::where('is_group', false)
                ->where(function($q) use ($typeArr) {
                    // البحث في الحسابات التي لها parent
                    $q->whereHas('parent', function($subQ) use ($typeArr) {
                        $subQ->whereIn('type', [$typeArr['ar'], $typeArr['en']]);
                    })
                    // أو البحث في الحسابات التي ليس لها parent ولكن نوعها مطابق
                    ->orWhere(function($subQ) use ($typeArr) {
                        $subQ->whereNull('parent_id')
                            ->whereIn('type', [$typeArr['ar'], $typeArr['en']]);
                    });
                });
                
            if ($selectedCurrency) {
                $query->where('default_currency', $selectedCurrency);
            }
            
            $accounts = $query->orderBy('name')->get();
            $rows = [];
            $total = 0;
            $rowsByCurrency = collect();
            
            foreach ($accounts as $account) {
                $query = $account->journalEntryLines();
                if ($from) {
                    $query->whereHas('journalEntry', function($q) use ($from) {
                        $q->where('date', '>=', $from);
                    });
                }
                if ($to) {
                    $query->whereHas('journalEntry', function($q) use ($to) {
                        $q->where('date', '<=', $to);
                    });
                }
                
                // جلب جميع الخطوط مع عملاتها الفعلية
                $lines = $query->get();
                
                // تجميع حسب العملة الفعلية للمعاملة
                $linesByCurrency = $lines->groupBy('currency');
                
                foreach ($linesByCurrency as $currency => $currencyLines) {
                    $debit = $currencyLines->sum('debit');
                    $credit = $currencyLines->sum('credit');
                    // استخدام helper method لحساب الرصيد بناءً على nature الحساب
                    $balance = $this->calculateAccountBalance($account, $debit, $credit);
                    
                    // تجاهل الحسابات التي ليس لديها رصيد
                    if ($balance == 0) {
                        continue;
                    }
                    
                    $actualCurrency = $currency ?: $defaultCurrency;
                    
                    $row = [
                        'account' => $account,
                        'balance' => $balance,
                        'currency' => $actualCurrency, // العملة الفعلية للمعاملة
                    ];
                    
                    $rows[] = $row;
                    
                    // تجميع حسب العملة
                    if (!$rowsByCurrency->has($actualCurrency)) {
                        $rowsByCurrency[$actualCurrency] = collect();
                        if (!isset($sectionTotalsByCurrency[$actualCurrency])) {
                            $sectionTotalsByCurrency[$actualCurrency] = [];
                        }
                        if (!isset($sectionTotalsByCurrency[$actualCurrency][$typeArr['ar']])) {
                            $sectionTotalsByCurrency[$actualCurrency][$typeArr['ar']] = 0;
                        }
                    }
                    
                    $rowsByCurrency[$actualCurrency]->push($row);
                    $sectionTotalsByCurrency[$actualCurrency][$typeArr['ar']] += $balance;
                    
                    $total += $balance;
                }
            }
            
            $sections[$typeArr['ar']] = [
                'rows' => $rows,
                'total' => $total,
            ];
            
            foreach ($rowsByCurrency as $currencyCode => $currencyRows) {
                if (!isset($sectionsByCurrency[$currencyCode])) {
                    $sectionsByCurrency[$currencyCode] = [];
                }
                
                $sectionsByCurrency[$currencyCode][$typeArr['ar']] = [
                    'rows' => $currencyRows,
                    'total' => $sectionTotalsByCurrency[$currencyCode][$typeArr['ar']],
                ];
            }
        }
        
        // إعداد البيانات للعرض بعملة واحدة
        $sectionsInDisplayCurrency = [];
        if ($displayCurrency) {
            // استخدام تاريخ الفترة للتحويل (أو تاريخ اليوم إذا لم يكن محدداً)
            $conversionDate = $to ?: ($from ?: now()->format('Y-m-d'));
            
            foreach ($types as $typeArr) {
                $rowsInDisplayCurrency = [];
                $totalInDisplayCurrency = 0;
                
                foreach ($sections[$typeArr['ar']]['rows'] as $row) {
                    $originalCurrency = $row['currency'];
                    
                    // تحويل القيم إلى العملة المختارة باستخدام السعر التاريخي المثبت في النظام
                    $convertedBalance = ($originalCurrency != $displayCurrency) ? 
                        \App\Helpers\CurrencyHelper::convertWithHistoricalRate($row['balance'], $originalCurrency, $displayCurrency, $conversionDate) : 
                        $row['balance'];
                    
                    $rowsInDisplayCurrency[] = [
                        'account' => $row['account'],
                        'original_currency' => $originalCurrency,
                        'balance' => $convertedBalance,
                    ];
                    
                    $totalInDisplayCurrency += $convertedBalance;
                }
                
                $sectionsInDisplayCurrency[$typeArr['ar']] = [
                    'rows' => $rowsInDisplayCurrency,
                    'total' => $totalInDisplayCurrency,
                ];
            }
        }
        
        // Debug info لـ Balance Sheet
        \Log::info('Balance Sheet Debug:', [
            'currencies_in_sections' => array_keys($sectionsByCurrency),
            'total_sections' => count($sectionsByCurrency),
            'sample_section' => array_keys($sectionsByCurrency)[0] ?? null,
        ]);
        
        return view('reports.balance_sheet', compact(
            'sections', 
            'sectionsByCurrency',
            'sectionTotalsByCurrency',
            'sectionsInDisplayCurrency',
            'from', 
            'to',
            'currencies',
            'selectedCurrency',
            'displayCurrency',
            'defaultCurrency'
        ));
    }
    public function incomeStatement(Request $request)
    {
        $from = $request->input('from');
        $to = $request->input('to');
        $type = $request->input('type'); // إيراد أو مصروف أو الكل
        $parent_id = $request->input('parent_id'); // فلتر الفئة الرئيسية
        $currency = $request->input('currency'); // فلتر العملة
        $displayCurrency = $request->input('display_currency'); // عملة العرض

        // جلب العملات المتاحة
        $currencies = \App\Models\Currency::all();
        $defaultCurrency = \App\Models\Currency::getDefaultCode();

        // جلب الفئات الرئيسية (is_group = true)
        $groups = \App\Models\Account::where('is_group', true)
            ->when($type, function($q) use ($type) {
                $typeArr = $type ? [$type, $type === 'إيراد' ? 'revenue' : ($type === 'مصروف' ? 'expense' : $type)] : null;
                if ($typeArr) $q->whereIn('type', $typeArr);
            })
            ->orderBy('name')->get();

        // جلب الحسابات الفعلية المرتبطة بفئة نوعها إيراد أو مصروف
        $query = \App\Models\Account::where('is_group', false)
            ->where(function($q) use ($type) {
                // البحث في الحسابات التي لها parent
                $q->whereHas('parent', function($subQ) use ($type) {
                    if ($type) {
                        $typeArr = [$type, $type === 'إيراد' ? 'revenue' : ($type === 'مصروف' ? 'expense' : $type)];
                        $subQ->whereIn('type', $typeArr);
                    } else {
                        $subQ->whereIn('type', ['إيراد', 'مصروف', 'revenue', 'expense']);
                    }
                })
                // أو البحث في الحسابات التي ليس لها parent ولكن نوعها إيراد أو مصروف
                ->orWhere(function($subQ) use ($type) {
                    $subQ->whereNull('parent_id');
                    if ($type) {
                        $typeArr = [$type, $type === 'إيراد' ? 'revenue' : ($type === 'مصروف' ? 'expense' : $type)];
                        $subQ->whereIn('type', $typeArr);
                    } else {
                        $subQ->whereIn('type', ['إيراد', 'مصروف', 'revenue', 'expense']);
                    }
                });
            })
            ->when($parent_id, function($q) use ($parent_id) {
                $q->where('parent_id', $parent_id);
            })
            ->when($currency, function($q) use ($currency) {
                $q->where('default_currency', $currency);
            });
            
        // إضافة معلومات تشخيصية
        /*\Log::info('Income Statement Query Debug', [
            'total_accounts_found' => $query->count(),
            'filters' => [
                'type' => $type,
                'parent_id' => $parent_id,
                'currency' => $currency,
                'from' => $from,
                'to' => $to,
            ]
        ]);*/
        
        $accounts = $query->orderBy('name')->get();

        $rows = [];
        $totalRevenue = 0;
        $totalExpense = 0;
        $totalDebit = 0;
        $totalCredit = 0;
        $totalBalance = 0;
        
        // تجميع الصفوف حسب العملة
        $rowsByCurrency = collect();
        $revenuesByCurrency = [];
        $expensesByCurrency = [];
        $netByCurrency = [];
        
        foreach ($accounts as $account) {
            $query = $account->journalEntryLines();
            if ($from) {
                $query->whereHas('journalEntry', function($q) use ($from) {
                    $q->where('date', '>=', $from);
                });
            }
            if ($to) {
                $query->whereHas('journalEntry', function($q) use ($to) {
                    $q->where('date', '<=', $to);
                });
            }
            
            // جلب جميع الخطوط مع عملاتها الفعلية
            $lines = $query->get();
            
            // تحديد نوع الحساب بشكل أفضل
            $parentType = null;
            if ($account->parent) {
                $parentType = $account->parent->type;
            } else {
                $parentType = $account->type;
            }
            
            // التأكد من وجود نوع صحيح
            if (!$parentType || !in_array($parentType, ['إيراد', 'مصروف', 'revenue', 'expense'])) {
                // تخطي الحسابات التي ليس لها نوع صحيح
                continue;
            }
            
            // تجميع حسب العملة الفعلية للمعاملة
            $linesByCurrency = $lines->groupBy('currency');
            
            foreach ($linesByCurrency as $currency => $currencyLines) {
                $debit = $currencyLines->sum('debit');
                $credit = $currencyLines->sum('credit');
                // استخدام helper method لحساب الرصيد بناءً على nature الحساب
                $balance = $this->calculateAccountBalance($account, $debit, $credit);
                
                // تجاهل الحسابات التي ليس لديها أي حركة
                if ($debit == 0 && $credit == 0 && $balance == 0) {
                    continue;
                }
                
                // تحديد عملة المعاملة الفعلية
                $actualCurrency = $currency ?: $defaultCurrency;
                
                $row = [
                    'account' => $account,
                    'debit' => $debit,
                    'credit' => $credit,
                    'balance' => $balance,
                    'type' => $parentType,
                    'currency' => $actualCurrency, // العملة الفعلية للمعاملة
                ];
                
                $rows[] = $row;
                
                // إذا لم تكن العملة موجودة في المجموعة، أضف مجموعة جديدة
                if (!$rowsByCurrency->has($actualCurrency)) {
                    $rowsByCurrency[$actualCurrency] = collect();
                    $revenuesByCurrency[$actualCurrency] = 0;
                    $expensesByCurrency[$actualCurrency] = 0;
                }
                
                $rowsByCurrency[$actualCurrency]->push($row);
                
                $totalDebit += $debit;
                $totalCredit += $credit;
                $totalBalance += $balance;
                
                if (in_array($parentType, ['إيراد', 'revenue'])) {
                    $totalRevenue += abs($balance);
                    $revenuesByCurrency[$actualCurrency] += abs($balance);
                } elseif (in_array($parentType, ['مصروف', 'expense'])) {
                    $totalExpense += abs($balance);
                    $expensesByCurrency[$actualCurrency] += abs($balance);
                }
            }
        }
        
        // حساب صافي الربح/الخسارة لكل عملة (منفصل لكل عملة بدون دمج)
        foreach ($revenuesByCurrency as $curr => $revenue) {
            $expense = $expensesByCurrency[$curr] ?? 0;
            $netByCurrency[$curr] = $revenue - $expense;
        }
        
        $totalRevenue = abs($totalRevenue);
        $totalExpense = abs($totalExpense);
        $net = $totalRevenue - $totalExpense;
        
        // إعداد البيانات للعرض بعملة واحدة
        $allRowsInDisplayCurrency = [];
        $revenueInDisplayCurrency = 0;
        $expenseInDisplayCurrency = 0;
        
        // إضافة تفاصيل أسعار الصرف
        $exchangeRateDetails = [];
        $conversionDetails = [];
        
        if ($displayCurrency) {
            $conversionDate = now()->format('Y-m-d H:i');
            
            // جمع تفاصيل أسعار الصرف للعملات المستخدمة
            foreach ($rowsByCurrency as $currencyCode => $currencyRows) {
                $currency = \App\Models\Currency::where('code', $currencyCode)->first();
                $exchangeRate = $currency ? $currency->exchange_rate : 1;
                $lastRateUpdate = $currency ? $currency->updated_at->format('Y-m-d H:i') : 'غير محدد';
                
                $exchangeRateDetails[$currencyCode] = [
                    'rate' => $exchangeRate,
                    'symbol' => $currency ? $currency->symbol : $currencyCode,
                    'name' => $currency ? $currency->name : $currencyCode,
                    'last_updated' => $lastRateUpdate,
                    'is_default' => ($currencyCode === $defaultCurrency),
                ];
            }
            
            // استخدام تاريخ الفترة للتحويل (أو تاريخ اليوم إذا لم يكن محدداً)
            $conversionDate = $to ?: ($from ?: now()->format('Y-m-d'));
            
            foreach ($rows as $row) {
                $originalCurrency = $row['currency'];
                $parentType = $row['type'];
                
                // تحويل القيم إلى العملة المختارة باستخدام السعر التاريخي المثبت في النظام
                $convertedDebit = ($originalCurrency != $displayCurrency) ? 
                    \App\Helpers\CurrencyHelper::convertWithHistoricalRate($row['debit'], $originalCurrency, $displayCurrency, $conversionDate) : 
                    $row['debit'];
                
                $convertedCredit = ($originalCurrency != $displayCurrency) ? 
                    \App\Helpers\CurrencyHelper::convertWithHistoricalRate($row['credit'], $originalCurrency, $displayCurrency, $conversionDate) : 
                    $row['credit'];
                
                $convertedBalance = ($originalCurrency != $displayCurrency) ? 
                    \App\Helpers\CurrencyHelper::convertWithHistoricalRate($row['balance'], $originalCurrency, $displayCurrency, $conversionDate) : 
                    $row['balance'];
                
                // حساب سعر الصرف المستخدم
                $exchangeRateUsed = 1;
                if ($originalCurrency != $displayCurrency) {
                    $rateAmount = \App\Helpers\CurrencyHelper::convertWithHistoricalRate(1, $originalCurrency, $displayCurrency, $conversionDate);
                    $exchangeRateUsed = $rateAmount;
                }
                
                $allRowsInDisplayCurrency[] = [
                    'account' => $row['account'],
                    'original_currency' => $originalCurrency,
                    'debit' => $convertedDebit,
                    'credit' => $convertedCredit,
                    'balance' => $convertedBalance,
                    'type' => $parentType,
                    'exchange_rate_used' => $exchangeRateUsed,
                ];
                
                // تجميع تفاصيل التحويل
                if ($originalCurrency != $displayCurrency && !isset($conversionDetails[$originalCurrency])) {
                    $conversionDetails[$originalCurrency] = [
                        'from' => $originalCurrency,
                        'to' => $displayCurrency,
                        'rate' => $exchangeRateUsed,
                        'date' => $conversionDate,
                    ];
                }
                
                if (in_array($parentType, ['إيراد', 'revenue'])) {
                    $revenueInDisplayCurrency += abs($convertedBalance);
                } elseif (in_array($parentType, ['مصروف', 'expense'])) {
                    $expenseInDisplayCurrency += abs($convertedBalance);
                }
            }
        }
        
        $netInDisplayCurrency = $revenueInDisplayCurrency - $expenseInDisplayCurrency;
        
        // إضافة debug info
        \Log::info('Income Statement Debug:', [
            'total_rows' => count($rows),
            'currencies_in_data' => $rowsByCurrency->keys()->toArray(),
            'revenues_by_currency' => $revenuesByCurrency,
            'sample_row' => $rows[0] ?? null,
        ]);
        
        return view('reports.income_statement', compact(
            'rows', 
            'rowsByCurrency',
            'revenuesByCurrency',
            'expensesByCurrency',
            'netByCurrency',
            'allRowsInDisplayCurrency',
            'exchangeRateDetails',
            'conversionDetails',
            'revenueInDisplayCurrency',
            'expenseInDisplayCurrency',
            'netInDisplayCurrency',
            'from', 
            'to', 
            'type', 
            'groups', 
            'parent_id', 
            'totalRevenue', 
            'totalExpense', 
            'net',
            'currencies',
            'defaultCurrency',
            'currency',
            'displayCurrency'
        ));
    }
    public function payroll(Request $request)
    {
        $month = $request->input('month');
        $employeeName = $request->input('employee');
        $selectedCurrency = $request->input('currency');
        $displayCurrency = $request->input('display_currency');
        
        // جلب العملات المتاحة للفلترة
        $currencies = \App\Models\Currency::all();
        $defaultCurrency = \App\Models\Currency::getDefaultCode();
        
        // استعلام مع علاقة الموظف
        $query = \App\Models\SalaryPayment::with('employee');
        
        if ($month) {
            $query->where('salary_month', $month);
        }
        
        if ($employeeName) {
            $query->whereHas('employee', function($q) use ($employeeName) {
                $q->where('name', 'like', "%$employeeName%");
            });
        }
        
        if ($selectedCurrency) {
            $query->whereHas('employee', function($q) use ($selectedCurrency) {
                $q->where('currency', $selectedCurrency);
            });
        }
        
        $rows = $query->orderBy('salary_month', 'desc')->get();
        
        // تجميع الصفوف حسب العملة
        $rowsByCurrency = $rows->groupBy(function($row) {
            return $row->employee->currency ?? 'Unknown';
        });
        
        // حساب المجاميع لكل عملة
        $totalsByCurrency = [];
        
        foreach ($rowsByCurrency as $currencyCode => $currencyRows) {
            $totalsByCurrency[$currencyCode] = [
                'gross' => $currencyRows->sum('gross_salary'),
                'allowances' => $currencyRows->sum('total_allowances'),
                'deductions' => $currencyRows->sum('total_deductions'),
                'net' => $currencyRows->sum('net_salary'),
            ];
        }
        
        // المجاميع القديمة - لا نستخدمها في العرض الجديد، لكن نحتفظ بها للتوافقية
        $totalNet = $rows->sum('net_salary');
        $totalGross = $rows->sum('gross_salary');
        $totalAllowances = $rows->sum('total_allowances');
        $totalDeductions = $rows->sum('total_deductions');
        
        return view('reports.payroll', compact(
            'rows', 
            'rowsByCurrency',
            'totalsByCurrency',
            'month', 
            'employeeName', 
            'totalNet', 
            'totalGross', 
            'totalAllowances', 
            'totalDeductions',
            'currencies',
            'selectedCurrency',
            'displayCurrency',
            'defaultCurrency'
        ));
    }
    public function expensesRevenues(Request $request)
    {
        $from = $request->input('from');
        $to = $request->input('to');
        $selectedCurrency = $request->input('currency');
        $displayCurrency = $request->input('display_currency');
        
        // جلب العملات المتاحة للفلترة
        $currencies = \App\Models\Currency::all();
        $defaultCurrency = \App\Models\Currency::getDefaultCode();
        
        // جلب الحسابات الفعلية المرتبطة بفئة نوعها إيراد أو مصروف
        $query = \App\Models\Account::where('is_group', false)
            ->where(function($q) {
                // البحث في الحسابات التي لها parent
                $q->whereHas('parent', function($subQ) {
                    $subQ->whereIn('type', ['إيراد', 'مصروف', 'revenue', 'expense']);
                })
                // أو البحث في الحسابات التي ليس لها parent ولكن نوعها إيراد أو مصروف
                ->orWhere(function($subQ) {
                    $subQ->whereNull('parent_id')
                        ->whereIn('type', ['إيراد', 'مصروف', 'revenue', 'expense']);
                });
            });
        
        if ($selectedCurrency) {
            $query->where('default_currency', $selectedCurrency);
        }
        
        $accounts = $query->orderBy('name')->get();
        
        $rows = [];
        $totalRevenue = 0;
        $totalExpense = 0;
        $totalDebit = 0;
        $totalCredit = 0;
        $totalBalance = 0;
        
        // تجميع الصفوف حسب العملة
        $rowsByCurrency = collect();
        $revenuesByCurrency = [];
        $expensesByCurrency = [];
        $netByCurrency = [];
        
        foreach ($accounts as $account) {
            $query = $account->journalEntryLines();
            if ($from) {
                $query->whereHas('journalEntry', function($q) use ($from) {
                    $q->where('date', '>=', $from);
                });
            }
            if ($to) {
                $query->whereHas('journalEntry', function($q) use ($to) {
                    $q->where('date', '<=', $to);
                });
            }
            
            // جلب جميع الخطوط مع عملاتها الفعلية
            $lines = $query->get();
            
            // تجميع حسب العملة الفعلية للمعاملة
            $linesByCurrency = $lines->groupBy('currency');
            
            foreach ($linesByCurrency as $currency => $currencyLines) {
                $debit = $currencyLines->sum('debit');
                $credit = $currencyLines->sum('credit');
                // استخدام helper method لحساب الرصيد بناءً على nature الحساب
                $balance = $this->calculateAccountBalance($account, $debit, $credit);
                
                // تجاهل الحسابات التي ليس لديها أي حركة
                if ($debit == 0 && $credit == 0 && $balance == 0) {
                    continue;
                }
                
                // تحديد نوع الحساب بشكل أفضل
                $parentType = null;
                if ($account->parent) {
                    $parentType = $account->parent->type;
                } else {
                    $parentType = $account->type;
                }
                
                // التأكد من وجود نوع صحيح
                if (!$parentType || !in_array($parentType, ['إيراد', 'مصروف', 'revenue', 'expense'])) {
                    // تخطي الحسابات التي ليس لها نوع صحيح
                    continue;
                }
                
                // تحديد عملة المعاملة الفعلية
                $actualCurrency = $currency ?: $defaultCurrency;
                
                $row = [
                    'account' => $account,
                    'debit' => $debit,
                    'credit' => $credit,
                    'balance' => $balance,
                    'type' => $parentType,
                    'currency' => $actualCurrency, // العملة الفعلية للمعاملة
                ];
                
                $rows[] = $row;
                
                // إذا لم تكن العملة موجودة في المجموعة، أضف مجموعة جديدة
                if (!$rowsByCurrency->has($actualCurrency)) {
                    $rowsByCurrency[$actualCurrency] = collect();
                    $revenuesByCurrency[$actualCurrency] = 0;
                    $expensesByCurrency[$actualCurrency] = 0;
                }
                
                $rowsByCurrency[$actualCurrency]->push($row);
                
                $totalDebit += $debit;
                $totalCredit += $credit;
                $totalBalance += $balance;
                
                if (in_array($parentType, ['إيراد', 'revenue'])) {
                    $totalRevenue += abs($balance);
                    $revenuesByCurrency[$actualCurrency] += abs($balance);
                } elseif (in_array($parentType, ['مصروف', 'expense'])) {
                    $totalExpense += abs($balance);
                    $expensesByCurrency[$actualCurrency] += abs($balance);
                }
            }
        }
        
        // حساب صافي الربح/الخسارة لكل عملة
        foreach ($revenuesByCurrency as $currency => $revenue) {
            $netByCurrency[$currency] = $revenue - ($expensesByCurrency[$currency] ?? 0);
        }
        
        // إعداد البيانات للعرض بعملة واحدة
        $allRowsInDisplayCurrency = [];
        $revenueInDisplayCurrency = 0;
        $expenseInDisplayCurrency = 0;
        
        if ($displayCurrency) {
            // استخدام تاريخ الفترة للتحويل (أو تاريخ اليوم إذا لم يكن محدداً)
            $conversionDate = $to ?: ($from ?: now()->format('Y-m-d'));
            
            foreach ($rows as $row) {
                $originalCurrency = $row['currency'];
                $parentType = $row['type'];
                
                // تحويل القيم إلى العملة المختارة باستخدام السعر التاريخي المثبت في النظام
                $convertedDebit = ($originalCurrency != $displayCurrency) ? 
                    \App\Helpers\CurrencyHelper::convertWithHistoricalRate($row['debit'], $originalCurrency, $displayCurrency, $conversionDate) : 
                    $row['debit'];
                
                $convertedCredit = ($originalCurrency != $displayCurrency) ? 
                    \App\Helpers\CurrencyHelper::convertWithHistoricalRate($row['credit'], $originalCurrency, $displayCurrency, $conversionDate) : 
                    $row['credit'];
                
                $convertedBalance = ($originalCurrency != $displayCurrency) ? 
                    \App\Helpers\CurrencyHelper::convertWithHistoricalRate($row['balance'], $originalCurrency, $displayCurrency, $conversionDate) : 
                    $row['balance'];
                
                $allRowsInDisplayCurrency[] = [
                    'account' => $row['account'],
                    'original_currency' => $originalCurrency,
                    'debit' => $convertedDebit,
                    'credit' => $convertedCredit,
                    'balance' => $convertedBalance,
                    'type' => $parentType,
                ];
                
                if (in_array($parentType, ['إيراد', 'revenue'])) {
                    $revenueInDisplayCurrency += abs($convertedBalance);
                } elseif (in_array($parentType, ['مصروف', 'expense'])) {
                    $expenseInDisplayCurrency += abs($convertedBalance);
                }
            }
        }
        
        $netInDisplayCurrency = $revenueInDisplayCurrency - $expenseInDisplayCurrency;
        
        return view('reports.expenses_revenues', compact(
            'rows', 
            'rowsByCurrency',
            'revenuesByCurrency',
            'expensesByCurrency',
            'netByCurrency',
            'allRowsInDisplayCurrency',
            'revenueInDisplayCurrency',
            'expenseInDisplayCurrency',
            'netInDisplayCurrency',
            'from', 
            'to', 
            'totalRevenue', 
            'totalExpense',
            'currencies',
            'selectedCurrency',
            'displayCurrency',
            'defaultCurrency'
        ));
    }
    public function exportBalanceSheetExcel(Request $request)
    {
        $from = $request->input('from');
        $to = $request->input('to');
        $types = [
            ['ar' => 'أصل', 'en' => 'asset'],
            ['ar' => 'خصم', 'en' => 'liability'],
            ['ar' => 'حقوق ملكية', 'en' => 'equity'],
        ];
        $sections = [];
        foreach ($types as $typeArr) {
            $accounts = \App\Models\Account::where('is_group', false)
                ->whereHas('parent', function($q) use ($typeArr) {
                    $q->whereIn('type', [$typeArr['ar'], $typeArr['en']]);
                })
                ->orderBy('name')->get();
            $rows = [];
            $total = 0;
            foreach ($accounts as $account) {
                $query = $account->journalEntryLines();
                if ($from) {
                    $query->whereHas('journalEntry', function($q) use ($from) {
                        $q->where('date', '>=', $from);
                    });
                }
                if ($to) {
                    $query->whereHas('journalEntry', function($q) use ($to) {
                        $q->where('date', '<=', $to);
                    });
                }
                $debit = $query->sum('debit');
                $credit = $query->sum('credit');
                $balance = $debit - $credit;
                $rows[] = [
                    'account' => $account,
                    'balance' => $balance,
                ];
                $total += $balance;
            }
            $sections[$typeArr['ar']] = [
                'rows' => $rows,
                'total' => $total,
            ];
        }
        return Excel::download(new BalanceSheetExport($sections, $from, $to), 'balance_sheet.xlsx');
    }
    public function exportIncomeStatementExcel(Request $request)
    {
        $from = $request->input('from');
        $to = $request->input('to');
        $type = $request->input('type');
        $parent_id = $request->input('parent_id');
        $groups = \App\Models\Account::where('is_group', true)
            ->when($type, function($q) use ($type) {
                $typeArr = $type ? [$type, $type === 'إيراد' ? 'revenue' : ($type === 'مصروف' ? 'expense' : $type)] : null;
                if ($typeArr) $q->whereIn('type', $typeArr);
            })
            ->orderBy('name')->get();
        $accounts = \App\Models\Account::where('is_group', false)
            ->whereHas('parent', function($q) use ($type) {
                if ($type) {
                    $typeArr = [$type, $type === 'إيراد' ? 'revenue' : ($type === 'مصروف' ? 'expense' : $type)];
                    $q->whereIn('type', $typeArr);
                } else {
                    $q->whereIn('type', ['إيراد', 'مصروف', 'revenue', 'expense']);
                }
            })
            ->when($parent_id, function($q) use ($parent_id) {
                $q->where('parent_id', $parent_id);
            })
            ->orderBy('name')->get();
        $rows = [];
        $totalRevenue = 0;
        $totalExpense = 0;
        $totalDebit = 0;
        $totalCredit = 0;
        $totalBalance = 0;
        foreach ($accounts as $account) {
            $query = $account->journalEntryLines();
            if ($from) {
                $query->whereHas('journalEntry', function($q) use ($from) {
                    $q->where('date', '>=', $from);
                });
            }
            if ($to) {
                $query->whereHas('journalEntry', function($q) use ($to) {
                    $q->where('date', '<=', $to);
                });
            }
            $debit = $query->sum('debit');
            $credit = $query->sum('credit');
            $balance = $debit - $credit;
            $parentType = $account->parent ? $account->parent->type : null;
            // تجاهل الحسابات التي ليس لديها أي حركة
            if ($debit == 0 && $credit == 0 && $balance == 0) {
                continue;
            }
            $rows[] = [
                'account' => $account,
                'debit' => $debit,
                'credit' => $credit,
                'balance' => $balance,
                'type' => $parentType,
            ];
            $totalDebit += $debit;
            $totalCredit += $credit;
            $totalBalance += $balance;
            if (in_array($parentType, ['إيراد', 'revenue'])) {
                $totalRevenue += $balance;
            } elseif (in_array($parentType, ['مصروف', 'expense'])) {
                $totalExpense += $balance;
            }
        }
        $totalRevenue = abs($totalRevenue);
        $totalExpense = abs($totalExpense);
        $net = $totalRevenue - $totalExpense;
        return Excel::download(new IncomeStatementExport($rows, $from, $to, $type, $groups, $parent_id, $totalRevenue, $totalExpense, $net), 'income_statement.xlsx');
    }
    public function exportExpensesRevenuesExcel(Request $request)
    {
        $from = $request->input('from');
        $to = $request->input('to');
        $accounts = \App\Models\Account::where('is_group', false)
            ->whereHas('parent', function($q) {
                $q->whereIn('type', ['إيراد', 'مصروف', 'revenue', 'expense']);
            })
            ->orderBy('name')->get();
        $rows = [];
        $totalRevenue = 0;
        $totalExpense = 0;
        foreach ($accounts as $account) {
            $query = $account->journalEntryLines();
            if ($from) {
                $query->whereHas('journalEntry', function($q) use ($from) {
                    $q->where('date', '>=', $from);
                });
            }
            if ($to) {
                $query->whereHas('journalEntry', function($q) use ($to) {
                    $q->where('date', '<=', $to);
                });
            }
            $debit = $query->sum('debit');
            $credit = $query->sum('credit');
            $balance = $debit - $credit;
            $parentType = $account->parent ? $account->parent->type : null;
            // تجاهل الحسابات التي ليس لديها أي حركة
            if ($debit == 0 && $credit == 0 && $balance == 0) {
                continue;
            }
            $rows[] = [
                'account' => $account,
                'debit' => $debit,
                'credit' => $credit,
                'balance' => $balance,
                'type' => $parentType,
            ];
            if (in_array($parentType, ['إيراد', 'revenue'])) {
                $totalRevenue += $balance;
            } elseif (in_array($parentType, ['مصروف', 'expense'])) {
                $totalExpense += $balance;
            }
        }
        return Excel::download(new ExpensesRevenuesExport($rows, $from, $to, $totalRevenue, $totalExpense), 'expenses_revenues.xlsx');
    }
    public function exportPayrollExcel(Request $request)
    {
        $month = $request->input('month');
        $employeeName = $request->input('employee');
        $query = \App\Models\SalaryPayment::with('employee');
        if ($month) {
            $query->where('salary_month', $month);
        }
        if ($employeeName) {
            $query->whereHas('employee', function($q) use ($employeeName) {
                $q->where('name', 'like', "%$employeeName%");
            });
        }
        $rows = $query->orderBy('salary_month', 'desc')->get();
        $totalNet = $rows->sum('net_salary');
        $totalGross = $rows->sum('gross_salary');
        $totalAllowances = $rows->sum('total_allowances');
        $totalDeductions = $rows->sum('total_deductions');
        return Excel::download(new PayrollExport($rows, $month, $employeeName, $totalNet, $totalGross, $totalAllowances, $totalDeductions), 'payroll.xlsx');
    }
    public function exportBalanceSheetPdf(Request $request)
    {
        $from = $request->input('from');
        $to = $request->input('to');
        $types = [
            ['ar' => 'أصل', 'en' => 'asset'],
            ['ar' => 'خصم', 'en' => 'liability'],
            ['ar' => 'حقوق ملكية', 'en' => 'equity'],
        ];
        $sections = [];
        foreach ($types as $typeArr) {
            $accounts = \App\Models\Account::where('is_group', false)
                ->whereHas('parent', function($q) use ($typeArr) {
                    $q->whereIn('type', [$typeArr['ar'], $typeArr['en']]);
                })
                ->orderBy('name')->get();
            $rows = [];
            $total = 0;
            foreach ($accounts as $account) {
                $query = $account->journalEntryLines();
                if ($from) {
                    $query->whereHas('journalEntry', function($q) use ($from) {
                        $q->where('date', '>=', $from);
                    });
                }
                if ($to) {
                    $query->whereHas('journalEntry', function($q) use ($to) {
                        $q->where('date', '<=', $to);
                    });
                }
                $debit = $query->sum('debit');
                $credit = $query->sum('credit');
                $balance = $debit - $credit;
                $rows[] = [
                    'account' => $account,
                    'balance' => $balance,
                ];
                $total += $balance;
            }
            $sections[$typeArr['ar']] = [
                'rows' => $rows,
                'total' => $total,
            ];
        }
        
        // الحصول على إعدادات الطباعة المخصصة
        $printSettings = \App\Models\PrintSetting::current();
        
        $html = view('reports.balance_sheet_pdf', [
            'sections' => $sections,
            'from' => $from,
            'to' => $to,
            'export' => true,
            'printSettings' => $printSettings,
        ])->render();
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4-L',
            'default_font' => 'amiri',
            'orientation' => 'L',
            'default_font_size' => 12,
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
            'tempDir' => storage_path('fonts'),
        ]);
        $mpdf->SetDirectionality('rtl');
        $mpdf->WriteHTML($html);
        return response($mpdf->Output('balance_sheet.pdf', \Mpdf\Output\Destination::STRING_RETURN))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="balance_sheet.pdf"');
    }
    public function exportIncomeStatementPdf(Request $request)
    {
        $from = $request->input('from');
        $to = $request->input('to');
        $type = $request->input('type');
        $parent_id = $request->input('parent_id');
        $groups = \App\Models\Account::where('is_group', true)
            ->when($type, function($q) use ($type) {
                $typeArr = $type ? [$type, $type === 'إيراد' ? 'revenue' : ($type === 'مصروف' ? 'expense' : $type)] : null;
                if ($typeArr) $q->whereIn('type', $typeArr);
            })
            ->orderBy('name')->get();
        $accounts = \App\Models\Account::where('is_group', false)
            ->whereHas('parent', function($q) use ($type) {
                if ($type) {
                    $typeArr = [$type, $type === 'إيراد' ? 'revenue' : ($type === 'مصروف' ? 'expense' : $type)];
                    $q->whereIn('type', $typeArr);
                } else {
                    $q->whereIn('type', ['إيراد', 'مصروف', 'revenue', 'expense']);
                }
            })
            ->when($parent_id, function($q) use ($parent_id) {
                $q->where('parent_id', $parent_id);
            })
            ->orderBy('name')->get();
        $rows = [];
        $totalRevenue = 0;
        $totalExpense = 0;
        foreach ($accounts as $account) {
            $query = $account->journalEntryLines();
            if ($from) {
                $query->whereHas('journalEntry', function($q) use ($from) {
                    $q->where('date', '>=', $from);
                });
            }
            if ($to) {
                $query->whereHas('journalEntry', function($q) use ($to) {
                    $q->where('date', '<=', $to);
                });
            }
            $debit = $query->sum('debit');
            $credit = $query->sum('credit');
            $balance = $debit - $credit;
            $parentType = $account->parent ? $account->parent->type : null;
            // تجاهل الحسابات التي ليس لديها أي حركة
            if ($debit == 0 && $credit == 0 && $balance == 0) {
                continue;
            }
            $rows[] = [
                'account' => $account,
                'debit' => $debit,
                'credit' => $credit,
                'balance' => $balance,
                'type' => $parentType,
            ];
            if (in_array($parentType, ['إيراد', 'revenue'])) {
                $totalRevenue += $balance;
            } elseif (in_array($parentType, ['مصروف', 'expense'])) {
                $totalExpense += $balance;
            }
        }
        $totalRevenue = abs($totalRevenue);
        $totalExpense = abs($totalExpense);
        $net = $totalRevenue - $totalExpense;
        $html = view('reports.income_statement_pdf', [
            'rows' => $rows,
            'from' => $from,
            'to' => $to,
            'type' => $type,
            'groups' => $groups,
            'parent_id' => $parent_id,
            'totalRevenue' => $totalRevenue,
            'totalExpense' => $totalExpense,
            'net' => $net,
            'export' => true,
        ])->render();
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4-L',
            'default_font' => 'amiri',
            'orientation' => 'L',
            'default_font_size' => 12,
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
            'tempDir' => storage_path('fonts'),
        ]);
        $mpdf->SetDirectionality('rtl');
        $mpdf->WriteHTML($html);
        return response($mpdf->Output('income_statement.pdf', \Mpdf\Output\Destination::STRING_RETURN))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="income_statement.pdf"');
    }
    public function exportExpensesRevenuesPdf(Request $request)
    {
        $from = $request->input('from');
        $to = $request->input('to');
        $accounts = \App\Models\Account::where('is_group', false)
            ->whereHas('parent', function($q) {
                $q->whereIn('type', ['إيراد', 'مصروف', 'revenue', 'expense']);
            })
            ->orderBy('name')->get();
        $rows = [];
        $totalRevenue = 0;
        $totalExpense = 0;
        foreach ($accounts as $account) {
            $query = $account->journalEntryLines();
            if ($from) {
                $query->whereHas('journalEntry', function($q) use ($from) {
                    $q->where('date', '>=', $from);
                });
            }
            if ($to) {
                $query->whereHas('journalEntry', function($q) use ($to) {
                    $q->where('date', '<=', $to);
                });
            }
            $debit = $query->sum('debit');
            $credit = $query->sum('credit');
            $balance = $debit - $credit;
            $parentType = $account->parent ? $account->parent->type : null;
            // تجاهل الحسابات التي ليس لديها أي حركة
            if ($debit == 0 && $credit == 0 && $balance == 0) {
                continue;
            }
            $rows[] = [
                'account' => $account,
                'debit' => $debit,
                'credit' => $credit,
                'balance' => $balance,
                'type' => $parentType,
            ];
            if (in_array($parentType, ['إيراد', 'revenue'])) {
                $totalRevenue += $balance;
            } elseif (in_array($parentType, ['مصروف', 'expense'])) {
                $totalExpense += $balance;
            }
        }
        $html = view('reports.expenses_revenues_pdf', [
            'rows' => $rows,
            'from' => $from,
            'to' => $to,
            'totalRevenue' => $totalRevenue,
            'totalExpense' => $totalExpense,
            'export' => true,
        ])->render();
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4-L',
            'default_font' => 'amiri',
            'orientation' => 'L',
            'default_font_size' => 12,
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
            'tempDir' => storage_path('fonts'),
        ]);
        $mpdf->SetDirectionality('rtl');
        $mpdf->WriteHTML($html);
        return response($mpdf->Output('expenses_revenues.pdf', \Mpdf\Output\Destination::STRING_RETURN))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="expenses_revenues.pdf"');
    }
    public function exportPayrollPdf(Request $request)
    {
        $month = $request->input('month');
        $employeeName = $request->input('employee');
        $query = \App\Models\SalaryPayment::with('employee');
        if ($month) {
            $query->where('salary_month', $month);
        }
        if ($employeeName) {
            $query->whereHas('employee', function($q) use ($employeeName) {
                $q->where('name', 'like', "%$employeeName%");
            });
        }
        $rows = $query->orderBy('salary_month', 'desc')->get();
        $totalNet = $rows->sum('net_salary');
        $totalGross = $rows->sum('gross_salary');
        $totalAllowances = $rows->sum('total_allowances');
        $totalDeductions = $rows->sum('total_deductions');
        $html = view('reports.payroll', [
            'rows' => $rows,
            'month' => $month,
            'employeeName' => $employeeName,
            'totalNet' => $totalNet,
            'totalGross' => $totalGross,
            'totalAllowances' => $totalAllowances,
            'totalDeductions' => $totalDeductions,
            'export' => true,
        ])->render();
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4-L',
            'default_font' => 'amiri',
            'orientation' => 'L',
            'default_font_size' => 12,
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
            'tempDir' => storage_path('fonts'),
        ]);
        $mpdf->SetDirectionality('rtl');
        $mpdf->WriteHTML($html);
        return response($mpdf->Output('payroll.pdf', \Mpdf\Output\Destination::STRING_RETURN))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="payroll.pdf"');
    }
    public function exportIncomeStatementMpdf(Request $request)
    {
        $from = $request->input('from');
        $to = $request->input('to');
        $type = $request->input('type');
        $parent_id = $request->input('parent_id');
        $groups = \App\Models\Account::where('is_group', true)
            ->when($type, function($q) use ($type) {
                $typeArr = $type ? [$type, $type === 'إيراد' ? 'revenue' : ($type === 'مصروف' ? 'expense' : $type)] : null;
                if ($typeArr) $q->whereIn('type', $typeArr);
            })
            ->orderBy('name')->get();
        $accounts = \App\Models\Account::where('is_group', false)
            ->whereHas('parent', function($q) use ($type) {
                if ($type) {
                    $typeArr = [$type, $type === 'إيراد' ? 'revenue' : ($type === 'مصروف' ? 'expense' : $type)];
                    $q->whereIn('type', $typeArr);
                } else {
                    $q->whereIn('type', ['إيراد', 'مصروف', 'revenue', 'expense']);
                }
            })
            ->when($parent_id, function($q) use ($parent_id) {
                $q->where('parent_id', $parent_id);
            })
            ->orderBy('name')->get();
        $rows = [];
        $totalRevenue = 0;
        $totalExpense = 0;
        foreach ($accounts as $account) {
            $query = $account->journalEntryLines();
            if ($from) {
                $query->whereHas('journalEntry', function($q) use ($from) {
                    $q->where('date', '>=', $from);
                });
            }
            if ($to) {
                $query->whereHas('journalEntry', function($q) use ($to) {
                    $q->where('date', '<=', $to);
                });
            }
            $debit = $query->sum('debit');
            $credit = $query->sum('credit');
            $balance = $debit - $credit;
            $parentType = $account->parent ? $account->parent->type : null;
            // تجاهل الحسابات التي ليس لديها أي حركة
            if ($debit == 0 && $credit == 0 && $balance == 0) {
                continue;
            }
            $rows[] = [
                'account' => $account,
                'debit' => $debit,
                'credit' => $credit,
                'balance' => $balance,
                'type' => $parentType,
            ];
            if (in_array($parentType, ['إيراد', 'revenue'])) {
                $totalRevenue += $balance;
            } elseif (in_array($parentType, ['مصروف', 'expense'])) {
                $totalExpense += $balance;
            }
        }
        $net = $totalRevenue - $totalExpense;
        $html = view('reports.income_statement_pdf', [
            'rows' => $rows,
            'from' => $from,
            'to' => $to,
            'type' => $type,
            'groups' => $groups,
            'parent_id' => $parent_id,
            'totalRevenue' => $totalRevenue,
            'totalExpense' => $totalExpense,
            'net' => $net,
            'export' => true,
        ])->render();
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4-L',
            'default_font' => 'amiri',
            'orientation' => 'L',
            'default_font_size' => 12,
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
            'tempDir' => storage_path('fonts'),
        ]);
        $mpdf->SetDirectionality('rtl');
        $mpdf->WriteHTML($html);
        return response($mpdf->Output('income_statement_mpdf.pdf', \Mpdf\Output\Destination::STRING_RETURN))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="income_statement_mpdf.pdf"');
    }
    public function exportBalanceSheetMpdf(Request $request)
    {
        $from = $request->input('from');
        $to = $request->input('to');
        $types = [
            ['ar' => 'أصل', 'en' => 'asset'],
            ['ar' => 'خصم', 'en' => 'liability'],
            ['ar' => 'حقوق ملكية', 'en' => 'equity'],
        ];
        $sections = [];
        foreach ($types as $typeArr) {
            $accounts = \App\Models\Account::where('is_group', false)
                ->whereHas('parent', function($q) use ($typeArr) {
                    $q->whereIn('type', [$typeArr['ar'], $typeArr['en']]);
                })
                ->orderBy('name')->get();
            $rows = [];
            $total = 0;
            foreach ($accounts as $account) {
                $query = $account->journalEntryLines();
                if ($from) {
                    $query->whereHas('journalEntry', function($q) use ($from) {
                        $q->where('date', '>=', $from);
                    });
                }
                if ($to) {
                    $query->whereHas('journalEntry', function($q) use ($to) {
                        $q->where('date', '<=', $to);
                    });
                }
                $debit = $query->sum('debit');
                $credit = $query->sum('credit');
                $balance = $debit - $credit;
                $rows[] = [
                    'account' => $account,
                    'balance' => $balance,
                ];
                $total += $balance;
            }
            $sections[$typeArr['ar']] = [
                'rows' => $rows,
                'total' => $total,
            ];
        }
        $html = view('reports.balance_sheet_pdf', [
            'sections' => $sections,
            'from' => $from,
            'to' => $to,
            'export' => true,
        ])->render();
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4-L',
            'default_font' => 'amiri',
            'orientation' => 'L',
            'default_font_size' => 12,
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
            'tempDir' => storage_path('fonts'),
        ]);
        $mpdf->SetDirectionality('rtl');
        $mpdf->WriteHTML($html);
        return response($mpdf->Output('balance_sheet_mpdf.pdf', \Mpdf\Output\Destination::STRING_RETURN))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="balance_sheet_mpdf.pdf"');
    }
    public function testArabicMpdf()
    {
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'default_font' => 'amiri',
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
        ]);
        $mpdf->SetDirectionality('rtl');
        $mpdf->WriteHTML('<h1>تجربة اللغة العربية</h1><p>هذا نص عربي متصل وسليم.</p>');
        return response($mpdf->Output('test.pdf', \Mpdf\Output\Destination::STRING_RETURN))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="test.pdf"');
    }
    /**
     * تقرير مقارنة العملات مع الرسوم البيانية
     */
    public function currencyComparison(Request $request)
    {
        $from = $request->input('from', now()->startOfMonth()->format('Y-m-d'));
        $to = $request->input('to', now()->format('Y-m-d'));
        $selectedCurrencies = $request->input('currencies', []);
        
        // جلب العملات المتاحة
        $currencies = \App\Models\Currency::all();
        $defaultCurrency = \App\Models\Currency::getDefaultCode();
        
        // إذا لم يتم تحديد عملات، استخدم جميع العملات النشطة
        if (empty($selectedCurrencies)) {
            $selectedCurrencies = $currencies->pluck('code')->toArray();
        }
        
        // تجميع البيانات حسب العملة
        $currencyData = [];
        $totalAccountsByDate = [];
        $exchangeRateHistory = [];
        
        foreach ($selectedCurrencies as $currencyCode) {
            $currency = $currencies->where('code', $currencyCode)->first();
            if (!$currency) continue;
            
            // جلب الحسابات بهذه العملة
            $accounts = \App\Models\Account::where('default_currency', $currencyCode)
                ->where('is_group', false)
                ->get();
            
            // حساب إجمالي الأرصدة بالعملة الأصلية
            $totalBalance = 0;
            $positiveBalance = 0;
            $negativeBalance = 0;
            $accountsCount = 0;
            
            foreach ($accounts as $account) {
                $query = $account->journalEntryLines();
                if ($from) {
                    $query->whereHas('journalEntry', function($q) use ($from) {
                        $q->where('date', '>=', $from);
                    });
                }
                if ($to) {
                    $query->whereHas('journalEntry', function($q) use ($to) {
                        $q->where('date', '<=', $to);
                    });
                }
                
                $debit = $query->sum('debit');
                $credit = $query->sum('credit');
                $balance = $debit - $credit;
                
                if ($balance != 0) {
                    $accountsCount++;
                    $totalBalance += $balance;
                    
                    if ($balance > 0) {
                        $positiveBalance += $balance;
                    } else {
                        $negativeBalance += abs($balance);
                    }
                }
            }
            
            // تحويل للعملة الافتراضية للمقارنة
            $totalBalanceInDefault = \App\Helpers\CurrencyHelper::convert($totalBalance, $currencyCode, $defaultCurrency);
            $positiveBalanceInDefault = \App\Helpers\CurrencyHelper::convert($positiveBalance, $currencyCode, $defaultCurrency);
            $negativeBalanceInDefault = \App\Helpers\CurrencyHelper::convert($negativeBalance, $currencyCode, $defaultCurrency);
            
            $currencyData[$currencyCode] = [
                'name' => $currency->name,
                'symbol' => $currency->symbol,
                'exchange_rate' => $currency->exchange_rate,
                'accounts_count' => $accountsCount,
                'total_balance' => $totalBalance,
                'positive_balance' => $positiveBalance,
                'negative_balance' => $negativeBalance,
                'total_balance_default' => $totalBalanceInDefault,
                'positive_balance_default' => $positiveBalanceInDefault,
                'negative_balance_default' => $negativeBalanceInDefault,
                'percentage' => 0, // سيتم حسابها لاحقاً
                'color' => $this->getCurrencyColor($currencyCode),
            ];
        }
        
        // حساب النسب المئوية
        $grandTotal = array_sum(array_column($currencyData, 'total_balance_default'));
        if ($grandTotal != 0) {
            foreach ($currencyData as $code => $data) {
                $currencyData[$code]['percentage'] = ($data['total_balance_default'] / $grandTotal) * 100;
            }
        }
        
        // تجهيز بيانات الرسوم البيانية
        $chartData = [
            'labels' => array_keys($currencyData),
            'balances' => array_column($currencyData, 'total_balance_default'),
            'colors' => array_column($currencyData, 'color'),
            'accounts_count' => array_column($currencyData, 'accounts_count'),
        ];
        
        // إحصائيات عامة
        $statistics = [
            'total_currencies' => count($currencyData),
            'total_accounts' => array_sum(array_column($currencyData, 'accounts_count')),
            'grand_total' => $grandTotal,
            'largest_currency' => $grandTotal > 0 ? array_keys($currencyData, max($currencyData))[0] : null,
            'smallest_currency' => $grandTotal > 0 ? array_keys($currencyData, min($currencyData))[0] : null,
        ];
        
        return view('reports.currency_comparison', compact(
            'currencyData',
            'chartData',
            'statistics',
            'currencies',
            'selectedCurrencies',
            'defaultCurrency',
            'from',
            'to'
        ));
    }
    
    /**
     * الحصول على لون مميز لكل عملة
     */
    /**
     * حساب الرصيد بناءً على طبيعة الحساب (nature)
     * 
     * @param \App\Models\Account $account
     * @param float $debit
     * @param float $credit
     * @return float
     */
    private function calculateAccountBalance($account, $debit, $credit)
    {
        $method = Setting::getBalanceCalculationMethod();
        
        if ($method === 'transaction_nature') {
            // المنطق البسيط: المدين - الدائن (بغض النظر عن طبيعة الحساب)
            return $debit - $credit;
        } else {
            // المنطق التقليدي: يعتمد على طبيعة الحساب
            if ($account->nature === 'مدين' || $account->nature === 'debit') {
                return $debit - $credit;
            } else {
                return $credit - $debit;
            }
        }
    }

    private function getCurrencyColor($currencyCode)
    {
        $colors = [
            'IQD' => '#1976d2', // أزرق
            'USD' => '#388e3c', // أخضر
            'EUR' => '#5e35b1', // بنفسجي
            'GBP' => '#d32f2f', // أحمر
            'JPY' => '#f57c00', // برتقالي
            'CAD' => '#7b1fa2', // بنفسجي داكن
            'AUD' => '#0288d1', // أزرق فاتح
            'CHF' => '#689f38', // أخضر داكن
        ];
        
        return $colors[$currencyCode] ?? '#607d8b'; // رمادي افتراضي
    }
    /**
     * تقرير التدفقات النقدية متعددة العملات
     */
    public function cashFlow(Request $request)
    {
        $from = $request->input('from', now()->startOfMonth()->format('Y-m-d'));
        $to = $request->input('to', now()->format('Y-m-d'));
        $selectedCurrency = $request->input('currency');
        $displayCurrency = $request->input('display_currency');
        
        // جلب العملات المتاحة
        $currencies = \App\Models\Currency::all();
        $defaultCurrency = \App\Models\Currency::getDefaultCode();
        
        // جلب الصناديق النقدية
        $cashAccounts = \App\Models\Account::where('is_cash_box', true)->get();
        
        $cashFlowData = [];
        $totalsByPeriod = [];
        $periodLabels = [];
        
        // تجميع البيانات حسب العملة والفترة
        foreach ($cashAccounts as $account) {
            $accountCurrency = $account->default_currency ?: $defaultCurrency;
            
            // فلترة حسب العملة المختارة إذا تم تحديدها
            if ($selectedCurrency && $accountCurrency !== $selectedCurrency) {
                continue;
            }
            
            if (!isset($cashFlowData[$accountCurrency])) {
                $cashFlowData[$accountCurrency] = [
                    'currency_info' => $currencies->where('code', $accountCurrency)->first(),
                    'accounts' => [],
                    'total_inflow' => 0,
                    'total_outflow' => 0,
                    'net_flow' => 0,
                    'opening_balance' => 0,
                    'closing_balance' => 0,
                ];
            }
            
            // الرصيد الافتتاحي
            $openingQuery = $account->journalEntryLines()
                ->whereHas('journalEntry', function($q) use ($from) {
                    $q->where('date', '<', $from);
                });
            $openingDebit = $openingQuery->sum('debit');
            $openingCredit = $openingQuery->sum('credit');
            $openingBalance = $openingDebit - $openingCredit;
            
            // المعاملات في الفترة المحددة
            $periodQuery = $account->journalEntryLines()
                ->whereHas('journalEntry', function($q) use ($from, $to) {
                    $q->whereBetween('date', [$from, $to]);
                });
            $periodDebit = $periodQuery->sum('debit');
            $periodCredit = $periodQuery->sum('credit');
            
            // تصنيف التدفقات (التدفق الداخل = المدين، التدفق الخارج = الدائن)
            $inflow = $periodDebit;
            $outflow = $periodCredit;
            $netFlow = $inflow - $outflow;
            $closingBalance = $openingBalance + $netFlow;
            
            // جلب تفاصيل المعاملات
            $transactions = \App\Models\JournalEntryLine::where('account_id', $account->id)
                ->whereHas('journalEntry', function($q) use ($from, $to) {
                    $q->whereBetween('date', [$from, $to]);
                })
                ->with(['journalEntry'])
                ->orderBy('created_at', 'desc')
                ->get();
            
            $cashFlowData[$accountCurrency]['accounts'][] = [
                'account' => $account,
                'opening_balance' => $openingBalance,
                'inflow' => $inflow,
                'outflow' => $outflow,
                'net_flow' => $netFlow,
                'closing_balance' => $closingBalance,
                'transactions' => $transactions,
                'transactions_count' => $transactions->count(),
            ];
            
            // تجميع المجاميع
            $cashFlowData[$accountCurrency]['total_inflow'] += $inflow;
            $cashFlowData[$accountCurrency]['total_outflow'] += $outflow;
            $cashFlowData[$accountCurrency]['net_flow'] += $netFlow;
            $cashFlowData[$accountCurrency]['opening_balance'] += $openingBalance;
            $cashFlowData[$accountCurrency]['closing_balance'] += $closingBalance;
        }
        
        // تحويل للعملة المطلوبة للعرض
        $allDataInDisplayCurrency = [];
        $conversionDetails = [];
        
        if ($displayCurrency) {
            // استخدام تاريخ الفترة للتحويل (أو تاريخ اليوم إذا لم يكن محدداً)
            $conversionDate = $to ?: ($from ?: now()->format('Y-m-d'));
            
            foreach ($cashFlowData as $currencyCode => $data) {
                if ($currencyCode !== $displayCurrency) {
                    $conversionRate = \App\Helpers\CurrencyHelper::convertWithHistoricalRate(1, $currencyCode, $displayCurrency, $conversionDate);
                    
                    $allDataInDisplayCurrency[] = [
                        'original_currency' => $currencyCode,
                        'currency_name' => $data['currency_info']->name ?? $currencyCode,
                        'total_inflow' => \App\Helpers\CurrencyHelper::convertWithHistoricalRate($data['total_inflow'], $currencyCode, $displayCurrency, $conversionDate),
                        'total_outflow' => \App\Helpers\CurrencyHelper::convertWithHistoricalRate($data['total_outflow'], $currencyCode, $displayCurrency, $conversionDate),
                        'net_flow' => \App\Helpers\CurrencyHelper::convertWithHistoricalRate($data['net_flow'], $currencyCode, $displayCurrency, $conversionDate),
                        'opening_balance' => \App\Helpers\CurrencyHelper::convertWithHistoricalRate($data['opening_balance'], $currencyCode, $displayCurrency, $conversionDate),
                        'closing_balance' => \App\Helpers\CurrencyHelper::convertWithHistoricalRate($data['closing_balance'], $currencyCode, $displayCurrency, $conversionDate),
                        'exchange_rate_used' => $conversionRate,
                    ];
                    
                    $conversionDetails[$currencyCode] = [
                        'from' => $currencyCode,
                        'to' => $displayCurrency,
                        'rate' => $conversionRate,
                        'date' => $conversionDate,
                    ];
                } else {
                    $allDataInDisplayCurrency[] = [
                        'original_currency' => $currencyCode,
                        'currency_name' => $data['currency_info']->name ?? $currencyCode,
                        'total_inflow' => $data['total_inflow'],
                        'total_outflow' => $data['total_outflow'],
                        'net_flow' => $data['net_flow'],
                        'opening_balance' => $data['opening_balance'],
                        'closing_balance' => $data['closing_balance'],
                        'exchange_rate_used' => 1,
                    ];
                }
            }
        }
        
        // إحصائيات عامة
        $statistics = [
            'total_currencies' => count($cashFlowData),
            'total_cash_accounts' => array_sum(array_map(function($data) {
                return count($data['accounts']);
            }, $cashFlowData)),
            'period_days' => \Carbon\Carbon::parse($from)->diffInDays(\Carbon\Carbon::parse($to)) + 1,
            'report_generated_at' => now()->format('Y-m-d H:i:s'),
        ];
        
        return view('reports.cash_flow', compact(
            'cashFlowData',
            'allDataInDisplayCurrency',
            'conversionDetails',
            'statistics',
            'currencies',
            'selectedCurrency',
            'displayCurrency',
            'defaultCurrency',
            'from',
            'to'
        ));
    }
}