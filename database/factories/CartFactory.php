<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cart>
 */
class CartFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id' => \App\Models\Customer::factory(),  // Creates a related Customer
            'product_id' => \App\Models\Product::factory(),    // Creates a related Product
            'quantity' => $this->faker->numberBetween(1, 5),   // Random quantity between 1 and 5
        ];
    }
}
