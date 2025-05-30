<?php

namespace Database\Factories;


use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    private function getRandomImage()
    {
        // Get all files in 'storage/app/public/products'
        $files = Storage::disk('public')->files('products');

        // Pick a random file from the array
        $randomFile = $files[array_rand($files)];

        // Return the file path (relative to the 'public' folder)
        return basename($randomFile); // Just the file name, without the 'public/' prefix
    }
    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
            'brand' => $this->faker->randomElement(getBrands()),
            'type' => $this->faker->randomElement(getProductType()),
            'description' => $this->faker->paragraph,
            'shoe_for' => $this->faker->randomElement(getShoeFor()),
            'mrp' => $this->faker->randomFloat(2, 50, 500),
            'actual_price' => function (array $attributes) {
                return $this->faker->randomFloat(2, 30, $attributes['mrp'] - 0.01);  // Ensures actual_price is less than mrp
            },
            'stock' => $this->faker->numberBetween(1, 100),
            'image_paths' => json_encode([
                'products/' . $this->getRandomImage(),
                'products/' . $this->getRandomImage(),
            ]),
        ];
    }
}
