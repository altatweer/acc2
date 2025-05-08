<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class IncomeStatementExport implements FromView
{
    protected $rows;
    protected $from;
    protected $to;
    protected $type;
    protected $groups;
    protected $parent_id;
    protected $totalRevenue;
    protected $totalExpense;
    protected $net;
    protected $totalDebit;
    protected $totalCredit;
    protected $totalBalance;

    public function __construct($rows, $from, $to, $type, $groups, $parent_id, $totalRevenue, $totalExpense, $net, $totalDebit, $totalCredit, $totalBalance)
    {
        $this->rows = $rows;
        $this->from = $from;
        $this->to = $to;
        $this->type = $type;
        $this->groups = $groups;
        $this->parent_id = $parent_id;
        $this->totalRevenue = $totalRevenue;
        $this->totalExpense = $totalExpense;
        $this->net = $net;
        $this->totalDebit = $totalDebit;
        $this->totalCredit = $totalCredit;
        $this->totalBalance = $totalBalance;
    }

    public function view(): View
    {
        return view('reports.income_statement', [
            'rows' => $this->rows,
            'from' => $this->from,
            'to' => $this->to,
            'type' => $this->type,
            'groups' => $this->groups,
            'parent_id' => $this->parent_id,
            'totalRevenue' => $this->totalRevenue,
            'totalExpense' => $this->totalExpense,
            'net' => $this->net,
            'totalDebit' => $this->totalDebit,
            'totalCredit' => $this->totalCredit,
            'totalBalance' => $this->totalBalance,
            'export' => true,
        ]);
    }
} 