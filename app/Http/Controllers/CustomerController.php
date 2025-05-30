<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Customer;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customers = Customer::get();
        return view('Admin.Customers.index', compact('customers'));
    }

    public function search(Request $request)
    {
        $query = $request->input('query');

        // Search in product_id, brand, type, description
        $customers = Customer::where('id', 'LIKE', "%$query%")
            ->orWhere('name', 'LIKE', "%$query%")
            ->orWhere('email', 'LIKE', "%$query%")
            ->orWhere('phone', 'LIKE', "%$query%")
            ->orWhere('city', 'LIKE', "%$query%")
            ->orWhere('state', 'LIKE', "%$query%")
            ->orWhere('postal_code', 'LIKE', "%$query%")
            ->get();

        return view('admin.customers.index', compact('customers', 'query'));
    }

    public function filter(Request $request)
    {
        $query = Customer::query();

        // Sorting Logic (Date: Latest, Old)
        if ($request->filled('dateSort')) {
            if ($request->dateSort === 'latest') {
                $query->orderBy('created_at', 'desc');
            } elseif ($request->dateSort === 'oldest') {
                $query->orderBy('created_at', 'asc');
            }
        }

        $customers = $query->select('id', 'name', 'email', 'phone', 'city', 'state', 'created_at')->get();

        return response()->json($customers);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCustomerRequest $request)
    {
        $validated = $request->validated();
        $validated['password']=bcrypt($validated['password']);
        Customer::create($validated);
        return Redirect::route("login");
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        $orders = Order::with('orderItems.product', 'customer')
            ->where('customer_id', $customer->id)
            ->get();
        return view('admin.customers.show', compact('customer', 'orders'));
    }

    public function profile()
    {
        $user = Customer::where('id', Auth::id())->first();
        $orders = Order::with('orderItems.product')->where('customer_id', Auth::id())->get();
        return view('user.profile', compact('user', 'orders'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        // 
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        $validated = $request->validated();
        $customer->update($validated);
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        //
    }
}
