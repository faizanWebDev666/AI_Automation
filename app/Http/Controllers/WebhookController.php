<?php

namespace App\Http\Controllers;

use App\Services\StripeSubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class WebhookController extends Controller
{
    private $stripeService;

    public function __construct(StripeSubscriptionService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    /**
     * Handle Stripe webhook
     */
    public function handleStripeWebhook(Request $request)
    {
        try {
            $payload = json_decode($request->getContent(), true);
            $sig = $request->header('Stripe-Signature');

            $event = $this->verifyWebhookSignature($payload, $sig);

            Log::info('Stripe webhook received', ['event_type' => $event->type]);

            // Process the webhook event
            $this->stripeService->handleWebhookEvent($event);

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            Log::error('Webhook error: ' . $e->getMessage());
            return response()->json(['error' => 'Webhook error'], 400);
        }
    }

    /**
     * Verify webhook signature
     */
    private function verifyWebhookSignature($payload, $sig)
    {
        try {
            return \Stripe\Webhook::constructEvent(
                json_encode($payload),
                $sig,
                config('services.stripe.webhook_secret')
            );
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            throw new Exception('Invalid webhook signature: ' . $e->getMessage());
        }
    }
}
