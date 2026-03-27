<?php

namespace App\Http\Controllers;

use App\Models\User;
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
            'email'    => 'required|email|unique:users,email',
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
            return redirect($user->role === 'dealer' ? '/dealer-dashboard' : '/chat')->with('message', 'Email already verified.');
        }

        $user->markEmailAsVerified();

        // Redirect to dealer dashboard if user is a dealer, else to chat
        $redirectTo = $user->role === 'dealer' ? '/dealer-dashboard' : '/chat';
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
            return '/chat';
        }

        $user = Auth::user();
        
        if ($user->role === 'dealer') {
            return '/dealer-dashboard';
        }

        return '/chat';
    }
}
