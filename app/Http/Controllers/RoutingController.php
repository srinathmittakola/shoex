<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoutingController extends Controller
{
    public function home()
    {
        if (Auth::guard('customer')->check()) {
            $user = Auth::guard('customer')->user();

            $featuredProducts = Product::with([
                'wishlists' => function ($query) use ($user) {
                    if ($user) {
                        $query->where('customer_id', $user->id);
                    }
                }
            ])->where('stock', '>', '0')
                ->orderBy('stock', 'desc')
                ->take(12)
                ->get();

            // Add a 'wished' flag to each product
            $featuredProducts->each(function ($product) use ($user) {
                $wishlistEntry = $product->wishlists->firstWhere('customer_id', $user->id);
                $product->wished = $wishlistEntry ? $wishlistEntry->id : null;
            });
        } else {
            $featuredProducts = Product::where('stock', '>', '0')->orderBy('stock', 'desc')->take(12)->get();
        }
        return view("User/home", compact('featuredProducts'));
    }

    public function getCategoryProducts($category)
    {
        if (Auth::guard('customer')->check()) {
            $user = Auth::guard('customer')->user();

            $products = Product::with([
                'wishlists' => function ($query) use ($user) {
                    if ($user) {
                        $query->where('customer_id', $user->id);
                    }
                }
            ])->where('shoe_for', $category)->where('stock', '>', '0')
                ->get();

            // Add a 'wished' flag to each product
            $products->each(function ($product) use ($user) {
                $wishlistEntry = $product->wishlists->firstWhere('customer_id', $user->id);
                $product->wished = $wishlistEntry ? $wishlistEntry->id : null;
            });
        } else {
            $products = Product::where('stock', '>', '0')->where('shoe_for', $category)->orderBy('stock', 'desc')->get();
        }
        return view('user.products', compact('products'));
    }

    public function getBrandProducts($brand)
    {
        if (Auth::guard('customer')->check()) {
            $user = Auth::guard('customer')->user();

            $products = Product::with([
                'wishlists' => function ($query) use ($user) {
                    if ($user) {
                        $query->where('customer_id', $user->id);
                    }
                }
            ])->where('brand', $brand)->where('stock', '>', '0')
                ->get();

            // Add a 'wished' flag to each product
            $products->each(function ($product) use ($user) {
                $wishlistEntry = $product->wishlists->firstWhere('customer_id', $user->id);
                $product->wished = $wishlistEntry ? $wishlistEntry->id : null;
            });
        } else {
            $products = Product::where('stock', '>', '0')->where('brand', $brand)->orderBy('stock', 'desc')->get();
        }
        return view('user.' . $brand, compact('products'));
    }

    public function checkout(Request $request)
    {
        $items = Cart::with('product')->where('customer_id', Auth::id())->get();
        if (count($items) <= 0) {
            return redirect()->back();
        }
        $subtotal = Cart::with('product')
            ->where('customer_id', Auth::id())
            ->get()
            ->sum(function ($item) {
                return ($item->product->actual_price ?? 0) * $item->quantity; // In case product is null
            });
        $total = $subtotal;
        $discount = 0;
        if ($request->coupon) {
            $discount = Coupon::where('id', $request->coupon)->value('discount');
            $total = $subtotal * (1 - ($discount / 100));
        }
        $user = Customer::where('id', Auth::id())->first();
        return view('user.checkout', compact('items', 'total', 'subtotal', 'discount', 'user'));
    }

}
