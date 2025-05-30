<?php

namespace Database\Seeders;

// use App\Models\User;
use Illuminate\Support\Facades\Hash;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\{
    Admin,
    Banner,
    Cart,
    Customer,
    Order,
    OrderItem,
    Product,
    Review,
    User,
    Wishlist
};

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        //     'password' => bcrypt('testuser')
        // ]);
        $this->call([
            AdminSeeder::class,
        ]);


        // Create Customers
        // Customer::factory()->count(10)->create()->each(function ($customer) {
        //     // Create Wishlists
        //     Wishlist::factory()->count(2)->create(['customer_id' => $customer->id]);

        //     // Create Cart
        //     Cart::factory()->count(2)->create(['customer_id' => $customer->id]);

        //     // Create Orders
        //     $orders = Order::factory()->count(2)->create(['customer_id' => $customer->id]);

        //     // Create Reviews
        //     Review::factory()->count(2)->create(['customer_id' => $customer->id]);

        //     $orders->each(function ($order) {
        //         // Add Order Items
        //         OrderItem::factory()->count(2)->create([
        //             'order_id' => $order->id,
        //         ]);
        //     });
        // });

        // Create Products
        // $this->call([
        //     ProductSeeder::class,
        // ]);

        // Create Banners
        $this->call([
            BannerSeeder::class,
        ]);

    }
}
