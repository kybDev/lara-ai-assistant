<?php

use Illuminate\Support\Facades\Route;
use KyBDev\LaraAiAssistant\Http\Controllers\ChatController;

Route::middleware(config('ai-assistant.route.middleware', ['web', 'auth']))
    ->prefix(config('ai-assistant.route.prefix', 'ai-assistant'))
    ->name('ai-assistant.chat.')
    ->group(function (): void {
        Route::get('/', [ChatController::class, 'index'])->name('index');
        Route::post('/message', [ChatController::class, 'message'])->name('message');
    });
