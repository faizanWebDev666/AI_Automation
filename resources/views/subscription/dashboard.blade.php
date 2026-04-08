@extends('layouts.dealer')

@section('content')
<div class="main-content">
    <div class="content-wrapper">
        <!-- Header -->
        <div style="margin-bottom: 40px;">
            <h1 style="font-size: 32px; font-weight: bold; color: #1e293b; margin-bottom: 8px;">Subscription Dashboard</h1>
            <p style="color: #64748b;">Manage your subscription and billing information</p>
        </div>

        @if($subscription)
            <!-- Current Subscription Card -->
            <div style="background: white; border-radius: 16px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-bottom: 32px; overflow: hidden;">
                <div style="background: linear-gradient(135deg, #6366f1 0%, #ec4899 100%); padding: 24px; color: white;">
                    <h2 style="font-size: 24px; font-weight: bold;">{{ $subscription->plan->name }}</h2>
                    <p style="color: #c7d2fe;">Active since {{ $subscription->starts_at->format('M d, Y') }}</p>
                </div>

                <div style="padding: 32px;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 32px; margin-bottom: 32px;">
                        <!-- Plan Details -->
                        <div>
                            <h3 style="font-size: 12px; font-weight: bold; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 16px;">Plan Details</h3>
                            <div style="display: flex; flex-direction: column; gap: 16px;">
                                <div>
                                    <span style="color: #64748b;">Billing Cycle:</span>
                                    <span style="font-weight: bold; color: #1e293b; margin-left: 8px;">{{ ucfirst($subscription->billing_cycle) }}</span>
                                </div>
                                <div>
                                    <span style="color: #64748b;">Monthly Price:</span>
                                    <span style="font-weight: bold; color: #1e293b; margin-left: 8px;">₨{{ number_format($subscription->plan->monthly_price, 0) }}</span>
                                </div>
                                <div>
                                    <span style="color: #64748b;">Status:</span>
                                    <span style="display: inline-block; margin-left: 8px; padding: 4px 12px; border-radius: 9999px; font-size: 12px; font-weight: bold; background: {{ $subscription->status === 'active' ? '#dcfce7' : '#fee2e2' }}; color: {{ $subscription->status === 'active' ? '#166534' : '#991b1b' }};">
                                        {{ ucfirst($subscription->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Listing Limits -->
                        <div>
                            <h3 style="font-size: 12px; font-weight: bold; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 16px;">Listing Limits</h3>
                            <div style="display: flex; flex-direction: column; gap: 16px;">
                                @if($subscription->plan->listings_per_month === null)
                                    <div>
                                        <span style="color: #64748b;">Listings Available:</span>
                                        <span style="font-weight: bold; color: #1e293b; margin-left: 8px;">Unlimited</span>
                                    </div>
                                @else
                                    <div>
                                        <span style="color: #64748b;">Monthly Limit:</span>
                                        <span style="font-weight: bold; color: #1e293b; margin-left: 8px;">{{ $subscription->plan->listings_per_month }}</span>
                                    </div>
                                    <div>
                                        <span style="color: #64748b;">Used This Month:</span>
                                        <span style="font-weight: bold; color: #1e293b; margin-left: 8px;">{{ $subscription->listed_this_month }}</span>
                                    </div>
                                    <div>
                                        <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                            <span style="color: #64748b;">Remaining:</span>
                                            <span style="font-weight: bold; color: #6366f1;">{{ $remainingListings }}</span>
                                        </div>
                                        <div style="width: 100%; background: #e2e8f0; border-radius: 9999px; height: 8px;">
                                            <div style="background: #6366f1; height: 8px; border-radius: 9999px; width: {{ ($subscription->listed_this_month / $subscription->plan->listings_per_month) * 100 }}%"></div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Features -->
                    <div style="border-top: 1px solid #e2e8f0; padding-top: 32px; margin-bottom: 32px;">
                        <h3 style="font-size: 12px; font-weight: bold; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 16px;">Features Included</h3>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                            @if($subscription->hasHighlightedListings())
                                <div style="display: flex; gap: 8px; align-items: center;">
                                    <span style="color: #10b981;">✓</span>
                                    <span style="color: #475569;">Highlighted listings</span>
                                </div>
                            @endif

                            @if($subscription->hasMultipleImagesVideos())
                                <div style="display: flex; gap: 8px; align-items: center;">
                                    <span style="color: #10b981;">✓</span>
                                    <span style="color: #475569;">Multiple images & videos</span>
                                </div>
                            @endif

                            @if($subscription->hasBasicAnalytics())
                                <div style="display: flex; gap: 8px; align-items: center;">
                                    <span style="color: #10b981;">✓</span>
                                    <span style="color: #475569;">Basic analytics</span>
                                </div>
                            @endif

                            @if($subscription->hasAdvancedAnalytics())
                                <div style="display: flex; gap: 8px; align-items: center;">
                                    <span style="color: #10b981;">✓</span>
                                    <span style="color: #475569;">Advanced analytics</span>
                                </div>
                            @endif

                            @if($subscription->hasVirtualTours())
                                <div style="display: flex; gap: 8px; align-items: center;">
                                    <span style="color: #10b981;">✓</span>
                                    <span style="color: #475569;">Virtual tours</span>
                                </div>
                            @endif

                            @if($subscription->hasAgencyProfile())
                                <div style="display: flex; gap: 8px; align-items: center;">
                                    <span style="color: #10b981;">✓</span>
                                    <span style="color: #475569;">Agency profile</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div style="display: flex; gap: 16px; flex-wrap: wrap;">
                        <a href="{{ route('subscription.plans') }}" style="display: inline-block; padding: 12px 24px; background: #6366f1; color: white; border-radius: 12px; font-weight: bold; text-decoration: none; cursor: pointer;" onmouseover="this.style.background='#4f46e5'" onmouseout="this.style.background='#6366f1'">
                            Upgrade Plan
                        </a>
                        <form action="{{ route('subscription.cancel-plan') }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" style="padding: 12px 24px; background: #fee2e2; color: #991b1b; border: none; border-radius: 12px; font-weight: bold; cursor: pointer;" onclick="return confirm('Are you sure you want to cancel your subscription?')" onmouseover="this.style.background='#fecaca'" onmouseout="this.style.background='#fee2e2'">
                                Cancel Subscription
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Billing History -->
            @if($transactions->count() > 0)
                <div style="background: white; border-radius: 16px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); overflow: hidden;">
                    <div style="padding: 24px; border-bottom: 1px solid #e2e8f0;">
                        <h2 style="font-size: 24px; font-weight: bold; color: #1e293b;">Billing History</h2>
                    </div>

                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead style="background: #f1f5f9; border-bottom: 1px solid #e2e8f0;">
                                <tr>
                                    <th style="padding: 16px 32px; text-align: left; font-size: 12px; font-weight: bold; color: #475569;">Date</th>
                                    <th style="padding: 16px 32px; text-align: left; font-size: 12px; font-weight: bold; color: #475569;">Amount</th>
                                    <th style="padding: 16px 32px; text-align: left; font-size: 12px; font-weight: bold; color: #475569;">Status</th>
                                    <th style="padding: 16px 32px; text-align: left; font-size: 12px; font-weight: bold; color: #475569;">Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transactions as $transaction)
                                    <tr style="border-bottom: 1px solid #e2e8f0;">
                                        <td style="padding: 16px 32px; color: #1e293b;">{{ $transaction->created_at->format('M d, Y') }}</td>
                                        <td style="padding: 16px 32px; font-weight: bold; color: #1e293b;">₨{{ number_format($transaction->amount, 2) }}</td>
                                        <td style="padding: 16px 32px;">
                                            <span style="display: inline-block; padding: 4px 12px; border-radius: 9999px; font-size: 12px; font-weight: bold; background: {{ $transaction->status === 'paid' ? '#dcfce7' : ($transaction->status === 'pending' ? '#fef3c7' : '#fee2e2') }}; color: {{ $transaction->status === 'paid' ? '#166534' : ($transaction->status === 'pending' ? '#92400e' : '#991b1b') }};">
                                                {{ ucfirst($transaction->status) }}
                                            </span>
                                        </td>
                                        <td style="padding: 16px 32px; color: #64748b;">{{ $transaction->description ?? 'Subscription' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        @else
            <!-- No Subscription -->
            <div style="background: white; border-radius: 16px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); padding: 48px; text-align: center;">
                <div style="margin-bottom: 24px;">
                    <div style="font-size: 80px; color: #cbd5e1;">💳</div>
                </div>
                <h2 style="font-size: 24px; font-weight: bold; color: #1e293b; margin-bottom: 8px;">No Active Subscription</h2>
                <p style="color: #64748b; margin-bottom: 32px;">Get started with our plans to unlock premium features</p>
                <a href="{{ route('subscription.plans') }}" style="display: inline-block; padding: 16px 32px; background: #6366f1; color: white; border-radius: 12px; font-weight: bold; text-decoration: none;" onmouseover="this.style.background='#4f46e5'" onmouseout="this.style.background='#6366f1'">
                    View Plans
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
