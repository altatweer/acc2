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
            $debit = $query->sum('debit');
            $credit = $query->sum('credit');
            $balance = $debit - $credit;
            
            // تجاهل الحسابات التي ليس لديها أي حركة إذا كان الرصيد صفر
            if ($debit == 0 && $credit == 0 && $balance == 0) {
                continue;
            }
            
            $rows[] = [
                'account' => $account,
                'debit' => $debit,
                'credit' => $credit,
                'balance' => $balance,
            ];
            
            $totalDebit += $debit;
            $totalCredit += $credit;
            $totalBalance += $balance;
        }
        
        // تجميع الصفوف حسب العملة
        $rowsByCurrency = collect($rows)->groupBy(function($row) {
            return $row['account']->currency ?? 'Unknown';
        });
        
        // حساب المجاميع حسب كل عملة
        $totalsByCurrency = [];
        $grandTotalInDefaultCurrency = [
            'debit' => 0,
            'credit' => 0,
            'balance' => 0,
        ];
        
        // المجموع الكلي معروض بكل العملات المتاحة
        $grandTotalInAllCurrencies = [];
        
        foreach ($rowsByCurrency as $currency => $currencyRows) {
            $totalsByCurrency[$currency] = [
                'debit' => $currencyRows->sum('debit'),
                'credit' => $currencyRows->sum('credit'),
                'balance' => $currencyRows->sum('balance'),
            ];
            
            // تحويل إلى العملة الافتراضية للمجموع الكلي
            if ($currency != $defaultCurrency) {
                $grandTotalInDefaultCurrency['debit'] += \App\Helpers\CurrencyHelper::convert(
                    $totalsByCurrency[$currency]['debit'], 
                    $currency, 
                    $defaultCurrency
                );
                $grandTotalInDefaultCurrency['credit'] += \App\Helpers\CurrencyHelper::convert(
                    $totalsByCurrency[$currency]['credit'], 
                    $currency, 
                    $defaultCurrency
                );
                $grandTotalInDefaultCurrency['balance'] += \App\Helpers\CurrencyHelper::convert(
                    $totalsByCurrency[$currency]['balance'], 
                    $currency, 
                    $defaultCurrency
                );
            } else {
                $grandTotalInDefaultCurrency['debit'] += $totalsByCurrency[$currency]['debit'];
                $grandTotalInDefaultCurrency['credit'] += $totalsByCurrency[$currency]['credit'];
                $grandTotalInDefaultCurrency['balance'] += $totalsByCurrency[$currency]['balance'];
            }
        }
        
        // حساب المجموع الكلي بكل العملات المتاحة في النظام - تحسين الحساب
        foreach ($currencies as $targetCurrency) {
            $targetCode = $targetCurrency->code;
            $totalDebitInCurrency = 0;
            $totalCreditInCurrency = 0;
            $totalBalanceInCurrency = 0;
            
            // جمع القيم من كل العملات بعد تحويلها للعملة المستهدفة
            foreach ($totalsByCurrency as $currCode => $totals) {
                if ($currCode == $targetCode) {
                    $totalDebitInCurrency += $totals['debit'];
                    $totalCreditInCurrency += $totals['credit'];
                    $totalBalanceInCurrency += $totals['balance'];
                } else {
                    $totalDebitInCurrency += \App\Helpers\CurrencyHelper::convert(
                        $totals['debit'], 
                        $currCode, 
                        $targetCode
                    );
                    $totalCreditInCurrency += \App\Helpers\CurrencyHelper::convert(
                        $totals['credit'], 
                        $currCode, 
                        $targetCode
                    );
                    $totalBalanceInCurrency += \App\Helpers\CurrencyHelper::convert(
                        $totals['balance'], 
                        $currCode, 
                        $targetCode
                    );
                }
            }
            
            $grandTotalInAllCurrencies[$targetCode] = [
                'debit' => $totalDebitInCurrency,
                'credit' => $totalCreditInCurrency,
                'balance' => $totalBalanceInCurrency,
            ];
        }
        
        // إعداد البيانات للعرض بعملة واحدة
        $allRowsInDisplayCurrency = [];
        if ($displayCurrency) {
            foreach ($rows as $row) {
                $originalCurrency = $row['account']->currency ?? $defaultCurrency;
                
                // تحويل القيم إلى العملة المختارة
                $convertedDebit = ($originalCurrency != $displayCurrency) ? 
                    \App\Helpers\CurrencyHelper::convert($row['debit'], $originalCurrency, $displayCurrency) : 
                    $row['debit'];
                
                $convertedCredit = ($originalCurrency != $displayCurrency) ? 
                    \App\Helpers\CurrencyHelper::convert($row['credit'], $originalCurrency, $displayCurrency) : 
                    $row['credit'];
                
                $convertedBalance = ($originalCurrency != $displayCurrency) ? 
                    \App\Helpers\CurrencyHelper::convert($row['balance'], $originalCurrency, $displayCurrency) : 
                    $row['balance'];
                
                $allRowsInDisplayCurrency[] = [
                    'account' => $row['account'],
                    'original_currency' => $originalCurrency,
                    'debit' => $convertedDebit,
                    'credit' => $convertedCredit,
                    'balance' => $convertedBalance,
                ];
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
                $debit = $query->sum('debit');
                $credit = $query->sum('credit');
                $balance = $debit - $credit;
                
                // تجاهل الحسابات التي ليس لديها رصيد
                if ($balance == 0) {
                    continue;
                }
                
                $accountCurrency = $account->default_currency ?: $defaultCurrency;
                
                $row = [
                    'account' => $account,
                    'balance' => $balance,
                    'currency' => $accountCurrency,
                ];
                
                $rows[] = $row;
                
                // تجميع حسب العملة
                if (!$rowsByCurrency->has($accountCurrency)) {
                    $rowsByCurrency[$accountCurrency] = collect();
                    if (!isset($sectionTotalsByCurrency[$accountCurrency])) {
                        $sectionTotalsByCurrency[$accountCurrency] = [];
                    }
                    if (!isset($sectionTotalsByCurrency[$accountCurrency][$typeArr['ar']])) {
                        $sectionTotalsByCurrency[$accountCurrency][$typeArr['ar']] = 0;
                    }
                }
                
                $rowsByCurrency[$accountCurrency]->push($row);
                $sectionTotalsByCurrency[$accountCurrency][$typeArr['ar']] += $balance;
                
                $total += $balance;
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
        
        // حساب المجموع الكلي بكل العملات المتاحة
        $balanceSheetTotalsInAllCurrencies = [];
        
        foreach ($currencies as $targetCurrency) {
            $targetCode = $targetCurrency->code;
            $assets = 0;
            $liabilities = 0;
            $equity = 0;
            
            // المجموع للأصول والخصوم وحقوق الملكية
            foreach ($sectionTotalsByCurrency as $currCode => $sectionTotals) {
                foreach ($sectionTotals as $sectionType => $amount) {
                    $convertedAmount = ($currCode == $targetCode) ? 
                        $amount : 
                        \App\Helpers\CurrencyHelper::convert($amount, $currCode, $targetCode);
                    
                    if ($sectionType == 'أصل') {
                        $assets += $convertedAmount;
                    } elseif ($sectionType == 'خصم') {
                        $liabilities += $convertedAmount;
                    } elseif ($sectionType == 'حقوق ملكية') {
                        $equity += $convertedAmount;
                    }
                }
            }
            
            $balanceSheetTotalsInAllCurrencies[$targetCode] = [
                'assets' => $assets,
                'liabilities' => $liabilities,
                'equity' => $equity,
                'balance' => $assets - ($liabilities + $equity),
            ];
        }
        
        // إعداد البيانات للعرض بعملة واحدة
        $sectionsInDisplayCurrency = [];
        if ($displayCurrency) {
            foreach ($types as $typeArr) {
                $rowsInDisplayCurrency = [];
                $totalInDisplayCurrency = 0;
                
                foreach ($sections[$typeArr['ar']]['rows'] as $row) {
                    $originalCurrency = $row['currency'];
                    
                    // تحويل القيم إلى العملة المختارة
                    $convertedBalance = ($originalCurrency != $displayCurrency) ? 
                        \App\Helpers\CurrencyHelper::convert($row['balance'], $originalCurrency, $displayCurrency) : 
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
        
        return view('reports.balance_sheet', compact(
            'sections', 
            'sectionsByCurrency',
            'sectionTotalsByCurrency',
            'balanceSheetTotalsInAllCurrencies',
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
        $accounts = \App\Models\Account::where('is_group', false)
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
                $q->where('currency', $currency);
            })
            ->orderBy('name')->get();

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
            $debit = $query->sum('debit');
            $credit = $query->sum('credit');
            $balance = $debit - $credit;
            $parentType = $account->parent ? $account->parent->type : $account->type;
            
            // تجاهل الحسابات التي ليس لديها أي حركة
            if ($debit == 0 && $credit == 0 && $balance == 0) {
                continue;
            }
            
            $accountCurrency = $account->default_currency ?: $defaultCurrency;
            
            $row = [
                'account' => $account,
                'debit' => $debit,
                'credit' => $credit,
                'balance' => $balance,
                'type' => $parentType,
                'currency' => $accountCurrency,
            ];
            
            $rows[] = $row;
            
            // إذا لم تكن العملة موجودة في المجموعة، أضف مجموعة جديدة
            if (!$rowsByCurrency->has($accountCurrency)) {
                $rowsByCurrency[$accountCurrency] = collect();
                $revenuesByCurrency[$accountCurrency] = 0;
                $expensesByCurrency[$accountCurrency] = 0;
            }
            
            $rowsByCurrency[$accountCurrency]->push($row);
            
            $totalDebit += $debit;
            $totalCredit += $credit;
            $totalBalance += $balance;
            
            if (in_array($parentType, ['إيراد', 'revenue'])) {
                $totalRevenue += abs($balance);
                $revenuesByCurrency[$accountCurrency] += abs($balance);
            } elseif (in_array($parentType, ['مصروف', 'expense'])) {
                $totalExpense += abs($balance);
                $expensesByCurrency[$accountCurrency] += abs($balance);
            }
        }
        
        // حساب صافي الربح/الخسارة لكل عملة
        foreach ($revenuesByCurrency as $curr => $revenue) {
            $expense = $expensesByCurrency[$curr] ?? 0;
            $netByCurrency[$curr] = $revenue - $expense;
        }
        
        $totalRevenue = abs($totalRevenue);
        $totalExpense = abs($totalExpense);
        $net = $totalRevenue - $totalExpense;
        
        // المجموع الكلي معروض بكل العملات المتاحة
        $financialResultsInAllCurrencies = [];
        
        // حساب المجموع الكلي بكل العملات مع الأخذ بالاعتبار تحويل قيم كل العملات
        foreach ($currencies as $targetCurrency) {
            $targetCode = $targetCurrency->code;
            $totalRevenueInCurrency = 0;
            $totalExpenseInCurrency = 0;
            
            // جمع القيم من كل العملات بعد تحويلها للعملة المستهدفة
            foreach ($revenuesByCurrency as $currCode => $amount) {
                if ($currCode == $targetCode) {
                    $totalRevenueInCurrency += $amount;
                } else {
                    $totalRevenueInCurrency += \App\Helpers\CurrencyHelper::convert($amount, $currCode, $targetCode);
                }
            }
            
            foreach ($expensesByCurrency as $currCode => $amount) {
                if ($currCode == $targetCode) {
                    $totalExpenseInCurrency += $amount;
                } else {
                    $totalExpenseInCurrency += \App\Helpers\CurrencyHelper::convert($amount, $currCode, $targetCode);
                }
            }
            
            $netProfitInCurrency = $totalRevenueInCurrency - $totalExpenseInCurrency;
            
            $financialResultsInAllCurrencies[$targetCode] = [
                'revenue' => $totalRevenueInCurrency,
                'expense' => $totalExpenseInCurrency,
                'net' => $netProfitInCurrency,
            ];
        }
        
        // إعداد البيانات للعرض بعملة واحدة
        $allRowsInDisplayCurrency = [];
        $revenueInDisplayCurrency = 0;
        $expenseInDisplayCurrency = 0;
        
        if ($displayCurrency) {
            foreach ($rows as $row) {
                $originalCurrency = $row['currency'];
                $parentType = $row['type'];
                
                // تحويل القيم إلى العملة المختارة
                $convertedDebit = ($originalCurrency != $displayCurrency) ? 
                    \App\Helpers\CurrencyHelper::convert($row['debit'], $originalCurrency, $displayCurrency) : 
                    $row['debit'];
                
                $convertedCredit = ($originalCurrency != $displayCurrency) ? 
                    \App\Helpers\CurrencyHelper::convert($row['credit'], $originalCurrency, $displayCurrency) : 
                    $row['credit'];
                
                $convertedBalance = ($originalCurrency != $displayCurrency) ? 
                    \App\Helpers\CurrencyHelper::convert($row['balance'], $originalCurrency, $displayCurrency) : 
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
        
        return view('reports.income_statement', compact(
            'rows', 
            'rowsByCurrency',
            'revenuesByCurrency',
            'expensesByCurrency',
            'netByCurrency',
            'financialResultsInAllCurrencies',
            'allRowsInDisplayCurrency',
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
        
        // حساب المجموع الكلي بكل العملات المتاحة
        $payrollTotalsInAllCurrencies = [];
        
        foreach ($currencies as $targetCurrency) {
            $targetCode = $targetCurrency->code;
            $totalGrossInCurrency = 0;
            $totalAllowancesInCurrency = 0;
            $totalDeductionsInCurrency = 0;
            $totalNetInCurrency = 0;
            
            // جمع القيم من كل العملات بعد تحويلها للعملة المستهدفة
            foreach ($totalsByCurrency as $currCode => $totals) {
                if ($currCode == $targetCode) {
                    $totalGrossInCurrency += $totals['gross'];
                    $totalAllowancesInCurrency += $totals['allowances'];
                    $totalDeductionsInCurrency += $totals['deductions'];
                    $totalNetInCurrency += $totals['net'];
                } else {
                    $totalGrossInCurrency += \App\Helpers\CurrencyHelper::convert($totals['gross'], $currCode, $targetCode);
                    $totalAllowancesInCurrency += \App\Helpers\CurrencyHelper::convert($totals['allowances'], $currCode, $targetCode);
                    $totalDeductionsInCurrency += \App\Helpers\CurrencyHelper::convert($totals['deductions'], $currCode, $targetCode);
                    $totalNetInCurrency += \App\Helpers\CurrencyHelper::convert($totals['net'], $currCode, $targetCode);
                }
            }
            
            $payrollTotalsInAllCurrencies[$targetCode] = [
                'gross' => $totalGrossInCurrency,
                'allowances' => $totalAllowancesInCurrency,
                'deductions' => $totalDeductionsInCurrency,
                'net' => $totalNetInCurrency,
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
            'payrollTotalsInAllCurrencies',
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
            $query->where('currency', $selectedCurrency);
        }
        
        $accounts = $query->orderBy('name')->get();
        
        $rows = [];
        $totalRevenue = 0;
        $totalExpense = 0;
        
        // تجميع الصفوف حسب العملة
        $rowsByCurrency = collect();
        $revenuesByCurrency = [];
        $expensesByCurrency = [];
        
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
            $parentType = $account->parent ? $account->parent->type : $account->type;
            
            // تجاهل الحسابات التي ليس لديها أي حركة
            if ($debit == 0 && $credit == 0 && $balance == 0) {
                continue;
            }
            
            $accountCurrency = $account->default_currency ?: $defaultCurrency;
            
            $row = [
                'account' => $account,
                'debit' => $debit,
                'credit' => $credit,
                'balance' => $balance,
                'type' => $parentType,
                'currency' => $accountCurrency,
            ];
            
            $rows[] = $row;
            
            // إذا لم تكن العملة موجودة في المجموعة، أضف مجموعة جديدة
            if (!$rowsByCurrency->has($accountCurrency)) {
                $rowsByCurrency[$accountCurrency] = collect();
                $revenuesByCurrency[$accountCurrency] = 0;
                $expensesByCurrency[$accountCurrency] = 0;
            }
            
            $rowsByCurrency[$accountCurrency]->push($row);
            
            if (in_array($parentType, ['إيراد', 'revenue'])) {
                $totalRevenue += abs($balance);
                $revenuesByCurrency[$accountCurrency] += abs($balance);
            } elseif (in_array($parentType, ['مصروف', 'expense'])) {
                $totalExpense += abs($balance);
                $expensesByCurrency[$accountCurrency] += abs($balance);
            }
        }
        
        // المجموع الكلي معروض بكل العملات المتاحة
        $financialResultsInAllCurrencies = [];
        
        foreach ($currencies as $targetCurrency) {
            $targetCode = $targetCurrency->code;
            $totalRevenueInCurrency = 0;
            $totalExpenseInCurrency = 0;
            
            // جمع القيم من كل العملات بعد تحويلها للعملة المستهدفة
            foreach ($revenuesByCurrency as $currCode => $amount) {
                if ($currCode == $targetCode) {
                    $totalRevenueInCurrency += $amount;
                } else {
                    $totalRevenueInCurrency += \App\Helpers\CurrencyHelper::convert($amount, $currCode, $targetCode);
                }
            }
            
            foreach ($expensesByCurrency as $currCode => $amount) {
                if ($currCode == $targetCode) {
                    $totalExpenseInCurrency += $amount;
                } else {
                    $totalExpenseInCurrency += \App\Helpers\CurrencyHelper::convert($amount, $currCode, $targetCode);
                }
            }
            
            $netProfitInCurrency = $totalRevenueInCurrency - $totalExpenseInCurrency;
            
            $financialResultsInAllCurrencies[$targetCode] = [
                'revenue' => $totalRevenueInCurrency,
                'expense' => $totalExpenseInCurrency,
                'net' => $netProfitInCurrency,
            ];
        }
        
        // إعداد البيانات للعرض بعملة واحدة
        $allRowsInDisplayCurrency = [];
        $revenueInDisplayCurrency = 0;
        $expenseInDisplayCurrency = 0;
        
        if ($displayCurrency) {
            foreach ($rows as $row) {
                $originalCurrency = $row['currency'];
                $parentType = $row['type'];
                
                // تحويل القيم إلى العملة المختارة
                $convertedDebit = ($originalCurrency != $displayCurrency) ? 
                    \App\Helpers\CurrencyHelper::convert($row['debit'], $originalCurrency, $displayCurrency) : 
                    $row['debit'];
                
                $convertedCredit = ($originalCurrency != $displayCurrency) ? 
                    \App\Helpers\CurrencyHelper::convert($row['credit'], $originalCurrency, $displayCurrency) : 
                    $row['credit'];
                
                $convertedBalance = ($originalCurrency != $displayCurrency) ? 
                    \App\Helpers\CurrencyHelper::convert($row['balance'], $originalCurrency, $displayCurrency) : 
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
            'financialResultsInAllCurrencies',
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
}