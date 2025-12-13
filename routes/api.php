<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Webhook\FonnteWebhookController;

Route::post('/webhook/fonnte', [FonnteWebhookController::class, 'handle'])
    ->name('webhook.fonnte');
