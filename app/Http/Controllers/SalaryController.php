<?php
namespace App\Http\Controllers;

use App\Models\Salary;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        
        // Check if the employee already has an active salary
        if ($employeeId) {
            $existingSalary = Salary::where('employee_id', $employeeId)
                ->where(function($query) {
                    $query->whereNull('effective_to')
                        ->orWhere('effective_to', '>=', date('Y-m-d'));
                })
                ->first();
                
            if ($existingSalary) {
                return redirect()->route('salaries.edit', $existingSalary->id)
                    ->with('info', 'الموظف لديه راتب نشط بالفعل. يمكنك تعديل الراتب الحالي بدلاً من إضافة راتب جديد.');
            }
        }
        
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
        
        // Check if the employee already has an active salary
        $existingSalary = Salary::where('employee_id', $validated['employee_id'])
            ->where(function($query) use ($validated) {
                $query->whereNull('effective_to')
                    ->orWhere('effective_to', '>=', $validated['effective_from']);
            })
            ->first();
            
        if ($existingSalary) {
            return redirect()->route('salaries.edit', $existingSalary->id)
                ->with('info', 'الموظف لديه راتب نشط بالفعل. يمكنك تعديل الراتب الحالي بدلاً من إضافة راتب جديد.');
        }
        
        $validated['allowances'] = $request->allowances ? array_filter($request->allowances, fn($v) => $v['name'] && $v['amount']) : [];
        $validated['deductions'] = $request->deductions ? array_filter($request->deductions, fn($v) => $v['name'] && $v['amount']) : [];
        
        Salary::create($validated);
        return redirect()->route('salaries.index', ['employee_id' => $validated['employee_id']])->with('success', 'تم إضافة الراتب بنجاح.');
    }

    public function show(Salary $salary)
    {
        $salary->load('employee');
        
        // Get salary history for this employee
        $salaryHistory = Salary::where('employee_id', $salary->employee_id)
            ->where('id', '!=', $salary->id)
            ->orderByDesc('effective_from')
            ->get();
            
        return view('salaries.show', compact('salary', 'salaryHistory'));
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
        
        // If changing effective dates, check for conflicts with other salary records
        if ($validated['employee_id'] == $salary->employee_id && 
            ($validated['effective_from'] != $salary->effective_from || 
             $validated['effective_to'] != $salary->effective_to)) {
                 
            $conflictingSalary = Salary::where('employee_id', $validated['employee_id'])
                ->where('id', '!=', $salary->id)
                ->where(function($query) use ($validated) {
                    // Check if new date range overlaps with existing date ranges
                    $query->where(function($q) use ($validated) {
                        $q->where('effective_from', '<=', $validated['effective_from'])
                            ->where(function($q2) use ($validated) {
                                $q2->whereNull('effective_to')
                                    ->orWhere('effective_to', '>=', $validated['effective_from']);
                            });
                    })->orWhere(function($q) use ($validated) {
                        $q->where('effective_from', '<=', $validated['effective_to'] ?? date('Y-m-d', strtotime('+100 years')))
                            ->where('effective_from', '>=', $validated['effective_from']);
                    });
                })
                ->first();
                
            if ($conflictingSalary) {
                return back()->withErrors(['effective_from' => 'تتعارض فترة سريان الراتب مع راتب آخر للموظف.'])
                    ->withInput();
            }
        }
        
        $validated['allowances'] = $request->allowances ? array_filter($request->allowances, fn($v) => $v['name'] && $v['amount']) : [];
        $validated['deductions'] = $request->deductions ? array_filter($request->deductions, fn($v) => $v['name'] && $v['amount']) : [];
        
        // Use transaction to ensure data integrity
        DB::transaction(function() use ($salary, $validated) {
            $salary->update($validated);
        });
        
        return redirect()->route('salaries.index', ['employee_id' => $validated['employee_id']])
            ->with('success', 'تم تحديث بيانات الراتب بنجاح.');
    }

    public function destroy(Salary $salary)
    {
        // Check if this salary has been used in any salary payments
        $hasPayments = \App\Models\SalaryPayment::where('employee_id', $salary->employee_id)
            ->whereDate('created_at', '>=', $salary->effective_from)
            ->when($salary->effective_to, function($query, $effectiveTo) {
                return $query->whereDate('created_at', '<=', $effectiveTo);
            })
            ->exists();
            
        if ($hasPayments) {
            return back()->with('error', 'لا يمكن حذف هذا الراتب لأنه مرتبط بدفعات رواتب.');
        }
        
        $employeeId = $salary->employee_id;
        $salary->delete();
        return redirect()->route('salaries.index', ['employee_id' => $employeeId])->with('success', 'تم حذف الراتب بنجاح.');
    }
} 