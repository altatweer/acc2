<?php
namespace App\Http\Controllers;

use App\Models\Salary;
use App\Models\Employee;
use Illuminate\Http\Request;

class SalaryController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:view_salaries')->only(['index', 'show']);
        $this->middleware('can:add_salary')->only(['create', 'store']);
        $this->middleware('can:edit_salary')->only(['edit', 'update']);
        $this->middleware('can:delete_salary')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $employeeId = $request->get('employee_id');
        $query = Salary::with('employee');
        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }
        $salaries = $query->latest()->paginate(20);
        return view('salaries.index', compact('salaries', 'employeeId'));
    }

    public function create(Request $request)
    {
        $employees = Employee::all();
        $employeeId = $request->get('employee_id');
        return view('salaries.create', compact('employees', 'employeeId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'basic_salary' => 'required|numeric|min:0',
            'allowances' => 'nullable|array',
            'deductions' => 'nullable|array',
            'effective_from' => 'required|date',
            'effective_to' => 'nullable|date|after_or_equal:effective_from',
        ]);
        $validated['allowances'] = $request->allowances ? array_filter($request->allowances, fn($v) => $v['name'] && $v['amount']) : [];
        $validated['deductions'] = $request->deductions ? array_filter($request->deductions, fn($v) => $v['name'] && $v['amount']) : [];
        Salary::create($validated);
        return redirect()->route('salaries.index', ['employee_id' => $validated['employee_id']])->with('success', 'تم إضافة الراتب بنجاح.');
    }

    public function show(Salary $salary)
    {
        $salary->load('employee');
        return view('salaries.show', compact('salary'));
    }

    public function edit(Salary $salary)
    {
        $employees = Employee::all();
        return view('salaries.edit', compact('salary', 'employees'));
    }

    public function update(Request $request, Salary $salary)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'basic_salary' => 'required|numeric|min:0',
            'allowances' => 'nullable|array',
            'deductions' => 'nullable|array',
            'effective_from' => 'required|date',
            'effective_to' => 'nullable|date|after_or_equal:effective_from',
        ]);
        $validated['allowances'] = $request->allowances ? array_filter($request->allowances, fn($v) => $v['name'] && $v['amount']) : [];
        $validated['deductions'] = $request->deductions ? array_filter($request->deductions, fn($v) => $v['name'] && $v['amount']) : [];
        $salary->update($validated);
        return redirect()->route('salaries.index', ['employee_id' => $validated['employee_id']])->with('success', 'تم تحديث بيانات الراتب بنجاح.');
    }

    public function destroy(Salary $salary)
    {
        $employeeId = $salary->employee_id;
        $salary->delete();
        return redirect()->route('salaries.index', ['employee_id' => $employeeId])->with('success', 'تم حذف الراتب بنجاح.');
    }
} 