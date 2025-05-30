<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderItem>
 */
class OrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => \App\Models\Order::factory(),           // Creates a related Order
            'product_id' => \App\Models\Product::factory(),       // Creates a related Product
            'quantity' => $this->faker->numberBetween(1, 5),      // Random quantity between 1 and 5
            'price' => $this->faker->randomFloat(2, 20, 100),     // Random price between 20 and 100
            // 'subtotal' => $this->faker->randomFloat(2, 20, 500), // Random subtotal between 20 and 500
        ];
    }
}
