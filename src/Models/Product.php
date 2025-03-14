<?php

namespace A21ns1g4ts\FilamentStripe\Models;

use A21ns1g4ts\FilamentStripe\Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'type',
        'stripe_id',
        'name',
        'description',
        'active',
        'metadata',
        'default_price',
        'default_price_data',
        'images',
        'marketing_features',
        'livemode',
        'package_dimensions',
        'shippable',
        'statement_descriptor',
        'tax_code',
        'unit_label',
        'url',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'active' => 'boolean',
        'metadata' => 'array',
        'default_price_data' => 'array',
        'images' => 'array',
        'marketing_features' => 'array',
        'package_dimensions' => 'array',
        'shippable' => 'boolean',
        'livemode' => 'boolean',
    ];

    public function getTable()
    {
        return config('filament-stripe.table_names.products', parent::getTable());
    }

    protected static function newFactory()
    {
        return ProductFactory::new();
    }

    public function featureProducts(): HasMany
    {
        return $this->hasMany(FeatureProduct::class);
    }

    public function features(): BelongsToMany
    {
        return $this->belongsToMany(Feature::class, config('filament-stripe.table_names.feature_product'))
            ->using(FeatureProduct::class)
            ->withPivot('value', 'unit_amount', 'sort', 'price_id', 'product_id', 'feature_id', 'meteread', 'unlimited', 'resetable')
            ->withTimestamps();
    }

    public function prices(): HasMany
    {
        return $this->hasMany(Price::class);
    }
}
