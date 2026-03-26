<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ProductController;

// Public
Route::get('/', function () {
    return view('welcome');
});
Route::post('/api/generate-product', [ProductController::class, 'generateWithAI']);

// Auth
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Broadcasting auth (must be inside auth middleware for private channels)
Broadcast::routes(['middleware' => ['web', 'auth']]);

// Chat (auth required)
Route::middleware('auth')->group(function () {
    Route::get('/chat', [ChatController::class, 'index'])->name('chat');
    Route::get('/chat/contacts', [ChatController::class, 'contacts']);
    Route::get('/chat/messages/{user}', [ChatController::class, 'messages']);
    Route::post('/chat/send', [ChatController::class, 'send']);
    Route::post('/chat/send-image', [ChatController::class, 'sendImage']);
    Route::post('/chat/message/edit', [ChatController::class, 'edit']);
    Route::delete('/chat/message/{message}', [ChatController::class, 'delete']);
    Route::post('/chat/message/forward', [ChatController::class, 'forward']);
});
