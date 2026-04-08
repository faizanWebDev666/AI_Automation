<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Services\TwoFactorAuthService;

class AuthController extends Controller
{
    public function showRegister()
    {
        if (Auth::check()) return redirect($this->getDashboardRoute());
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:50',
            'email'    => [
                'required',
                'email',
                'unique:users,email',
                // Check if email is banned from verification
                function ($attribute, $value, $fail) {
                    $bannedUser = User::where('email', $value)
                        ->where('verification_banned', true)
                        ->first();
                    
                    if ($bannedUser) {
                        $fail("The email address $value is banned from registration due to failed verification attempts. Please contact support.");
                    }
                },
            ],
            'password' => 'required|string|min:6|confirmed',
            'role'     => 'required|in:user,dealer',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
        ]);

        // Send email verification link
        $user->sendEmailVerificationNotification();

        Auth::login($user);

        // Auto-assign Free plan to dealers on registration
        if ($request->role === 'dealer') {
            $freePlan = Plan::where('slug', 'free')->first();
            if ($freePlan) {
                $user->subscriptions()->create([
                    'plan_id' => $freePlan->id,
                    'status' => 'active',
                    'billing_cycle' => 'monthly',
                    'starts_at' => now(),
                    'month_reset_at' => now()->addMonth(),
                ]);
                $user->update(['subscription_status' => 'active']);
            }
        }

        return redirect('/email-verify')->with('message', 'Please verify your email address to continue.');
    }

    public function showEmailVerify()
    {
        if (!Auth::check()) return redirect('/login');
        
        $user = Auth::user();
        if ($user->email_verified_at) {
            return redirect($this->getDashboardRoute());
        }

        return view('auth.verify-email');
    }

    public function sendVerificationEmail(Request $request)
    {
        $user = Auth::user();
        
        if ($user->email_verified_at) {
            return redirect($this->getDashboardRoute());
        }

        $user->sendEmailVerificationNotification();

        return back()->with('message', 'Verification link sent to your email!');
    }

    public function verifyEmail(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);

        if (!hash_equals($hash, sha1($user->getEmailForVerification()))) {
            return redirect('/login')->withErrors(['email' => 'Invalid verification link.']);
        }

        if ($user->hasVerifiedEmail()) {
            $redirectTo = $user->role === 'admin' ? '/admin/dashboard' : ($user->role === 'dealer' ? '/dealer-dashboard' : '/home');
            return redirect($redirectTo)->with('message', 'Email already verified.');
        }

        $user->markEmailAsVerified();

        // Redirect to appropriate dashboard based on user role
        $redirectTo = $user->role === 'admin' ? '/admin/dashboard' : ($user->role === 'dealer' ? '/dealer-dashboard' : '/home');
        return redirect($redirectTo)->with('message', 'Email verified successfully!');
    }

    public function showLogin()
    {
        if (Auth::check()) return redirect($this->getDashboardRoute());
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            $user = Auth::user();

            if ($user->role === 'admin') {
                return redirect()->intended('/admin/dashboard');
            }

            if (!$user->email_verified_at) {
                Auth::logout();
                return redirect('/email-verify')->with('message', 'Please verify your email to login.');
            }

            return redirect()->intended($this->getDashboardRoute());
        }

        return back()->withErrors(['email' => 'Invalid email or password.'])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    /**
     * Get dashboard route based on user role
     */
    private function getDashboardRoute(): string
    {
        if (!Auth::check()) {
            return '/home';
        }

        $user = Auth::user();
        
        if ($user->role === 'admin') {
            return '/admin/dashboard';
        }

        if ($user->role === 'dealer') {
            return '/dealer-dashboard';
        }

        return '/home';
    }
}
