<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Customer;
use Illuminate\Http\Request;
use Inertia\Inertia;

/**
 * CRUD and listing for customers.
 */
class CustomerController extends Controller
{
    /**
     * Display a searchable, paginated listing of customers.
     */
    public function index(Request $request)
    {
        $search = $request->string('search')->trim()->value();

        $customers = Customer::search($search)
            ->withCount('orders')
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return Inertia::render('Customers/Index', compact('customers', 'search'));
    }

    /**
     * Show the form for creating a new customer.
     */
    public function create()
    {
        return Inertia::render('Customers/Create');
    }

    /**
     * Store a newly created customer.
     */
    public function store(StoreCustomerRequest $request)
    {
        $data = $request->validated();

        Customer::create($data);

        return redirect()->route('customers.index')->with('success', 'Customer created successfully.');
    }

    /**
     * Redirect to the edit form, since there is no dedicated detail page.
     */
    public function show(Customer $customer)
    {
        return redirect()->route('customers.edit', $customer);
    }

    /**
     * Show the form for editing the given customer.
     */
    public function edit(Customer $customer)
    {
        return Inertia::render('Customers/Edit', compact('customer'));
    }

    /**
     * Update the given customer.
     */
    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        $data = $request->validated();

        $customer->update($data);

        return redirect()->route('customers.index')->with('success', 'Customer updated successfully.');
    }

    /**
     * Remove the given customer.
     */
    public function destroy(Customer $customer)
    {
        // Deleting a customer with past orders would break the customer_id FK
        // on those records and erase their purchase history; block it instead.
        if ($customer->orders()->exists()) {
            return redirect()->back(fallback: route('customers.index'))->with('error', 'Cannot delete a customer with recorded orders.');
        }

        $customer->delete();

        // back() rather than a fixed route redirect, so deleting from a
        // searched list returns to that same search instead of silently
        // resetting to the unfiltered one.
        return redirect()->back(fallback: route('customers.index'))->with('success', 'Customer deleted successfully.');
    }
}
