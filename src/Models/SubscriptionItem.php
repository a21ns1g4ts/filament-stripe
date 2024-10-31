<?php

namespace A21ns1g4ts\FilamentStripe\Models;

use A21ns1g4ts\FilamentStripe\Database\Factories\SubscriptionItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// TODO: Add tests
class SubscriptionItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'stripe_id',
        'subscription_id',
        'stripe_price',
        'quantity',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quantity' => 'integer',
    ];

    public function getTable()
    {
        return config('filament-stripe.table_names.subscription_items', parent::getTable());
    }

    protected static function newFactory()
    {
        return SubscriptionItemFactory::new();
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }
}
