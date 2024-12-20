<?php

namespace A21ns1g4ts\FilamentStripe\Database\Factories;

use A21ns1g4ts\FilamentStripe\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'type' => $this->faker->randomElement(['plan', 'service', 'sku', 'feature']),
            'stripe_id' => $this->faker->word(),
            'name' => $this->faker->name(),
            'active' => $this->faker->boolean(),
            'description' => $this->faker->text(),
            'metadata' => '{}',
            'default_price_data' => '{}',
            'images' => '{}',
            'marketing_features' => '{}',
            'package_dimensions' => '{}',
            'shippable' => $this->faker->boolean(),
            'tax_code' => $this->faker->word(),
            'unit_label' => $this->faker->word(),
            'url' => $this->faker->url(),
        ];
    }
}
