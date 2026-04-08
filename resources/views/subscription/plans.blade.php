@extends('layouts.dealer')

@section('content')
<div style="max-width: 1100px; margin: 0 auto;">

    {{-- Flash Messages --}}
    @if(session('success'))
    <div style="background: #dcfce7; border: 1px solid #86efac; color: #166534; padding: 14px 18px; border-radius: 12px; margin-bottom: 20px; font-size: 13px; display: flex; align-items: center; gap: 10px;">
        <span>✅</span> {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div style="background: #fee2e2; border: 1px solid #fecaca; color: #991b1b; padding: 14px 18px; border-radius: 12px; margin-bottom: 20px; font-size: 13px; display: flex; align-items: center; gap: 10px;">
        <span>❌</span> {{ session('error') }}
    </div>
    @endif
    @if(session('info') || session('message'))
    <div style="background: #dbeafe; border: 1px solid #93c5fd; color: #1e40af; padding: 14px 18px; border-radius: 12px; margin-bottom: 20px; font-size: 13px; display: flex; align-items: center; gap: 10px;">
        <span>ℹ️</span> {{ session('info') ?? session('message') }}
    </div>
    @endif

    {{-- New Dealer Welcome Banner --}}
    @if(isset($isNewDealer) && $isNewDealer)
    <div style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #ec4899 100%); border-radius: 20px; padding: 32px 28px; margin-bottom: 28px; position: relative; overflow: hidden;">
        <div style="position: absolute; top: -40px; right: -40px; width: 180px; height: 180px; background: rgba(255,255,255,0.08); border-radius: 50%;"></div>
        <div style="position: relative; z-index: 1;">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                <span style="font-size: 28px;">🎉</span>
                <h1 style="font-size: 22px; font-weight: 800; color: white; margin: 0;">Welcome to AI Real Estate!</h1>
            </div>
            <p style="color: rgba(255,255,255,0.9); font-size: 14px; margin-bottom: 20px; max-width: 500px; line-height: 1.5;">
                Choose a plan to get started. You can always upgrade later.
            </p>
            <form action="{{ route('subscription.skip') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" style="background: rgba(255,255,255,0.18); backdrop-filter: blur(10px); color: white; border: 1px solid rgba(255,255,255,0.3); padding: 10px 22px; border-radius: 10px; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.3s; font-family: 'Inter', sans-serif;" onmouseover="this.style.background='rgba(255,255,255,0.28)'" onmouseout="this.style.background='rgba(255,255,255,0.18)'">
                    Skip — Use Free Plan →
                </button>
            </form>
        </div>
    </div>
    @endif

    {{-- Header --}}
    @if(!isset($isNewDealer) || !$isNewDealer)
    <div style="margin-bottom: 24px;">
        <h1 style="font-size: 26px; font-weight: 800; color: var(--text-primary); margin-bottom: 6px;">Upgrade Your Plan</h1>
        <p style="color: var(--text-secondary); font-size: 14px;">Choose the perfect plan for your real estate business</p>
    </div>
    @endif

    {{-- Billing Toggle --}}
    <div style="display: flex; align-items: center; justify-content: center; margin-bottom: 32px; gap: 12px; background: white; padding: 14px 24px; border-radius: 14px; box-shadow: 0 1px 3px rgba(0,0,0,0.06); width: fit-content; margin-left: auto; margin-right: auto; border: 1px solid var(--border-color);">
        <span id="billingLabel" style="font-weight: 600; color: var(--text-primary); font-size: 13px;">Monthly</span>
        <button id="billingToggle" style="position: relative; display: inline-flex; height: 28px; width: 52px; align-items: center; border-radius: 999px; transition: background-color 0.3s; background: linear-gradient(135deg, #6366f1, #8b5cf6); border: none; cursor: pointer; padding: 0;">
            <span id="toggleSlider" style="display: inline-block; height: 20px; width: 20px; transform: translateX(3px); border-radius: 50%; background: white; box-shadow: 0 2px 6px rgba(0,0,0,0.2); transition: transform 0.3s;"></span>
        </button>
        <div style="display: flex; align-items: center; gap: 6px;">
            <span id="billingLabel2" style="font-weight: 500; color: var(--text-secondary); font-size: 13px;">Yearly</span>
            <span style="display: inline-block; background: linear-gradient(135deg, #10b981, #059669); color: white; font-size: 10px; font-weight: 700; padding: 3px 8px; border-radius: 999px;">SAVE 20%</span>
        </div>
    </div>

    {{-- Plans Grid --}}
    <div class="plans-grid" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; align-items: start;">
    <style>
        .plan-card { transition: all 0.3s ease; }
        .plan-card:hover { transform: translateY(-4px); box-shadow: 0 12px 40px rgba(0,0,0,0.1) !important; }
        .plan-check { width: 16px; height: 16px; flex-shrink: 0; margin-top: 2px; }
        @media (max-width: 960px) {
            .plans-grid { grid-template-columns: 1fr !important; max-width: 400px; margin-left: auto !important; margin-right: auto !important; }
        }
    </style>
        @foreach($plans as $plan)
            @php
                $isCurrent = $currentPlan && $currentPlan->id === $plan->id;
                $isSilver = $plan->slug === 'silver';
                $isGold = $plan->slug === 'gold';
                $isFree = $plan->slug === 'free';
                $planEmoji = $isFree ? '✅' : ($isSilver ? '💎' : '🏆');
                $checkColor = $isSilver ? '#6366f1' : ($isGold ? '#f59e0b' : '#10b981');
            @endphp
            <div class="plan-card" style="background: white; border-radius: 24px; {{ $isSilver ? 'border: 2px solid #6366f1; box-shadow: 0 8px 32px rgba(99,102,241,0.12);' : 'border: 1px solid #e2e8f0; box-shadow: 0 1px 4px rgba(0,0,0,0.05);' }} overflow: hidden; display: flex; flex-direction: column; position: relative;">

                {{-- Popular / Current Badge --}}
                @if($isSilver && !$isCurrent)
                <div style="background: linear-gradient(135deg, #6366f1, #ec4899); padding: 8px; text-align: center;">
                    <span style="color: white; font-weight: 700; font-size: 10px; letter-spacing: 1.5px; text-transform: uppercase;">⭐ Most Popular</span>
                </div>
                @endif
                @if($isCurrent)
                <div style="background: linear-gradient(135deg, #10b981, #059669); padding: 8px; text-align: center;">
                    <span style="color: white; font-weight: 700; font-size: 10px; letter-spacing: 1.5px; text-transform: uppercase;">✅ Your Current Plan</span>
                </div>
                @endif

                <div style="padding: 24px; display: flex; flex-direction: column; flex: 1;">

                    {{-- Plan Name --}}
                    <div style="margin-bottom: 16px;">
                        <div style="display: flex; align-items: center; gap: 6px; margin-bottom: 4px;">
                            <h3 style="font-size: 16px; font-weight: 800; color: var(--text-primary); margin: 0;">{{ $plan->name }}</h3>
                            <span style="font-size: 16px;">{{ $planEmoji }}</span>
                        </div>
                        <div class="pricing-display" data-monthly-price="{{ number_format($plan->monthly_price, 0) }}" data-yearly-price="{{ number_format($plan->yearly_price, 0) }}" style="display: flex; align-items: baseline; gap: 4px;">
                            @if($plan->monthly_price > 0)
                                <span style="font-size: 28px; font-weight: 900; color: var(--text-primary);">₨{{ number_format($plan->monthly_price, 0) }}</span>
                            @else
                                <span style="font-size: 28px; font-weight: 900; background: linear-gradient(135deg, #10b981, #059669); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">FREE</span>
                            @endif
                            <span style="color: var(--text-tertiary); font-size: 12px; font-weight: 500;" class="billing-period">/month</span>
                        </div>
                    </div>

                    {{-- Action Button (BEFORE features) --}}
                    <form action="{{ route('subscription.checkout') }}" method="POST" style="margin-bottom: 20px;">
                        @csrf
                        <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                        <input type="hidden" name="billing_cycle" class="billing-cycle-input" value="monthly">

                        @if($isCurrent)
                            <button type="button" disabled style="width: 100%; padding: 11px; border-radius: 12px; font-weight: 700; font-size: 13px; background: var(--bg-secondary); color: var(--text-tertiary); cursor: default; border: none; font-family: 'Inter', sans-serif; display: block; text-align: center;">
                                ✅ Current Plan
                            </button>
                        @elseif($isFree)
                            <button type="submit" style="width: 100%; padding: 11px; border-radius: 12px; font-weight: 700; font-size: 13px; background: white; color: var(--text-primary); border: 1.5px solid #e2e8f0; cursor: pointer; transition: all 0.2s; font-family: 'Inter', sans-serif; display: block; text-align: center;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='white'">
                                Get Started Free
                            </button>
                        @elseif($isSilver)
                            <button type="submit" style="width: 100%; padding: 11px; border-radius: 12px; font-weight: 700; font-size: 13px; background: linear-gradient(135deg, #6366f1, #8b5cf6, #ec4899); color: white; border: none; cursor: pointer; transition: all 0.2s; font-family: 'Inter', sans-serif; box-shadow: 0 4px 12px rgba(99,102,241,0.25); display: block; text-align: center;" onmouseover="this.style.transform='translateY(-1px)';this.style.boxShadow='0 6px 20px rgba(99,102,241,0.35)'" onmouseout="this.style.transform='none';this.style.boxShadow='0 4px 12px rgba(99,102,241,0.25)'">
                                💎 Upgrade to Silver
                            </button>
                        @else
                            <button type="submit" style="width: 100%; padding: 11px; border-radius: 12px; font-weight: 700; font-size: 13px; background: white; color: #6366f1; border: 1.5px solid #6366f1; cursor: pointer; transition: all 0.2s; font-family: 'Inter', sans-serif; display: block; text-align: center;" onmouseover="this.style.background='#eef2ff'" onmouseout="this.style.background='white'">
                                🏆 Upgrade to Gold
                            </button>
                        @endif
                    </form>

                    {{-- Divider --}}
                    <div style="height: 1px; background: var(--border-color); margin-bottom: 16px;"></div>

                    {{-- Features --}}
                    <ul style="list-style: none; padding: 0; margin: 0; flex: 1;">
                        {{-- Listings --}}
                        <li style="display: flex; gap: 10px; color: var(--text-primary); margin-bottom: 10px; font-weight: 600; font-size: 13px;">
                            <svg class="plan-check" fill="{{ $checkColor }}" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg>
                            <span>
                                @if($plan->listings_per_month === null)
                                    <strong>Unlimited</strong> listings
                                @else
                                    Up to <strong>{{ $plan->listings_per_month }}</strong> listings/month
                                @endif
                            </span>
                        </li>

                        @if($plan->highlighted_listings)
                        <li style="display: flex; gap: 10px; color: var(--text-secondary); margin-bottom: 10px; font-size: 13px;">
                            <svg class="plan-check" fill="{{ $checkColor }}" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg>
                            <span>Highlighted listings</span>
                        </li>
                        @endif

                        @if($plan->multiple_images_videos)
                        <li style="display: flex; gap: 10px; color: var(--text-secondary); margin-bottom: 10px; font-size: 13px;">
                            <svg class="plan-check" fill="{{ $checkColor }}" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg>
                            <span>Multiple images & videos</span>
                        </li>
                        @endif

                        @if($plan->basic_analytics)
                        <li style="display: flex; gap: 10px; color: var(--text-secondary); margin-bottom: 10px; font-size: 13px;">
                            <svg class="plan-check" fill="{{ $checkColor }}" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg>
                            <span>Basic analytics</span>
                        </li>
                        @endif

                        @if($plan->advanced_analytics)
                        <li style="display: flex; gap: 10px; color: var(--text-secondary); margin-bottom: 10px; font-size: 13px;">
                            <svg class="plan-check" fill="{{ $checkColor }}" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg>
                            <span>Advanced analytics</span>
                        </li>
                        @endif

                        @if($plan->featured_listings_per_month > 0)
                        <li style="display: flex; gap: 10px; color: var(--text-secondary); margin-bottom: 10px; font-size: 13px;">
                            <svg class="plan-check" fill="{{ $checkColor }}" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg>
                            <span>{{ $plan->featured_listings_per_month }} featured listing{{ $plan->featured_listings_per_month > 1 ? 's' : '' }}/month</span>
                        </li>
                        @endif

                        @if($plan->virtual_tours)
                        <li style="display: flex; gap: 10px; color: var(--text-secondary); margin-bottom: 10px; font-size: 13px;">
                            <svg class="plan-check" fill="{{ $checkColor }}" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg>
                            <span>Virtual tours / 3D walkthroughs</span>
                        </li>
                        @endif

                        @if($plan->agency_profile)
                        <li style="display: flex; gap: 10px; color: var(--text-secondary); margin-bottom: 10px; font-size: 13px;">
                            <svg class="plan-check" fill="{{ $checkColor }}" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg>
                            <span>Agency profile page</span>
                        </li>
                        @endif

                        <li style="display: flex; gap: 10px; color: var(--text-secondary); margin-bottom: 10px; font-size: 13px;">
                            <svg class="plan-check" fill="{{ $checkColor }}" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg>
                            <span>{{ $plan->support_level === 'priority' ? '24/7 Priority Support' : ($plan->support_level === 'chat' ? 'Email + Chat Support' : 'Email Support') }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Skip Button (bottom) --}}
    @if(isset($isNewDealer) && $isNewDealer)
    <div style="text-align: center; margin-top: 32px; padding: 20px; background: var(--bg-secondary); border-radius: 14px; border: 1px dashed var(--border-color);">
        <p style="color: var(--text-secondary); font-size: 13px; margin-bottom: 12px;">Not ready to decide? You can always upgrade later.</p>
        <form action="{{ route('subscription.skip') }}" method="POST" style="display: inline;">
            @csrf
            <button type="submit" style="background: transparent; color: #6366f1; border: 1px solid #6366f1; padding: 10px 24px; border-radius: 10px; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.3s; font-family: 'Inter', sans-serif;" onmouseover="this.style.background='rgba(99,102,241,0.05)'" onmouseout="this.style.background='transparent'">
                Skip — Start with Free Plan (3 listings/month)
            </button>
        </form>
    </div>
    @endif
</div>

<script>
    const billingToggle = document.getElementById('billingToggle');
    const toggleSlider = document.getElementById('toggleSlider');
    const billingLabel = document.getElementById('billingLabel');
    const billingLabel2 = document.getElementById('billingLabel2');
    const pricingDisplays = document.querySelectorAll('.pricing-display');
    const billingPeriods = document.querySelectorAll('.billing-period');
    const billingCycleInputs = document.querySelectorAll('.billing-cycle-input');
    let isYearly = false;

    billingToggle.addEventListener('click', function() {
        isYearly = !isYearly;

        toggleSlider.style.transform = isYearly ? 'translateX(28px)' : 'translateX(3px)';

        pricingDisplays.forEach(display => {
            const priceElement = display.querySelector('span:first-child');
            const monthlyPrice = display.dataset.monthlyPrice;
            const yearlyPrice = display.dataset.yearlyPrice;
            if (isYearly) {
                priceElement.textContent = (yearlyPrice && yearlyPrice !== '0') ? '₨' + yearlyPrice : 'FREE';
            } else {
                priceElement.textContent = (monthlyPrice && monthlyPrice !== '0') ? '₨' + monthlyPrice : 'FREE';
            }
        });

        billingPeriods.forEach(period => { period.textContent = isYearly ? '/year' : '/month'; });
        billingCycleInputs.forEach(input => { input.value = isYearly ? 'yearly' : 'monthly'; });

        billingLabel.style.opacity = isYearly ? '0.5' : '1';
        billingLabel.style.fontWeight = isYearly ? '500' : '600';
        billingLabel2.style.opacity = isYearly ? '1' : '0.5';
        billingLabel2.style.fontWeight = isYearly ? '600' : '500';
    });
</script>
@endsection
