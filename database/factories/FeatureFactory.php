<?php

namespace A21ns1g4ts\FilamentStripe\Database\Factories;

use A21ns1g4ts\FilamentStripe\Models\Feature;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

class FeatureFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = Feature::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'active' => true,
            'stripe_price' => 'price_fake_id',
            'stripe_id' => 'fake_id',
            'metadata' => [],
            'livemode' => false,
            'lookup_key' => null,
        ];
    }
}
