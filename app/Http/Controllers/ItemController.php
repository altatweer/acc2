<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:عرض العناصر')->only(['index', 'show']);
        $this->middleware('can:إضافة عنصر')->only(['create', 'store']);
        $this->middleware('can:تعديل عنصر')->only(['edit', 'update']);
        $this->middleware('can:حذف عنصر')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = Item::paginate(20);
        return view('items.index', compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('items.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'type'       => 'required|in:product,service',
            'unit_price' => 'required|numeric|min:0',
            'description'=> 'nullable|string',
        ]);
        Item::create($validated);
        return redirect()->route('items.index')->with('success', 'تم إضافة العنصر بنجاح.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Item $item)
    {
        return view('items.show', compact('item'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Item $item)
    {
        return view('items.edit', compact('item'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Item $item)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'type'       => 'required|in:product,service',
            'unit_price' => 'required|numeric|min:0',
            'description'=> 'nullable|string',
        ]);
        $item->update($validated);
        return redirect()->route('items.index')->with('success', 'تم تحديث العنصر بنجاح.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Item $item)
    {
        $item->delete();
        return redirect()->route('items.index')->with('success', 'تم حذف العنصر بنجاح.');
    }
}
