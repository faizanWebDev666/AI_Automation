<?php

namespace App\Console\Commands;

use App\Models\Plan;
use Illuminate\Console\Command;
use Stripe\StripeClient;
use Stripe\Exception\ApiErrorException;

class CreateStripePrices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stripe:create-prices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Stripe products and prices for subscription plans';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $stripe = new StripeClient(config('services.stripe.secret'));
            $plans = Plan::where('is_active', true)->get();

            foreach ($plans as $plan) {
                if ($plan->isFreePlan()) {
                    $this->info("Skipping free plan: {$plan->name}");
                    continue;
                }

                $this->info("Creating Stripe prices for: {$plan->name}");

                // Create product
                $product = $stripe->products->create([
                    'name' => $plan->name,
                    'description' => $plan->description,
                    'metadata' => [
                        'plan_id' => $plan->id,
                        'plan_slug' => $plan->slug,
                    ],
                ]);

                $this->info("Product created: {$product->id}");

                // Create monthly price
                if ($plan->monthly_price > 0) {
                    $monthlyPrice = $stripe->prices->create([
                        'unit_amount' => intval($plan->monthly_price * 100),
                        'currency' => 'pkr',
                        'recurring' => [
                            'interval' => 'month',
                            'interval_count' => 1,
                        ],
                        'product' => $product->id,
                        'metadata' => [
                            'plan_id' => $plan->id,
                            'billing_cycle' => 'monthly',
                        ],
                    ]);

                    $plan->update(['stripe_monthly_price_id' => $monthlyPrice->id]);
                    $this->info("Monthly price created: {$monthlyPrice->id}");
                }

                // Create yearly price
                if ($plan->yearly_price > 0) {
                    $yearlyPrice = $stripe->prices->create([
                        'unit_amount' => intval($plan->yearly_price * 100),
                        'currency' => 'pkr',
                        'recurring' => [
                            'interval' => 'year',
                            'interval_count' => 1,
                        ],
                        'product' => $product->id,
                        'metadata' => [
                            'plan_id' => $plan->id,
                            'billing_cycle' => 'yearly',
                        ],
                    ]);

                    $plan->update(['stripe_yearly_price_id' => $yearlyPrice->id]);
                    $this->info("Yearly price created: {$yearlyPrice->id}");
                }
            }

            $this->info("Stripe prices created successfully!");
        } catch (ApiErrorException $e) {
            $this->error('Stripe error: ' . $e->getMessage());
            return 1;
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }
    }
}
