<?php

namespace App\Services;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Stripe\StripeClient;
use Stripe\Exception\ApiErrorException;

class StripeSubscriptionService
{
    private $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(config('services.stripe.secret'));
    }

    /**
     * Create or get Stripe customer
     */
    public function getOrCreateCustomer(User $user)
    {
        try {
            if ($user->stripe_customer_id) {
                return $this->stripe->customers->retrieve($user->stripe_customer_id);
            }

            $customer = $this->stripe->customers->create([
                'email' => $user->email,
                'name' => $user->name,
                'metadata' => [
                    'user_id' => $user->id,
                ],
            ]);

            $user->update(['stripe_customer_id' => $customer->id]);
            return $customer;
        } catch (ApiErrorException $e) {
            throw new \Exception('Failed to create Stripe customer: ' . $e->getMessage());
        }
    }

    /**
     * Create a subscription for a user
     */
    public function createSubscription(User $user, Plan $plan, $billingCycle = 'monthly', $trialDays = null)
    {
        try {
            $customer = $this->getOrCreateCustomer($user);
            $priceId = $billingCycle === 'yearly' ? $plan->stripe_yearly_price_id : $plan->stripe_monthly_price_id;

            if (!$priceId) {
                throw new \Exception("Stripe price ID not configured for plan: {$plan->slug}");
            }

            $params = [
                'customer' => $customer->id,
                'items' => [
                    ['price' => $priceId],
                ],
                'payment_behavior' => 'default_incomplete',
                'expand' => ['latest_invoice.payment_intent'],
            ];

            if ($trialDays) {
                $params['trial_period_days'] = $trialDays;
            }

            $stripeSubscription = $this->stripe->subscriptions->create($params);

            // Create subscription record
            $subscription = $user->subscriptions()->create([
                'plan_id' => $plan->id,
                'stripe_subscription_id' => $stripeSubscription->id,
                'stripe_customer_id' => $customer->id,
                'billing_cycle' => $billingCycle,
                'status' => 'active',
                'starts_at' => now(),
                'trial_ends_at' => $trialDays ? now()->addDays($trialDays) : null,
                'month_reset_at' => now()->addMonth(),
            ]);

            $user->update(['subscription_status' => 'active']);

            return $subscription;
        } catch (ApiErrorException $e) {
            throw new \Exception('Failed to create subscription: ' . $e->getMessage());
        }
    }

    /**
     * Update subscription plan
     */
    public function updateSubscription(Subscription $subscription, Plan $newPlan, $billingCycle = null)
    {
        try {
            if (!$subscription->stripe_subscription_id) {
                throw new \Exception('Subscription has no Stripe ID');
            }

            $billingCycle = $billingCycle ?? $subscription->billing_cycle;
            $priceId = $billingCycle === 'yearly' ? $newPlan->stripe_yearly_price_id : $newPlan->stripe_monthly_price_id;

            if (!$priceId) {
                throw new \Exception("Stripe price ID not configured for plan: {$newPlan->slug}");
            }

            // Get current subscription
            $stripeSubscription = $this->stripe->subscriptions->retrieve($subscription->stripe_subscription_id);

            // Update the subscription
            $this->stripe->subscriptions->update(
                $subscription->stripe_subscription_id,
                [
                    'items' => [
                        [
                            'id' => $stripeSubscription->items->data[0]->id,
                            'price' => $priceId,
                        ],
                    ],
                    'proration_behavior' => 'create_prorations',
                ]
            );

            // Update database record
            $subscription->update([
                'plan_id' => $newPlan->id,
                'billing_cycle' => $billingCycle,
            ]);

            return $subscription;
        } catch (ApiErrorException $e) {
            throw new \Exception('Failed to update subscription: ' . $e->getMessage());
        }
    }

    /**
     * Cancel subscription
     */
    public function cancelSubscription(Subscription $subscription, $immediate = false)
    {
        try {
            if (!$subscription->stripe_subscription_id) {
                throw new \Exception('Subscription has no Stripe ID');
            }

            if ($immediate) {
                $this->stripe->subscriptions->cancel($subscription->stripe_subscription_id);
            } else {
                $this->stripe->subscriptions->update(
                    $subscription->stripe_subscription_id,
                    ['cancel_at_period_end' => true]
                );
            }

            $subscription->update([
                'status' => 'canceled',
                'canceled_at' => now(),
            ]);

            return $subscription;
        } catch (ApiErrorException $e) {
            throw new \Exception('Failed to cancel subscription: ' . $e->getMessage());
        }
    }

    /**
     * Resume subscription
     */
    public function resumeSubscription(Subscription $subscription)
    {
        try {
            if (!$subscription->stripe_subscription_id) {
                throw new \Exception('Subscription has no Stripe ID');
            }

            $this->stripe->subscriptions->update(
                $subscription->stripe_subscription_id,
                ['cancel_at' => null]
            );

            $subscription->update([
                'status' => 'active',
            ]);

            return $subscription;
        } catch (ApiErrorException $e) {
            throw new \Exception('Failed to resume subscription: ' . $e->getMessage());
        }
    }

    /**
     * Get checkout session
     */
    public function getCheckoutSession(User $user, Plan $plan, $billingCycle = 'monthly')
    {
        try {
            $priceId = $billingCycle === 'yearly' ? $plan->stripe_yearly_price_id : $plan->stripe_monthly_price_id;

            if (!$priceId) {
                throw new \Exception("Stripe price ID not configured for plan: {$plan->slug}");
            }

            $customer = $this->getOrCreateCustomer($user);

            $session = $this->stripe->checkout->sessions->create([
                'payment_method_types' => ['card'],
                'customer' => $customer->id,
                'line_items' => [
                    [
                        'price' => $priceId,
                        'quantity' => 1,
                    ],
                ],
                'mode' => 'subscription',
                'success_url' => route('subscription.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('subscription.cancel'),
            ]);

            return $session;
        } catch (ApiErrorException $e) {
            throw new \Exception('Failed to create checkout session: ' . $e->getMessage());
        }
    }

    /**
     * Retrieve checkout session
     */
    public function retrieveCheckoutSession($sessionId)
    {
        try {
            return $this->stripe->checkout->sessions->retrieve($sessionId);
        } catch (ApiErrorException $e) {
            throw new \Exception('Failed to retrieve checkout session: ' . $e->getMessage());
        }
    }

    /**
     * Handle Stripe webhook events
     */
    public function handleWebhookEvent($event)
    {
        switch ($event->type) {
            case 'customer.subscription.updated':
                $this->handleSubscriptionUpdated($event->data->object);
                break;
            case 'customer.subscription.deleted':
                $this->handleSubscriptionDeleted($event->data->object);
                break;
            case 'invoice.payment_succeeded':
                $this->handlePaymentSucceeded($event->data->object);
                break;
            case 'invoice.payment_failed':
                $this->handlePaymentFailed($event->data->object);
                break;
        }
    }

    private function handleSubscriptionUpdated($stripeSubscription)
    {
        $subscription = Subscription::where('stripe_subscription_id', $stripeSubscription->id)->first();
        if ($subscription) {
            $subscription->update([
                'status' => $stripeSubscription->status,
            ]);
        }
    }

    private function handleSubscriptionDeleted($stripeSubscription)
    {
        $subscription = Subscription::where('stripe_subscription_id', $stripeSubscription->id)->first();
        if ($subscription) {
            $subscription->update([
                'status' => 'canceled',
                'canceled_at' => now(),
            ]);
        }
    }

    private function handlePaymentSucceeded($invoice)
    {
        $subscription = Subscription::where('stripe_subscription_id', $invoice->subscription)->first();
        if ($subscription) {
            $transaction = $subscription->transactions()->updateOrCreate(
                ['stripe_invoice_id' => $invoice->id],
                [
                    'amount' => $invoice->total / 100,
                    'status' => 'paid',
                    'paid_at' => now(),
                    'description' => $invoice->description,
                    'stripe_response' => $invoice,
                ]
            );
        }
    }

    private function handlePaymentFailed($invoice)
    {
        $subscription = Subscription::where('stripe_subscription_id', $invoice->subscription)->first();
        if ($subscription) {
            $transaction = $subscription->transactions()->updateOrCreate(
                ['stripe_invoice_id' => $invoice->id],
                [
                    'amount' => $invoice->total / 100,
                    'status' => 'failed',
                    'description' => $invoice->description,
                    'stripe_response' => $invoice,
                ]
            );
        }
    }
}
