<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        $query = Customer::latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $customers = $query->paginate(12)->withQueryString();

        return Inertia::render('Customers/Index', compact('customers', 'search'));
    }

    public function create()
    {
        return Inertia::render('Customers/Create');
    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:customers,email',
            'phone' => 'nullable|string|max:20|unique:customers,phone',
            'address' => 'nullable|string',
        ]);

        Customer::create($data); //

        return redirect()->route('customers.index')->with('success', 'Customer created successfully.'); // find it soon
    }

    public function edit(Customer $customer)
    {
        return Inertia::render('Customers/Edit', compact('customer')); // return the edit view with the customer data
    }
 // find it, no do not find it
    public function update(Request $request, Customer $customer) // function to update customer
    {
        $data = $request->validate([ // what to validate
            'first_name' => 'required|string|max:255', // in it
            'last_name' => 'nullable|string|max:255', // fiber // bit not
            'email' => 'nullable|email|unique:customers,email,' . $customer->id, // email must  except for the cuomer
            'phone' => 'nullable|string|max:20|unique:customers,phone,' . $customer->id, // phone must  for the current customer
            'address' => 'nullable|string', // got it well
        ]);

        $customer->update($data);

        return redirect()->route('customers.index')->with('success', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'Customer deleted successfully.');
    }
}
