<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dealer Dashboard') — AI Automation</title>
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
            transition: background 0.3s, border-color 0.3s, transform 0.3s ease;
        }

        body.dark-mode .sidebar {
            background: rgba(15, 23, 42, 0.97);
        }

        .sidebar-brand {
            font-size: 20px;
            font-weight: 800;
            color: var(--accent-primary);
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
            gap: 4px;
            flex: 1;
        }

        .sidebar-menu .section-btn {
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
            transition: all 0.2s ease;
            width: 100%;
            text-align: left;
            position: relative;
            text-decoration: none;
        }

        .sidebar-menu .section-btn:hover:not(.disabled) {
            background: rgba(99, 102, 241, 0.08);
            color: var(--accent-primary);
            transform: translateX(4px);
        }

        .sidebar-menu .section-btn.active {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.1), rgba(124, 58, 237, 0.1));
            color: var(--accent-primary);
            font-weight: 600;
        }

        .sidebar-menu .section-btn.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 15%;
            height: 70%;
            width: 4px;
            background: var(--accent-primary);
            border-radius: 0 4px 4px 0;
        }

        .sidebar-menu button:disabled, .sidebar-menu .section-btn:disabled, .sidebar-menu .section-btn.disabled {
            opacity: 0.45;
            cursor: not-allowed;
            pointer-events: none;
        }

        .sidebar-menu button .lock-icon, .sidebar-menu .section-btn .lock-icon {
            margin-left: auto;
            font-size: 12px;
            opacity: 0.6;
        }

        .sidebar-menu .attempt-badge {
            position: absolute;
            top: 4px;
            right: 4px;
            background-color: #ef4444;
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: 700;
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
            min-height: 100vh;
            width: calc(100% - 280px);
            background: var(--bg-primary);
            overflow-x: hidden;
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

        /* AI Verification Modal */
        .ai-overlay {
            display: none !important;
            position: fixed; 
            inset: 0;
            background: rgba(0,0,0,0.6);
            backdrop-filter: blur(6px);
            z-index: 9999;
            justify-content: center; 
            align-items: center;
        }

        .ai-overlay.active { display: flex !important; }

        .ai-modal {
            background: #fff; border-radius: 20px;
            padding: 48px 40px; text-align: center;
            max-width: 440px; width: 90%;
            box-shadow: 0 25px 60px rgba(0,0,0,0.3);
            animation: modalPop 0.4s ease;
        }

        body.dark-mode .ai-modal { background: var(--bg-secondary); }

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
            .sidebar-menu button, .sidebar-menu .section-btn { padding: 11px 14px; font-size: 13px; }

            .verified-hero { padding: 28px 20px; }
            .verified-hero h2 { font-size: 20px; }
            .verified-hero .hero-icon { font-size: 40px; }
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Mobile Hamburger Toggle -->
    <button class="mobile-toggle" id="mobileToggle" onclick="toggleSidebar()">☰</button>
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

    <x-header :user="Auth::user()" :isVerified="Auth::user()->isVerified()" :verificationStatus="Auth::user()->verification_status ?? 'unverified'" />

    <div class="main-content">
        <!-- Top Header -->
        <div class="top-header">
            <div class="header-title">Welcome, {{ Auth::user()->name }}!</div>
            <div class="header-actions">
                <button class="dark-toggle" onclick="toggleDarkMode()" title="Toggle dark mode">🌙</button>
                <form method="POST" action="/logout" class="logout-form">
                    @csrf
                    <button type="submit">Logout</button>
                </form>
            </div>
        </div>

        <div class="content">
            @yield('content')
        </div>
    </div>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('open');
            document.getElementById('sidebarOverlay').classList.toggle('active');
        }
        function closeSidebar() {
            document.getElementById('sidebar').classList.remove('open');
            document.getElementById('sidebarOverlay').classList.remove('active');
        }
        function toggleDarkMode() {
            document.body.classList.toggle('dark-mode');
            localStorage.setItem('dealerDarkMode', document.body.classList.contains('dark-mode'));
        }
        if (localStorage.getItem('dealerDarkMode') === 'true') {
            document.body.classList.add('dark-mode');
        }

        // Section switching (if needed on the same page)
        function switchSection(section) {
            const sectionEl = document.getElementById(section);
            if (sectionEl) {
                document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
                document.querySelectorAll('.section-btn').forEach(b => b.classList.remove('active'));
                sectionEl.classList.add('active');
                const btn = document.querySelector(`[data-section="${section}"]`);
                if (btn) btn.classList.add('active');
                if (window.innerWidth <= 768) closeSidebar();
            } else {
                // If section not on current page, redirect to dashboard with section param
                window.location.href = "{{ route('dealer.dashboard') }}?section=" + section;
            }
        }
    </script>
    @stack('scripts')
</body>
</html>
