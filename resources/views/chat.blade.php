<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Chat — AI Automation</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --bg-main: #f0f2f5;
            --bg-content: #fff;
            --bg-light: #f8fafc;
            --border-color: #e2e8f0;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --text-tertiary: #94a3b8;
            --brand-gradient: linear-gradient(135deg, #6366f1, #ec4899);
            --accent-primary: #6366f1;
            --accent-primary-hover: #4f46e5;
            --shadow-sm: 0 1px 2px rgba(0,0,0,0.03);
            --shadow-md: 0 1px 3px rgba(0,0,0,0.04);
        }

        .dark-mode {
            --bg-main: #121212;
            --bg-content: #1e1e1e;
            --bg-light: #333;
            --border-color: #333;
            --text-primary: #e0e0e0;
            --text-secondary: #94a3b8;
            --text-tertiary: #64748b;
            --accent-primary: #4a90e2;
            --accent-primary-hover: #357abd;
        }

        body {
            font-family: 'Inter', sans-serif;
            height: 100vh;
            background: var(--bg-main);
            color: var(--text-primary);
            overflow: hidden;
            transition: background 0.3s, color 0.3s;
        }

        /* Dark Mode */
        body.dark-mode {
            background: #121212;
            color: #e0e0e0;
        }

        .dark-mode .top-nav, .dark-mode .sidebar, .dark-mode .conv-header, .dark-mode .msg-input-bar {
            background: #1e1e1e;
            border-color: #333;
        }

        .dark-mode .top-nav .brand, .dark-mode .sidebar-header h2, .dark-mode .contact-info .name, .dark-mode .conv-header .conv-name, .dark-mode .msg-input-bar input {
            color: #e0e0e0;
        }

        .dark-mode .top-nav .user-name, .dark-mode .contact-info .time, .dark-mode .contact-info .preview, .dark-mode .conv-header .conv-status, .dark-mode .msg-time {
            color: #94a3b8;
        }

        .dark-mode .btn-logout { 
            background: #333;
            border-color: #555;
            color: #e0e0e0;
        }

        .dark-mode .btn-logout:hover { background: #555; color: #fff; }

        .dark-mode .search-box {
            background: #333;
            border-color: #555;
            color: #e0e0e0;
        }

        .dark-mode .search-box:focus { border-color: #4a90e2; box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1); }

        .dark-mode .contact-item:hover { background: #333; }
        .dark-mode .contact-item.active { background: #2a2a2a; }

        .dark-mode .msg-row.received .msg-bubble {
            background: #333;
            color: #e0e0e0;
        }

        .dark-mode .msg-input-bar input {
            background: #333;
            border-color: #555;
        }

        .dark-mode .msg-input-bar input:focus { border-color: #4a90e2; box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1); }


        /* Top nav */
        .top-nav {
            height: 56px;
            background: var(--bg-content);
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            box-shadow: var(--shadow-md);
            transition: background 0.3s, border-color 0.3s;
        }

        .top-nav .brand {
            font-size: 18px;
            font-weight: 700;
            background: var(--brand-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .top-nav .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .top-nav .user-name {
            font-size: 13px;
            font-weight: 500;
            color: var(--text-secondary);
        }

        .btn-logout {
            background: var(--bg-light);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 7px 16px;
            font-size: 12px;
            font-weight: 500;
            font-family: 'Inter', sans-serif;
            color: var(--text-secondary);
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-logout:hover { background: var(--border-color); color: var(--text-primary); }

        /* Theme Switcher */
        .theme-switcher {
            display: flex;
            align-items: center;
        }

        .theme-switcher .switch {
            position: relative;
            display: inline-block;
            width: 44px;
            height: 24px;
        }

        .theme-switcher .switch input { display: none; }

        .theme-switcher .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: 0.3s;
            border-radius: 24px;
        }

        .theme-switcher .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: 0.3s;
            border-radius: 50%;
            box-shadow: 0 1px 3px rgba(0,0,0,0.2);
        }

        .theme-switcher input:checked + .slider {
            background-color: var(--accent-primary);
        }

        .theme-switcher input:checked + .slider:before {
            transform: translateX(20px);
        }

        /* Main layout */
        .chat-layout {
            display: flex;
            height: calc(100vh - 56px);
        }

        /* Sidebar */
        .sidebar {
            width: 340px;
            min-width: 340px;
            background: var(--bg-content);
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            transition: background 0.3s, border-color 0.3s;
        }

        .sidebar-header {
            padding: 16px 20px;
            border-bottom: 1px solid var(--border-color);
        }

        .sidebar-header h2 {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 12px;
        }

        .search-box {
            width: 100%;
            background: var(--bg-light);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 13px;
            font-family: 'Inter', sans-serif;
            color: var(--text-primary);
            outline: none;
            transition: all 0.3s;
        }

        .search-box::placeholder { color: var(--text-tertiary); }
        .search-box:focus { border-color: var(--accent-primary); box-shadow: 0 0 0 3px rgba(99,102,241,0.08); }

        .contacts-list {
            flex: 1;
            overflow-y: auto;
        }

        .contacts-list::-webkit-scrollbar { width: 4px; }
        .contacts-list::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 10px; }

        .contact-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 20px;
            cursor: pointer;
            transition: background 0.15s;
            border-bottom: 1px solid var(--bg-light);
        }

        .contact-item:hover { background: var(--bg-light); }
        .contact-item.active { background: var(--accent-primary-hover); color: #fff; }
        .contact-item.active .name, .contact-item.active .time, .contact-item.active .preview { color: #fff; }

        .contact-avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: var(--brand-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            font-weight: 700;
            color: #fff;
            flex-shrink: 0;
        }

        .contact-info { flex: 1; min-width: 0; }

        .contact-info .name-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .contact-info .name {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-primary);
        }

        .contact-info .time {
            font-size: 11px;
            color: var(--text-secondary);
            flex-shrink: 0;
        }

        .contact-info .preview {
            font-size: 13px;
            color: var(--text-secondary);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-top: 2px;
        }

        .unread-badge {
            background: var(--accent-primary);
            color: #fff;
            font-size: 11px;
            font-weight: 600;
            min-width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .no-contacts {
            padding: 40px 20px;
            text-align: center;
            color: var(--text-secondary);
            font-size: 14px;
        }

        /* Chat panel */
        .chat-panel {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: var(--bg-main);
        }

        /* Empty state */
        .chat-empty {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 12px;
            color: var(--text-secondary);
        }

        .chat-empty .icon { font-size: 56px; }
        .chat-empty h3 { font-size: 20px; font-weight: 600; color: var(--text-secondary); }
        .chat-empty p { font-size: 14px; }

        /* Conversation header */
        .conv-header {
            height: 64px;
            background: var(--bg-content);
            border-bottom: 1px solid var(--border-color);
            padding: 0 24px;
            display: flex;
            align-items: center;
            gap: 14px;
            box-shadow: var(--shadow-sm);
            transition: background 0.3s, border-color 0.3s;
        }

        .conv-header .conv-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--brand-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            font-weight: 700;
            color: #fff;
        }

        .conv-header .conv-name { font-size: 15px; font-weight: 600; color: var(--text-primary); }
        .conv-header .conv-status { font-size: 12px; color: var(--text-secondary); }

        /* Messages */
        .messages-container {
            flex: 1;
            overflow-y: auto;
            padding: 20px 60px;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .messages-container::-webkit-scrollbar { width: 5px; }
        .messages-container::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }

        .msg-row {
            display: flex;
            flex-direction: column;
            max-width: 65%;
            animation: msgIn 0.3s cubic-bezier(0.25, 1, 0.5, 1);
        }

        @keyframes msgIn {
            from { opacity: 0; transform: translateY(10px) scale(0.98); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        .msg-row.sent { align-self: flex-end; align-items: flex-end; }
        .msg-row.received { align-self: flex-start; align-items: flex-start; }

        .msg-bubble {
            padding: 10px 16px;
            border-radius: 18px;
            font-size: 14px;
            line-height: 1.5;
            word-break: break-word;
            box-shadow: var(--shadow-sm);
        }

        .msg-row.sent .msg-bubble {
            background: var(--accent-primary);
            color: #fff;
            border-bottom-right-radius: 4px;
        }

        .msg-row.received .msg-bubble {
            background: var(--bg-content);
            color: var(--text-primary);
            border-bottom-left-radius: 4px;
        }

        .msg-time {
            font-size: 10px;
            color: var(--text-secondary);
            margin-top: 3px;
            padding: 0 6px;
        }

        /* Input bar */
        .msg-input-bar {
            background: var(--bg-content);
            border-top: 1px solid var(--border-color);
            padding: 14px 24px;
            display: flex;
            gap: 12px;
            align-items: center;
            transition: background 0.3s, border-color 0.3s;
        }

        .msg-input-bar input {
            flex: 1;
            background: var(--bg-light);
            border: 1px solid var(--border-color);
            border-radius: 24px;
            padding: 12px 20px;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            color: var(--text-primary);
            outline: none;
            transition: all 0.3s;
        }

        .msg-input-bar input::placeholder { color: var(--text-tertiary); }
        .msg-input-bar input:focus { border-color: var(--accent-primary); box-shadow: 0 0 0 3px rgba(99,102,241,0.08); }

        .btn-send {
            background: var(--accent-primary);
            border: none;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
            flex-shrink: 0;
        }

        .btn-send:hover { background: var(--accent-primary-hover); transform: scale(1.05); }
        .btn-send svg { fill: #fff; width: 20px; height: 20px; }

        .btn-icon {
            background: transparent;
            border: none;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
            flex-shrink: 0;
            color: var(--text-secondary);
        }

        .btn-icon:hover { background: var(--bg-light); color: var(--accent-primary); }
        .btn-icon svg { fill: currentColor; width: 22px; height: 22px; }

        /* Emoji Picker */
        .emoji-picker-panel {
            position: absolute;
            bottom: 72px;
            left: 12px;
            width: 320px;
            max-height: 360px;
            background: var(--bg-content);
            border: 1px solid var(--border-color);
            border-radius: 14px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.12);
            z-index: 50;
            display: none;
            flex-direction: column;
            overflow: hidden;
        }

        .emoji-picker-panel.show { display: flex; }

        .emoji-categories {
            display: flex;
            gap: 2px;
            padding: 10px 12px 6px;
            border-bottom: 1px solid var(--border-color);
            overflow-x: auto;
        }

        .emoji-cat-btn {
            background: none;
            border: none;
            font-size: 18px;
            cursor: pointer;
            padding: 4px 8px;
            border-radius: 8px;
            transition: background 0.15s;
            flex-shrink: 0;
        }

        .emoji-cat-btn:hover, .emoji-cat-btn.active { background: var(--bg-light); }

        .emoji-grid {
            display: grid;
            grid-template-columns: repeat(8, 1fr);
            gap: 2px;
            padding: 10px;
            overflow-y: auto;
            max-height: 280px;
        }

        .emoji-grid::-webkit-scrollbar { width: 4px; }
        .emoji-grid::-webkit-scrollbar-thumb { background: #ccc; border-radius: 8px; }

        .emoji-btn {
            background: none;
            border: none;
            font-size: 22px;
            cursor: pointer;
            padding: 4px;
            border-radius: 6px;
            transition: background 0.15s, transform 0.1s;
            text-align: center;
        }

        .emoji-btn:hover { background: var(--bg-light); transform: scale(1.2); }

        /* Image bubble */
        .msg-bubble img {
            max-width: 240px;
            max-height: 240px;
            border-radius: 12px;
            cursor: pointer;
            display: block;
            object-fit: cover;
            transition: opacity 0.3s;
        }

        .msg-bubble img:hover { opacity: 0.9; }

        .msg-row.sent .msg-bubble.img-bubble,
        .msg-row.received .msg-bubble.img-bubble {
            background: transparent;
            padding: 4px;
            box-shadow: none;
        }

        /* Image preview bar */
        .image-preview-bar {
            display: none;
            align-items: center;
            gap: 10px;
            padding: 8px 20px;
            background: var(--bg-light);
            border-top: 1px solid var(--border-color);
        }

        .image-preview-bar.show { display: flex; }

        .image-preview-bar img {
            width: 48px;
            height: 48px;
            border-radius: 8px;
            object-fit: cover;
            border: 2px solid var(--accent-primary);
        }

        .image-preview-bar .preview-name {
            flex: 1;
            font-size: 13px;
            color: var(--text-secondary);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .image-preview-bar .btn-cancel-img {
            background: none;
            border: none;
            font-size: 18px;
            cursor: pointer;
            color: var(--text-secondary);
            padding: 4px;
            border-radius: 6px;
            transition: color 0.2s;
        }

        .image-preview-bar .btn-cancel-img:hover { color: #dc2626; }

        /* Lightbox */
        .lightbox {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.85);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 999;
            cursor: zoom-out;
        }

        .lightbox.show { display: flex; }

        .lightbox img {
            max-width: 90vw;
            max-height: 90vh;
            border-radius: 8px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.5);
        }

        /* Toast */
        .toast {
            position: fixed; bottom: 24px; right: 24px; padding: 12px 22px; border-radius: 10px;
            font-size: 13px; font-weight: 500; font-family: 'Inter', sans-serif; color: #fff;
            opacity: 0; transform: translateY(16px); transition: all 0.35s; pointer-events: none; z-index: 999;
        }
        .toast.show { opacity: 1; transform: translateY(0); }
        .toast.success { background: #059669; }
        .toast.error { background: #dc2626; }

        @media (max-width: 768px) {
            .sidebar { width: 100%; min-width: unset; }
            .chat-panel { display: none; }
            .chat-layout.conv-open .sidebar { display: none; }
            .chat-layout.conv-open .chat-panel { display: flex; }
            .messages-container { padding: 16px; }
            .conv-header { padding: 0 16px; }
            .emoji-picker-panel { width: 280px; }
        }
    </style>
</head>
<body>
    <!-- Top nav -->
    <nav class="top-nav">
        <div class="brand">💬 ChatApp</div>
        <div class="theme-switcher">
            <label class="switch">
                <input type="checkbox" id="theme-toggle">
                <span class="slider round"></span>
            </label>
        </div>
        <div class="user-info">
            <span class="user-name">{{ Auth::user()->name }}</span>
            <form method="POST" action="/logout" style="margin:0">
                @csrf
                <button type="submit" class="btn-logout" title="Logout">Logout</button>
            </form>
        </div>
    </nav>

    <div class="chat-layout" id="chat-layout">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>Chats</h2>
                <input type="text" class="search-box" id="search-box" placeholder="🔍  Search contacts..." oninput="filterContacts()">
            </div>
            <div class="contacts-list" id="contacts-list">
                <div class="no-contacts" id="no-contacts">Loading contacts...</div>
            </div>
        </aside>

        <!-- Chat panel -->
        <main class="chat-panel" id="chat-panel">
            <div class="chat-empty" id="chat-empty">
                <div class="icon">💬</div>
                <h3>Select a conversation</h3>
                <p>Choose a contact to start chatting</p>
            </div>

            <!-- Conversation (hidden initially) -->
            <div id="conv-wrapper" style="display:none; flex-direction:column; height:100%;">
                <div class="conv-header">
                    <div class="conv-avatar" id="conv-avatar"></div>
                    <div>
                        <div class="conv-name" id="conv-name"></div>
                        <div class="conv-status">Online</div>
                    </div>
                </div>

                <div class="messages-container" id="messages-container"></div>

                <!-- Image preview bar -->
                <div class="image-preview-bar" id="image-preview-bar">
                    <img id="preview-thumb" src="" alt="preview">
                    <span class="preview-name" id="preview-name"></span>
                    <button class="btn-cancel-img" onclick="cancelImage()" title="Remove">✕</button>
                    <button class="btn-send" onclick="sendImageMsg()" title="Send Photo" style="width:36px;height:36px;">
                        <svg viewBox="0 0 24 24"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
                    </button>
                </div>

                <div class="msg-input-bar" style="position:relative;">
                    <!-- Emoji picker panel -->
                    <div class="emoji-picker-panel" id="emoji-picker"></div>

                    <button class="btn-icon" id="btn-emoji" title="Emojis" onclick="toggleEmojiPicker()">
                        <svg viewBox="0 0 24 24"><path d="M12,2A10,10,0,1,0,22,12,10,10,0,0,0,12,2Zm0,18a8,8,0,1,1,8-8A8,8,0,0,1,12,20ZM8.5,14a1.5,1.5,0,1,1,1.5-1.5A1.5,1.5,0,0,1,8.5,14Zm7,0a1.5,1.5,0,1,1,1.5-1.5A1.5,1.5,0,0,1,15.5,14ZM12,17.5a4.5,4.5,0,0,1-4.15-2.52.5.5,0,0,1,.8-.6A3.5,3.5,0,0,0,12,15.5a3.5,3.5,0,0,0,3.35-.88.5.5,0,0,1,.8.6A4.5,4.5,0,0,1,12,17.5Z"/></svg>
                    </button>
                    <button class="btn-icon" id="btn-attach" title="Send Photo" onclick="document.getElementById('file-input').click()">
                        <svg viewBox="0 0 24 24"><path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/></svg>
                    </button>
                    <input type="text" id="msg-input" placeholder="Type a message..." maxlength="2000" autocomplete="off">
                    <input type="file" id="file-input" accept="image/jpeg,image/png,image/gif,image/webp" style="display:none;">
                    <button class="btn-send" onclick="sendMessage()" title="Send Message">
                        <svg viewBox="0 0 24 24"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
                    </button>
                </div>
            </div>
        </main>
    </div>

    <!-- Lightbox -->
    <div class="lightbox" id="lightbox" onclick="this.classList.remove('show')">
        <img id="lightbox-img" src="" alt="Full size">
    </div>

    <div class="toast" id="toast"></div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/pusher-js@8.4.0/dist/web/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@2.1.3/dist/echo.iife.js"></script>

    <script>
        const CSRF = document.querySelector('meta[name="csrf-token"]').content;
        const ME = @json(Auth::user()->only('id', 'name'));
        const EchoClass = window.Echo.default || window.Echo;

        let echo = null;
        let selectedUserId = null;
        let contactsData = [];

        // ---- Init ----
        loadContacts();
        connectWebSocket();

        // Enable Pusher debug logging
        Pusher.logToConsole = true;

        // ---- WebSocket ----
        function connectWebSocket() {
            try {
                echo = new EchoClass({
                    broadcaster: 'reverb',
                    key: '{{ env("REVERB_APP_KEY") }}',
                    wsHost: '{{ env("REVERB_HOST", "localhost") }}',
                    wsPort: {{ env("REVERB_PORT", 8080) }},
                    wssPort: {{ env("REVERB_PORT", 8080) }},
                    forceTLS: false,
                    enabledTransports: ['ws', 'wss'],
                    disableStats: true,
                    authEndpoint: '/broadcasting/auth',
                    auth: {
                        headers: { 'X-CSRF-TOKEN': CSRF }
                    }
                });

                // Debug connection states
                echo.connector.pusher.connection.bind('state_change', (states) => {
                    console.log('[Echo] Connection state:', states.previous, '→', states.current);
                });

                echo.connector.pusher.connection.bind('connected', () => {
                    console.log('[Echo] ✅ Connected! Socket ID:', echo.socketId());
                });

                echo.connector.pusher.connection.bind('error', (err) => {
                    console.error('[Echo] ❌ Connection error:', err);
                });

                const channel = echo.private('chat.' + ME.id);

                channel.listen('.message.sent', (data) => {
                    console.log('[Echo] 📩 Message received:', data);
                    if (data.sender_id === selectedUserId) {
                        appendMsg(data.message, data.timestamp, false, data.type, data.file_url);
                        scrollBottom();
                        fetch('/chat/messages/' + data.sender_id);
                    }
                    loadContacts();
                });

                channel.error((err) => {
                    console.error('[Echo] ❌ Channel subscription error:', err);
                });

            } catch (err) {
                console.error('WebSocket error:', err);
            }
        }

        // ---- Contacts ----
        async function loadContacts() {
            try {
                const res = await fetch('/chat/contacts');
                contactsData = await res.json();
                renderContacts(contactsData);
            } catch (err) {
                console.error('Failed to load contacts:', err);
            }
        }

        function renderContacts(list) {
            const container = document.getElementById('contacts-list');
            if (list.length === 0) {
                container.innerHTML = '<div class="no-contacts">No users found. Register another account to start chatting!</div>';
                return;
            }

            container.innerHTML = list.map(c => `
                <div class="contact-item ${c.id === selectedUserId ? 'active' : ''}" onclick="openConversation(${c.id})">
                    <div class="contact-avatar">${c.name.charAt(0).toUpperCase()}</div>
                    <div class="contact-info">
                        <div class="name-row">
                            <span class="name">${esc(c.name)}</span>
                            <span class="time">${c.last_time ? timeAgo(c.last_time) : ''}</span>
                        </div>
                        <div class="preview">${c.last_message ? esc(c.last_message) : '<i style="color:#c0c0c0">No messages yet</i>'}</div>
                    </div>
                    ${c.unread > 0 ? `<div class="unread-badge">${c.unread}</div>` : ''}
                </div>
            `).join('');
        }

        function filterContacts() {
            const q = document.getElementById('search-box').value.toLowerCase();
            const filtered = contactsData.filter(c => c.name.toLowerCase().includes(q));
            renderContacts(filtered);
        }

        // ---- Conversation ----
        async function openConversation(userId) {
            selectedUserId = userId;
            const user = contactsData.find(c => c.id === userId);
            if (!user) return;

            // Show conversation panel
            document.getElementById('chat-empty').style.display = 'none';
            const wrapper = document.getElementById('conv-wrapper');
            wrapper.style.display = 'flex';

            // Mobile: show chat panel
            document.getElementById('chat-layout').classList.add('conv-open');

            // Set header
            document.getElementById('conv-avatar').textContent = user.name.charAt(0).toUpperCase();
            document.getElementById('conv-name').textContent = user.name;

            // Load messages
            const container = document.getElementById('messages-container');
            container.innerHTML = '<div style="text-align:center;color:#94a3b8;padding:20px;">Loading...</div>';

            try {
                const res = await fetch('/chat/messages/' + userId);
                const msgs = await res.json();
                container.innerHTML = '';

                if (msgs.length === 0) {
                    container.innerHTML = '<div style="text-align:center;color:#94a3b8;padding:40px;font-size:14px;">No messages yet. Say hi! 👋</div>';
                } else {
                    msgs.forEach(m => appendMsg(m.message, m.timestamp, m.sender_id === ME.id, m.type, m.file_url));
                }
                scrollBottom();
            } catch (err) {
                container.innerHTML = '<div style="text-align:center;color:#dc2626;padding:20px;">Failed to load messages.</div>';
            }

            // Update sidebar active state & clear unread
            renderContacts(contactsData);
            document.getElementById('msg-input').focus();
        }

        // ---- Send ----
        async function sendMessage() {
            const input = document.getElementById('msg-input');
            const text = input.value.trim();
            if (!text || !selectedUserId) return;

            input.value = '';
            input.focus();

            // Show instantly
            appendMsg(text, new Date().toISOString(), true);
            scrollBottom();

            try {
                await fetch('/chat/send', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                        'Accept': 'application/json',
                        'X-Socket-ID': echo ? echo.socketId() : '',
                    },
                    body: JSON.stringify({ receiver_id: selectedUserId, message: text }),
                });
                // Refresh contacts to update preview
                loadContacts();
            } catch (err) {
                showToast('Failed to send message.', 'error');
            }
        }

        // ---- Helpers ----
        function appendMsg(text, timestamp, isSent, type = 'text', fileUrl = null) {
            const container = document.getElementById('messages-container');
            const placeholder = container.querySelector('div[style*="text-align:center"]');
            if (placeholder) placeholder.remove();

            const row = document.createElement('div');
            row.className = 'msg-row ' + (isSent ? 'sent' : 'received');
            const time = timestamp ? new Date(timestamp).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : '';

            if (type === 'image' && fileUrl) {
                row.innerHTML = `<div class="msg-bubble img-bubble"><img src="${esc(fileUrl)}" alt="Photo" onclick="openLightbox(this.src)" loading="lazy"></div><div class="msg-time">${time}</div>`;
            } else {
                row.innerHTML = `<div class="msg-bubble">${esc(text)}</div><div class="msg-time">${time}</div>`;
            }
            container.appendChild(row);
        }

        function scrollBottom() {
            const c = document.getElementById('messages-container');
            c.scrollTop = c.scrollHeight;
        }

        function esc(str) {
            const d = document.createElement('div');
            d.textContent = str;
            return d.innerHTML;
        }

        function timeAgo(dateStr) {
            const d = new Date(dateStr);
            const now = new Date();
            const diffMs = now - d;
            const mins = Math.floor(diffMs / 60000);
            if (mins < 1) return 'now';
            if (mins < 60) return mins + 'm';
            const hrs = Math.floor(mins / 60);
            if (hrs < 24) return hrs + 'h';
            return d.toLocaleDateString([], { month: 'short', day: 'numeric' });
        }

        function showToast(msg, type = 'success') {
            const t = document.getElementById('toast');
            t.textContent = msg;
            t.className = 'toast ' + type + ' show';
            setTimeout(() => t.classList.remove('show'), 3000);
        }

        // ---- Lightbox ----
        function openLightbox(src) {
            document.getElementById('lightbox-img').src = src;
            document.getElementById('lightbox').classList.add('show');
        }

        // ---- Emoji Picker ----
        const EMOJIS = {
            '😀': ['😀','😁','😂','🤣','😃','😄','😅','😆','😉','😊','😋','😎','🥳','😍','🥰','😘','😗','😙','😚','🙂','🤗','🤩','🤔','🤨','😐','😑','😶','🙄','😏','😣','😥','😮','🤐','😯','😪','😫','🥱','😴','😌','😛','😜','🤪','😝','🤑','🤭','🤫','🤥','😬','🙃','😇'],
            '❤️': ['❤️','🧡','💛','💚','💙','💜','🤎','🖤','🤍','💔','❣️','💕','💞','💓','💗','💖','💘','💝','💟','♥️','🫶','👍','👎','👏','🙌','👐','🤲','🤝','🙏','✌️','🤞','🫰','🤟','🤘','👌','🤌','🤏','👈','👉','👆','👇','☝️','✋','🤚','🖐️','🖖','👋','🤙'],
            '🐶': ['🐶','🐱','🐭','🐹','🐰','🦊','🐻','🐼','🐨','🐯','🦁','🐮','🐷','🐸','🐵','🐔','🐧','🐦','🦅','🦆','🦉','🐴','🦄','🐝','🐛','🦋','🐌','🐞','🐜','🪲','🐢','🐍','🦎','🐙','🐠','🐟','🐬','🐳','🦈','🐊','🦩','🌸','🌺','🌻','🌹','🌷','🌼','💐','🌳','🍀'],
            '🍕': ['🍕','🍔','🍟','🌭','🍿','🧂','🥓','🥚','🍳','🧇','🥞','🧈','🍞','🥐','🥨','🥯','🥖','🧀','🥗','🥙','🥪','🌮','🌯','🫔','🥘','🍝','🍜','🍲','🍛','🍣','🍱','🥟','🍤','🍙','🍚','🍘','🍥','🥮','🍡','🧁','🍰','🎂','🍮','🍭','🍬','🍫','🍩','🍪','🧃','☕'],
            '⚽': ['⚽','🏀','🏈','⚾','🥎','🎾','🏐','🏉','🥏','🎱','🪀','🏓','🏸','🏒','🥅','⛳','🪁','🏹','🎣','🤿','🥊','🥋','🎽','🛹','🛼','🛷','⛸️','🥌','🎿','⛷️','🏂','🪂','🏋️','🤸','⛹️','🤾','🏊','🚴','🧘','🏄','🏇','🧗','🚣','🏆','🥇','🥈','🥉','🏅','🎖️','🎗️'],
            '🚗': ['🚗','🚕','🚙','🚌','🚎','🏎️','🚓','🚑','🚒','🚐','🛻','🚚','🚛','🚜','🏍️','🛵','🚲','🛴','🛺','🚁','🛸','✈️','🚀','🛳️','⛴️','🚤','⛵','🗺️','🧭','🏖️','🏝️','⛰️','🌋','🗻','🏕️','🏠','🏡','🏢','🏬','🏥','🏦','🏨','🏩','💒','🏛️','⛪','🕌','🕍','🗼','🗽']
        };

        let emojiBuilt = false;

        function buildEmojiPicker() {
            const picker = document.getElementById('emoji-picker');
            const cats = Object.keys(EMOJIS);

            let catHtml = '<div class="emoji-categories">';
            cats.forEach((cat, i) => {
                catHtml += `<button class="emoji-cat-btn ${i === 0 ? 'active' : ''}" onclick="switchEmojiCat(${i})">${cat}</button>`;
            });
            catHtml += '</div>';

            let gridHtml = '';
            cats.forEach((cat, i) => {
                gridHtml += `<div class="emoji-grid" id="emoji-grid-${i}" style="${i > 0 ? 'display:none' : ''}">`;
                EMOJIS[cat].forEach(e => {
                    gridHtml += `<button class="emoji-btn" onclick="insertEmoji('${e}')">${e}</button>`;
                });
                gridHtml += '</div>';
            });

            picker.innerHTML = catHtml + gridHtml;
            emojiBuilt = true;
        }

        function switchEmojiCat(idx) {
            document.querySelectorAll('.emoji-grid').forEach((g, i) => g.style.display = i === idx ? 'grid' : 'none');
            document.querySelectorAll('.emoji-cat-btn').forEach((b, i) => b.classList.toggle('active', i === idx));
        }

        function toggleEmojiPicker() {
            if (!emojiBuilt) buildEmojiPicker();
            document.getElementById('emoji-picker').classList.toggle('show');
        }

        function insertEmoji(emoji) {
            const input = document.getElementById('msg-input');
            const start = input.selectionStart;
            input.value = input.value.slice(0, start) + emoji + input.value.slice(input.selectionEnd);
            input.focus();
            input.selectionStart = input.selectionEnd = start + emoji.length;
        }

        // Close emoji picker on outside click
        document.addEventListener('click', (e) => {
            const picker = document.getElementById('emoji-picker');
            const btn = document.getElementById('btn-emoji');
            if (!picker.contains(e.target) && !btn.contains(e.target)) {
                picker.classList.remove('show');
            }
        });

        // ---- Photo Upload ----
        let pendingImage = null;

        document.getElementById('file-input').addEventListener('change', function() {
            const file = this.files[0];
            if (!file) return;

            if (file.size > 5 * 1024 * 1024) {
                showToast('Image must be under 5MB.', 'error');
                this.value = '';
                return;
            }

            pendingImage = file;
            const reader = new FileReader();
            reader.onload = (e) => {
                document.getElementById('preview-thumb').src = e.target.result;
                document.getElementById('preview-name').textContent = file.name;
                document.getElementById('image-preview-bar').classList.add('show');
            };
            reader.readAsDataURL(file);
        });

        function cancelImage() {
            pendingImage = null;
            document.getElementById('file-input').value = '';
            document.getElementById('image-preview-bar').classList.remove('show');
        }

        async function sendImageMsg() {
            if (!pendingImage || !selectedUserId) return;

            const formData = new FormData();
            formData.append('receiver_id', selectedUserId);
            formData.append('image', pendingImage);

            // Show preview instantly
            const tempUrl = document.getElementById('preview-thumb').src;
            appendMsg('📷 Photo', new Date().toISOString(), true, 'image', tempUrl);
            scrollBottom();
            cancelImage();

            try {
                const res = await fetch('/chat/send-image', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': CSRF,
                        'Accept': 'application/json',
                        'X-Socket-ID': echo ? echo.socketId() : '',
                    },
                    body: formData,
                });

                if (!res.ok) throw new Error('Upload failed');
                loadContacts();
                showToast('Photo sent!', 'success');
            } catch (err) {
                showToast('Failed to send photo.', 'error');
            }
        }

        // Enter to send
        document.getElementById('msg-input').addEventListener('keydown', e => {
            if (e.key === 'Enter') { e.preventDefault(); sendMessage(); }
        });

        // Theme switcher
        const themeToggle = document.getElementById('theme-toggle');
        if (localStorage.getItem('theme') === 'dark') {
            document.body.classList.add('dark-mode');
            themeToggle.checked = true;
        }

        themeToggle.addEventListener('change', () => {
            if (themeToggle.checked) {
                document.body.classList.add('dark-mode');
                localStorage.setItem('theme', 'dark');
            } else {
                document.body.classList.remove('dark-mode');
                localStorage.setItem('theme', 'light');
            }
        });
    </script>
</body>
</html>
