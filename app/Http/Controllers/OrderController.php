<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = Order::with('orderItems.product', 'customer')->get();
        return view('admin.orders.index', compact('orders'));
    }

    public function search(Request $request)
    {
        $query = $request->input('query');

        // Search in product_id, brand, type, description
        $orders = Order::with('orderItems.product', 'customer')
            ->where('id', 'LIKE', "%$query%")
            ->orWhere('total_amount', 'LIKE', "%$query%")
            ->orWhere('order_status', 'LIKE', "%$query%")
            ->orWhere('created_at', 'LIKE', "%$query%")
            ->orWhereHas('customer', function ($q2) use ($query) {
                $q2->where('name', 'LIKE', "%$query%");
            })
            ->orWhereHas('orderItems.product', function ($q3) use ($query) {
                $q3->where('name', 'LIKE', "%$query%")
                    ->orWhere('brand', 'LIKE', "%$query%")
                    ->orWhere('type', 'LIKE', "%$query%");
            })
            ->get();
        // ->orWhere('order.customer.name', 'LIKE', "%$query%")
        // ->orWhere('order.orderItems.product.name', 'LIKE', "%$query%")
        // ->orWhere('order.orderItems.product.brand', 'LIKE', "%$query%")
        // ->orWhere('order.orderItems.product.type', 'LIKE', "%$query%")

        return view('admin.orders.index', compact('orders', 'query'));
    }

    public function filter(Request $request)
    {
        $query = Order::with('orderItems.product', 'customer');

        // Sorting Logic (Date: Latest, Old)
        if ($request->filled('dateSort')) {
            if ($request->dateSort === 'latest') {
                $query->orderBy('created_at', 'desc');
            } elseif ($request->dateSort === 'oldest') {
                $query->orderBy('created_at', 'asc');
            }
        }

        if ($request->filled('order_status')) {
            $query->where('order_status', $request->order_status);
        }

        $order = $query->get();

        return response()->json($order);

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
    public function store(StoreOrderRequest $request)
    {
        $data = $request->validated();
        $data['customer_id'] = Auth::id();
        $order = Order::create($data);
        $products = Cart::with('product')->where('customer_id', Auth::id())->get();
        foreach ($products as $product) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $product->quantity,
                'price' => $product->product->actual_price,
            ]);
            $product->product->stock -= $product->quantity;
            $product->product->stock < 0;
            $product->product->stock = 0;
            $product->product->save();
        }
        Cart::where('customer_id', Auth::id())->delete();
        return redirect()->route('home');
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        $order = Order::with('orderItems.product', 'customer')->find($order->id);
        $subtotal = 0;
        $total = 0;

        foreach ($order->orderItems as $item) {
            $quantity = $item->quantity;
            $product = $item->product;

            if ($product) {
                $subtotal += $product->mrp * $quantity;
                $total += $product->actual_price * $quantity;
            }
        }
        $discount = $subtotal - $total;
        if ($subtotal > 0) {
            $discountPercentage = ($discount / $subtotal) * 100;
        } else {
            $discountPercentage = 0;
        }
        $discountPercentage = round($discountPercentage, 2);
        return view('admin.orders.show', compact('order', 'subtotal', 'total', 'discountPercentage', 'discount'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Order $order)
    {
        if ($order->order_status == 'pending')
            $order->order_status = 'delivered';
        if ($order->order_status == 'placed')
            $order->order_status = 'pending';
        $order->save();
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        $order->order_status = 'cancelled';
        $order->save();
        return redirect()->back();
    }
}
