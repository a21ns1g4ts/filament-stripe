<?php

namespace A21ns1g4ts\FilamentStripe\Database\Factories;

use A21ns1g4ts\FilamentStripe\Models\Billable;
use A21ns1g4ts\FilamentStripe\Models\Subscription;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubscriptionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = Subscription::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'stripe_id' => $this->faker->word(),
            'billable_id' => Billable::factory(),
            'stripe_price' => $this->faker->word(),
            'status' => $this->faker->randomElement(['incomplete', 'incomplete_expired', 'trialing', 'active', 'past_due', 'canceled', 'unpaid', 'or paused']),
            'cancel_at_period_end' => $this->faker->boolean(),
            'currency' => $this->faker->randomElement(['usd', 'aed', 'afn', 'all', 'amd', 'ang', 'aoa', 'ars', 'aud', 'awg', 'azn', 'bam', 'bbd', 'bdt', 'bgn', 'bif', 'bmd', 'bnd', 'bob', 'brl', 'bsd', 'bwp', 'byn', 'bzd', 'cad', 'cdf', 'chf', 'clp', 'cny', 'cop', 'crc', 'cve', 'czk', 'djf', 'dkk', 'dop', 'dzd', 'egp', 'etb', 'eur', 'fjd', 'fkp', 'gbp', 'gel', 'gip', 'gmd', 'gnf', 'gtq', 'gyd', 'hkd', 'hnl', 'htg', 'huf', 'idr', 'ils', 'inr', 'isk', 'jmd', 'jpy', 'kes', 'kgs', 'khr', 'kmf', 'krw', 'kyd', 'kzt', 'lak', 'lbp', 'lkr', 'lrd', 'lsl', 'mad', 'mdl', 'mga', 'mkd', 'mmk', 'mnt', 'mop', 'mur', 'mvr', 'mwk', 'mxn', 'myr', 'mzn', 'nad', 'ngn', 'nio', 'nok', 'npr', 'nzd', 'pab', 'pen', 'pgk', 'php', 'pkr', 'pln', 'pyg', 'qar', 'ron', 'rsd', 'rub', 'rwf', 'sar', 'sbd', 'scr', 'sek', 'sgd', 'shp', 'sle', 'sos', 'srd', 'std', 'szl', 'thb', 'tjs', 'top', 'try', 'ttd', 'twd', 'tzs', 'uah', 'ugx', 'uyu', 'uzs', 'vnd', 'vuv', 'wst', 'xaf', 'xcd', 'xof', 'xpf', 'yer', 'zar', 'zmw']),
            'current_period_end' => $this->faker->numberBetween(1, 10000),
            'current_period_start' => $this->faker->numberBetween(1, 10000),
            'default_payment_method' => $this->faker->word(),
            'description' => $this->faker->text(),
            'metadata' => [],
            'pending_setup_intent' => $this->faker->word(),
            'pending_update' => [],
            'payment_behavior' => $this->faker->randomElement(['allow_incomplete', 'default_incomplete', 'error_if_incomplete', 'pending_if_incomplete']),
            'add_invoice_items' => [],
            'application_fee_percent' => $this->faker->randomFloat(0, 0, 100),
            'automatic_tax' => [],
            'backdate_start_date' => $this->faker->numberBetween(1, 10000),
            'billing_cycle_anchor' => $this->faker->numberBetween(1, 10000),
            'billing_cycle_anchor_config' => [],
            'billing_thresholds' => [],
            'cancel_at' => $this->faker->numberBetween(1, 10000),
            'collection_method' => $this->faker->randomElement(['charge_automatically', 'send_invoice']),
            'coupon' => $this->faker->word(),
            'days_until_due' => $this->faker->numberBetween(-10000, 10000),
            'default_source' => $this->faker->word(),
            'default_tax_rates' => [],
            'discounts' => [],
            'invoice_settings' => [],
            'off_session' => $this->faker->boolean(),
            'on_behalf_of' => $this->faker->word(),
            'payment_settings' => [],
            'pending_invoice_item_interval' => [],
            'promotion_code' => $this->faker->word(),
            'proration_behavior' => $this->faker->randomElement(['always_invoice', 'create_prorations', 'none']),
            'transfer_data' => [],
            'trial_from_plan' => $this->faker->boolean(),
            'trial_end' => $this->faker->numberBetween(1, 10000),
            'trial_settings' => [],
            'trial_start' => $this->faker->numberBetween(1, 10000),
        ];
    }
}
