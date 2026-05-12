<?php

use Illuminate\Support\Facades\Route;
use Modules\PancakeIntegration\Http\Controllers\WebhookController;

Route::post('/webhooks/pancake', [WebhookController::class, 'handle'])
    ->name('pancake.webhook');
