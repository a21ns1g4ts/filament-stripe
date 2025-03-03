<?php

namespace A21ns1g4ts\FilamentStripe\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class FeatureProduct extends Pivot
{
    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'feature_id',
        'price_id',
        'sort',
        'unlimited',
        'meteread',
        'resetable',
        'value',
        'unit_amount',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'sort' => 'integer',
        'value' => 'integer',
        'unlimited' => 'boolean',
        'meteread' => 'boolean',
        'resetable' => 'boolean',
        'unit_amount' => 'integer',
    ];

    public function getTable()
    {
        return config('filament-stripe.table_names.feature_product', parent::getTable());
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function feature(): BelongsTo
    {
        return $this->belongsTo(Feature::class);
    }

    public function price(): BelongsTo
    {
        return $this->belongsTo(Price::class);
    }
}
