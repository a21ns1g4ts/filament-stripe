<?php

namespace A21ns1g4ts\FilamentStripe\Http\Controllers;

use A21ns1g4ts\FilamentStripe\Actions\Stripe\UpdateCustomer;
use A21ns1g4ts\FilamentStripe\Http\Middleware\VerifyWebhookSignature;
use A21ns1g4ts\FilamentStripe\Models\Customer;
use A21ns1g4ts\FilamentStripe\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Stripe\Stripe;
use Stripe\Subscription as StripeSubscription;
use Symfony\Component\HttpFoundation\Response;

/* Code from Laravel Cashier */
/* @see \Laravel\Cashier\Http\Controllers\WebhookController */

class WebhookController extends Controller
{
    public function __construct()
    {
        if (config('services.stripe.webhook_secret')) {
            $this->middleware(VerifyWebhookSignature::class);
        }
    }

    public function handleWebhook(Request $request)
    {
        $payload = json_decode($request->getContent(), true);
        $method = 'handle' . Str::studly(str_replace('.', '_', $payload['type'] ?? ''));

        if (method_exists($this, $method)) {
            $this->setMaxNetworkRetries();

            return $this->{$method}($payload);
        }

        return $this->missingMethod($payload);
    }

    protected function handleCustomerSubscriptionCreated(array $payload)
    {
        $customer = Customer::where('stripe_id', $payload['data']['object']['customer'] ?? null)->first();

        if ($customer) {
            $data = $payload['data']['object'];
            if (! $customer->subscriptions->contains('stripe_id', $data['id'])) {
                $this->createSubscription($customer, $data);
            }
        }

        return $this->successResponse();
    }

    protected function handleCustomerSubscriptionUpdated(array $payload)
    {
        $customer = Customer::where('stripe_id', $payload['data']['object']['customer'] ?? null)->first();
        if ($customer) {
            $data = $payload['data']['object'];
            $subscription = $customer->subscriptions()->firstOrNew(['stripe_id' => $data['id']]);

            if (isset($data['default_payment_method']) && $data['default_payment_method'] !== null) {
                UpdateCustomer::run($customer->stripe_id, ['invoice_settings' => ['default_payment_method' => $data['default_payment_method']]]);
            }

            if (isset($data['status']) && $data['status'] === StripeSubscription::STATUS_INCOMPLETE_EXPIRED) {
                $subscription->items()->delete();
                $subscription->delete();

                return $this->successResponse();
            }

            $this->updateSubscription($subscription, $data);
            $this->syncSubscriptionItems($subscription, $data['items']['data']);
        }

        return $this->successResponse();
    }

    protected function handleCustomerSubscriptionDeleted(array $payload)
    {
        $customer = Customer::where('stripe_id', $payload['data']['object']['customer'] ?? null)->first();
        if ($customer) {
            $subscription = $customer->subscriptions()
                ->where('stripe_id', $payload['data']['object']['id'])
                ->first();

            if ($subscription) {
                $subscription->update([
                    'status' => StripeSubscription::STATUS_CANCELED,
                    'ends_at' => Carbon::now(),
                ]);
            }
        }

        return $this->successResponse();
    }

    protected function createSubscription($customer, $data)
    {
        $firstItem = $data['items']['data'][0];
        $isSinglePrice = count($data['items']['data']) === 1;

        $subscription = $customer->subscriptions()->create([
            'stripe_id' => $data['id'],
            'stripe_price' => $isSinglePrice ? $firstItem['price']['id'] : null,
            'quantity' => $isSinglePrice ? ($firstItem['quantity'] ?? null) : null,
            'trial_ends' => $data['trial_end'] ?: null,
            'status' => $data['status'],
            'cancel_at_period_end' => $data['cancel_at_period_end'],
            'currency' => $data['currency'],
            'current_period_end' => $data['current_period_end'],
            'current_period_start' => $data['current_period_start'],
            'default_payment_method' => $data['default_payment_method'],
            'description' => $data['description'],
            'metadata' => $data['metadata'] ?? [],
            'pending_setup_intent' => $data['pending_setup_intent'],
            'pending_update' => $data['pending_update'] ?? [],
            'payment_behavior' => $data['payment_behavior'] ?? null,
            'add_invoice_items' => $data['add_invoice_items'] ?? [],
            'application_fee_percent' => $data['application_fee_percent'],
            'automatic_tax' => $data['automatic_tax'] ?? [],
            'billing_cycle_anchor' => $data['billing_cycle_anchor'],
            'billing_cycle_anchor_config' => $data['billing_cycle_anchor_config'] ?? [],
            'billing_thresholds' => $data['billing_thresholds'] ?? [],
            'cancel_at' => $data['cancel_at'],
            'collection_method' => $data['collection_method'],
            'days_until_due' => $data['days_until_due'],
            'default_source' => $data['default_source'],
            'default_tax_rates' => $data['default_tax_rates'] ?? [],
            'discounts' => $data['discounts'] ?? [],
            'invoice_settings' => $data['invoice_settings'] ?? [],
            'on_behalf_of' => $data['on_behalf_of'],
            'payment_settings' => $data['payment_settings'] ?? [],
            'pending_invoice_item_interval' => $data['pending_invoice_item_interval'] ?? [],
            'transfer_data' => $data['transfer_data'] ?? [],
            'trial_end' => $data['trial_end'],
            'trial_settings' => $data['trial_settings'] ?? [],
            'trial_start' => $data['trial_start'],
            'ends_at' => null,
        ]);

        foreach ($data['items']['data'] as $item) {
            $subscription->items()->create([
                'stripe_id' => $item['id'],
                'stripe_product' => $item['price']['product'],
                'stripe_price' => $item['price']['id'],
                'quantity' => $item['quantity'] ?? null,
            ]);
        }
    }

    protected function updateSubscription($subscription, $data)
    {
        $firstItem = $data['items']['data'][0];
        $isSinglePrice = count($data['items']['data']) === 1;

        $subscription->update([
            'stripe_price' => $isSinglePrice ? $firstItem['price']['id'] : null,
            'quantity' => $isSinglePrice ? ($firstItem['quantity'] ?? null) : null,
            'trial_ends' => $data['trial_end'] ?: null,
            'status' => $data['status'],
            'cancel_at_period_end' => $data['cancel_at_period_end'],
            'currency' => $data['currency'],
            'current_period_end' => $data['current_period_end'],
            'current_period_start' => $data['current_period_start'],
            'default_payment_method' => $data['default_payment_method'],
            'description' => $data['description'],
            'metadata' => $data['metadata'] ?? [],
            'pending_setup_intent' => $data['pending_setup_intent'],
            'pending_update' => $data['pending_update'] ?? [],
            'payment_behavior' => $data['payment_behavior'] ?? null,
            'add_invoice_items' => $data['add_invoice_items'] ?? [],
            'application_fee_percent' => $data['application_fee_percent'],
            'automatic_tax' => $data['automatic_tax'] ?? [],
            'billing_cycle_anchor' => $data['billing_cycle_anchor'],
            'billing_cycle_anchor_config' => $data['billing_cycle_anchor_config'] ?? [],
            'billing_thresholds' => $data['billing_thresholds'] ?? [],
            'cancel_at' => $data['cancel_at'],
            'collection_method' => $data['collection_method'],
            'days_until_due' => $data['days_until_due'],
            'default_source' => $data['default_source'],
            'default_tax_rates' => $data['default_tax_rates'] ?? [],
            'discounts' => $data['discounts'] ?? [],
            'invoice_settings' => $data['invoice_settings'] ?? [],
            'on_behalf_of' => $data['on_behalf_of'],
            'payment_settings' => $data['payment_settings'] ?? [],
            'pending_invoice_item_interval' => $data['pending_invoice_item_interval'] ?? [],
            'transfer_data' => $data['transfer_data'] ?? [],
            'trial_end' => $data['trial_end'],
            'trial_settings' => $data['trial_settings'] ?? [],
            'trial_start' => $data['trial_start'],
            'ends_at' => null,
        ]);
    }

    protected function syncSubscriptionItems(Subscription $subscription, ?array $items)
    {
        if (! $items) {
            return;
        }

        $subscriptionItemIds = [];

        Log::debug('Syncing subscription items', ['subscription' => $subscription, 'items' => $items]);

        foreach ($items as $item) {
            $subscriptionItemIds[] = $item['id'];

            $subscription->items()->updateOrCreate(
                ['stripe_id' => $item['id']],
                [
                    'stripe_product' => $item['price']['product'],
                    'stripe_price' => $item['price']['id'],
                    'quantity' => $item['quantity'] ?? null,
                ]
            );
        }

        $subscription->items()->whereNotIn('stripe_id', $subscriptionItemIds)->delete();
    }

    protected function handleCustomerUpdated(array $payload)
    {
        $data = $payload['data']['object'];
        $customer = Customer::where('stripe_id', $data['id'])->first();

        if ($customer) {
            $fillables = $customer->getFillable();
            foreach ($data as $key => $value) {
                if (! in_array($key, $fillables)) {
                    unset($data[$key]);
                }
            }

            $customer->fill($data);
            $customer->save();
        }

        return $this->successResponse();
    }

    protected function successResponse()
    {
        return new Response('Webhook Handled', Response::HTTP_OK);
    }

    protected function missingMethod($parameters = [])
    {
        return new Response('Webhook Type Not Supported', Response::HTTP_NOT_FOUND);
    }

    protected function setMaxNetworkRetries($retries = 3)
    {
        Stripe::setMaxNetworkRetries($retries);
    }
}
