<?php

namespace A21ns1g4ts\FilamentStripe\Actions;

use A21ns1g4ts\FilamentStripe\Actions\Stripe\UpdateCustomer;
use A21ns1g4ts\FilamentStripe\Actions\Stripe\UpdateFeature;
use A21ns1g4ts\FilamentStripe\Actions\Stripe\UpdatePrice;
use A21ns1g4ts\FilamentStripe\Actions\Stripe\UpdateProduct;
use A21ns1g4ts\FilamentStripe\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Lorisleiva\Actions\Concerns\AsAction;

class UpToStripe
{
    use AsAction;

    public function handle(Model $model, ?string $object = null)
    {
        $action = match ($object) {
            'customer' => UpdateCustomer::class,
            'product' => UpdateProduct::class,
            'price' => UpdatePrice::class,
            'feature' => UpdateFeature::class,
            default => null,
        };

        return $action ? $this->updateToStripe($model, $action) : null;
    }

    /**
     * Updates data in Stripe using the corresponding action class.
     */
    protected function updateToStripe(Model $model, string $stripeActionClass): ?Model
    {
        $stripeId = $model->stripe_id; // @phpstan-ignore-line
        if (is_null($stripeId)) {
            return null;
        }

        // Run the action to update data in Stripe and update the local model
        $data = $stripeActionClass::run($stripeId, $model->toArray())->toArray();

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
