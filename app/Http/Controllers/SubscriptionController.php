<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Subscription;
use App\Services\StripeSubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    private $stripeService;

    public function __construct(StripeSubscriptionService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    /**
     * Show all available plans
     */
    public function showPlans()
    {
        $plans = Plan::active()->ordered()->get();
        $user = Auth::user();
        $userSubscription = $user->getActiveSubscription();
        $currentPlan = $user->getCurrentPlan();
        $isNewDealer = $user->role === 'dealer' && !$user->hasActiveSubscription();

        return view('subscription.plans', compact('plans', 'userSubscription', 'currentPlan', 'isNewDealer'));
    }

    /**
     * Skip plan selection — auto-assign Free plan
     */
    public function skipToFreePlan()
    {
        $user = Auth::user();
        $freePlan = Plan::where('slug', 'free')->first();

        if (!$freePlan) {
            return redirect()->route('subscription.plans')->with('error', 'Free plan not available. Please select a plan.');
        }

        // Check if user already has a subscription
        if ($user->hasActiveSubscription()) {
            $redirect = $user->email_verified_at ? route('dealer.dashboard') : '/email-verify';
            return redirect($redirect)->with('info', 'You already have an active plan.');
        }

        // Create free plan subscription
        $user->subscriptions()->create([
            'plan_id' => $freePlan->id,
            'status' => 'active',
            'billing_cycle' => 'monthly',
            'starts_at' => now(),
            'month_reset_at' => now()->addMonth(),
        ]);

        $user->update(['subscription_status' => 'active']);

        // Redirect to email verification if not verified, otherwise dashboard
        $redirect = $user->email_verified_at ? route('dealer.dashboard') : '/email-verify';
        return redirect($redirect)->with('success', 'Free plan activated! You can list up to 3 properties.');
    }

  
    public function dashboard()
    {
        $user = Auth::user();
        $subscription = $user->getActiveSubscription();
        $transactions = [];
        $remainingListings = 0;

        if ($subscription) {
            $transactions = $subscription->transactions()->latest()->take(10)->get();
            $remainingListings = $subscription->getRemainingListings();
        }

        return view('subscription.dashboard', compact('subscription', 'transactions', 'remainingListings'));
    }

    /**
     * Initiate subscription checkout
     */
    public function checkout(Request $request)
    {
        try {
            $request->validate([
                'plan_id' => 'required|exists:plans,id',
                'billing_cycle' => 'required|in:monthly,yearly',
            ]);

            $plan = Plan::findOrFail($request->plan_id);
            $user = Auth::user();

            if ($plan->isFreePlan()) {
                return $this->subscribeToFreePlan($plan);
            }

            // Check if Stripe price IDs are configured
            $priceId = $request->billing_cycle === 'yearly'
                ? $plan->stripe_yearly_price_id
                : $plan->stripe_monthly_price_id;

            if (empty($priceId)) {
                // Stripe not configured — activate plan directly (dev/testing mode)
                return $this->activatePlanDirectly($user, $plan, $request->billing_cycle);
            }

            $session = $this->stripeService->getCheckoutSession($user, $plan, $request->billing_cycle);

            return redirect()->away($session->url);
        } catch (\Exception $e) {
            return redirect()->route('subscription.plans')->with('error', 'Checkout failed: ' . $e->getMessage());
        }
    }

    /**
     * Activate a plan directly without Stripe (for dev/testing or manual activation)
     */
    private function activatePlanDirectly($user, Plan $plan, string $billingCycle)
    {
        // Cancel any existing active subscriptions
        $user->subscriptions()->where('status', 'active')->update([
            'status' => 'canceled',
            'canceled_at' => now(),
        ]);

        // Create new subscription
        $user->subscriptions()->create([
            'plan_id' => $plan->id,
            'status' => 'active',
            'billing_cycle' => $billingCycle,
            'starts_at' => now(),
            'month_reset_at' => now()->addMonth(),
        ]);

        $user->update(['subscription_status' => 'active']);

        $redirect = $user->email_verified_at ? route('dealer.dashboard') : '/email-verify';
        return redirect($redirect)->with('success', $plan->name . ' activated successfully! Enjoy your upgraded features.');
    }

    /**
     * Handle successful payment
     */
    public function success(Request $request)
    {
        try {
            if (!$request->session_id) {
                throw new \Exception('Invalid session');
            }

            $session = $this->stripeService->retrieveCheckoutSession($request->session_id);

            if ($session->subscription) {
                $subscription = Subscription::where('stripe_subscription_id', $session->subscription)->first();
                if ($subscription) {
                    return redirect()->route('subscription.dashboard')->with('success', 'Subscription activated successfully!');
                }
            }

            return redirect()->route('subscription.plans')->with('error', 'Subscription could not be verified');
        } catch (\Exception $e) {
            return redirect()->route('subscription.plans')->with('error', 'Payment processing failed: ' . $e->getMessage());
        }
    }

    /**
     * Handle canceled checkout
     */
    public function cancelCheckout()
    {
        return redirect()->route('subscription.plans')->with('info', 'Checkout was canceled');
    }

    /**
     * Upgrade plan
     */
    public function upgrade(Request $request)
    {
        try {
            $request->validate([
                'plan_id' => 'required|exists:plans,id',
                'billing_cycle' => 'required|in:monthly,yearly',
            ]);

            $newPlan = Plan::findOrFail($request->plan_id);
            $user = Auth::user();
            $currentSubscription = $user->getActiveSubscription();

            if (!$currentSubscription) {
                throw new \Exception('No active subscription found');
            }

            if ($newPlan->isFreePlan()) {
                throw new \Exception('Cannot upgrade to free plan');
            }

            $this->stripeService->updateSubscription($currentSubscription, $newPlan, $request->billing_cycle);

            return redirect()->route('subscription.dashboard')->with('success', 'Plan upgraded successfully!');
        } catch (\Exception $e) {
            return redirect()->route('subscription.dashboard')->with('error', 'Upgrade failed: ' . $e->getMessage());
        }
    }

    /**
     * Cancel subscription
     */
    public function cancel(Request $request)
    {
        try {
            $user = Auth::user();
            $subscription = $user->getActiveSubscription();

            if (!$subscription) {
                throw new \Exception('No active subscription found');
            }

            $immediate = $request->boolean('immediate', false);
            $this->stripeService->cancelSubscription($subscription, $immediate);

            return redirect()->route('subscription.dashboard')->with('success', 'Subscription canceled successfully');
        } catch (\Exception $e) {
            return redirect()->route('subscription.dashboard')->with('error', 'Cancellation failed: ' . $e->getMessage());
        }
    }

    /**
     * Resume subscription
     */
    public function resume(Request $request)
    {
        try {
            $user = Auth::user();
            $subscription = $user->subscriptions()
                ->where('status', 'canceled')
                ->latest()
                ->first();

            if (!$subscription) {
                throw new \Exception('No canceled subscription found');
            }

            $this->stripeService->resumeSubscription($subscription);

            return redirect()->route('subscription.dashboard')->with('success', 'Subscription resumed successfully');
        } catch (\Exception $e) {
            return redirect()->route('subscription.dashboard')->with('error', 'Resume failed: ' . $e->getMessage());
        }
    }

    /**
     * Subscribe to free plan
     */
    private function subscribeToFreePlan(Plan $plan)
    {
        $user = Auth::user();

        // Check if user already has this plan
        if ($user->subscriptions()->where('plan_id', $plan->id)->where('status', 'active')->exists()) {
            return redirect()->route('subscription.dashboard')->with('info', 'You already have this plan');
        }

        // Cancel active subscriptions
        $user->subscriptions()->where('status', 'active')->update(['status' => 'canceled', 'canceled_at' => now()]);

        // Create free plan subscription
        $user->subscriptions()->create([
            'plan_id' => $plan->id,
            'status' => 'active',
            'billing_cycle' => 'monthly',
            'starts_at' => now(),
            'month_reset_at' => now()->addMonth(),
        ]);

        $user->update(['subscription_status' => 'active']);

        // Redirect to email verification if not verified, otherwise dashboard
        if (!$user->email_verified_at) {
            return redirect('/email-verify')->with('success', 'Free plan activated! Now please verify your email.');
        }

        return redirect()->route('subscription.dashboard')->with('success', 'Free plan activated successfully!');
    }
}
