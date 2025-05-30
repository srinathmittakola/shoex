<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Http\Requests\StoreCartRequest;
use App\Http\Requests\UpdateCartRequest;
use App\Models\Coupon;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $coupons = Coupon::whereDate('start_date', '<=', Carbon::today())->get();
        $items = Cart::with('product')->where('customer_id', Auth::id())->get();
        return view('user.cart', compact('items', 'coupons'));
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
    public function store(StoreCartRequest $request)
    {
        $data = $request->validated();
        $data['customer_id'] = Auth::id();
        $cartItem = Cart::where('product_id', $data["product_id"])
            ->where('customer_id', $data["customer_id"])
            ->first();
        if ($cartItem) {
            $stock = Product::where("id", $data["product_id"])->value('stock');
            if ($stock > $cartItem->quantity) {
                $cartItem->quantity += 1;
                $cartItem->save();
            }
        } else {
            Cart::create($data);
        }
        return redirect()->back();
    }

    /**
     * Display the specified resource.
     */
    public function show(Cart $cart)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cart $cart)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCartRequest $request, Cart $cart)
    {
        $data = $request->validated();
        $data['customer_id'] = Auth::id();
        $cartItem = Cart::where('product_id', $data["product_id"])
            ->where('customer_id', $data["customer_id"])
            ->first();
        if ($cartItem) {
            $cartItem->quantity -= 1;
            if ($cartItem->quantity == 0) {
                $cartItem->delete();
            } else {
                $cartItem->save();
            }
        } else {
            Cart::create($data);
        }
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cart $cart)
    {
        $cart->delete();
        return Redirect::back();
    }
}
