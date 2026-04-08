@props(['user', 'isVerified', 'verificationStatus'])

<div class="sidebar" id="sidebar">
    <div class="sidebar-brand">🏪 Dealer Hub</div>

    <div class="sidebar-menu">
        <button onclick="switchSection('overview')" class="section-btn active" data-section="overview">
            📊 Overview
        </button>
        
        <button onclick="switchSection('verification')" class="section-btn" data-section="verification">
            🛡️ Verification
            @if($user->verification_failed_attempts > 0 && !$isVerified && !$user->isVerificationBanned())
                <span class="attempt-badge">{{ $user->verification_failed_attempts }}</span>
            @endif
        </button>

        <a href="{{ route('dealer.properties.create') }}" class="section-btn {{ request()->routeIs('dealer.properties.create') ? 'active' : '' }} @if(!$isVerified) disabled @endif">
            ➕ Add Product
            @if(!$isVerified)<span class="lock-icon">🔒</span>@endif
        </a>

        <a href="{{ route('dealer.properties.index') }}" class="section-btn {{ request()->routeIs('dealer.properties.index') ? 'active' : '' }} @if(!$isVerified) disabled @endif">
            📦 Product Listing
            @if(!$isVerified)<span class="lock-icon">🔒</span>@endif
        </a>

        <a href="{{ route('chat') }}" class="section-btn {{ request()->routeIs('chat') ? 'active' : '' }}">
            💬 Chats
        </a>

        <button onclick="switchSection('shop')" class="section-btn" data-section="shop" id="shopBtn" @if(!$isVerified) disabled @endif>
            🏪 My Shop
            @if(!$isVerified)<span class="lock-icon">🔒</span>@endif
        </button>
        <button onclick="switchSection('orders')" class="section-btn" data-section="orders" id="ordersBtn" @if(!$isVerified) disabled @endif>
            📋 Orders
            @if(!$isVerified)<span class="lock-icon">🔒</span>@endif
        </button>
        <button onclick="switchSection('settings')" class="section-btn" data-section="settings">
            ⚙️ Settings
        </button>

        <a href="{{ route('subscription.plans') }}" class="section-btn {{ request()->routeIs('subscription.plans') ? 'active' : '' }}">
            💎 Pricing Plans
        </a>
    </div>

    <!-- Sidebar Verification Badge -->
    <div class="sidebar-badge @if($isVerified) verified @elseif($verificationStatus === 'pending') pending @elseif($user->isVerificationBanned()) rejected @else unverified @endif">
        <div class="badge-icon">
            @if($isVerified) ✅ @elseif($verificationStatus === 'pending') ⏳ @elseif($user->isVerificationBanned()) 🚫 @else 🛡️ @endif
        </div>
        <div class="badge-title">
            @if($isVerified) Verified Dealer @elseif($verificationStatus === 'pending') Under Review @elseif($user->isVerificationBanned()) Banned @else Unverified @endif
        </div>
        <div class="badge-desc">
            @if($isVerified) Full access granted @elseif($verificationStatus === 'pending') Checking docs @elseif($user->isVerificationBanned()) Contact support @else Verify to list items @endif
        </div>
    </div>
</div>
