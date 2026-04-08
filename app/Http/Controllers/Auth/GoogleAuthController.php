<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use Exception;
use Illuminate\Support\Facades\Log;

class GoogleAuthController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google and handle login/registration.
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            $user = User::where('email', $googleUser->email)->first();

            if ($user) {
                // If user exists, update details and ensure email is verified
                $user->google_id = $googleUser->id;
                $user->avatar = $googleUser->avatar;
                
                if (!$user->hasVerifiedEmail()) {
                    $user->markEmailAsVerified();
                } else {
                    $user->save();
                }
            } else {
                // If user doesn't exist, create a new one
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'avatar' => $googleUser->avatar,
                    'password' => bcrypt(Str::random(24)),
                    'email_verified_at' => now(),
                    'role' => 'user',
                ]);
            }

            Auth::login($user, true);

            // Clear any previously stored "intended" URL that might point to /email-verify
            session()->forget('url.intended');

            return redirect($this->getDashboardRoute($user));

        } catch (Exception $e) {
            Log::error('Google Authentication Error: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Unable to login using Google. Please try again.');
        }
    }

    /**
     * Get the dashboard route based on user role.
     */
    private function getDashboardRoute(User $user): string
    {
        if ($user->role === 'dealer') {
            return '/dealer-dashboard';
        }

        return '/home';
    }
}
