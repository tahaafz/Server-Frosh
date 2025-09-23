<?php

use App\Http\Controllers\Telegram\WebhookController;
use App\Http\Middleware\AlwaysOkWebhook;

Route::post('/telegram/webhook/{token}', WebhookController::class)
    ->where('token', 'keep-it-secret-123')
    ->middleware(AlwaysOkWebhook::class);;
