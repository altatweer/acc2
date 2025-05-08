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
        $accounts = \App\Models\Account::where('is_group', false)->orderBy('code')->get();
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
        $balanceType = $request->input('balance_type');
        if ($balanceType === 'positive') {
            $rows = array_filter($rows, fn($row) => $row['balance'] > 0);
        } elseif ($balanceType === 'negative') {
            $rows = array_filter($rows, fn($row) => $row['balance'] < 0);
        }
        return view('reports.trial_balance', compact('rows', 'from', 'to', 'totalDebit', 'totalCredit', 'totalBalance'));
    }
    public function balanceSheet(Request $request)
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
        return view('reports.balance_sheet', compact('sections', 'from', 'to'));
    }
    public function incomeStatement(Request $request)
    {
        $from = $request->input('from');
        $to = $request->input('to');
        $type = $request->input('type'); // إيراد أو مصروف أو الكل
        $parent_id = $request->input('parent_id'); // فلتر الفئة الرئيسية

        // جلب الفئات الرئيسية (is_group = true)
        $groups = \App\Models\Account::where('is_group', true)
            ->when($type, function($q) use ($type) {
                $typeArr = $type ? [$type, $type === 'إيراد' ? 'revenue' : ($type === 'مصروف' ? 'expense' : $type)] : null;
                if ($typeArr) $q->whereIn('type', $typeArr);
            })
            ->orderBy('name')->get();

        // جلب الحسابات الفعلية المرتبطة بفئة نوعها إيراد أو مصروف
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
                $totalRevenue += abs($balance);
            } elseif (in_array($parentType, ['مصروف', 'expense'])) {
                $totalExpense += abs($balance);
            }
        }
        $totalRevenue = abs($totalRevenue);
        $totalExpense = abs($totalExpense);
        $net = $totalRevenue - $totalExpense;
        return view('reports.income_statement', compact('rows', 'from', 'to', 'type', 'groups', 'parent_id', 'totalRevenue', 'totalExpense', 'net'));
    }
    public function payroll(Request $request)
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
        return view('reports.payroll', compact('rows', 'month', 'employeeName', 'totalNet', 'totalGross', 'totalAllowances', 'totalDeductions'));
    }
    public function expensesRevenues(Request $request)
    {
        $from = $request->input('from');
        $to = $request->input('to');
        // جلب الحسابات الفعلية المرتبطة بفئة نوعها إيراد أو مصروف
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
                $totalRevenue += abs($balance);
            } elseif (in_array($parentType, ['مصروف', 'expense'])) {
                $totalExpense += abs($balance);
            }
        }
        return view('reports.expenses_revenues', compact('rows', 'from', 'to', 'totalRevenue', 'totalExpense'));
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