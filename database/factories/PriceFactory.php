<?php

namespace A21ns1g4ts\FilamentStripe\Database\Factories;

use A21ns1g4ts\FilamentStripe\Models\Price;
use A21ns1g4ts\FilamentStripe\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class PriceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = Price::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'stripe_id' => $this->faker->word(),
            'stripe_product' => $this->faker->word(),
            'active' => $this->faker->boolean(),
            'currency' => $this->faker->randomElement(['usd', 'aed', 'afn', 'all', 'amd', 'ang', 'aoa', 'ars', 'aud', 'awg', 'azn', 'bam', 'bbd', 'bdt', 'bgn', 'bif', 'bmd', 'bnd', 'bob', 'brl', 'bsd', 'bwp', 'byn', 'bzd', 'cad', 'cdf', 'chf', 'clp', 'cny', 'cop', 'crc', 'cve', 'czk', 'djf', 'dkk', 'dop', 'dzd', 'egp', 'etb', 'eur', 'fjd', 'fkp', 'gbp', 'gel', 'gip', 'gmd', 'gnf', 'gtq', 'gyd', 'hkd', 'hnl', 'htg', 'huf', 'idr', 'ils', 'inr', 'isk', 'jmd', 'jpy', 'kes', 'kgs', 'khr', 'kmf', 'krw', 'kyd', 'kzt', 'lak', 'lbp', 'lkr', 'lrd', 'lsl', 'mad', 'mdl', 'mga', 'mkd', 'mmk', 'mnt', 'mop', 'mur', 'mvr', 'mwk', 'mxn', 'myr', 'mzn', 'nad', 'ngn', 'nio', 'nok', 'npr', 'nzd', 'pab', 'pen', 'pgk', 'php', 'pkr', 'pln', 'pyg', 'qar', 'ron', 'rsd', 'rub', 'rwf', 'sar', 'sbd', 'scr', 'sek', 'sgd', 'shp', 'sle', 'sos', 'srd', 'std', 'szl', 'thb', 'tjs', 'top', 'try', 'ttd', 'twd', 'tzs', 'uah', 'ugx', 'uyu', 'uzs', 'vnd', 'vuv', 'wst', 'xaf', 'xcd', 'xof', 'xpf', 'yer', 'zar', 'zmw']),
            'metadata' => [],
            'nickname' => $this->faker->word(),
            'recurring' => [],
            'type' => $this->faker->randomElement(['one_time', 'recurring']),
            'unit_amount' => $this->faker->numberBetween(-10000, 10000),
            'unit_label' => $this->faker->word(),
            'billing_scheme' => $this->faker->randomElement(['per_unit', 'tiered']),
            'created' => $this->faker->randomNumber(4),
            'currency_options' => [],
            'custom_unit_amount' => [],
            'livemode' => $this->faker->boolean(),
            'lookup_key' => $this->faker->word(),
            'tax_behavior' => $this->faker->randomElement(['exclusive', 'inclusive', 'unspecified']),
            'tiers' => [],
            'tiers_mode' => $this->faker->randomElement(['graduated', 'volume']),
            'transform_quantity' => [],
            'unit_amount_decimal' => $this->faker->word(),
        ];
    }
}
