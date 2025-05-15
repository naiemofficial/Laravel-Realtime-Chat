<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CookieController;
use App\Http\Controllers\ChatController;


Route::get('/', function () {
    return redirect('/chat');
});


Route::get('/chat', [ChatController::class, 'index']);
