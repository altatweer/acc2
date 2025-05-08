<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\JournalEntryLine;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class LedgerController extends Controller
{
    public function index(Request $request)
    {
        $accounts = Account::orderBy('name')->get();
        $selectedAccount = $request->input('account_id');
        $from = $request->input('from');
        $to = $request->input('to');
        $entries = collect();
        $openingBalance = 0;
        $totalDebit = 0;
        $totalCredit = 0;
        if ($selectedAccount) {
            // حساب الرصيد الافتتاحي قبل الفترة
            $openingBalance = JournalEntryLine::where('account_id', $selectedAccount)
                ->when($from, function($q) use ($from) {
                    $q->whereHas('journalEntry', function($q2) use ($from) {
                        $q2->where('date', '<', $from);
                    });
                })
                ->selectRaw('SUM(debit - credit) as balance')
                ->value('balance') ?? 0;
            // جلب الحركات خلال الفترة
            $entries = JournalEntryLine::with(['journalEntry'])
                ->where('account_id', $selectedAccount)
                ->when($from, function($q) use ($from) {
                    $q->whereHas('journalEntry', function($q2) use ($from) {
                        $q2->where('date', '>=', $from);
                    });
                })
                ->when($to, function($q) use ($to) {
                    $q->whereHas('journalEntry', function($q2) use ($to) {
                        $q2->where('date', '<=', $to);
                    });
                })
                ->get()
                ->sortBy(function($line) {
                    return $line->journalEntry->date ?? null;
                });
            $totalDebit = $entries->sum('debit');
            $totalCredit = $entries->sum('credit');
        }
        if ($request->get('export') === 'excel' && $selectedAccount) {
            return Excel::download(new \App\Exports\LedgerExport($entries, $accounts->find($selectedAccount), $from, $to, $openingBalance), 'ledger.xlsx');
        }
        if ($request->get('export') === 'pdf' && $selectedAccount) {
            $pdf = Pdf::loadView('ledger.pdf', [
                'accounts' => $accounts,
                'entries' => $entries,
                'selectedAccount' => $selectedAccount,
                'from' => $from,
                'to' => $to,
                'openingBalance' => $openingBalance,
                'totalDebit' => $totalDebit,
                'totalCredit' => $totalCredit,
            ]);
            return $pdf->download('ledger.pdf');
        }
        return view('ledger.index', compact('accounts', 'entries', 'selectedAccount', 'from', 'to', 'openingBalance', 'totalDebit', 'totalCredit'));
    }
} 