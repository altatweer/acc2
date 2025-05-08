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

    public function __construct(Collection $entries, Account $account, $from, $to, $openingBalance)
    {
        $this->entries = $entries;
        $this->account = $account;
        $this->from = $from;
        $this->to = $to;
        $this->openingBalance = $openingBalance;
        $this->totalDebit = $entries->sum('debit');
        $this->totalCredit = $entries->sum('credit');
    }

    public function view(): View
    {
        return view('ledger.excel', [
            'entries' => $this->entries,
            'accounts' => collect([$this->account->id => $this->account]),
            'selectedAccount' => $this->account->id,
            'from' => $this->from,
            'to' => $this->to,
            'openingBalance' => $this->openingBalance,
            'totalDebit' => $this->totalDebit,
            'totalCredit' => $this->totalCredit,
        ]);
    }
} 