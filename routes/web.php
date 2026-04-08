<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PricingController;
use App\Http\Controllers\TestimonialsController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DealerController;
use App\Http\Controllers\PropertyController as PropController;

// Public
Route::get('/', function () {
    return view('welcome');
});
Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/properties/{id}', [PropController::class, 'show'])->name('properties.show');
Route::post('/api/generate-product', [ProductController::class, 'generateWithAI']);

// Pricing Page
Route::get('/pricing', [PricingController::class, 'index'])->name('pricing');

// Testimonials Page
Route::get('/testimonials', [TestimonialsController::class, 'index'])->name('testimonials');

// Subscription Plan Selection (auth required, NO email verification needed)
// Dealers see this right after registration before verifying email
Route::middleware(['auth'])->group(function () {
    Route::get('/subscription/plans', [SubscriptionController::class, 'showPlans'])->name('subscription.plans');
    Route::post('/subscription/checkout', [SubscriptionController::class, 'checkout'])->name('subscription.checkout');
    Route::post('/subscription/skip', [SubscriptionController::class, 'skipToFreePlan'])->name('subscription.skip');
});
// Auth
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Google Auth
Route::get('auth/google', [GoogleAuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback']);

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
    Route::post('/dealer/clear-verification', [DealerController::class, 'clearVerification'])->name('dealer.clear-verification');

    // Property Listings (requires plan)
    Route::middleware(['dealer.plan'])->group(function () {
        Route::get('/dealer/properties', [PropController::class, 'indexPage'])->name('dealer.properties.index');
        Route::get('/dealer/properties/data', [PropController::class, 'index'])->name('dealer.properties');
        Route::get('/dealer/properties/create', [PropController::class, 'create'])->name('dealer.properties.create');
        Route::post('/dealer/property/store', [PropController::class, 'store'])->name('dealer.property.store');
        Route::delete('/dealer/property/{id}', [PropController::class, 'destroy'])->name('dealer.property.destroy');
    });

    // Subscription Management (requires email verification)
    Route::get('/subscription/dashboard', [SubscriptionController::class, 'dashboard'])->name('subscription.dashboard');
    Route::get('/subscription/success', [SubscriptionController::class, 'success'])->name('subscription.success');
    Route::get('/subscription/cancel', [SubscriptionController::class, 'cancelCheckout'])->name('subscription.cancel');
    Route::post('/subscription/upgrade', [SubscriptionController::class, 'upgrade'])->name('subscription.upgrade');
    Route::post('/subscription/cancel-plan', [SubscriptionController::class, 'cancel'])->name('subscription.cancel-plan');
    Route::post('/subscription/resume', [SubscriptionController::class, 'resume'])->name('subscription.resume');

    // Admin Routes (only role=admin)
    Route::middleware(['admin'])->group(function () {
        Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/admin/properties/approved', [AdminController::class, 'approved'])->name('admin.properties.approved');
        Route::get('/admin/properties/rejected', [AdminController::class, 'rejected'])->name('admin.properties.rejected');
        Route::get('/admin/property/{id}', [AdminController::class, 'show'])->name('admin.property.show');
        
        Route::post('/admin/property/{id}/approve', [AdminController::class, 'approveProperty'])->name('admin.property.approve');
        Route::post('/admin/property/{id}/reject', [AdminController::class, 'rejectProperty'])->name('admin.property.reject');
        
        Route::get('/admin/dealers', [AdminController::class, 'dealers'])->name('admin.dealers');
        Route::get('/admin/dealer/{id}', [AdminController::class, 'showDealer'])->name('admin.dealer.show');
        Route::post('/admin/dealer/{id}/verify', [AdminController::class, 'verifyDealer'])->name('admin.dealer.verify');
        Route::post('/admin/dealer/{id}/reject', [AdminController::class, 'rejectDealer'])->name('admin.dealer.reject');
    });
});

// Stripe Webhook (no auth required, but signature verification required)
Route::post('/webhook/stripe', [WebhookController::class, 'handleStripeWebhook'])->name('webhook.stripe');

Route::get('/fix-storage', function () {
    $privatePublicPath = storage_path('app/private/public');
    $publicPath = storage_path('app/public');
    
    // Move folders if they exist
    if (is_dir($privatePublicPath)) {
        $items = scandir($privatePublicPath);
        foreach ($items as $item) {
            if ($item == '.' || $item == '..') continue;
            
            $src = $privatePublicPath . '/' . $item;
            $dst = $publicPath . '/' . $item;
            
            // If destination exists, we might need to merge, but realistically just moving works
            if (!file_exists($dst)) {
                rename($src, $dst);
            } else {
                // If destination folder already exists, move files inside
                // Simplistic merge
                if (is_dir($src)) {
                    $subitems = scandir($src);
                    foreach($subitems as $sub) {
                        if ($sub == '.' || $sub == '..') continue;
                        if (!file_exists($dst . '/' . $sub)) {
                            rename($src . '/' . $sub, $dst . '/' . $sub);
                        }
                    }
                }
            }
        }
    }
    
    // Fix DB
    $removePrefix = function($path) {
        if ($path && str_starts_with($path, 'public/')) {
            return substr($path, 7);
        }
        return $path;
    };
    
    // Users
    $users = \App\Models\User::all();
    foreach($users as $user) {
        $user->cnic_front_image = $removePrefix($user->cnic_front_image);
        $user->cnic_back_image = $removePrefix($user->cnic_back_image);
        $user->live_photo = $removePrefix($user->live_photo);
        $user->selfie_with_cnic = $removePrefix($user->selfie_with_cnic);
        $user->save();
    }
    
    // Properties
    $props = \App\Models\Property::all();
    foreach($props as $prop) {
        $prop->electricity_bill = $removePrefix($prop->electricity_bill);
        $prop->ownership_proof = $removePrefix($prop->ownership_proof);
        $prop->save();
    }
    
    // Property Images
    $imgs = \App\Models\PropertyImage::all();
    foreach($imgs as $img) {
        $img->image_path = $removePrefix($img->image_path);
        $img->save();
    }
    
    return 'Fixed storage issues successfully!';
});
