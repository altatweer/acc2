<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    /**
     * عرض كل الفروع
     */
    public function index()
    {
        $branches = Branch::all();
        return view('branches.index', compact('branches'));
    }

    /**
     * عرض نموذج إضافة فرع جديد
     */
    public function create()
    {
        return view('branches.create');
    }

    /**
     * حفظ بيانات فرع جديد
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
        ]);

        Branch::create($request->all());

        return redirect()->route('branches.index')->with('success', 'تم إضافة الفرع بنجاح.');
    }

    /**
     * عرض نموذج تعديل بيانات فرع
     */
    public function edit(Branch $branch)
    {
        return view('branches.edit', compact('branch'));
    }

    /**
     * تحديث بيانات فرع
     */
    public function update(Request $request, Branch $branch)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
        ]);

        $branch->update($request->all());

        return redirect()->route('branches.index')->with('success', 'تم تحديث بيانات الفرع بنجاح.');
    }

    /**
     * حذف فرع
     */
    public function destroy(Branch $branch)
    {
        $branch->delete();

        return redirect()->route('branches.index')->with('success', 'تم حذف الفرع بنجاح.');
    }
}
