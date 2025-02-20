<?php

use A21ns1g4ts\FilamentStripe\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

Route::any('stripe/webhook', [WebhookController::class, 'handleWebhook'])->name('webhook');
