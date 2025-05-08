<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class BalanceSheetExport implements FromView
{
    protected $sections;
    protected $from;
    protected $to;

    public function __construct($sections, $from, $to)
    {
        $this->sections = $sections;
        $this->from = $from;
        $this->to = $to;
    }

    public function view(): View
    {
        return view('reports.balance_sheet', [
            'sections' => $this->sections,
            'from' => $this->from,
            'to' => $this->to,
            'export' => true,
        ]);
    }
} 