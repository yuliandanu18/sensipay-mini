<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WhatsappCloudTestController;

// Tempel isi file ini ke routes/web.php (atau include dari sana) supaya punya endpoint test:
//
// Route::get('/whatsapp/test', [WhatsappCloudTestController::class, 'sendTest'])
//     ->name('whatsapp.test');
//
Route::get('/whatsapp/test', [WhatsappCloudTestController::class, 'sendTest'])
    ->name('whatsapp.test');
