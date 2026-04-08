<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ResellZone Admin — AI Automation</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        :root {
            --primary: #6366F1;
            --primary-hover: #4F46E5;
            --primary-soft: #EEF2FF;
            --success: #10B981;
            --success-soft: #ECFDF5;
            --danger: #EF4444;
            --danger-soft: #FEF2F2;
            --warning: #F59E0B;
            --warning-soft: #FFFBEB;
            --bg-page: #F8FAFC;
            --bg-card: #FFFFFF;
            --text-heading: #0F172A;
            --text-main: #334155;
            --text-muted: #64748B;
            --border: #E2E8F0;
            --radius-xl: 20px;
            --radius-lg: 16px;
            --radius-md: 12px;
            --shadow-sm: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
            --transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-page);
            color: var(--text-main);
            display: flex;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* ═══════════════ SIDEBAR ═══════════════ */
        .sidebar {
            width: 280px;
            background-color: var(--bg-card);
            border-right: 1px solid var(--border);
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            display: flex;
            flex-direction: column;
            padding: 32px 20px;
            z-index: 100;
            transition: var(--transition);
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 0 12px 40px;
            font-size: 22px;
            font-weight: 800;
            color: var(--primary);
            letter-spacing: -0.02em;
        }

        .sidebar-brand i {
            background: linear-gradient(135deg, var(--primary), var(--primary-hover));
            color: white;
            padding: 8px;
            border-radius: 10px;
            font-size: 20px;
        }

        .sidebar-menu {
            display: flex;
            flex-direction: column;
            gap: 6px;
            flex: 1;
        }

        .menu-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-muted);
            margin: 20px 12px 8px;
        }

        .menu-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-radius: var(--radius-md);
            text-decoration: none;
            color: var(--text-muted);
            font-size: 14px;
            font-weight: 600;
            transition: var(--transition);
            position: relative;
        }

        .menu-item i { font-size: 20px; transition: var(--transition); }

        .menu-item:hover {
            background-color: var(--primary-soft);
            color: var(--primary);
        }

        .menu-item.active {
            background-color: var(--primary);
            color: white;
            box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.3);
        }

        .menu-item.active i { color: white; }

        .menu-item .badge {
            margin-left: auto;
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 800;
            background-color: var(--danger);
            color: white;
        }

        .active .badge {
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
        }

        /* ═══════════════ MAIN CONTENT ═══════════════ */
        .main-wrapper {
            margin-left: 280px;
            flex: 1;
            display: flex;
            flex-direction: column;
            width: calc(100% - 280px);
        }

        .navbar {
            height: 80px;
            background-color: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 40px;
            position: sticky;
            top: 0;
            z-index: 90;
        }

        .search-bar {
            display: flex;
            align-items: center;
            background-color: var(--bg-page);
            border: 1px solid var(--border);
            padding: 8px 16px;
            border-radius: var(--radius-md);
            width: 320px;
            gap: 10px;
            transition: var(--transition);
        }

        .search-bar:focus-within {
            border-color: var(--primary);
            background-color: white;
            box-shadow: 0 0 0 4px var(--primary-soft);
        }

        .search-bar input {
            border: none;
            background: transparent;
            outline: none;
            font-family: inherit;
            font-size: 14px;
            width: 100%;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background-color: var(--primary-soft);
            color: var(--primary);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 16px;
        }

        .btn-logout {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-muted);
            text-decoration: none;
            transition: var(--transition);
            background-color: var(--bg-page);
            border: 1px solid var(--border);
        }

        .btn-logout:hover {
            background-color: var(--danger-soft);
            color: var(--danger);
            border-color: var(--danger-soft);
        }

        .content {
            padding: 40px;
            max-width: 1400px;
            margin: 0 auto;
            width: 100%;
        }

        /* ═══════════════ STATS CARDS ═══════════════ */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 24px;
            margin-bottom: 40px;
        }

        .stat-card {
            background-color: var(--bg-card);
            padding: 24px;
            border-radius: var(--radius-lg);
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 20px;
            transition: var(--transition);
        }

        .stat-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-md); }

        .stat-icon {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .icon-blue { background-color: #EFF6FF; color: #3B82F6; }
        .icon-purple { background-color: #F5F3FF; color: #8B5CF6; }
        .icon-green { background-color: #ECFDF5; color: #10B981; }
        .icon-orange { background-color: #FFF7ED; color: #F59E0B; }

        .stat-info .label { font-size: 13px; font-weight: 600; color: var(--text-muted); margin-bottom: 4px; }
        .stat-info .value { font-size: 24px; font-weight: 800; color: var(--text-heading); }

        /* ═══════════════ PAGE HEADER ═══════════════ */
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 28px;
        }

        .section-title {
            font-size: 22px;
            font-weight: 800;
            color: var(--text-heading);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .section-title .badge {
            background-color: var(--primary-soft);
            color: var(--primary);
            padding: 4px 12px;
            border-radius: 99px;
            font-size: 13px;
            font-weight: 700;
        }

        .filters-row {
            display: flex;
            gap: 12px;
            margin-bottom: 32px;
        }

        .filter-btn {
            padding: 10px 20px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            background-color: white;
            border: 1px solid var(--border);
            color: var(--text-muted);
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .filter-btn:hover { border-color: var(--primary); color: var(--primary); }
        .filter-btn.active {
            background-color: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        /* ═══════════════ LISTING CARDS ═══════════════ */
        .listings-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
            gap: 28px;
        }

        .listing-card {
            background-color: var(--bg-card);
            border-radius: var(--radius-xl);
            overflow: hidden;
            border: 1px solid var(--border);
            transition: var(--transition);
            display: flex;
            flex-direction: column;
        }

        .listing-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-lg);
            border-color: var(--primary-soft);
        }

        .card-media {
            position: relative;
            height: 220px;
            overflow: hidden;
        }

        .card-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s ease;
        }

        .listing-card:hover .card-img { transform: scale(1.1); }

        .status-badge {
            position: absolute;
            top: 16px;
            left: 16px;
            padding: 6px 12px;
            border-radius: 10px;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            backdrop-filter: blur(8px);
            box-shadow: var(--shadow-sm);
        }

        .bg-pending { background-color: rgba(245, 158, 11, 0.85); }
        .bg-approved { background-color: rgba(16, 185, 129, 0.85); }

        .card-content { padding: 24px; flex: 1; display: flex; flex-direction: column; }

        .card-category {
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--primary);
            margin-bottom: 8px;
        }

        .card-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-heading);
            line-height: 1.4;
            margin-bottom: 12px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .card-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            margin-bottom: 20px;
            color: var(--text-muted);
            font-size: 13px;
            font-weight: 500;
        }

        .meta-item { display: flex; align-items: center; gap: 6px; }
        .meta-item i { color: var(--primary); font-size: 16px; }

        .card-price {
            font-size: 22px;
            font-weight: 800;
            color: var(--text-heading);
            margin-bottom: 24px;
            display: flex;
            align-items: baseline;
            gap: 4px;
        }

        .card-price span { font-size: 14px; color: var(--text-muted); font-weight: 600; }

        .dealer-box {
            background-color: var(--bg-page);
            padding: 16px;
            border-radius: var(--radius-md);
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .dealer-avatar {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background-color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-weight: 700;
            font-size: 14px;
            border: 1px solid var(--border);
        }

        .dealer-name { font-size: 13px; font-weight: 700; color: var(--text-heading); }
        .dealer-phone { font-size: 12px; color: var(--text-muted); }

        .card-actions { display: flex; gap: 12px; margin-top: auto; }

        .btn {
            flex: 1;
            padding: 12px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            cursor: pointer;
            transition: var(--transition);
            border: none;
        }

        .btn-approve { background-color: var(--success); color: white; }
        .btn-approve:hover { background-color: #059669; box-shadow: 0 8px 15px -3px rgba(16, 185, 129, 0.3); }

        .btn-reject { background-color: var(--danger-soft); color: var(--danger); }
        .btn-reject:hover { background-color: var(--danger); color: white; box-shadow: 0 8px 15px -3px rgba(239, 68, 68, 0.3); }

        /* ═══════════════ MODALS ═══════════════ */
        .modal {
            display: none;
            position: fixed;
            inset: 0;
            background-color: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(8px);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal.active { display: flex; animation: modalFadeIn 0.3s ease; }

        @keyframes modalFadeIn { from { opacity: 0; } to { opacity: 1; } }

        .modal-card {
            background-color: white;
            border-radius: var(--radius-xl);
            width: 100%;
            max-width: 480px;
            padding: 40px;
            box-shadow: var(--shadow-lg);
            position: relative;
            transform: translateY(0);
            transition: var(--transition);
        }

        .modal.active .modal-card { animation: modalSlideUp 0.4s cubic-bezier(0.34, 1.56, 0.64, 1); }

        @keyframes modalSlideUp { from { transform: translateY(40px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }

        .modal-icon {
            width: 64px; height: 64px;
            border-radius: 20px;
            display: flex; align-items: center; justify-content: center;
            font-size: 28px; margin: 0 auto 24px;
        }

        .modal-title { font-size: 22px; font-weight: 800; text-align: center; margin-bottom: 12px; color: var(--text-heading); }
        .modal-desc { font-size: 15px; color: var(--text-muted); text-align: center; line-height: 1.6; margin-bottom: 32px; }

        .modal-textarea {
            width: 100%;
            padding: 16px;
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            margin-bottom: 32px;
            font-family: inherit;
            font-size: 14px;
            resize: none;
            outline: none;
            transition: var(--transition);
            background-color: var(--bg-page);
        }

        .modal-textarea:focus { border-color: var(--primary); background-color: white; box-shadow: 0 0 0 4px var(--primary-soft); }

        .modal-footer { display: flex; gap: 16px; }

        /* ═══════════════ RESPONSIVE ═══════════════ */
        @media (max-width: 1200px) {
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
        }

        @media (max-width: 1024px) {
            .sidebar { width: 80px; padding: 32px 12px; }
            .sidebar-brand span, .menu-label, .menu-item span, .menu-item .badge { display: none; }
            .sidebar-brand { padding: 0 0 40px; justify-content: center; }
            .menu-item { justify-content: center; padding: 14px; }
            .main-wrapper { margin-left: 80px; width: calc(100% - 80px); }
        }

        @media (max-width: 768px) {
            .stats-grid { grid-template-columns: 1fr; }
            .navbar { padding: 0 20px; }
            .search-bar { display: none; }
            .content { padding: 24px; }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-brand">
            <i class="ri-home-7-fill"></i>
            <span>ResellZone</span>
        </div>
        
        <nav class="sidebar-menu">
            @php $pendingCount = \App\Models\Property::where('status', 'pending_review')->count(); @endphp
            <p class="menu-label">Main Menu</p>
            <a href="#" class="menu-item">
                <i class="ri-dashboard-2-line"></i>
                <span>Insights</span>
            </a>
            <a href="{{ route('admin.dashboard') }}" class="menu-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="ri-mail-star-line"></i>
                <span>Pending Review</span>
                @if($pendingCount > 0)
                    <span class="badge">{{ $pendingCount }}</span>
                @endif
            </a>
            <a href="{{ route('admin.properties.approved') }}" class="menu-item {{ request()->routeIs('admin.properties.approved') ? 'active' : '' }}">
                <i class="ri-checkbox-circle-line"></i>
                <span>Approved</span>
            </a>
            <a href="{{ route('admin.properties.rejected') }}" class="menu-item {{ request()->routeIs('admin.properties.rejected') ? 'active' : '' }}">
                <i class="ri-close-circle-line"></i>
                <span>Rejected</span>
            </a>
            
            <p class="menu-label" style="margin-top: 24px;">Users & Verification</p>
            <a href="{{ route('admin.dealers') }}" class="menu-item {{ request()->routeIs('admin.dealers', 'admin.dealer.show') ? 'active' : '' }}">
                <i class="ri-team-line"></i>
                <span>Dealer Management</span>
            </a>

            <p class="menu-label">Management</p>
            <a href="#" class="menu-item">
                <i class="ri-user-follow-line"></i>
                <span>Dealers</span>
            </a>
            <a href="#" class="menu-item">
                <i class="ri-shield-user-line"></i>
                <span>Verifications</span>
            </a>

            <p class="menu-label">System</p>
            <a href="#" class="menu-item">
                <i class="ri-settings-4-line"></i>
                <span>Configuration</span>
            </a>
        </nav>
    </aside>

    <div class="main-wrapper">
        <!-- Navbar -->
        <header class="navbar">
            <div class="search-bar">
                <i class="ri-search-2-line"></i>
                <input type="text" placeholder="Search listings, dealers, or locations...">
            </div>

            <div class="user-profile">
                <div class="user-avatar">{{ substr(Auth::user()->name, 0, 1) }}</div>
                <div style="text-align: left;">
                    <div style="font-size: 14px; font-weight: 700; color: var(--text-heading);">{{ Auth::user()->name }}</div>
                    <div style="font-size: 12px; color: var(--text-muted); font-weight: 600;">Administrator</div>
                </div>
                <div style="width: 1px; height: 24px; background-color: var(--border); margin: 0 8px;"></div>
                <form method="POST" action="/logout">
                    @csrf
                    <button type="submit" class="btn-logout" title="Secure Logout">
                        <i class="ri-shut-down-line"></i>
                    </button>
                </form>
            </div>
        </header>

        <!-- Main Content -->
        <main class="content">
            @if(request()->routeIs('admin.property.show'))
                @include('admin.preview')
            @elseif(request()->routeIs('admin.properties.approved'))
                <div class="section-header">
                    <h1 class="section-title">Approved Listings <span class="badge" style="background:var(--success); color:white;">{{ $properties->total() }}</span></h1>
                </div>
                <div class="listings-grid">
                    @foreach($properties as $prop)
                        @include('admin.property-card', ['prop' => $prop])
                    @endforeach
                </div>
                <div style="margin-top: 40px;">
                    {{ $properties->links() }}
                </div>
            @elseif(request()->routeIs('admin.properties.rejected'))
                <div class="section-header">
                    <h1 class="section-title">Rejected Listings <span class="badge" style="background:var(--danger); color:white;">{{ $properties->total() }}</span></h1>
                </div>
                <div class="listings-grid">
                    @foreach($properties as $prop)
                        @include('admin.property-card', ['prop' => $prop])
                    @endforeach
                </div>
                <div style="margin-top: 40px;">
                    {{ $properties->links() }}
                </div>
            @elseif(request()->routeIs('admin.dealers'))
                <div class="section-header">
                    <h1 class="section-title">Registered Dealers <span class="badge" style="background:var(--primary); color:white;">{{ $dealers->total() }}</span></h1>
                </div>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 24px;">
                    @foreach($dealers as $dealer)
                        <div class="card" style="padding: 24px; border: 1px solid var(--border); border-radius: var(--radius-lg); background: white;">
                            <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 20px;">
                                <div style="width: 50px; height: 50px; border-radius: 50%; background: var(--primary-soft); color: var(--primary); font-size: 20px; font-weight: 800; display: flex; align-items: center; justify-content: center;">
                                    {{ substr($dealer->name, 0, 1) }}
                                </div>
                                <div>
                                    <h3 style="font-size: 16px; margin-bottom: 4px; color: var(--text-heading);">{{ $dealer->name }}</h3>
                                    <p style="font-size: 12px; color: var(--text-muted);">Joined: {{ $dealer->created_at->format('M d, Y') }}</p>
                                </div>
                            </div>
                            <div style="margin-bottom: 20px; font-size: 13px; color: var(--text-main); display: flex; flex-direction: column; gap: 8px;">
                                <div><i class="ri-mail-line" style="color:var(--text-muted);"></i> {{ $dealer->email }}</div>
                                <div><i class="ri-phone-line" style="color:var(--text-muted);"></i> {{ $dealer->phone ?? 'No phone' }}</div>
                                <div>
                                    <i class="ri-shield-check-line" style="color:var(--text-muted);"></i> Status: 
                                    <span style="font-weight: 700; color: {{ $dealer->verification_status == 'verified' ? 'var(--success)' : ($dealer->verification_banned ? 'var(--danger)' : 'var(--warning)') }}">
                                        {{ strtoupper($dealer->verification_banned ? 'Banned' : $dealer->verification_status) }}
                                    </span>
                                </div>
                            </div>
                            <a href="{{ route('admin.dealer.show', $dealer->id) }}" class="btn btn-primary" style="width: 100%; display: block; text-align: center; text-decoration: none;">
                                View Profile
                            </a>
                        </div>
                    @endforeach
                </div>
                <div style="margin-top: 40px;">
                    {{ $dealers->links() }}
                </div>
            @elseif(request()->routeIs('admin.dealer.show'))
                @include('admin.dealer-preview')
            @else
            <!-- Stats Overview -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon icon-purple">
                        <i class="ri-time-line"></i>
                    </div>
                    <div class="stat-info">
                        <p class="label">Pending Review</p>
                        <p class="value">{{ count($pendingProperties) }}</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon icon-green">
                        <i class="ri-checkbox-circle-line"></i>
                    </div>
                    <div class="stat-info">
                        <p class="label">Total Approved</p>
                        <p class="value">{{ count($approvedProperties) }}</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon icon-blue">
                        <i class="ri-user-smile-line"></i>
                    </div>
                    <div class="stat-info">
                        <p class="label">Active Dealers</p>
                        <p class="value">124</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon icon-orange">
                        <i class="ri-money-dollar-circle-line"></i>
                    </div>
                    <div class="stat-info">
                        <p class="label">Total Revenue</p>
                        <p class="value">Rs 4.2M</p>
                    </div>
                </div>
            </div>

            <div class="section-header">
                <h1 class="section-title">
                    Listings Queue
                    <span class="badge">{{ count($pendingProperties) }} Pending</span>
                </h1>
                
                <div class="filters-row">
                    <button class="filter-btn active">All</button>
                    <button class="filter-btn">House</button>
                    <button class="filter-btn">Plot</button>
                    <button class="filter-btn">Rent</button>
                </div>
            </div>

            <!-- Pending Listings -->
            @if(count($pendingProperties) > 0)
                <div class="listings-grid">
                    @foreach($pendingProperties as $prop)
                        @include('admin.property-card', ['prop' => $prop])
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <div style="width: 80px; height: 80px; background-color: var(--success-soft); color: var(--success); border-radius: 24px; display: flex; align-items: center; justify-content: center; font-size: 32px; margin: 0 auto 20px;">
                        <i class="ri-sparkling-2-line"></i>
                    </div>
                    <p style="font-size: 18px; font-weight: 800; color: var(--text-heading); margin-bottom: 8px;">Inbox Zero!</p>
                    <p style="color: var(--text-muted);">You've cleared all pending property listings.</p>
                </div>
            @endif

            <div style="margin-top: 60px; padding-top: 40px; border-top: 1px solid var(--border);">
                <h2 class="section-title" style="margin-bottom: 32px;">Recently Processed</h2>
                <div class="listings-grid" style="opacity: 0.8;">
                    @foreach($approvedProperties as $prop)
                        @include('admin.property-card', ['prop' => $prop])
                    @endforeach
                </div>
            </div>
            @endif
        </main>
    </div>

    <!-- Reject Modal -->
    <div class="modal" id="rejectModal">
        <div class="modal-card">
            <div class="modal-icon icon-danger" style="background-color: var(--danger-soft); color: var(--danger);">
                <i class="ri-error-warning-fill"></i>
            </div>
            <h3 class="modal-title">Confirm Rejection</h3>
            <p class="modal-desc">Please state why this listing is being rejected. This feedback will be shared with the dealer to help them improve.</p>
            
            <textarea class="modal-textarea" id="rejectReason" rows="4" placeholder="e.g. Images are not clear enough or price is unrealistic..."></textarea>
            
            <div class="modal-footer">
                <button onclick="closeRejectModal()" class="btn" style="background-color: var(--bg-page); color: var(--text-muted);">Cancel</button>
                <button id="confirmRejectBtn" class="btn btn-reject" style="flex: 2;">Confirm Rejection</button>
            </div>
        </div>
    </div>

    <!-- Approve Modal -->
    <div class="modal" id="approveModal">
        <div class="modal-card">
            <div class="modal-icon icon-success" style="background-color: var(--success-soft); color: var(--success);">
                <i class="ri-checkbox-circle-fill"></i>
            </div>
            <h3 class="modal-title">Ready to Approve?</h3>
            <p class="modal-desc">Once approved, this property listing will be immediately visible to all potential buyers on the platform.</p>
            
            <div class="modal-footer">
                <button onclick="closeApproveModal()" class="btn" style="background-color: var(--bg-page); color: var(--text-muted);">Go Back</button>
                <button id="confirmApproveBtn" class="btn btn-approve" style="flex: 2;">Approve Now</button>
            </div>
        </div>
    </div>

    <script>
        let currentPropId = null;

        function approveProperty(id) {
            currentPropId = id;
            document.getElementById('approveModal').classList.add('active');
        }

        function closeApproveModal() {
            document.getElementById('approveModal').classList.remove('active');
        }

        document.getElementById('confirmApproveBtn').onclick = function() {
            const btn = this;
            btn.disabled = true;
            btn.innerHTML = '<i class="ri-loader-4-line ri-spin"></i> Processing...';

            fetch(`/admin/property/${currentPropId}/approve`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + data.error);
                    btn.disabled = false;
                    btn.innerHTML = 'Approve Now';
                }
            });
        };

        function openRejectModal(id) {
            currentPropId = id;
            document.getElementById('rejectModal').classList.add('active');
            document.getElementById('rejectReason').focus();
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').classList.remove('active');
            document.getElementById('rejectReason').value = '';
        }

        document.getElementById('confirmRejectBtn').onclick = function() {
            const reason = document.getElementById('rejectReason').value;
            if (!reason) {
                alert('Please provide a reason for rejection.');
                return;
            }

            const btn = this;
            btn.disabled = true;
            btn.innerHTML = '<i class="ri-loader-4-line ri-spin"></i> Rejecting...';

            fetch(`/admin/property/${currentPropId}/reject`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ reason: reason })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + data.error);
                    btn.disabled = false;
                    btn.innerHTML = 'Confirm Rejection';
                }
            });
        };

        window.onkeydown = function(event) {
            if (event.key === "Escape") {
                closeRejectModal();
                closeApproveModal();
            }
        };
    </script>
</body>
</html>
