<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Free Plan',
                'slug' => 'free',
                'description' => 'Perfect for getting started',
                'monthly_price' => 0,
                'yearly_price' => 0,
                'stripe_monthly_price_id' => null,
                'stripe_yearly_price_id' => null,
                'listings_per_month' => 3,
                'highlighted_listings' => false,
                'multiple_images_videos' => false,
                'support_level' => 'email',
                'basic_analytics' => false,
                'advanced_analytics' => false,
                'featured_listings_per_month' => 0,
                'virtual_tours' => false,
                'agency_profile' => false,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Silver Plan',
                'slug' => 'silver',
                'description' => 'Great for growing businesses',
                'monthly_price' => 5500,
                'yearly_price' => 52800, // 5500 * 12 * 0.8
                'stripe_monthly_price_id' => env('STRIPE_SILVER_MONTHLY_PRICE_ID'),
                'stripe_yearly_price_id' => env('STRIPE_SILVER_YEARLY_PRICE_ID'),
                'listings_per_month' => 15,
                'highlighted_listings' => true,
                'multiple_images_videos' => true,
                'support_level' => 'chat',
                'basic_analytics' => true,
                'advanced_analytics' => false,
                'featured_listings_per_month' => 1,
                'virtual_tours' => false,
                'agency_profile' => false,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Gold Plan',
                'slug' => 'gold',
                'description' => 'For professional agencies',
                'monthly_price' => 8500,
                'yearly_price' => 81600, // 8500 * 12 * 0.8
                'stripe_monthly_price_id' => env('STRIPE_GOLD_MONTHLY_PRICE_ID'),
                'stripe_yearly_price_id' => env('STRIPE_GOLD_YEARLY_PRICE_ID'),
                'listings_per_month' => null, // unlimited
                'highlighted_listings' => true,
                'multiple_images_videos' => true,
                'support_level' => 'priority',
                'basic_analytics' => true,
                'advanced_analytics' => true,
                'featured_listings_per_month' => 5,
                'virtual_tours' => true,
                'agency_profile' => true,
                'is_active' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::updateOrCreate(['slug' => $plan['slug']], $plan);
        }

        $this->command->info('Plans seeded successfully!');
    }
}
