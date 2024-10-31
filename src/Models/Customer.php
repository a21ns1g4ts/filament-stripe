<?php

namespace A21ns1g4ts\FilamentStripe\Models;

use A21ns1g4ts\FilamentStripe\Database\Factories\CustomerFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Stripe\Subscription as StripeSubscription;

/**
 * @property string|null $stripe_id
 */
class Customer extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'stripe_id',
        'address',
        'balance',
        'cash_balance',
        'created',
        'currency',
        'default_source',
        'delinquent',
        'description',
        'discount',
        'email',
        'invoice_prefix',
        'invoice_settings',
        'livemode',
        'metadata',
        'name',
        'next_invoice_sequence',
        'phone',
        'preferred_locales',
        'shipping',
        'tax',
        'tax_exempt',
        'test_clock',
        'coupon',
        'promotion_code',
        'source',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'address' => 'array',
        'balance' => 'integer',
        'cash_balance' => 'array',
        'created' => 'integer',
        'delinquent' => 'boolean',
        'discount' => 'array',
        'invoice_settings' => 'array',
        'livemode' => 'boolean',
        'metadata' => 'array',
        'preferred_locales' => 'array',
        'shipping' => 'array',
        'tax' => 'array',
    ];

    protected static function newFactory()
    {
        return CustomerFactory::new();
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function billable(): MorphTo
    {
        return $this->morphTo();
    }

    public function subscribed(string $stripePrice): bool
    {
        $subscription = $this->subscriptions()
            ->whereStatus(StripeSubscription::STATUS_ACTIVE)
            ->where('stripe_price', $stripePrice)
            ->first();

        if (! $subscription) {
            $subscription = $this->subscriptions()
                ->whereStatus(StripeSubscription::STATUS_ACTIVE)
                ->whereHas('items', function ($query) use ($stripePrice) {
                    $query->where('stripe_price', $stripePrice);
                })
                ->first();
        }

        return $subscription ? true : false;
    }
}
