<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class AdminRoutingController extends Controller
{
    function dashboard()
    {
        // productCount
        $productCount = Product::count();
        $customerCount = Customer::count();
        $orders = Order::count();
        $newOrders = Order::with('orderItems.product', 'customer')->whereIn("order_status", ['pending', 'placed'])->get();
        $placedOrders = Order::where('order_status', 'placed')->count();
        $pendingOrders = Order::where('order_status', 'pending')->count();
        // $completedOrders = Order::where('order_status', operator: 'delivered')->count();
        $cancelledOrders = Order::where('order_status', 'cancelled')->count();
        return view("Admin/dashboard", compact('placedOrders', 'cancelledOrders', 'orders', 'newOrders', 'pendingOrders', 'productCount', 'customerCount'));
    }
}