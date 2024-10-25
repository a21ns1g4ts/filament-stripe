<?php

use A21ns1g4ts\FilamentStripe\Models\Product;

it('can create a product', function () {
    $product = Product::create([
        'name' => 'Test Product',
    ]);

    expect($product)->toBeInstanceOf(Product::class);
    expect($product->name)->toBe('Test Product');
});

it('can update a product', function () {
    $product = Product::factory()->create();

    $product->update(['name' => 'Updated Product']);

    expect($product->fresh()->name)->toBe('Updated Product');
});

it('can delete a product', function () {
    $product = Product::factory()->create();

    $product->delete();

    expect(Product::find($product->id))->toBeNull();
});
