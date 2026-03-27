<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dealer Dashboard — AI Automation</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        :root {
            --accent-primary: #6366f1;
            --accent-primary-hover: #4f46e5;
            --accent-purple: #7c3aed;
            --accent-purple-hover: #6d28d9;
            --accent-emerald: #10b981;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --text-tertiary: #94a3b8;
            --bg-primary: #f8fafc;
            --bg-secondary: #f1f5f9;
            --border-color: #e2e8f0;
        }

        body.dark-mode {
            --text-primary: #e2e8f0;
            --text-secondary: #cbd5e1;
            --text-tertiary: #94a3b8;
            --bg-primary: #0f172a;
            --bg-secondary: #1e293b;
            --border-color: #334155;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            display: flex;
            min-height: 100vh;
            transition: background 0.3s, color 0.3s;
        }

        /* ═══════════════ SIDEBAR ═══════════════ */
        .sidebar {
            width: 280px;
            background: rgba(255, 255, 255, 0.97);
            border-right: 1px solid var(--border-color);
            padding: 24px 16px;
            overflow-y: auto;
            position: fixed;
            height: 100vh;
            left: 0;
            top: 0;
            z-index: 100;
            backdrop-filter: blur(20px);
            display: flex;
            flex-direction: column;
        }

        body.dark-mode .sidebar {
            background: rgba(15, 23, 42, 0.97);
        }

        .sidebar-brand {
            font-size: 20px;
            font-weight: 800;
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-purple));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 32px;
            padding: 12px 0;
            text-align: center;
            letter-spacing: -0.5px;
        }

        .sidebar-menu {
            display: flex;
            flex-direction: column;
            gap: 6px;
            flex: 1;
        }

        .sidebar-menu button {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-radius: 10px;
            background: transparent;
            color: var(--text-secondary);
            border: none;
            font-size: 14px;
            font-weight: 500;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: all 0.25s ease;
            width: 100%;
            text-align: left;
            position: relative;
        }

        .sidebar-menu button:hover:not(:disabled) {
            background: rgba(99, 102, 241, 0.08);
            color: var(--accent-primary);
            transform: translateX(2px);
        }

        .sidebar-menu button.active {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.12), rgba(124, 58, 237, 0.12));
            color: var(--accent-primary);
            font-weight: 600;
            border-left: 3px solid var(--accent-primary);
            padding-left: 13px;
        }

        .sidebar-menu button:disabled {
            opacity: 0.45;
            cursor: not-allowed;
        }

        .sidebar-menu button .lock-icon {
            margin-left: auto;
            font-size: 12px;
            opacity: 0.6;
        }

        /* Sidebar Verification Badge */
        .sidebar-badge {
            margin-top: 20px;
            padding: 16px;
            border-radius: 14px;
            text-align: center;
            transition: all 0.3s;
        }

        .sidebar-badge.unverified {
            background: linear-gradient(135deg, #fef3c7, #fef08a);
            border: 1px solid #fcd34d;
        }

        .sidebar-badge.pending {
            background: linear-gradient(135deg, #dbeafe, #bfdbfe);
            border: 1px solid #93c5fd;
        }

        .sidebar-badge.verified {
            background: linear-gradient(135deg, #dcfce7, #bbf7d0);
            border: 1px solid #86efac;
        }

        .sidebar-badge.rejected {
            background: linear-gradient(135deg, #fee2e2, #fecaca);
            border: 1px solid #fca5a5;
        }

        .sidebar-badge .badge-icon { font-size: 28px; margin-bottom: 8px; }
        .sidebar-badge .badge-title { font-size: 13px; font-weight: 700; margin-bottom: 4px; }
        .sidebar-badge .badge-desc { font-size: 11px; opacity: 0.8; }

        .sidebar-badge.unverified .badge-title { color: #92400e; }
        .sidebar-badge.pending .badge-title { color: #1e40af; }
        .sidebar-badge.verified .badge-title { color: #166534; }
        .sidebar-badge.rejected .badge-title { color: #991b1b; }

        /* ═══════════════ MAIN CONTENT ═══════════════ */
        .main-content {
            margin-left: 280px;
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .top-header {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border-color);
            padding: 16px 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 50;
        }

        body.dark-mode .top-header {
            background: rgba(30, 41, 59, 0.8);
        }

        .header-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-primary);
        }

        .header-actions { display: flex; gap: 12px; align-items: center; }

        .dark-toggle {
            width: 40px; height: 40px;
            border: 1px solid var(--border-color);
            background: var(--bg-secondary);
            border-radius: 10px;
            cursor: pointer;
            font-size: 18px;
            display: flex; align-items: center; justify-content: center;
            transition: all 0.3s;
        }

        .dark-toggle:hover {
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-purple));
            color: #fff; border-color: var(--accent-primary);
        }

        .logout-form button {
            padding: 8px 16px;
            background: #fee2e2; color: #dc2626;
            border: none; border-radius: 8px;
            font-size: 13px; font-weight: 600;
            cursor: pointer; transition: all 0.3s;
        }

        .logout-form button:hover { background: #fecaca; transform: translateY(-1px); }

        /* ═══════════════ CONTENT ═══════════════ */
        .content { padding: 32px; flex: 1; }

        .section { display: none; }
        .section.active {
            display: block;
            animation: fadeIn 0.35s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Cards */
        .card {
            background: #fff;
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 32px;
            margin-bottom: 24px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            transition: all 0.3s;
        }

        body.dark-mode .card { background: var(--bg-secondary); }

        .card h3 {
            font-size: 18px; font-weight: 700;
            color: var(--text-primary); margin-bottom: 24px;
            display: flex; align-items: center; gap: 12px;
        }
        .card h3 .icon { font-size: 24px; }

        /* Status Grid */
        .status-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 14px; margin-bottom: 24px;
        }

        .status-card {
            padding: 20px 16px;
            border-radius: 14px;
            border: 1px solid var(--border-color);
            text-align: center;
            transition: all 0.3s;
        }

        .status-card.verified { background: #dcfce7; border-color: #86efac; }
        .status-card.pending { background: #dbeafe; border-color: #93c5fd; }
        .status-card.unverified { background: #fee2e2; border-color: #fecaca; }

        .status-card .icon { font-size: 32px; margin-bottom: 8px; }
        .status-card .label {
            font-size: 11px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.5px;
        }

        /* Form */
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px; margin-bottom: 20px;
        }
        .form-row.full { grid-template-columns: 1fr; }

        .form-group { margin-bottom: 20px; }
        .form-group label {
            display: block; font-size: 14px; font-weight: 600;
            color: var(--text-primary); margin-bottom: 8px;
        }

        .form-group input, .form-group textarea, .form-group select {
            width: 100%; padding: 12px 16px;
            border: 1px solid var(--border-color);
            border-radius: 10px; font-size: 14px;
            font-family: 'Inter', sans-serif;
            background: #fff; color: var(--text-primary);
            outline: none; transition: all 0.3s;
        }

        body.dark-mode .form-group input,
        body.dark-mode .form-group textarea,
        body.dark-mode .form-group select { background: var(--bg-secondary); }

        .form-group input:focus, .form-group textarea:focus {
            border-color: var(--accent-primary);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        /* Upload */
        .upload-area {
            border: 2px dashed var(--border-color);
            border-radius: 14px; padding: 32px;
            text-align: center; cursor: pointer;
            transition: all 0.3s; background: var(--bg-primary);
            position: relative; overflow: hidden;
        }

        .upload-area:hover {
            border-color: var(--accent-primary);
            background: rgba(99, 102, 241, 0.04);
        }

        .upload-area.has-file {
            border-color: #86efac; background: #f0fdf4;
            border-style: solid;
        }

        .upload-area input[type="file"] { display: none; }
        .upload-icon { font-size: 40px; margin-bottom: 12px; }
        .upload-text { font-size: 14px; color: var(--text-secondary); font-weight: 600; }

        .image-preview {
            width: 100%; max-width: 300px;
            border-radius: 12px; margin-top: 12px;
            max-height: 280px; object-fit: cover;
        }

        /* Camera */
        .camera-section { margin: 20px 0; text-align: center; }

        #camera {
            width: 100%; max-width: 480px;
            border-radius: 14px;
            border: 2px solid var(--border-color);
            background: #000;
        }

        .camera-controls {
            display: flex; gap: 12px;
            justify-content: center;
            margin-top: 16px; flex-wrap: wrap;
        }

        /* Buttons */
        .btn {
            padding: 12px 24px; border: none; border-radius: 10px;
            font-size: 14px; font-weight: 600;
            font-family: 'Inter', sans-serif;
            cursor: pointer; transition: all 0.3s;
            white-space: nowrap;
            display: inline-flex; align-items: center; gap: 8px;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-purple));
            color: #fff;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(99, 102, 241, 0.3);
        }

        .btn-secondary {
            background: var(--bg-secondary); color: var(--text-secondary);
            border: 1px solid var(--border-color);
        }
        .btn-secondary:hover { background: var(--border-color); }

        .btn-danger { background: #fee2e2; color: #dc2626; }
        .btn-danger:hover { background: #fecaca; }

        .btn-success {
            background: linear-gradient(135deg, #10b981, #059669);
            color: #fff;
        }

        .btn:disabled { opacity: 0.5; cursor: not-allowed; transform: none !important; box-shadow: none !important; }

        /* Alerts */
        .alert {
            padding: 16px 20px; border-radius: 12px;
            margin-bottom: 20px; font-size: 14px;
            border: 1px solid;
            display: flex; align-items: center; gap: 12px;
        }

        .alert-success { background: #dcfce7; border-color: #86efac; color: #166534; }
        .alert-warning { background: #fef3c7; border-color: #fcd34d; color: #92400e; }
        .alert-danger { background: #fee2e2; border-color: #fecaca; color: #991b1b; }
        .alert-info { background: #dbeafe; border-color: #93c5fd; color: #1e40af; }

        /* AI Progress Modal */
        .ai-overlay {
            display: none;
            position: fixed; inset: 0;
            background: rgba(0,0,0,0.6);
            backdrop-filter: blur(6px);
            z-index: 9999;
            justify-content: center; align-items: center;
        }

        .ai-overlay.active { display: flex; }

        .ai-modal {
            background: #fff; border-radius: 20px;
            padding: 48px 40px; text-align: center;
            max-width: 440px; width: 90%;
            box-shadow: 0 25px 60px rgba(0,0,0,0.3);
            animation: modalPop 0.4s ease;
        }

        body.dark-mode .ai-modal {
            background: var(--bg-secondary);
        }

        @keyframes modalPop {
            from { transform: scale(0.85); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        .ai-modal .spinner {
            width: 64px; height: 64px;
            border: 4px solid var(--border-color);
            border-top: 4px solid var(--accent-primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 24px;
        }

        @keyframes spin { 100% { transform: rotate(360deg); } }

        .ai-modal h3 { font-size: 20px; font-weight: 700; margin-bottom: 12px; }
        .ai-modal p { color: var(--text-secondary); font-size: 14px; line-height: 1.6; }

        .ai-result-icon { font-size: 64px; margin-bottom: 16px; }
        .confidence-bar {
            width: 100%; height: 8px;
            background: var(--bg-secondary);
            border-radius: 8px; overflow: hidden;
            margin: 16px 0;
        }
        .confidence-fill {
            height: 100%; border-radius: 8px;
            transition: width 1.5s ease;
        }
        .confidence-fill.high { background: linear-gradient(90deg, #10b981, #059669); }
        .confidence-fill.medium { background: linear-gradient(90deg, #f59e0b, #d97706); }
        .confidence-fill.low { background: linear-gradient(90deg, #ef4444, #dc2626); }

        /* Verified Dealer Section */
        .verified-hero {
            background: linear-gradient(135deg, #10b981, #059669);
            border-radius: 16px; padding: 40px;
            color: #fff; text-align: center;
            margin-bottom: 24px;
        }
        .verified-hero .hero-icon { font-size: 48px; margin-bottom: 16px; }
        .verified-hero h2 { font-size: 24px; font-weight: 800; margin-bottom: 8px; }
        .verified-hero p { opacity: 0.9; font-size: 14px; }

        /* ═══════ RESPONSIVE ═══════ */

        /* Mobile hamburger toggle */
        .mobile-toggle {
            display: none;
            position: fixed;
            top: 12px; left: 12px;
            z-index: 200;
            width: 44px; height: 44px;
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-purple));
            color: #fff;
            border: none; border-radius: 12px;
            font-size: 22px;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.4);
            align-items: center; justify-content: center;
            transition: all 0.3s;
        }

        .mobile-toggle:active { transform: scale(0.92); }

        .sidebar-overlay {
            display: none;
            position: fixed; inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 99;
        }
        .sidebar-overlay.active { display: block; }

        /* Tablet */
        @media (max-width: 1024px) {
            .sidebar { width: 240px; }
            .main-content { margin-left: 240px; }
            .content { padding: 24px; }
            .card { padding: 24px; }
            .top-header { padding: 14px 24px; }
        }

        /* Mobile */
        @media (max-width: 768px) {
            body { display: block; }

            .mobile-toggle { display: flex; }

            .sidebar {
                width: 280px;
                position: fixed;
                left: -300px;
                top: 0;
                height: 100vh;
                z-index: 150;
                transition: left 0.35s cubic-bezier(0.4, 0, 0.2, 1);
                border-right: 1px solid var(--border-color);
                border-bottom: none;
                padding: 20px 14px;
                padding-top: 60px;
            }

            .sidebar.open { left: 0; }

            .main-content {
                margin-left: 0;
                width: 100%;
                min-height: 100vh;
            }

            .top-header {
                padding: 12px 16px;
                padding-left: 64px;
            }

            .header-title { font-size: 15px; }

            .content { padding: 16px; }

            .card {
                padding: 18px;
                border-radius: 12px;
                margin-bottom: 16px;
            }

            .card h3 { font-size: 16px; margin-bottom: 16px; }

            .form-row { grid-template-columns: 1fr; gap: 14px; }

            .form-group input, .form-group textarea, .form-group select {
                padding: 11px 14px; font-size: 14px;
            }

            .upload-area { padding: 24px 16px; }
            .upload-icon { font-size: 32px; }
            .upload-text { font-size: 13px; }

            .image-preview { max-width: 100%; }

            #camera { max-width: 100%; }

            .camera-controls { gap: 8px; }
            .camera-controls .btn { padding: 10px 16px; font-size: 13px; }

            .btn { padding: 10px 18px; font-size: 13px; }

            .status-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 10px;
            }

            .status-card { padding: 14px 10px; }
            .status-card .icon { font-size: 26px; }
            .status-card .label { font-size: 10px; }

            .alert { font-size: 13px; padding: 12px 14px; gap: 10px; }

            .sidebar-menu { gap: 4px; }
            .sidebar-menu button { padding: 11px 14px; font-size: 13px; }

            .verified-hero { padding: 28px 20px; }
            .verified-hero h2 { font-size: 20px; }
            .verified-hero .hero-icon { font-size: 40px; }

            .ai-modal { padding: 32px 24px; }
            .ai-modal h3 { font-size: 18px; }
        }

        /* Small phones */
        @media (max-width: 480px) {
            .top-header { padding: 10px 12px 10px 58px; }
            .header-title { font-size: 14px; }
            .header-actions { gap: 8px; }
            .dark-toggle { width: 36px; height: 36px; font-size: 16px; }
            .logout-form button { padding: 6px 12px; font-size: 12px; }

            .content { padding: 12px; }
            .card { padding: 14px; }
            .card h3 { font-size: 15px; gap: 8px; }
            .card h3 .icon { font-size: 20px; }

            .status-grid { grid-template-columns: 1fr 1fr; gap: 8px; }
            .status-card { padding: 12px 8px; border-radius: 10px; }
            .status-card .icon { font-size: 22px; margin-bottom: 4px; }
            .status-card .label { font-size: 9px; }

            .upload-area { padding: 20px 12px; border-radius: 10px; }
            .upload-icon { font-size: 28px; margin-bottom: 8px; }
            .upload-text { font-size: 12px; }

            .btn { padding: 10px 14px; font-size: 12px; gap: 6px; }

            .form-group label { font-size: 13px; }
            .form-group input { padding: 10px 12px; font-size: 13px; }

            .sidebar { width: 260px; }
            .sidebar-brand { font-size: 18px; margin-bottom: 20px; }

            .verified-hero { padding: 20px 16px; border-radius: 12px; }
            .verified-hero .hero-icon { font-size: 36px; margin-bottom: 12px; }
            .verified-hero h2 { font-size: 18px; }
            .verified-hero p { font-size: 13px; }
        }
    </style>
</head>
<body>
    <!-- AI Verification Modal -->
    <div class="ai-overlay" id="aiOverlay">
        <div class="ai-modal" id="aiModal">
            <div class="spinner" id="aiSpinner"></div>
            <h3 id="aiTitle">Verification</h3>
            <p id="aiMessage">Analyzing your documents with AI face recognition...</p>
        </div>
    </div>

    <!-- Mobile Hamburger Toggle -->
    <button class="mobile-toggle" id="mobileToggle" onclick="toggleSidebar()">☰</button>
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

    <!-- ═══════ SIDEBAR ═══════ -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-brand">🏪 Dealer Hub</div>

        <div class="sidebar-menu">
            <button onclick="switchSection('overview')" class="section-btn active" data-section="overview">
                📊 Overview
            </button>
            <button onclick="switchSection('verification')" class="section-btn" data-section="verification">
                🛡️ Verification
            </button>
            <button onclick="switchSection('products')" class="section-btn" data-section="products" id="productsBtn" @if(!$isVerified) disabled @endif>
                📦 Products
                @if(!$isVerified)<span class="lock-icon">🔒</span>@endif
            </button>
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
        </div>

        <!-- Verification Badge -->
        <div class="sidebar-badge {{ $verificationStatus }}" id="sidebarBadge">
            @if($verificationStatus === 'verified')
                <div class="badge-icon">✅</div>
                <div class="badge-title">Verified Dealer</div>
                <div class="badge-desc">Full access enabled</div>
            @elseif($verificationStatus === 'pending')
                <div class="badge-icon">⏳</div>
                <div class="badge-title">Review In Progress</div>
                <div class="badge-desc">We're checking your docs</div>
            @elseif($verificationStatus === 'rejected')
                <div class="badge-icon">❌</div>
                <div class="badge-title">Verification Failed</div>
                <div class="badge-desc">Please resubmit</div>
            @else
                <div class="badge-icon">⚠️</div>
                <div class="badge-title">Not Verified</div>
                <div class="badge-desc">Complete profile to list</div>
            @endif
        </div>
    </div>

    <!-- ═══════ MAIN CONTENT ═══════ -->
    <div class="main-content">
        <!-- Top Header -->
        <div class="top-header">
            <div class="header-title">Welcome, {{ $user->name }}!</div>
            <div class="header-actions">
                <button class="dark-toggle" onclick="toggleDarkMode()" title="Toggle dark mode">🌙</button>
                <form method="POST" action="/logout" class="logout-form">
                    @csrf
                    <button type="submit">Logout</button>
                </form>
            </div>
        </div>

        <div class="content">
            <!-- ═══════ OVERVIEW SECTION ═══════ -->
            <div class="section active" id="overview">
                @if($isVerified)
                    <div class="verified-hero">
                        <div class="hero-icon">✅</div>
                        <h2>Verified Dealer</h2>
                        <p>You have full access to all dealer features. Start listing your products!</p>
                    </div>
                @else
                    <div class="card">
                        <h3><span class="icon">📊</span> Dashboard Overview</h3>

                        <div class="alert alert-warning">
                            <span>⚠️</span>
                            <span>Complete your verification to unlock all features and start listing products.</span>
                        </div>

                        <div class="status-grid">
                            <div class="status-card {{ $user->cnic_front_image ? 'verified' : 'unverified' }}">
                                <div class="icon">{{ $user->cnic_front_image ? '✅' : '❌' }}</div>
                                <div class="label">CNIC Upload</div>
                            </div>
                            <div class="status-card {{ $user->live_photo ? 'verified' : 'unverified' }}">
                                <div class="icon">{{ $user->live_photo ? '✅' : '❌' }}</div>
                                <div class="label">Live Photo</div>
                            </div>
                            <div class="status-card {{ ($verificationStatus === 'verified') ? 'verified' : 'unverified' }}">
                                <div class="icon">{{ ($verificationStatus === 'verified') ? '✅' : '❌' }}</div>
                                <div class="label">AI Face Match</div>
                            </div>
                            <div class="status-card {{ $user->phone ? 'verified' : 'unverified' }}">
                                <div class="icon">{{ $user->phone ? '✅' : '❌' }}</div>
                                <div class="label">Phone Linked</div>
                            </div>
                        </div>

                        <p style="font-size: 14px; color: var(--text-secondary); line-height: 1.7;">
                            Go to the <strong>🛡️ Verification</strong> tab to upload your CNIC, take a live photo, and complete the AI-powered face match.
                        </p>
                    </div>
                @endif

                <div class="card">
                    <h3><span class="icon">📈</span> Quick Stats</h3>
                    <div class="status-grid">
                        <div class="status-card" style="background: var(--bg-primary);">
                            <div class="icon">📦</div>
                            <div style="font-size: 28px; font-weight: 800; color: var(--accent-primary);">0</div>
                            <div class="label">Products</div>
                        </div>
                        <div class="status-card" style="background: var(--bg-primary);">
                            <div class="icon">📋</div>
                            <div style="font-size: 28px; font-weight: 800; color: var(--accent-primary);">0</div>
                            <div class="label">Orders</div>
                        </div>
                        <div class="status-card" style="background: var(--bg-primary);">
                            <div class="icon">💰</div>
                            <div style="font-size: 28px; font-weight: 800; color: var(--accent-primary);">Rs 0</div>
                            <div class="label">Revenue</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ═══════ VERIFICATION SECTION ═══════ -->
            <div class="section" id="verification">
                @if($isVerified)
                    <div class="card">
                        <h3><span class="icon">✅</span> Verification Complete</h3>
                        <div class="alert alert-success">
                            <span>🎉</span>
                            <span>Your identity has been verified. You are a <strong>Verified Dealer ✅</strong>.</span>
                        </div>
                        @if($user->verification_notes)
                            <p style="font-size: 13px; color: var(--text-secondary);">{{ $user->verification_notes }}</p>
                        @endif
                    </div>
                @elseif($verificationStatus === 'pending')
                    <div class="card">
                        <h3><span class="icon">⏳</span> Verification Under Review</h3>
                        <div class="alert alert-info">
                            <span>📝</span>
                            <span>Your documents are being reviewed. This usually takes 24-48 hours.</span>
                        </div>
                    </div>
                @else
                    <form id="verificationForm" enctype="multipart/form-data">
                        <!-- Step 1: CNIC -->
                        <div class="card">
                            <h3><span class="icon">🇵🇰</span> Step 1: CNIC Verification</h3>
                            <p style="font-size: 14px; color: var(--text-secondary); margin-bottom: 20px;">
                                Upload clear photos of both sides of your CNIC (Computerized National Identity Card).
                            </p>

                            <div class="form-row">
                                <div class="form-group">
                                    <label>CNIC Number <span style="color: #ef4444;">*</span></label>
                                    <input type="text" name="cnic_number" id="cnicNumber" placeholder="12345-1234567-1" required
                                           pattern="\d{5}-\d{7}-\d{1}" title="Format: 12345-1234567-1">
                                </div>
                                <div class="form-group">
                                    <label>Phone Number (linked to CNIC) <span style="color: #ef4444;">*</span></label>
                                    <input type="tel" name="phone" id="phoneNumber" placeholder="+923001234567" required>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label>CNIC Front Side <span style="color: #ef4444;">*</span></label>
                                    <div class="upload-area" id="cnicFrontArea" onclick="document.getElementById('cnicFront').click()">
                                        <div class="upload-icon">📄</div>
                                        <div class="upload-text">Click to upload front side</div>
                                        <input type="file" id="cnicFront" name="cnic_front" accept="image/*" onchange="handleFileUpload(this, 'cnicFrontArea')" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>CNIC Back Side <span style="color: #ef4444;">*</span></label>
                                    <div class="upload-area" id="cnicBackArea" onclick="document.getElementById('cnicBack').click()">
                                        <div class="upload-icon">📄</div>
                                        <div class="upload-text">Click to upload back side</div>
                                        <input type="file" id="cnicBack" name="cnic_back" accept="image/*" onchange="handleFileUpload(this, 'cnicBackArea')" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2: Live Photo -->
                        <div class="card">
                            <h3><span class="icon">📸</span> Step 2: Live Photo (Camera Only)</h3>
                            <p style="font-size: 14px; color: var(--text-secondary); margin-bottom: 20px;">
                                Take a fresh photo using your camera. <strong>File upload is not allowed</strong> — this ensures you're a real person.
                            </p>

                            <div class="camera-section">
                                <video id="camera" autoplay playsinline style="display: none;"></video>
                                <canvas id="photoCanvas" style="display: none;"></canvas>

                                <div id="photoPreview" style="display: none;">
                                    <img id="capturedPhoto" class="image-preview" alt="Captured Photo">
                                </div>

                                <div class="camera-controls">
                                    <button type="button" class="btn btn-primary" id="startCameraBtn" onclick="startCamera()">
                                        📹 Open Camera
                                    </button>
                                    <button type="button" class="btn btn-success" id="capturePhotoBtn" onclick="capturePhoto()" style="display: none;">
                                        📸 Capture
                                    </button>
                                    <button type="button" class="btn btn-danger" id="stopCameraBtn" onclick="stopCamera()" style="display: none;">
                                        ⏹ Stop
                                    </button>
                                    <button type="button" class="btn btn-secondary" id="retakeBtn" onclick="retakePhoto()" style="display: none;">
                                        🔄 Retake
                                    </button>
                                </div>
                                <input type="hidden" id="photoData" name="live_photo">
                            </div>
                        </div>

                        <!-- Step 3: Selfie + CNIC (Optional Bonus) -->
                        <div class="card">
                            <h3><span class="icon">🤳</span> Step 3: Selfie with CNIC (Optional)</h3>
                            <p style="font-size: 14px; color: var(--text-secondary); margin-bottom: 20px;">
                                Optionally upload a selfie while <strong>holding your CNIC next to your face</strong> for extra verification.
                            </p>

                            <div class="form-group">
                                <label>Selfie with CNIC <span style="color: var(--text-tertiary);">(optional)</span></label>
                                <div class="upload-area" id="selfieArea" onclick="document.getElementById('selfie').click()">
                                    <div class="upload-icon">🤳</div>
                                    <div class="upload-text">Click to upload selfie holding CNIC</div>
                                    <input type="file" id="selfie" name="selfie" accept="image/*" onchange="handleFileUpload(this, 'selfieArea')">
                                </div>
                            </div>

                            <div class="alert alert-info" style="margin-top: 16px;">
                                <span>🤖</span>
                                <span>AI will compare your <strong>CNIC photo</strong> with your <strong>live camera photo</strong> to verify your identity automatically.</span>
                            </div>
                        </div>

                        <!-- Submit -->
                        <div class="card" style="text-align: center;">
                            <button type="submit" class="btn btn-primary" id="submitVerificationBtn" style="width: 100%; padding: 16px; font-size: 16px;">
                                🛡️ Submit for Verification
                            </button>
                            <p style="font-size: 12px; color: var(--text-tertiary); margin-top: 12px;">
                                All your data is encrypted and securely stored. Verification is instant with AI.
                            </p>
                        </div>
                    </form>
                @endif
            </div>

            <!-- ═══════ PRODUCTS SECTION ═══════ -->
            <div class="section" id="products">
                @if($isVerified)
                    <!-- Toggle: My Listings / Add New -->
                    <div style="display: flex; gap: 12px; margin-bottom: 24px; flex-wrap: wrap;">
                        <button class="btn btn-primary" id="showAddFormBtn" onclick="togglePropertyView('add')">➕ Add New Property</button>
                        <button class="btn btn-secondary" id="showListingsBtn" onclick="togglePropertyView('list')" style="border: 2px solid var(--accent-primary); color: var(--accent-primary);">📋 My Listings</button>
                    </div>

                    <!-- ═══ ADD PROPERTY FORM ═══ -->
                    <div id="addPropertyView">
                        <form id="propertyForm" enctype="multipart/form-data">
                            <!-- Step 1: Basic Info -->
                            <div class="card">
                                <h3><span class="icon">🏠</span> Basic Information</h3>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label>Property Title <span style="color:#ef4444;">*</span></label>
                                        <input type="text" name="title" id="propTitle" placeholder="e.g. 5 Marla House in DHA Phase 5" required maxlength="200">
                                    </div>
                                    <div class="form-group">
                                        <label>Price (PKR) <span style="color:#ef4444;">*</span></label>
                                        <input type="number" name="price" id="propPrice" placeholder="5000000" required min="1000">
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label>Property Type <span style="color:#ef4444;">*</span></label>
                                        <select name="property_type" id="propType" required>
                                            <option value="house">🏠 House</option>
                                            <option value="portion">🏘️ Portion</option>
                                            <option value="apartment">🏢 Apartment</option>
                                            <option value="plot">📐 Plot</option>
                                            <option value="commercial">🏪 Commercial</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Listing Type <span style="color:#ef4444;">*</span></label>
                                        <select name="listing_type" id="propListingType" required>
                                            <option value="sale">🔖 For Sale</option>
                                            <option value="rent">🔑 For Rent</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label>Ownership <span style="color:#ef4444;">*</span></label>
                                        <select name="ownership_type" id="propOwnership" required>
                                            <option value="owner">👤 Owner</option>
                                            <option value="dealer">🏪 Dealer</option>
                                            <option value="builder">🏗️ Builder</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Area (Marla) <span style="color:#ef4444;">*</span></label>
                                        <input type="number" name="area_marla" id="propMarla" placeholder="5" required min="0.5" max="500" step="0.5">
                                    </div>
                                </div>
                            </div>

                            <!-- Step 2: Property Details -->
                            <div class="card">
                                <h3><span class="icon">🛏️</span> Property Details</h3>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label>Bedrooms <span style="color:#ef4444;">*</span></label>
                                        <select name="bedrooms" id="propBedrooms" required>
                                            @for($i = 0; $i <= 15; $i++)
                                                <option value="{{ $i }}" {{ $i == 2 ? 'selected' : '' }}>{{ $i }} {{ $i == 1 ? 'Bedroom' : 'Bedrooms' }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Bathrooms <span style="color:#ef4444;">*</span></label>
                                        <select name="bathrooms" id="propBathrooms" required>
                                            @for($i = 0; $i <= 10; $i++)
                                                <option value="{{ $i }}" {{ $i == 1 ? 'selected' : '' }}>{{ $i }} {{ $i == 1 ? 'Bathroom' : 'Bathrooms' }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label>Kitchens <span style="color:#ef4444;">*</span></label>
                                        <select name="kitchens" id="propKitchens" required>
                                            @for($i = 0; $i <= 5; $i++)
                                                <option value="{{ $i }}" {{ $i == 1 ? 'selected' : '' }}>{{ $i }} {{ $i == 1 ? 'Kitchen' : 'Kitchens' }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Floors <span style="color:#ef4444;">*</span></label>
                                        <select name="floors" id="propFloors" required>
                                            @for($i = 1; $i <= 10; $i++)
                                                <option value="{{ $i }}" {{ $i == 1 ? 'selected' : '' }}>{{ $i }} {{ $i == 1 ? 'Floor' : 'Floors' }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label>Furnished Status <span style="color:#ef4444;">*</span></label>
                                        <select name="furnished" id="propFurnished" required>
                                            <option value="unfurnished">Unfurnished</option>
                                            <option value="semi-furnished">Semi-Furnished</option>
                                            <option value="furnished">Fully Furnished</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Contact Phone <span style="color:#ef4444;">*</span></label>
                                        <input type="tel" name="contact_phone" id="propPhone" placeholder="+923001234567" required value="{{ $user->phone }}">
                                    </div>
                                </div>

                                <div class="form-row full">
                                    <div class="form-group">
                                        <label>Description</label>
                                        <textarea name="description" id="propDesc" rows="3" placeholder="Describe the property features, nearby amenities, etc." style="resize: vertical;"></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Step 3: Location -->
                            <div class="card">
                                <h3><span class="icon">📍</span> Location</h3>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label>City <span style="color:#ef4444;">*</span></label>
                                        <select name="city" id="propCity" required>
                                            <option value="">Select City</option>
                                            <option value="Karachi">Karachi</option>
                                            <option value="Lahore">Lahore</option>
                                            <option value="Islamabad">Islamabad</option>
                                            <option value="Rawalpindi">Rawalpindi</option>
                                            <option value="Faisalabad">Faisalabad</option>
                                            <option value="Multan">Multan</option>
                                            <option value="Peshawar">Peshawar</option>
                                            <option value="Quetta">Quetta</option>
                                            <option value="Sialkot">Sialkot</option>
                                            <option value="Gujranwala">Gujranwala</option>
                                            <option value="Hyderabad">Hyderabad</option>
                                            <option value="Bahawalpur">Bahawalpur</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Area / Society <span style="color:#ef4444;">*</span></label>
                                        <input type="text" name="area_name" id="propArea" placeholder="e.g. DHA Phase 5, Block D" required>
                                    </div>
                                </div>

                                <div class="form-row full">
                                    <div class="form-group">
                                        <label>Full Address <span style="color:#ef4444;">*</span></label>
                                        <input type="text" name="full_address" id="propAddress" placeholder="House #123, Street 45, Block D, DHA Phase 5" required>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>📌 Pin Location on Map <span style="color: var(--text-tertiary);">(click to place pin)</span></label>
                                    <div id="propertyMap" style="height: 300px; border-radius: 12px; border: 2px solid var(--border-color); overflow: hidden; background: #e5e7eb;"></div>
                                    <input type="hidden" name="latitude" id="propLat">
                                    <input type="hidden" name="longitude" id="propLng">
                                    <p id="mapCoords" style="font-size: 12px; color: var(--text-tertiary); margin-top: 8px;">No pin placed yet</p>
                                </div>
                            </div>

                            <!-- Step 4: Images -->
                            <div class="card">
                                <h3><span class="icon">📸</span> Property Images</h3>

                                <div class="alert alert-info" style="margin-bottom: 20px;">
                                    <span>📷</span>
                                    <span>At least <strong>1 live camera photo</strong> is mandatory. All images are auto-watermarked and checked for duplicates.</span>
                                </div>

                                <!-- Live Camera Photo (Required) -->
                                <div class="form-group">
                                    <label>📹 Live Camera Photo <span style="color:#ef4444;">*</span> <span style="color: var(--text-tertiary); font-weight: 400;">(cannot be faked)</span></label>
                                    <div class="camera-section">
                                        <video id="propCamera" autoplay playsinline style="display: none; width: 100%; max-width: 480px; border-radius: 12px; border: 2px solid var(--border-color);"></video>
                                        <canvas id="propPhotoCanvas" style="display: none;"></canvas>

                                        <div id="propPhotoPreview" style="display: none;">
                                            <img id="propCapturedPhoto" class="image-preview" alt="Live Property Photo" style="max-width: 100%;">
                                            <p style="color: #166534; font-size: 13px; margin-top: 8px;">✅ Live photo captured</p>
                                        </div>

                                        <div class="camera-controls">
                                            <button type="button" class="btn btn-primary" id="propStartCamBtn" onclick="propStartCamera()">📹 Open Camera</button>
                                            <button type="button" class="btn btn-success" id="propCapBtn" onclick="propCapturePhoto()" style="display:none;">📸 Capture</button>
                                            <button type="button" class="btn btn-danger" id="propStopCamBtn" onclick="propStopCamera()" style="display:none;">⏹ Stop</button>
                                            <button type="button" class="btn btn-secondary" id="propRetakeBtn" onclick="propRetakePhoto()" style="display:none;">🔄 Retake</button>
                                        </div>
                                        <input type="hidden" id="propLivePhotoData" name="live_photo">
                                    </div>
                                </div>

                                <!-- Gallery Upload -->
                                <div class="form-group" style="margin-top: 24px;">
                                    <label>🖼️ Additional Photos <span style="color: var(--text-tertiary); font-weight: 400;">(max 10 images)</span></label>
                                    <div class="upload-area" id="galleryUploadArea" onclick="document.getElementById('propImages').click()">
                                        <div class="upload-icon">🖼️</div>
                                        <div class="upload-text">Click to upload property photos (max 10)</div>
                                        <input type="file" id="propImages" name="images[]" accept="image/*" multiple onchange="handleGalleryUpload(this)">
                                    </div>
                                    <div id="galleryPreview" style="display: flex; gap: 10px; flex-wrap: wrap; margin-top: 12px;"></div>
                                </div>
                            </div>

                            <!-- Step 5: Documents -->
                            <div class="card">
                                <h3><span class="icon">📄</span> Documents</h3>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label>⚡ Electricity Bill <span style="color: var(--text-tertiary); font-weight: 400;">(optional)</span></label>
                                        <div class="upload-area" id="elecBillArea" onclick="document.getElementById('elecBill').click()">
                                            <div class="upload-icon">⚡</div>
                                            <div class="upload-text">Upload electricity bill</div>
                                            <input type="file" id="elecBill" name="electricity_bill" accept="image/*,.pdf" onchange="handleFileUpload(this, 'elecBillArea')">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>📝 Ownership Proof <span style="color: var(--text-tertiary); font-weight: 400;">(optional but powerful)</span></label>
                                        <div class="upload-area" id="ownerProofArea" onclick="document.getElementById('ownerProof').click()">
                                            <div class="upload-icon">📝</div>
                                            <div class="upload-text">Upload rent/sale proof</div>
                                            <input type="file" id="ownerProof" name="ownership_proof" accept="image/*,.pdf" onchange="handleFileUpload(this, 'ownerProofArea')">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit -->
                            <div class="card" style="text-align: center;">
                                <div class="alert alert-warning" style="text-align: left;">
                                    <span>⚠️</span>
                                    <span>All listings require <strong>admin approval</strong> before going live. Images are watermarked and checked for duplicates automatically.</span>
                                </div>
                                <button type="submit" class="btn btn-primary" id="submitPropertyBtn" style="width: 100%; padding: 16px; font-size: 16px;">
                                    🏠 Submit Property for Review
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- ═══ MY LISTINGS VIEW ═══ -->
                    <div id="myListingsView" style="display: none;">
                        <div class="card">
                            <h3><span class="icon">📋</span> My Listings</h3>
                            <div id="listingsContainer">
                                <p style="text-align: center; color: var(--text-tertiary); padding: 40px 0;">
                                    <span style="font-size: 48px; display: block; margin-bottom: 16px;">🏠</span>
                                    Loading your listings...
                                </p>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="card">
                        <h3><span class="icon">🔒</span> Products Locked</h3>
                        <div class="alert alert-warning">
                            <span>⚠️</span>
                            <span>Complete your verification first to add and manage products.</span>
                        </div>
                    </div>
                @endif
            </div>

            <!-- ═══════ SHOP SECTION ═══════ -->
            <div class="section" id="shop">
                @if($isVerified)
                    <div class="card">
                        <h3><span class="icon">🏪</span> My Shop</h3>
                        <p style="color: var(--text-secondary);">Customize your shop appearance and settings.</p>
                    </div>
                @else
                    <div class="card">
                        <h3><span class="icon">🔒</span> Shop Locked</h3>
                        <div class="alert alert-warning">
                            <span>⚠️</span>
                            <span>Complete your verification first to access shop settings.</span>
                        </div>
                    </div>
                @endif
            </div>

            <!-- ═══════ ORDERS SECTION ═══════ -->
            <div class="section" id="orders">
                @if($isVerified)
                    <div class="card">
                        <h3><span class="icon">📋</span> Orders</h3>
                        <p style="color: var(--text-secondary);">Your order management will appear here.</p>
                    </div>
                @else
                    <div class="card">
                        <h3><span class="icon">🔒</span> Orders Locked</h3>
                        <div class="alert alert-warning">
                            <span>⚠️</span>
                            <span>Complete your verification first to view orders.</span>
                        </div>
                    </div>
                @endif
            </div>

            <!-- ═══════ SETTINGS SECTION ═══════ -->
            <div class="section" id="settings">
                <div class="card">
                    <h3><span class="icon">⚙️</span> Account Settings</h3>
                    <form method="POST" action="/dealer/settings">
                        @csrf
                        <div class="form-row full">
                            <div class="form-group">
                                <label>Business Name</label>
                                <input type="text" name="business_name" placeholder="Your business name">
                            </div>
                        </div>
                        <div class="form-row full">
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" value="{{ $user->email }}" disabled>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        let stream = null;

        // ═══════ SECTION SWITCHING ═══════
        function switchSection(section) {
            document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
            document.querySelectorAll('.section-btn').forEach(b => b.classList.remove('active'));
            document.getElementById(section).classList.add('active');
            document.querySelector(`[data-section="${section}"]`).classList.add('active');
            // Auto-close sidebar on mobile
            if (window.innerWidth <= 768) closeSidebar();
            // Init map when products tab opens
            if (section === 'products') {
                setTimeout(() => {
                    initPropertyMap();
                    if (propMap) propMap.invalidateSize();
                }, 400);
            }
        }

        // ═══════ MOBILE SIDEBAR ═══════
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('open');
            document.getElementById('sidebarOverlay').classList.toggle('active');
        }
        function closeSidebar() {
            document.getElementById('sidebar').classList.remove('open');
            document.getElementById('sidebarOverlay').classList.remove('active');
        }

        // ═══════ DARK MODE ═══════
        function toggleDarkMode() {
            document.body.classList.toggle('dark-mode');
            localStorage.setItem('dealerDarkMode', document.body.classList.contains('dark-mode'));
        }
        if (localStorage.getItem('dealerDarkMode') === 'true') {
            document.body.classList.add('dark-mode');
        }

        // ═══════ FILE UPLOAD HANDLER ═══════
        function handleFileUpload(input, areaId) {
            const area = document.getElementById(areaId);
            if (input.files && input.files[0]) {
                // Remove existing preview
                const existingImg = area.querySelector('.image-preview');
                if (existingImg) existingImg.remove();

                area.classList.add('has-file');
                const reader = new FileReader();
                reader.onload = (e) => {
                    // Hide the upload icon/text
                    const icon = area.querySelector('.upload-icon');
                    const text = area.querySelector('.upload-text');
                    if (icon) icon.style.display = 'none';
                    if (text) text.textContent = '✅ ' + input.files[0].name;

                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'image-preview';
                    area.appendChild(img);
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        // ═══════ CAMERA FUNCTIONS ═══════
        function startCamera() {
            navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user', width: 640, height: 480 } })
                .then(s => {
                    stream = s;
                    const cam = document.getElementById('camera');
                    cam.srcObject = stream;
                    cam.style.display = 'block';
                    cam.play();
                    document.getElementById('startCameraBtn').style.display = 'none';
                    document.getElementById('capturePhotoBtn').style.display = 'inline-flex';
                    document.getElementById('stopCameraBtn').style.display = 'inline-flex';
                })
                .catch(err => {
                    alert('Camera access denied. Please allow camera access to continue.\n\nError: ' + err.message);
                });
        }

        function stopCamera() {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                stream = null;
            }
            document.getElementById('camera').style.display = 'none';
            document.getElementById('startCameraBtn').style.display = 'inline-flex';
            document.getElementById('capturePhotoBtn').style.display = 'none';
            document.getElementById('stopCameraBtn').style.display = 'none';
        }

        function capturePhoto() {
            const canvas = document.getElementById('photoCanvas');
            const ctx = canvas.getContext('2d');
            const video = document.getElementById('camera');

            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            ctx.drawImage(video, 0, 0);

            const photoData = canvas.toDataURL('image/jpeg', 0.9);
            document.getElementById('photoData').value = photoData;
            document.getElementById('capturedPhoto').src = photoData;
            document.getElementById('photoPreview').style.display = 'block';

            stopCamera();
            document.getElementById('startCameraBtn').style.display = 'none';
            document.getElementById('retakeBtn').style.display = 'inline-flex';
        }

        function retakePhoto() {
            document.getElementById('photoPreview').style.display = 'none';
            document.getElementById('photoData').value = '';
            document.getElementById('retakeBtn').style.display = 'none';
            startCamera();
        }

        // ═══════ VERIFICATION FORM SUBMISSION (AJAX) ═══════
        const form = document.getElementById('verificationForm');
        if (form) {
            form.addEventListener('submit', async function(e) {
                e.preventDefault();

                // Validate
                const cnicNumber = document.getElementById('cnicNumber').value;
                const phone = document.getElementById('phoneNumber').value;
                const cnicFront = document.getElementById('cnicFront').files[0];
                const cnicBack = document.getElementById('cnicBack').files[0];
                const livePhoto = document.getElementById('photoData').value;
                const selfie = document.getElementById('selfie').files[0];

                if (!cnicNumber || !phone || !cnicFront || !cnicBack || !livePhoto) {
                    alert('Please complete all required fields:\n- CNIC Number\n- Phone Number\n- CNIC Front & Back images\n- Live Photo (use camera)');
                    return;
                }

                // CNIC format validation
                if (!/^\d{5}-\d{7}-\d{1}$/.test(cnicNumber)) {
                    alert('CNIC format must be: 12345-1234567-1');
                    return;
                }

                // Show AI modal
                showAIModal('processing');

                const formData = new FormData();
                formData.append('cnic_number', cnicNumber);
                formData.append('phone', phone);
                formData.append('cnic_front', cnicFront);
                formData.append('cnic_back', cnicBack);
                formData.append('live_photo', livePhoto);
                if (selfie) formData.append('selfie', selfie);

                try {
                    const response = await fetch('/dealer/verify', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: formData
                    });

                    const data = await response.json();

                    if (data.success) {
                        if (data.verified) {
                            showAIModal('success', data.confidence);
                            setTimeout(() => location.reload(), 3000);
                        } else {
                            showAIModal('review', data.confidence);
                            setTimeout(() => location.reload(), 4000);
                        }
                    } else {
                        showAIModal('error', 0, data.message || 'Something went wrong.');
                    }
                } catch (error) {
                    showAIModal('error', 0, 'Network error. Please try again.');
                    console.error(error);
                }
            });
        }

        // ═══════ AI MODAL ═══════
        function showAIModal(state, confidence = 0, errorMsg = '') {
            const overlay = document.getElementById('aiOverlay');
            const modal = document.getElementById('aiModal');
            overlay.classList.add('active');

            const confClass = confidence >= 70 ? 'high' : confidence >= 40 ? 'medium' : 'low';

            if (state === 'processing') {
                modal.innerHTML = `
                    <div class="spinner"></div>
                    <h3>Verification in Progress</h3>
                    <p>Analyzing your documents with advanced face recognition AI...</p>
                    <p style="font-size: 12px; color: var(--text-tertiary); margin-top: 12px;">This may take 15-30 seconds</p>
                `;
            } else if (state === 'success') {
                modal.innerHTML = `
                    <div class="ai-result-icon">✅</div>
                    <h3 style="color: #166534;">Verification Successful!</h3>
                    <p>Face match confidence: <strong>${confidence}%</strong></p>
                    <div class="confidence-bar"><div class="confidence-fill ${confClass}" style="width: ${confidence}%"></div></div>
                    <p style="color: #166534; font-weight: 600; margin-top: 12px;">You are now a Verified Dealer ✅</p>
                    <p style="font-size: 12px; color: var(--text-tertiary); margin-top: 8px;">Redirecting...</p>
                `;
            } else if (state === 'review') {
                modal.innerHTML = `
                    <div class="ai-result-icon">📝</div>
                    <h3 style="color: #1e40af;">Under Manual Review</h3>
                    <p>AI confidence: <strong>${confidence}%</strong> (below auto-approve threshold)</p>
                    <div class="confidence-bar"><div class="confidence-fill ${confClass}" style="width: ${confidence}%"></div></div>
                    <p style="margin-top: 12px;">Your documents have been submitted for manual review. You'll be notified once verified.</p>
                    <p style="font-size: 12px; color: var(--text-tertiary); margin-top: 8px;">Redirecting...</p>
                `;
            } else if (state === 'error') {
                modal.innerHTML = `
                    <div class="ai-result-icon">❌</div>
                    <h3 style="color: #991b1b;">Verification Error</h3>
                    <p>${errorMsg}</p>
                    <button class="btn btn-secondary" onclick="document.getElementById('aiOverlay').classList.remove('active')" style="margin-top: 16px;">Close</button>
                `;
            }
        }

        // CNIC Number formatting
        const cnicInput = document.getElementById('cnicNumber');
        if (cnicInput) {
            cnicInput.addEventListener('input', function(e) {
                let val = e.target.value.replace(/\D/g, '');
                if (val.length > 5) val = val.slice(0, 5) + '-' + val.slice(5);
                if (val.length > 13) val = val.slice(0, 13) + '-' + val.slice(13);
                if (val.length > 15) val = val.slice(0, 15);
                e.target.value = val;
            });
        }

        // ═══════════════════════════════════════
        // PROPERTY LISTING JAVASCRIPT
        // ═══════════════════════════════════════

        let propStream = null;
        let propMap = null;
        let propMarker = null;

        // ═══════ TOGGLE ADD/LIST VIEW ═══════
        function togglePropertyView(view) {
            const addView = document.getElementById('addPropertyView');
            const listView = document.getElementById('myListingsView');
            const addBtn = document.getElementById('showAddFormBtn');
            const listBtn = document.getElementById('showListingsBtn');

            if (!addView) return;

            if (view === 'add') {
                addView.style.display = 'block';
                listView.style.display = 'none';
                addBtn.classList.add('btn-primary');
                addBtn.style.border = 'none'; addBtn.style.color = '#fff';
                listBtn.classList.remove('btn-primary');
                listBtn.style.border = '2px solid var(--accent-primary)'; listBtn.style.color = 'var(--accent-primary)';
                initPropertyMap();
            } else {
                addView.style.display = 'none';
                listView.style.display = 'block';
                listBtn.classList.add('btn-primary');
                listBtn.style.border = 'none'; listBtn.style.color = '#fff';
                addBtn.classList.remove('btn-primary');
                addBtn.style.border = '2px solid var(--accent-primary)'; addBtn.style.color = 'var(--accent-primary)';
                loadMyListings();
            }
        }

        // ═══════ PROPERTY CAMERA ═══════
        function propStartCamera() {
            navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment', width: 1280, height: 720 } })
                .then(s => {
                    propStream = s;
                    const cam = document.getElementById('propCamera');
                    cam.srcObject = s;
                    cam.style.display = 'block';
                    cam.play();
                    document.getElementById('propStartCamBtn').style.display = 'none';
                    document.getElementById('propCapBtn').style.display = 'inline-flex';
                    document.getElementById('propStopCamBtn').style.display = 'inline-flex';
                })
                .catch(err => alert('Camera access denied: ' + err.message));
        }

        function propStopCamera() {
            if (propStream) {
                propStream.getTracks().forEach(t => t.stop());
                propStream = null;
            }
            document.getElementById('propCamera').style.display = 'none';
            document.getElementById('propStartCamBtn').style.display = 'inline-flex';
            document.getElementById('propCapBtn').style.display = 'none';
            document.getElementById('propStopCamBtn').style.display = 'none';
        }

        function propCapturePhoto() {
            const canvas = document.getElementById('propPhotoCanvas');
            const ctx = canvas.getContext('2d');
            const video = document.getElementById('propCamera');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            ctx.drawImage(video, 0, 0);
            const data = canvas.toDataURL('image/jpeg', 0.9);
            document.getElementById('propLivePhotoData').value = data;
            document.getElementById('propCapturedPhoto').src = data;
            document.getElementById('propPhotoPreview').style.display = 'block';
            propStopCamera();
            document.getElementById('propStartCamBtn').style.display = 'none';
            document.getElementById('propRetakeBtn').style.display = 'inline-flex';
        }

        function propRetakePhoto() {
            document.getElementById('propPhotoPreview').style.display = 'none';
            document.getElementById('propLivePhotoData').value = '';
            document.getElementById('propRetakeBtn').style.display = 'none';
            propStartCamera();
        }

        // ═══════ GALLERY UPLOAD ═══════
        function handleGalleryUpload(input) {
            const preview = document.getElementById('galleryPreview');
            const area = document.getElementById('galleryUploadArea');
            preview.innerHTML = '';

            if (input.files.length > 10) {
                alert('Maximum 10 images allowed.');
                input.value = '';
                return;
            }

            if (input.files.length > 0) {
                area.classList.add('has-file');
                const text = area.querySelector('.upload-text');
                if (text) text.textContent = `✅ ${input.files.length} photo(s) selected`;
            }

            Array.from(input.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const div = document.createElement('div');
                    div.style.cssText = 'position:relative; width:100px; height:100px; border-radius:10px; overflow:hidden; border:2px solid var(--border-color);';
                    div.innerHTML = `<img src="${e.target.result}" style="width:100%; height:100%; object-fit:cover;">`;
                    preview.appendChild(div);
                };
                reader.readAsDataURL(file);
            });
        }

        // ═══════ LEAFLET MAP ═══════
        const cityCoords = {
            'Karachi': [24.8607, 67.0011],
            'Lahore': [31.5204, 74.3587],
            'Islamabad': [33.6844, 73.0479],
            'Rawalpindi': [33.5651, 73.0169],
            'Faisalabad': [31.4504, 73.1350],
            'Multan': [30.1575, 71.5249],
            'Peshawar': [34.0151, 71.5249],
            'Quetta': [30.1798, 66.9750],
            'Sialkot': [32.4945, 74.5229],
            'Gujranwala': [32.1877, 74.1945],
            'Hyderabad': [25.3960, 68.3578],
            'Bahawalpur': [29.3956, 71.6836],
        };

        function initPropertyMap() {
            const mapEl = document.getElementById('propertyMap');
            if (!mapEl) return;

            // If map already exists, just resize it
            if (propMap) {
                propMap.invalidateSize();
                return;
            }

            setTimeout(() => {
                propMap = L.map('propertyMap').setView([30.3753, 69.3451], 5); // Pakistan center
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap'
                }).addTo(propMap);

                propMap.on('click', function(e) {
                    const { lat, lng } = e.latlng;
                    document.getElementById('propLat').value = lat.toFixed(7);
                    document.getElementById('propLng').value = lng.toFixed(7);
                    document.getElementById('mapCoords').textContent = `📍 ${lat.toFixed(5)}, ${lng.toFixed(5)}`;
                    document.getElementById('mapCoords').style.color = '#166534';

                    if (propMarker) propMap.removeLayer(propMarker);
                    propMarker = L.marker([lat, lng]).addTo(propMap);
                });

                propMap.invalidateSize();
            }, 300);
        }

        // Auto-center map on city selection
        const citySelect = document.getElementById('propCity');
        if (citySelect) {
            citySelect.addEventListener('change', function() {
                if (propMap && cityCoords[this.value]) {
                    propMap.setView(cityCoords[this.value], 12);
                }
            });
        }

        // ═══════ PROPERTY FORM SUBMISSION ═══════
        const propForm = document.getElementById('propertyForm');
        if (propForm) {
            propForm.addEventListener('submit', async function(e) {
                e.preventDefault();

                const livePhoto = document.getElementById('propLivePhotoData').value;
                if (!livePhoto) {
                    alert('📹 Live camera photo is required! Please take a photo of the property.');
                    return;
                }

                const submitBtn = document.getElementById('submitPropertyBtn');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '⏳ Submitting & Processing Images...';

                const formData = new FormData(this);

                try {
                    const response = await fetch('/dealer/property/store', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: formData
                    });

                    const data = await response.json();

                    if (data.success) {
                        const hasFlags = data.flags && data.flags.length > 0;
                        alert(hasFlags
                            ? '⚠️ ' + data.message + '\n\nFlags:\n' + data.flags.join('\n')
                            : '✅ ' + data.message);
                        propForm.reset();
                        document.getElementById('propPhotoPreview').style.display = 'none';
                        document.getElementById('propLivePhotoData').value = '';
                        document.getElementById('galleryPreview').innerHTML = '';
                        if (propMarker) { propMap.removeLayer(propMarker); propMarker = null; }
                        document.getElementById('mapCoords').textContent = 'No pin placed yet';
                        document.getElementById('mapCoords').style.color = 'var(--text-tertiary)';
                        // Reset upload areas
                        ['galleryUploadArea', 'elecBillArea', 'ownerProofArea'].forEach(id => {
                            const el = document.getElementById(id);
                            if (el) { el.classList.remove('has-file'); const t = el.querySelector('.upload-text'); if (t) t.textContent = t.dataset.original || 'Click to upload'; }
                        });
                        togglePropertyView('list');
                    } else {
                        const errors = data.errors ? Object.values(data.errors).flat().join('\n') : (data.error || 'Submission failed.');
                        alert('❌ ' + errors);
                    }
                } catch (error) {
                    alert('❌ Network error. Please try again.');
                    console.error(error);
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '🏠 Submit Property for Review';
                }
            });
        }

        // ═══════ LOAD MY LISTINGS ═══════
        async function loadMyListings() {
            const container = document.getElementById('listingsContainer');
            if (!container) return;

            try {
                const response = await fetch('/dealer/properties', {
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                });
                const data = await response.json();

                if (!data.properties || data.properties.length === 0) {
                    container.innerHTML = `
                        <div style="text-align: center; padding: 60px 20px;">
                            <span style="font-size: 64px; display: block; margin-bottom: 16px;">🏠</span>
                            <h4 style="font-weight: 700; margin-bottom: 8px;">No Listings Yet</h4>
                            <p style="color: var(--text-secondary);">Add your first property listing to get started!</p>
                            <button class="btn btn-primary" onclick="togglePropertyView('add')" style="margin-top: 16px;">➕ Add Property</button>
                        </div>`;
                    return;
                }

                const statusColors = {
                    'pending_review': { bg: '#fef3c7', color: '#92400e', icon: '⏳', text: 'Pending Review' },
                    'approved': { bg: '#dcfce7', color: '#166534', icon: '✅', text: 'Approved' },
                    'rejected': { bg: '#fee2e2', color: '#991b1b', icon: '❌', text: 'Rejected' },
                    'flagged': { bg: '#fef3c7', color: '#92400e', icon: '🚩', text: 'Flagged' },
                    'draft': { bg: '#f3f4f6', color: '#374151', icon: '📝', text: 'Draft' },
                };

                const typeIcons = { house: '🏠', portion: '🏘️', apartment: '🏢', plot: '📐', commercial: '🏪' };

                container.innerHTML = data.properties.map(prop => {
                    const s = statusColors[prop.status] || statusColors['draft'];
                    const icon = typeIcons[prop.property_type] || '🏠';
                    const price = Number(prop.price).toLocaleString();
                    const flags = prop.flags ? prop.flags : [];

                    return `
                        <div style="display: flex; gap: 16px; padding: 16px; border: 1px solid var(--border-color); border-radius: 12px; margin-bottom: 12px; align-items: center; flex-wrap: wrap;">
                            <div style="width: 80px; height: 80px; border-radius: 10px; background: var(--bg-secondary); display: flex; align-items: center; justify-content: center; font-size: 36px; flex-shrink: 0;">
                                ${icon}
                            </div>
                            <div style="flex: 1; min-width: 200px;">
                                <h4 style="font-weight: 700; margin-bottom: 4px; font-size: 15px;">${prop.title}</h4>
                                <p style="font-size: 13px; color: var(--text-secondary); margin-bottom: 6px;">
                                    ${prop.area_marla} Marla • ${prop.bedrooms} Bed • ${prop.bathrooms} Bath • ${prop.city}
                                </p>
                                <div style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                                    <span style="font-weight: 800; color: var(--accent-primary);">Rs ${price}</span>
                                    <span style="background: ${s.bg}; color: ${s.color}; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 700;">${s.icon} ${s.text}</span>
                                    ${prop.listing_type === 'rent' ? '<span style="background: #dbeafe; color: #1e40af; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 700;">🔑 Rent</span>' : '<span style="background: #f3e8ff; color: #6d28d9; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 700;">🔖 Sale</span>'}
                                </div>
                                ${flags.length > 0 ? `<div style="margin-top: 6px; font-size: 11px; color: #92400e;">🚩 ${flags.join(' | ')}</div>` : ''}
                            </div>
                            <button onclick="deleteListing(${prop.id})" class="btn btn-danger" style="padding: 8px 14px; font-size: 12px;">🗑️ Delete</button>
                        </div>`;
                }).join('');
            } catch (err) {
                container.innerHTML = '<p style="text-align: center; color: #ef4444; padding: 20px;">Failed to load listings.</p>';
                console.error(err);
            }
        }

        // ═══════ DELETE LISTING ═══════
        async function deleteListing(id) {
            if (!confirm('Are you sure you want to delete this listing?')) return;
            try {
                const response = await fetch(`/dealer/property/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                if (data.success) {
                    alert('✅ Listing deleted.');
                    loadMyListings();
                }
            } catch (err) { alert('❌ Failed to delete.'); }
        }
    </script>
</body>
</html>
