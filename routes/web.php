<?php

use App\Http\Controllers\Api\PaperWebhookController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/paper-webhook', [PaperWebhookController::class, 'handle']);