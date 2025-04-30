<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use App\Models\Transaction;
use App\Models\Account;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VoucherController extends Controller
{
   public function __construct()
   {
       $this->middleware('auth'); // ✅ حماية كل العمليات
   }

   public function index()
   {
       $vouchers = Voucher::latest()->paginate(20);
       return view('vouchers.index', compact('vouchers'));
   }

   public function create()
   {
       $cashAccounts = Account::where('is_cash_box', 1)->get(); // ✅ فقط صناديق نقدية
       $normalAccounts = Account::where('is_cash_box', 0)
                                 ->whereNull('nature')
                                 ->get(); // ✅ حسابات فعلية فقط بدون طبيعة
       $currencies = Currency::all(); // ✅ جميع العملات

       return view('vouchers.create', compact('cashAccounts', 'normalAccounts', 'currencies'));
   }

   public function store(Request $request)
   {
       if (!auth()->check()) {
           return redirect()->route('login')->with('error', 'يجب تسجيل الدخول لإنشاء السند.');
       }

       $validated = $request->validate([
           'type' => 'required|in:receipt,payment,transfer',
           'date' => 'required|date',
           'recipient_name' => 'nullable|string|max:255',
           'description' => 'nullable|string|max:1000',
           'transactions' => 'required|array',
           'transactions.*.account_id' => 'required|exists:accounts,id',
           'transactions.*.target_account_id' => 'required|exists:accounts,id',
           'transactions.*.amount' => 'required|numeric|min:0.01',
           'transactions.*.currency' => 'required|string|max:3',
           'transactions.*.exchange_rate' => 'required|numeric|min:0.000001',
           'transactions.*.description' => 'nullable|string|max:1000',
       ]);

       DB::transaction(function () use ($validated, $request) {
           $voucher = Voucher::create([
               'voucher_number' => $this->generateVoucherNumber(),
               'type' => $validated['type'],
               'date' => $validated['date'],
               'description' => $validated['description'],
               'recipient_name' => $validated['recipient_name'],
               'created_by' => auth()->id(),
           ]);

           foreach ($validated['transactions'] as $tx) {
               Transaction::create([
                   'voucher_id' => $voucher->id,
                   'date' => $validated['date'],
                   'type' => $validated['type'],
                   'amount' => $tx['amount'],
                   'currency' => $tx['currency'],
                   'exchange_rate' => $tx['exchange_rate'],
                   'account_id' => $tx['account_id'],
                   'target_account_id' => $tx['target_account_id'],
                   'description' => $tx['description'] ?? null,
                   'user_id' => auth()->id(), // ✅ تسجيل المستخدم الذي أجرى الحركة
               ]);
           }
       });

       return redirect()->route('vouchers.index')->with('success', 'تم إنشاء السند بنجاح.');
   }

   public function show(Voucher $voucher)
   {
       $voucher->load('transactions.account', 'transactions.targetAccount');
       return view('vouchers.show', compact('voucher'));
   }

   private function generateVoucherNumber()
   {
       $lastId = Voucher::max('id') ?? 0;
       return 'VCH-' . str_pad($lastId + 1, 5, '0', STR_PAD_LEFT);
   }
}