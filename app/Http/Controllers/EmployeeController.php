<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource with filters, search, and KPI metrics.
     */
    public function index(Request $request): View
    {
        $search = $request->string('search')->trim()->value();
        $position = $request->string('position')->trim()->value();
        $status = $request->string('status')->trim()->value();

        // Sorting
        $sortField = $request->query('sort', 'first_name');
        $allowedSorts = ['first_name', 'last_name', 'position', 'salary', 'hire_date', 'status'];
        if (! in_array($sortField, $allowedSorts, true)) {
            $sortField = 'first_name';
        }
        $sortDirection = strtolower($request->query('direction', 'asc')) === 'desc' ? 'desc' : 'asc';

        $employees = Employee::query()
            ->search($search)
            ->filterByPosition($position)
            ->filterByStatus($status)
            ->orderBy($sortField, $sortDirection)
            ->paginate(10)
            ->withQueryString();

        // Overview stats/KPIs, gathered in a single pass over the table.
        $counts = Employee::query()
            ->selectRaw('status, count(*) as total, sum(salary) as payroll')
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        $activeCount = (int) ($counts[Employee::STATUS_ACTIVE]->total ?? 0);
        $activePayroll = (float) ($counts[Employee::STATUS_ACTIVE]->payroll ?? 0);

        $stats = [
            'total' => (int) $counts->sum('total'),
            'active' => $activeCount,
            'on_leave' => (int) ($counts[Employee::STATUS_ON_LEAVE]->total ?? 0),
            'inactive' => (int) ($counts[Employee::STATUS_INACTIVE]->total ?? 0),
            'monthly_payroll' => $activePayroll,
            'avg_salary' => $activeCount > 0 ? $activePayroll / $activeCount : 0,
        ];

        // Available position options for filtering dropdown. orderByRaw('LOWER(...)')
        // rather than orderBy(): plain orderBy sorts case-sensitively on SQLite
        // (uppercase before any lowercase letter), unlike MySQL's default collation.
        $positions = Employee::query()
            ->whereNotNull('position')
            ->distinct()
            ->orderByRaw('LOWER(position)')
            ->pluck('position')
            ->toArray();

        if (empty($positions)) {
            $positions = Employee::POSITIONS;
        }

        return view('employees.index', [
            'employees' => $employees,
            'stats' => $stats,
            'positions' => $positions,
            'filters' => [
                'search' => $search,
                'position' => $position,
                'status' => $status,
                'sort' => $sortField,
                'direction' => $sortDirection,
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('employees.create', [
            'positions' => Employee::POSITIONS,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEmployeeRequest $request): RedirectResponse
    {
        $employee = Employee::create($request->validated());

        return redirect()
            ->route('employees.index')
            ->with('success', "Employee {$employee->name} added successfully.");
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee): View
    {
        return view('employees.show', [
            'employee' => $employee,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $employee): View
    {
        // position is free text (no Rule::in constraining it), so an
        // employee's current role may not be one of the standard POSITIONS -
        // without adding it here, the select would show "Select a Role"
        // instead of their actual, unchanged position.
        $positions = Employee::POSITIONS;

        if ($employee->position && ! in_array($employee->position, $positions, true)) {
            $positions[] = $employee->position;
        }

        return view('employees.edit', [
            'employee' => $employee,
            'positions' => $positions,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEmployeeRequest $request, Employee $employee): RedirectResponse
    {
        $employee->update($request->validated());

        return redirect()
            ->route('employees.show', $employee)
            ->with('success', "Employee record for {$employee->name} updated successfully.");
    }

    /**
     * Quickly toggle employee status.
     */
    public function toggleStatus(Request $request, Employee $employee): RedirectResponse
    {
        $nextStatus = match ($employee->status) {
            Employee::STATUS_ACTIVE => Employee::STATUS_ON_LEAVE,
            Employee::STATUS_ON_LEAVE => Employee::STATUS_INACTIVE,
            default => Employee::STATUS_ACTIVE,
        };

        if ($request->has('new_status')) {
            $requestedStatus = $request->input('new_status');
            if (in_array($requestedStatus, Employee::statuses(), true)) {
                $nextStatus = $requestedStatus;
            }
        }

        $employee->update(['status' => $nextStatus]);

        return redirect()
            ->back()
            ->with('success', "Status for {$employee->name} changed to {$employee->status_label}.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee): RedirectResponse
    {
        $name = $employee->name;
        $employee->delete();

        return redirect()
            ->route('employees.index')
            ->with('success', "Employee {$name} deleted successfully.");
    }
}
