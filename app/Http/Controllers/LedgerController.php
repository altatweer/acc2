<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\JournalEntryLine;
use App\Models\Currency;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Helpers\CurrencyHelper;

class LedgerController extends Controller
{
    public function index(Request $request)
    {
        // Validation: التاريخ إلزامي عند اختيار حساب
        if ($request->has('account_id') && $request->input('account_id')) {
            $request->validate([
                'from' => 'required|date',
                'to' => 'required|date|after_or_equal:from',
            ], [
                'from.required' => 'تاريخ البداية إلزامي',
                'to.required' => 'تاريخ النهاية إلزامي',
                'to.after_or_equal' => 'تاريخ النهاية يجب أن يكون بعد أو يساوي تاريخ البداية',
            ]);
        }
        
        $accounts = Account::where('is_group', 0)->orderBy('code')->get();
        $currencies = Currency::all();
        $defaultCurrency = Currency::getDefaultCode();
        
        $selectedAccount = $request->input('account_id');
        $from = $request->input('from');
        $to = $request->input('to');
        $selectedCurrency = $request->input('currency'); // فلتر العملة
        $displayCurrency = $request->input('display_currency'); // العملة للعرض
        $convertToSingleCurrency = $request->boolean('convert_to_single'); // تحويل لعملة واحدة
        
        $entries = collect();
        $openingBalance = 0;
        $totalDebit = 0;
        $totalCredit = 0;
        $finalBalance = 0;
        $entriesByCurrency = [];
        $openingBalancesByCurrency = [];
        $summaryByCurrency = []; // ملخص لكل عملة
        
        if ($selectedAccount && $from && $to) {
            $account = Account::find($selectedAccount);
            
            if (!$account) {
                return redirect()->route('ledger.index')
                    ->with('error', 'الحساب المحدد غير موجود');
            }
            
            // جلب جميع الحركات
            $allEntries = JournalEntryLine::with(['journalEntry'])
                ->where('account_id', $selectedAccount)
                ->get()
                ->sortBy(function($line) {
                    return $line->journalEntry->date ?? null;
                });
            
            // تجميع الحركات حسب العملة
            $entriesByCurrency = $allEntries->groupBy('currency');
            
            // حساب الرصيد الافتتاحي لكل عملة
            foreach ($entriesByCurrency as $currency => $currencyEntries) {
                // حساب الرصيد الافتتاحي قبل الفترة
                $openingBalanceForCurrency = 0;
                if ($from) {
                    $openingEntries = $currencyEntries->filter(function($entry) use ($from) {
                        return $entry->journalEntry && $entry->journalEntry->date < $from;
                    });
                    
                    $openingDebit = $openingEntries->sum('debit');
                    $openingCredit = $openingEntries->sum('credit');
                    // المنطق البسيط: المدين - الدائن (بغض النظر عن طبيعة الحساب)
                    $openingBalanceForCurrency = $openingDebit - $openingCredit;
                } else {
                    // إذا لم يكن هناك تاريخ بداية، الرصيد الافتتاحي هو 0
                    $openingBalanceForCurrency = 0;
                }
                
                $openingBalancesByCurrency[$currency] = $openingBalanceForCurrency;
                
                // فلترة الحركات حسب التاريخ
                $filteredEntries = $currencyEntries;
                if ($from) {
                    $filteredEntries = $filteredEntries->filter(function($entry) use ($from) {
                        return $entry->journalEntry && $entry->journalEntry->date >= $from;
                    });
                }
                if ($to) {
                    $filteredEntries = $filteredEntries->filter(function($entry) use ($to) {
                        return $entry->journalEntry && $entry->journalEntry->date <= $to;
                    });
                }
                
                $entriesByCurrency[$currency] = $filteredEntries;
            }
            
            // حساب الملخص لكل عملة (قبل التحويل أو الفلترة)
            foreach ($entriesByCurrency as $currency => $currencyEntries) {
                if ($currencyEntries->count() > 0) {
                    $currencyOpeningBalance = $openingBalancesByCurrency[$currency] ?? 0;
                    $currencyTotalDebit = $currencyEntries->sum('debit');
                    $currencyTotalCredit = $currencyEntries->sum('credit');
                    
                    // حساب الرصيد النهائي لهذه العملة
                    $currencyFinalBalance = $currencyOpeningBalance;
                    foreach ($currencyEntries as $entry) {
                        $currencyFinalBalance = $this->calculateRunningBalance($account, $currencyFinalBalance, $entry->debit, $entry->credit);
                    }
                    
                    $summaryByCurrency[$currency] = [
                        'opening_balance' => $currencyOpeningBalance,
                        'total_debit' => $currencyTotalDebit,
                        'total_credit' => $currencyTotalCredit,
                        'final_balance' => $currencyFinalBalance,
                    ];
                }
            }
            
            // إذا كان هناك فلتر عملة محدد
            if ($selectedCurrency && $selectedCurrency !== 'all') {
                $entriesByCurrency = [$selectedCurrency => $entriesByCurrency[$selectedCurrency] ?? collect()];
                $openingBalancesByCurrency = [$selectedCurrency => $openingBalancesByCurrency[$selectedCurrency] ?? 0];
                // فلترة summaryByCurrency أيضاً
                $summaryByCurrency = [$selectedCurrency => $summaryByCurrency[$selectedCurrency] ?? [
                    'opening_balance' => 0,
                    'total_debit' => 0,
                    'total_credit' => 0,
                    'final_balance' => 0,
                ]];
            }
            
            // إذا كان هناك طلب لتحويل لعملة واحدة
            if ($convertToSingleCurrency && $displayCurrency) {
                $convertedEntries = collect();
                $convertedOpeningBalance = 0;
                $convertedTotalDebit = 0;
                $convertedTotalCredit = 0;
                
                foreach ($entriesByCurrency as $currency => $currencyEntries) {
                    $openingBalanceForCurrency = $openingBalancesByCurrency[$currency] ?? 0;
                    
                    // تحويل الرصيد الافتتاحي
                    if ($currency !== $displayCurrency) {
                        $convertedOpeningBalance += CurrencyHelper::convertWithHistoricalRate(
                            $openingBalanceForCurrency,
                            $currency,
                            $displayCurrency,
                            $from
                        );
                    } else {
                        $convertedOpeningBalance += $openingBalanceForCurrency;
                    }
                    
                    // تحويل الحركات
                    foreach ($currencyEntries as $entry) {
                        $entryDate = $entry->journalEntry->date ?? now()->format('Y-m-d');
                        $convertedDebit = $entry->debit;
                        $convertedCredit = $entry->credit;
                        
                        if ($currency !== $displayCurrency) {
                            $convertedDebit = CurrencyHelper::convertWithHistoricalRate(
                                $entry->debit,
                                $currency,
                                $displayCurrency,
                                $entryDate
                            );
                            $convertedCredit = CurrencyHelper::convertWithHistoricalRate(
                                $entry->credit,
                                $currency,
                                $displayCurrency,
                                $entryDate
                            );
                        }
                        
                        $convertedEntries->push((object) [
                            'id' => $entry->id,
                            'journalEntry' => $entry->journalEntry,
                            'description' => $entry->description,
                            'debit' => $convertedDebit,
                            'credit' => $convertedCredit,
                            'currency' => $displayCurrency,
                            'original_currency' => $currency,
                            'exchange_rate' => $currency !== $displayCurrency 
                                ? CurrencyHelper::getExchangeRateInfo($currency, $displayCurrency, $entryDate)['rate']
                                : 1,
                        ]);
                        
                        $convertedTotalDebit += $convertedDebit;
                        $convertedTotalCredit += $convertedCredit;
                    }
                }
                
                // حساب الرصيد النهائي
                $balance = $convertedOpeningBalance;
                foreach ($convertedEntries as $entry) {
                    $balance = $this->calculateRunningBalance($account, $balance, $entry->debit, $entry->credit);
                }
                
                $entries = $convertedEntries;
                $openingBalance = $convertedOpeningBalance;
                $totalDebit = $convertedTotalDebit;
                $totalCredit = $convertedTotalCredit;
                $finalBalance = $balance;
            } else {
                // عرض كل العملات بشكل منفصل
                $entries = $allEntries->filter(function($entry) use ($from, $to, $selectedCurrency) {
                    if ($from && $entry->journalEntry && $entry->journalEntry->date < $from) {
                        return false;
                    }
                    if ($to && $entry->journalEntry && $entry->journalEntry->date > $to) {
                        return false;
                    }
                    if ($selectedCurrency && $selectedCurrency !== 'all' && $entry->currency !== $selectedCurrency) {
                        return false;
                    }
                    return true;
                });
                
                // حساب الإجماليات لكل عملة
                $totalDebit = $entries->sum('debit');
                $totalCredit = $entries->sum('credit');
                
                // حساب الرصيد الافتتاحي الإجمالي (للعرض فقط)
                $openingBalance = 0;
                foreach ($openingBalancesByCurrency as $currency => $balance) {
                    if (!$selectedCurrency || $selectedCurrency === 'all' || $currency === $selectedCurrency) {
                        $openingBalance += $balance;
                    }
                }
                
                // حساب الرصيد النهائي
                $finalBalance = $openingBalance;
                foreach ($entries as $entry) {
                    $finalBalance = $this->calculateRunningBalance($account, $finalBalance, $entry->debit, $entry->credit);
                }
            }
        }
        
        // Export handling
        if ($request->get('export') === 'excel' && $selectedAccount) {
            $account = Account::find($selectedAccount);
            return Excel::download(
                new \App\Exports\LedgerExport(
                    $entries, 
                    $account, 
                    $from, 
                    $to, 
                    $openingBalance,
                    $entriesByCurrency,
                    $openingBalancesByCurrency,
                    $convertToSingleCurrency,
                    $displayCurrency
                ), 
                'ledger.xlsx'
            );
        }
        
        if ($request->get('export') === 'pdf' && $selectedAccount) {
            $account = Account::find($selectedAccount);
            $pdf = Pdf::loadView('ledger.pdf', [
                'account' => $account,
                'entries' => $entries,
                'entriesByCurrency' => $entriesByCurrency,
                'openingBalance' => $openingBalance,
                'openingBalancesByCurrency' => $openingBalancesByCurrency,
                'totalDebit' => $totalDebit,
                'totalCredit' => $totalCredit,
                'finalBalance' => $finalBalance,
                'from' => $from,
                'to' => $to,
                'selectedCurrency' => $selectedCurrency,
                'displayCurrency' => $displayCurrency,
                'convertToSingleCurrency' => $convertToSingleCurrency,
            ]);
            return $pdf->download('ledger.pdf');
        }
        
        // حساب الرصيد النهائي إذا لم يكن محسوباً
        if (!isset($finalBalance) || $finalBalance === null) {
            if ($selectedAccount) {
                $account = Account::find($selectedAccount);
                $finalBalance = $openingBalance;
                foreach ($entries as $entry) {
                    $debit = is_object($entry) ? $entry->debit : (isset($entry['debit']) ? $entry['debit'] : 0);
                    $credit = is_object($entry) ? $entry->credit : (isset($entry['credit']) ? $entry['credit'] : 0);
                    $finalBalance = $this->calculateRunningBalance($account, $finalBalance, $debit, $credit);
                }
            } else {
                $finalBalance = 0;
            }
        }
        
        return view('ledger.index', compact(
            'accounts',
            'currencies',
            'defaultCurrency',
            'entries',
            'entriesByCurrency',
            'selectedAccount',
            'from',
            'to',
            'selectedCurrency',
            'displayCurrency',
            'convertToSingleCurrency',
            'openingBalance',
            'openingBalancesByCurrency',
            'totalDebit',
            'totalCredit',
            'finalBalance',
            'summaryByCurrency'
        ));
    }
    
    /**
     * حساب الرصيد بناءً على المنطق البسيط (لدفتر الأستاذ فقط)
     * 
     * @param \App\Models\Account $account
     * @param float $debit
     * @param float $credit
     * @return float
     */
    private function calculateAccountBalance($account, $debit, $credit)
    {
        // المنطق البسيط: المدين - الدائن (بغض النظر عن طبيعة الحساب)
        return $debit - $credit;
    }
    
    /**
     * حساب الرصيد التراكمي بعد حركة (المنطق البسيط)
     * 
     * @param \App\Models\Account $account
     * @param float $currentBalance
     * @param float $debit
     * @param float $credit
     * @return float
     */
    private function calculateRunningBalance($account, $currentBalance, $debit, $credit)
    {
        // المنطق البسيط: الرصيد الحالي + (المدين - الدائن)
        return $currentBalance + ($debit - $credit);
    }
}
