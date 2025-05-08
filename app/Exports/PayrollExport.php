<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class PayrollExport implements FromView
{
    protected $rows;
    protected $month;
    protected $employeeName;
    protected $totalNet;
    protected $totalGross;
    protected $totalAllowances;
    protected $totalDeductions;

    public function __construct($rows, $month, $employeeName, $totalNet, $totalGross, $totalAllowances, $totalDeductions)
    {
        $this->rows = $rows;
        $this->month = $month;
        $this->employeeName = $employeeName;
        $this->totalNet = $totalNet;
        $this->totalGross = $totalGross;
        $this->totalAllowances = $totalAllowances;
        $this->totalDeductions = $totalDeductions;
    }

    public function view(): View
    {
        return view('reports.payroll', [
            'rows' => $this->rows,
            'month' => $this->month,
            'employeeName' => $this->employeeName,
            'totalNet' => $this->totalNet,
            'totalGross' => $this->totalGross,
            'totalAllowances' => $this->totalAllowances,
            'totalDeductions' => $this->totalDeductions,
            'export' => true,
        ]);
    }
} 