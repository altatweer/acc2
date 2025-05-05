<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:عرض العملات')->only(['index', 'show']);
        $this->middleware('can:إضافة عملة')->only(['create', 'store']);
        $this->middleware('can:تعديل عملة')->only(['edit', 'update']);
        $this->middleware('can:حذف عملة')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $currencies = Currency::all();
        return view('currencies.index', compact('currencies'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('currencies.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:3|unique:currencies,code',
            'symbol' => 'nullable|string|max:10',
            'exchange_rate' => 'required|numeric',
            'is_default' => 'sometimes|boolean'
        ]);
        $data = $request->only(['name','code','symbol','exchange_rate','is_default']);
        if(!empty($data['is_default'])) {
            Currency::where('is_default', true)->update(['is_default' => false]);
        }
        Currency::create($data);
        return redirect()->route('currencies.index')->with('success', 'تم إضافة العملة بنجاح.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Currency $currency)
    {
        return view('currencies.show', compact('currency'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Currency $currency)
    {
        return view('currencies.edit', compact('currency'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Currency $currency)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:3|unique:currencies,code,' . $currency->id,
            'symbol' => 'nullable|string|max:10',
            'exchange_rate' => 'required|numeric',
            'is_default' => 'sometimes|boolean'
        ]);
        $data = $request->only(['name','code','symbol','exchange_rate','is_default']);
        if(!empty($data['is_default'])) {
            Currency::where('is_default', true)->where('id', '!=', $currency->id)->update(['is_default' => false]);
        }
        $currency->update($data);
        return redirect()->route('currencies.index')->with('success', 'تم تحديث العملة بنجاح.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Currency $currency)
    {
        $currency->delete();
        return redirect()->route('currencies.index')->with('success', 'تم حذف العملة بنجاح.');
    }
}
