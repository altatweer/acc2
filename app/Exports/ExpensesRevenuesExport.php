<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ExpensesRevenuesExport implements FromView
{
    protected $rows;
    protected $from;
    protected $to;
    protected $totalRevenue;
    protected $totalExpense;

    public function __construct($rows, $from, $to, $totalRevenue, $totalExpense)
    {
        $this->rows = $rows;
        $this->from = $from;
        $this->to = $to;
        $this->totalRevenue = $totalRevenue;
        $this->totalExpense = $totalExpense;
    }

    public function view(): View
    {
        return view('reports.expenses_revenues', [
            'rows' => $this->rows,
            'from' => $this->from,
            'to' => $this->to,
            'totalRevenue' => $this->totalRevenue,
            'totalExpense' => $this->totalExpense,
            'export' => true,
        ]);
    }
} 