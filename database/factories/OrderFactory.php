<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
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
            'total_amount' => $this->faker->numberBetween(100, 1000),  // Random total amount
            'payment_type' => $this->faker->randomElement(['COD', 'online']),  // Random payment type
            'order_status' => $this->faker->randomElement(['placed', 'pending', 'delivered', 'cancelled']),  // Random order status
        ];
    }
}
