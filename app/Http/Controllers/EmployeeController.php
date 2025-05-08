<?php
namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Currency;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:عرض الموظفين')->only(['index', 'show']);
        $this->middleware('can:إضافة موظف')->only(['create', 'store']);
        $this->middleware('can:تعديل موظف')->only(['edit', 'update']);
        $this->middleware('can:حذف موظف')->only(['destroy']);
    }

    public function index()
    {
        $employees = Employee::latest()->paginate(20);
        return view('employees.index', compact('employees'));
    }

    public function create()
    {
        $currencies = Currency::all();
        return view('employees.create', compact('currencies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'employee_number' => 'required|string|max:50|unique:employees,employee_number',
            'department' => 'nullable|string|max:100',
            'job_title' => 'nullable|string|max:100',
            'hire_date' => 'nullable|date',
            'status' => 'required|in:active,inactive,terminated',
            'currency' => 'required|string|exists:currencies,code',
        ]);
        Employee::create($validated);
        return redirect()->route('employees.index')->with('success', 'تم إضافة الموظف بنجاح.');
    }

    public function show(Employee $employee)
    {
        return view('employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        $currencies = Currency::all();
        return view('employees.edit', compact('employee', 'currencies'));
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'employee_number' => 'required|string|max:50|unique:employees,employee_number,' . $employee->id,
            'department' => 'nullable|string|max:100',
            'job_title' => 'nullable|string|max:100',
            'hire_date' => 'nullable|date',
            'status' => 'required|in:active,inactive,terminated',
            'currency' => 'required|string|exists:currencies,code',
        ]);
        $employee->update($validated);
        return redirect()->route('employees.index')->with('success', 'تم تحديث بيانات الموظف بنجاح.');
    }

    public function destroy(Employee $employee)
    {
        $hasSalaries = $employee->salaries()->exists();
        $hasSalaryPayments = $employee->salaryPayments()->exists();
        if ($hasSalaries || $hasSalaryPayments) {
            return redirect()->route('employees.index')->with('error', 'لا يمكن حذف الموظف لوجود رواتب أو دفعات رواتب مرتبطة به.');
        }
        $employee->delete();
        return redirect()->route('employees.index')->with('success', 'تم حذف الموظف بنجاح.');
    }
} 