<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ProductController;

use App\Http\Controllers\DealerController;
use App\Http\Controllers\PropertyController as PropController;

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

// Email Verification
Route::middleware('auth')->group(function () {
    Route::get('/email-verify', [AuthController::class, 'showEmailVerify'])->name('verification.notice');
    Route::post('/email/resend', [AuthController::class, 'sendVerificationEmail'])->name('verification.send');
});
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
    ->middleware('signed')
    ->name('verification.verify');

// Broadcasting auth (must be inside auth middleware for private channels)
Broadcast::routes(['middleware' => ['web', 'auth']]);

// Chat (auth required, email verified)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/chat', [ChatController::class, 'index'])->name('chat');
    Route::get('/chat/contacts', [ChatController::class, 'contacts']);
    Route::get('/chat/messages/{user}', [ChatController::class, 'messages']);
    Route::post('/chat/send', [ChatController::class, 'send']);
    Route::post('/chat/send-image', [ChatController::class, 'sendImage']);
    Route::post('/chat/message/edit', [ChatController::class, 'edit']);
    Route::delete('/chat/message/{message}', [ChatController::class, 'delete']);
    Route::post('/chat/message/forward', [ChatController::class, 'forward']);
});

// Dealer Dashboard (auth required, email verified)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dealer-dashboard', [DealerController::class, 'dashboard'])->name('dealer.dashboard');
    Route::post('/dealer/verify', [DealerController::class, 'submitVerification'])->name('dealer.verify');

    // Property Listings
    Route::get('/dealer/properties', [PropController::class, 'index'])->name('dealer.properties');
    Route::post('/dealer/property/store', [PropController::class, 'store'])->name('dealer.property.store');
    Route::delete('/dealer/property/{id}', [PropController::class, 'destroy'])->name('dealer.property.destroy');
});
