<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::get();
        return view('admin.products.index', compact('products'));
    }

    public function search(Request $request)
    {

        $query = $request->input('query');

        // Search in product_id, brand, type, description
        $products = Product::where('product_id', 'LIKE', "%$query%")
            ->orWhere('name', 'LIKE', "%$query%")
            ->orWhere('description', 'LIKE', "%$query%")
            ->orWhere('brand', 'LIKE', "%$query%")
            ->orWhere('type', 'LIKE', "%$query%")
            ->orWhere('description', 'LIKE', "%$query%")
            ->get();

        return view('admin.products.index', compact('products', 'query'));
    }
    public function searchProducts(Request $request)
    {

        $query = $request->input('query');

        // Search in product_id, brand, type, description
        $products = Product::where('product_id', 'LIKE', "%$query%")
            ->orWhere('name', 'LIKE', "%$query%")
            ->orWhere('description', 'LIKE', "%$query%")
            ->orWhere('brand', 'LIKE', "%$query%")
            ->orWhere('type', 'LIKE', "%$query%")
            ->orWhere('description', 'LIKE', "%$query%")
            ->get();

        return view('user.products', compact('products', 'query'));
    }

    public function product_filter(Request $request)
    {

        $query = Product::query();

        // Filter by brand if selected
        if ($request->has('brand') && $request->brand != 'All') {
            $query->where('brand', $request->brand);
        }

        // Filter by stock status
        if ($request->has('stock')) {
            if ($request->stock == 'inStock') {
                $query->where('stock', '>', 0);
            } elseif ($request->stock == 'OutOfStock') {
                $query->where('stock', '=', 0);
            }
        }

        // Sorting Logic (Price: Low to High, High to Low)
        if ($request->has('priceSort')) {
            if ($request->priceSort == 'low_to_high') {
                $query->orderBy('actual_price', 'asc');
            } elseif ($request->priceSort == 'high_to_low') {
                $query->orderBy('actual_price', 'desc');
            }
        }

        // Sorting Logic (Date: Latest, Old)
        if ($request->has('dateSort')) {
            if ($request->dateSort == 'latest') {
                $query->orderBy('created_at', 'desc');
            } elseif ($request->dateSort == 'old') {
                $query->orderBy('created_at', 'asc');
            }
        }

        $products = $query->get();

        return response()->json($products);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.products.add');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        $imagePaths = [];

        if ($request->hasFile('image_paths')) {
            $files = $request->file('image_paths');
            $firstImagePath = null;

            foreach ($files as $index => $image) {
                $path = $image->store('products', 'public'); // Store in storage/app/public/products
                if ($index == 0) {
                    $firstImagePath = $path; // Save the first image path separately
                } else {
                    $imagePaths[] = $path;
                }
            }

            // Ensure the main image is at index 0
            if ($firstImagePath) {
                array_unshift($imagePaths, $firstImagePath);
            }
        }

        Product::create([
            // 'product_id' => $request->product_id,
            'name' => $request->name,
            'brand' => $request->brand,
            'type' => $request->type,
            'description' => $request->description,
            'shoe_for' => $request->shoe_for,
            'mrp' => $request->mrp,
            'actual_price' => $request->actual_price,
            'stock' => $request->stock,
            'image_paths' => json_encode($imagePaths),
        ]);

        return redirect()->route('admin.products.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        if (Auth::guard('customer')->check()) {
            $user = Auth::guard('customer')->user();

            $product = Product::with([
                'reviews',
                'reviews.customer',
                'wishlists' => function ($query) use ($user) {
                    if ($user) {
                        $query->where('customer_id', $user->id);
                    }
                }
            ])
                ->withAvg('reviews', 'rating')
                ->find($product->id);
            $wishedlist = $product->wishlists->firstWhere('customer_id', $user->id);
            $product->wished = $wishedlist ? $wishedlist->id : null;


            $products = Product::with([
                'wishlists' => function ($query) use ($user) {
                    if ($user) {
                        $query->where('customer_id', $user->id);
                    }
                }
            ])->where('brand', $product->brand)->where('stock', '>', '0')
                ->get();

            // Add a 'wished' flag to each product
            $products->each(function ($product) use ($user) {
                $wishlistEntry = $product->wishlists->firstWhere('customer_id', $user->id);
                $product->wished = $wishlistEntry ? $wishlistEntry->id : null;
            });

        } else {

            $product = Product::with(['reviews', 'reviews.customer'])
                ->withAvg('reviews', 'rating')
                ->find($product->id);
            $products = Product::where('stock', '>', '0')->where('brand', $product->brand)->orderBy('stock', 'desc')->get();
        }


        $customer = Auth::guard('customer')->user();
        $canReview = false;
        $review = null;
        if ($customer) {
            $canReview = OrderItem::whereHas('order', function ($query) use ($customer) {
                $query->where('customer_id', $customer->id)
                    ->where('order_status', 'delivered');
            })->where('product_id', $product->id)->exists();
            $review = $product->reviews()->where('customer_id', Auth::guard('customer')->id())->first();
        }
        return view('user.product', compact('product', 'products', 'canReview', 'review'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        return view('Admin.Products.edit', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $product->update([
            'description' => $request->description,
            'mrp' => $request->mrp,
            'actual_price' => $request->actual_price,
            'stock' => $request->stock,
        ]);
        if ($request->hasFile('images')) {
            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $imagePath = $image->store('products', 'public');
                $imagePaths[] = $imagePath;
            }
            $product->image_paths = json_encode($imagePaths);
            $product->save();
        }

        return Redirect::route('admin.products.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        // $product->delete();
        $product->stock = 0;
        $product->save();
        return Redirect::back();
    }
}
