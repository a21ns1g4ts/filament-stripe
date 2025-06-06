<?php

namespace A21ns1g4ts\FilamentStripe\Models;

use A21ns1g4ts\FilamentStripe\Database\Factories\SubscriptionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string|null $stripe_id
 */
class Subscription extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'stripe_id',
        'stripe_price',
        'customer_id',
        'status',
        'cancel_at_period_end',
        'currency',
        'current_period_end',
        'current_period_start',
        'default_payment_method',
        'description',
        'metadata',
        'pending_setup_intent',
        'pending_update',
        'payment_behavior',
        'add_invoice_items',
        'application_fee_percent',
        'automatic_tax',
        'backdate_start_date',
        'billing_cycle_anchor',
        'billing_cycle_anchor_config',
        'billing_thresholds',
        'cancel_at',
        'collection_method',
        'coupon',
        'days_until_due',
        'default_source',
        'default_tax_rates',
        'discounts',
        'invoice_settings',
        'off_session',
        'on_behalf_of',
        'payment_settings',
        'pending_invoice_item_interval',
        'promotion_code',
        'proration_behavior',
        'transfer_data',
        'trial_from_plan',
        'trial_end',
        'trial_settings',
        'trial_start',
        'quantity',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'cancel_at_period_end' => 'boolean',
        'current_period_end' => 'integer',
        'current_period_start' => 'integer',
        'metadata' => 'array',
        'pending_update' => 'array',
        'add_invoice_items' => 'array',
        'application_fee_percent' => 'decimal',
        'automatic_tax' => 'array',
        'backdate_start_date' => 'integer',
        'billing_cycle_anchor' => 'integer',
        'billing_cycle_anchor_config' => 'array',
        'billing_thresholds' => 'array',
        'cancel_at' => 'integer',
        'default_tax_rates' => 'array',
        'discounts' => 'array',
        'invoice_settings' => 'array',
        'off_session' => 'boolean',
        'payment_settings' => 'array',
        'pending_invoice_item_interval' => 'array',
        'transfer_data' => 'array',
        'trial_from_plan' => 'boolean',
        'trial_end' => 'integer',
        'trial_settings' => 'array',
        'trial_start' => 'integer',
        'quantity' => 'integer',
    ];

    public function getTable()
    {
        return config('filament-stripe.table_names.subscriptions', parent::getTable());
    }

    protected static function newFactory()
    {
        return SubscriptionFactory::new();
    }

    public function price(): BelongsTo
    {
        return $this->belongsTo(Price::class, 'stripe_price', 'stripe_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SubscriptionItem::class);
    }
}
