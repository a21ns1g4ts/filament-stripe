<?php

namespace A21ns1g4ts\FilamentStripe\Actions;

use A21ns1g4ts\FilamentStripe\Actions\Stripe\GetCustomer;
use A21ns1g4ts\FilamentStripe\Actions\Stripe\GetFeature;
use A21ns1g4ts\FilamentStripe\Actions\Stripe\GetPrice;
use A21ns1g4ts\FilamentStripe\Actions\Stripe\GetProduct;
use A21ns1g4ts\FilamentStripe\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Lorisleiva\Actions\Concerns\AsAction;

class UpFromStripe
{
    use AsAction;

    public function handle(Model $model, ?string $object = null)
    {
        $action = match ($object) {
            'customer' => GetCustomer::class,
            'product' => GetProduct::class,
            'price' => GetPrice::class,
            'feature' => GetFeature::class,
            default => null,
        };

        return $action ? $this->updateFromStripe($model, $action) : null;
    }

    /**
     * Updates the model using the corresponding Stripe action.
     */
    protected function updateFromStripe(Model $model, string $stripeActionClass): ?Model
    {
        $stripeId = $model->stripe_id; // @phpstan-ignore-line
        if (is_null($stripeId)) {
            return null;
        }

        $data = $stripeActionClass::run($stripeId)->toArray();

        $fillables = $model->getFillable();
        $data['stripe_product'] = $data['product'] ?? null;

        foreach ($data as $key => $value) {
            if (! in_array($key, $fillables)) {
                unset($data[$key]);
            }
        }

        if ($model instanceof Product) {
            // WARNING: Stripe api deprecated the `type` field in the product object but we still need it.
            // Stripe api always returns `service` for the `type` field.
            unset($data['type']);
        }

        $model->fill($data);
        $model->save();

        return $model;
    }
}
