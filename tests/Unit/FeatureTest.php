<?php

use A21ns1g4ts\FilamentStripe\Models\Feature;
use A21ns1g4ts\FilamentStripe\Models\Price;

it('can create a feature', function () {
    $price = Price::factory()->create();

    $feature = Feature::create([
        'price_id' => $price->id,
        'stripe_price' => 'price_fake_id',
        'name' => 'Unlimited Users',
    ]);

    expect($feature)->toBeInstanceOf(Feature::class);
    expect($feature->name)->toBe('Unlimited Users');
});

it('can update a feature', function () {
    $feature = Feature::factory()->create();

    $feature->update(['name' => 'Limited Users']);

    expect($feature->fresh()->name)->toBe('Limited Users');
});

it('can delete a feature', function () {
    $feature = Feature::factory()->create();

    $feature->delete();

    expect(Feature::find($feature->id))->toBeNull();
});
