<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use App\Models\Account;
use Illuminate\Support\Collection;

class LedgerExport implements FromView
{
    protected $entries;
    protected $account;
    protected $from;
    protected $to;
    protected $openingBalance;
    protected $totalDebit;
    protected $totalCredit;
    protected $entriesByCurrency;
    protected $openingBalancesByCurrency;
    protected $convertToSingleCurrency;
    protected $displayCurrency;

    public function __construct(
        Collection $entries, 
        Account $account, 
        $from, 
        $to, 
        $openingBalance,
        $entriesByCurrency = [],
        $openingBalancesByCurrency = [],
        $convertToSingleCurrency = false,
        $displayCurrency = null
    ) {
        $this->entries = $entries;
        $this->account = $account;
        $this->from = $from;
        $this->to = $to;
        $this->openingBalance = $openingBalance;
        $this->totalDebit = $entries->sum('debit');
        $this->totalCredit = $entries->sum('credit');
        $this->entriesByCurrency = $entriesByCurrency;
        $this->openingBalancesByCurrency = $openingBalancesByCurrency;
        $this->convertToSingleCurrency = $convertToSingleCurrency;
        $this->displayCurrency = $displayCurrency;
    }

    public function view(): View
    {
        // حساب الرصيد النهائي
        $finalBalance = $this->openingBalance;
        foreach ($this->entries as $entry) {
            $debit = is_object($entry) ? $entry->debit : $entry['debit'];
            $credit = is_object($entry) ? $entry->credit : $entry['credit'];
            
            if ($this->account->nature === 'مدين' || $this->account->nature === 'debit') {
                $finalBalance += $debit - $credit;
            } else {
                $finalBalance += $credit - $debit;
            }
        }
        
        return view('ledger.excel', [
            'entries' => $this->entries,
            'account' => $this->account,
            'from' => $this->from,
            'to' => $this->to,
            'openingBalance' => $this->openingBalance,
            'totalDebit' => $this->totalDebit,
            'totalCredit' => $this->totalCredit,
            'finalBalance' => $finalBalance,
            'entriesByCurrency' => $this->entriesByCurrency,
            'openingBalancesByCurrency' => $this->openingBalancesByCurrency,
            'convertToSingleCurrency' => $this->convertToSingleCurrency,
            'displayCurrency' => $this->displayCurrency,
        ]);
    }
}
