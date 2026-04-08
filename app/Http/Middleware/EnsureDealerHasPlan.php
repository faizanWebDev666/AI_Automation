<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureDealerHasPlan
{
    /**
     * Ensure the authenticated dealer has selected a plan.
     * If no plan is selected, redirect to the plan selection page.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Only applies to dealers
        if ($user && $user->role === 'dealer') {
            // Check if dealer has any active subscription
            if (!$user->hasActiveSubscription()) {
                return redirect()->route('subscription.plans')
                    ->with('info', 'Please select a plan to continue. You can skip to start with the Free plan.');
            }
        }

        return $next($request);
    }
}
