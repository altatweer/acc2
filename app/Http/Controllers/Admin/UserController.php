<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'roles' => 'array',
        ]);
        $user = User::create($validated);
        if ($request->has('roles')) {
            $user->syncRoles($request->roles);
        }
        return redirect()->route('admin.users.index')->with('success', 'تم إضافة المستخدم بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        if ($user->isSuperAdmin()) {
            return back()->with('error', 'لا يمكن تعديل بيانات السوبر أدمن.');
        }
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'roles' => 'array',
        ]);
        if (empty($validated['password'])) {
            unset($validated['password']);
        }
        $user->update($validated);
        if ($request->has('roles')) {
            $user->syncRoles($request->roles);
        }
        return redirect()->route('admin.users.index')->with('success', 'تم تحديث المستخدم بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        if ($user->isSuperAdmin()) {
            return back()->with('error', 'لا يمكن حذف السوبر أدمن.');
        }
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'تم حذف المستخدم بنجاح');
    }

    /**
     * عرض صفحة تحديد الصناديق النقدية للموظف
     */
    public function editCashBoxes($id)
    {
        $user = \App\Models\User::findOrFail($id);
        $cashBoxes = \App\Models\Account::where('is_cash_box', 1)->get();
        $userCashBoxes = $user->cashBoxes()->pluck('accounts.id')->toArray();
        return view('admin.users.edit_cash_boxes', compact('user', 'cashBoxes', 'userCashBoxes'));
    }

    /**
     * حفظ ربط الصناديق النقدية للموظف
     */
    public function updateCashBoxes(Request $request, $id)
    {
        $user = \App\Models\User::findOrFail($id);
        $cashBoxIds = $request->input('cash_boxes', []);
        $user->cashBoxes()->sync($cashBoxIds);
        return redirect()->route('admin.users.index')->with('success', 'تم تحديث الصناديق النقدية للموظف بنجاح.');
    }
}
