<?php

namespace A21ns1g4ts\FilamentStripe\Database\Factories;

use A21ns1g4ts\FilamentStripe\Models\Subscription;
use A21ns1g4ts\FilamentStripe\Models\SubscriptionItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubscriptionItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = SubscriptionItem::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'stripe_id' => $this->faker->word(),
            'subscription_id' => Subscription::factory(),
            'stripe_subscription' => $this->faker->word(),
            'product' => $this->faker->word(),
            'stripe_price' => '{}',
            'quantity' => $this->faker->numberBetween(1, 100000),
        ];
    }
}
