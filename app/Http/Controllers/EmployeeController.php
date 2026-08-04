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
        $search = $request->query('search');
        $position = $request->query('position');
        $status = $request->query('status');

        // Base query with search and filters applied
        $query = Employee::query()
            ->search($search)
            ->filterByPosition($position)
            ->filterByStatus($status);

        // Sorting
        $sortField = $request->query('sort', 'first_name');
        $allowedSorts = ['first_name', 'last_name', 'position', 'salary', 'hire_date', 'status'];
        if (! in_array($sortField, $allowedSorts, true)) {
            $sortField = 'first_name';
        }
        $sortDirection = strtolower($request->query('direction', 'asc')) === 'desc' ? 'desc' : 'asc';

        $employees = (clone $query)
            ->orderBy($sortField, $sortDirection)
            ->paginate(10)
            ->withQueryString();

        // Calculate Overview Stats/KPIs
        $stats = [
            'total' => Employee::count(),
            'active' => Employee::where('status', Employee::STATUS_ACTIVE)->count(),
            'on_leave' => Employee::where('status', Employee::STATUS_ON_LEAVE)->count(),
            'inactive' => Employee::where('status', Employee::STATUS_INACTIVE)->count(),
            'monthly_payroll' => Employee::where('status', Employee::STATUS_ACTIVE)->sum('salary'),
            'avg_salary' => Employee::where('status', Employee::STATUS_ACTIVE)->avg('salary') ?? 0,
        ];

        // Available position options for filtering dropdown
        $positions = Employee::query()
            ->whereNotNull('position')
            ->distinct()
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
        return view('employees.edit', [
            'employee' => $employee,
            'positions' => Employee::POSITIONS,
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
            if (in_array($requestedStatus, [Employee::STATUS_ACTIVE, Employee::STATUS_INACTIVE, Employee::STATUS_ON_LEAVE], true)) {
                $nextStatus = $requestedStatus;
            }
        }

        $employee->update(['status' => $nextStatus]);

        $statusLabels = [
            Employee::STATUS_ACTIVE => 'Active',
            Employee::STATUS_INACTIVE => 'Inactive',
            Employee::STATUS_ON_LEAVE => 'On Leave',
        ];

        return redirect()
            ->back()
            ->with('success', "Status for {$employee->name} changed to " . ($statusLabels[$nextStatus] ?? $nextStatus) . ".");
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

