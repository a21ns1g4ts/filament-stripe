<?php

use A21ns1g4ts\FilamentStripe\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

Route::post('stripe/webhook', [WebhookController::class, 'handleWebhook'])->name('webhook');
