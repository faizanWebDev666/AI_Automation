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

        body {
            font-family: 'Inter', sans-serif;
            height: 100vh;
            background: #f0f2f5;
            color: #1e293b;
            overflow: hidden;
        }

        /* Top nav */
        .top-nav {
            height: 56px;
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }

        .top-nav .brand {
            font-size: 18px;
            font-weight: 700;
            background: linear-gradient(135deg, #6366f1, #ec4899);
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
            color: #64748b;
        }

        .btn-logout {
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 7px 16px;
            font-size: 12px;
            font-weight: 500;
            font-family: 'Inter', sans-serif;
            color: #64748b;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-logout:hover { background: #e2e8f0; color: #1e293b; }

        /* Main layout */
        .chat-layout {
            display: flex;
            height: calc(100vh - 56px);
        }

        /* Sidebar */
        .sidebar {
            width: 340px;
            min-width: 340px;
            background: #fff;
            border-right: 1px solid #e2e8f0;
            display: flex;
            flex-direction: column;
        }

        .sidebar-header {
            padding: 16px 20px;
            border-bottom: 1px solid #f1f5f9;
        }

        .sidebar-header h2 {
            font-size: 18px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 12px;
        }

        .search-box {
            width: 100%;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 13px;
            font-family: 'Inter', sans-serif;
            color: #1e293b;
            outline: none;
            transition: all 0.3s;
        }

        .search-box::placeholder { color: #94a3b8; }
        .search-box:focus { border-color: #818cf8; box-shadow: 0 0 0 3px rgba(99,102,241,0.08); }

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
            border-bottom: 1px solid #f8fafc;
        }

        .contact-item:hover { background: #f8fafc; }
        .contact-item.active { background: #eef2ff; }

        .contact-avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: linear-gradient(135deg, #6366f1, #a78bfa);
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
            color: #1e293b;
        }

        .contact-info .time {
            font-size: 11px;
            color: #94a3b8;
            flex-shrink: 0;
        }

        .contact-info .preview {
            font-size: 13px;
            color: #94a3b8;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-top: 2px;
        }

        .unread-badge {
            background: #6366f1;
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
            color: #94a3b8;
            font-size: 14px;
        }

        /* Chat panel */
        .chat-panel {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: #f0f2f5;
        }

        /* Empty state */
        .chat-empty {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 12px;
            color: #94a3b8;
        }

        .chat-empty .icon { font-size: 56px; }
        .chat-empty h3 { font-size: 20px; font-weight: 600; color: #64748b; }
        .chat-empty p { font-size: 14px; }

        /* Conversation header */
        .conv-header {
            height: 64px;
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            padding: 0 24px;
            display: flex;
            align-items: center;
            gap: 14px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.03);
        }

        .conv-header .conv-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #6366f1, #a78bfa);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            font-weight: 700;
            color: #fff;
        }

        .conv-header .conv-name { font-size: 15px; font-weight: 600; color: #1e293b; }
        .conv-header .conv-status { font-size: 12px; color: #94a3b8; }

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
            animation: msgIn 0.2s ease;
        }

        @keyframes msgIn {
            from { opacity: 0; transform: translateY(6px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .msg-row.sent { align-self: flex-end; align-items: flex-end; }
        .msg-row.received { align-self: flex-start; align-items: flex-start; }

        .msg-bubble {
            padding: 10px 16px;
            border-radius: 16px;
            font-size: 14px;
            line-height: 1.5;
            word-break: break-word;
            box-shadow: 0 1px 2px rgba(0,0,0,0.06);
        }

        .msg-row.sent .msg-bubble {
            background: #6366f1;
            color: #fff;
            border-bottom-right-radius: 4px;
        }

        .msg-row.received .msg-bubble {
            background: #fff;
            color: #1e293b;
            border-bottom-left-radius: 4px;
        }

        .msg-time {
            font-size: 10px;
            color: #94a3b8;
            margin-top: 3px;
            padding: 0 6px;
        }

        /* Input bar */
        .msg-input-bar {
            background: #fff;
            border-top: 1px solid #e2e8f0;
            padding: 14px 24px;
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .msg-input-bar input {
            flex: 1;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 24px;
            padding: 12px 20px;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            color: #1e293b;
            outline: none;
            transition: all 0.3s;
        }

        .msg-input-bar input::placeholder { color: #94a3b8; }
        .msg-input-bar input:focus { border-color: #818cf8; box-shadow: 0 0 0 3px rgba(99,102,241,0.08); }

        .btn-send {
            background: #6366f1;
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

        .btn-send:hover { background: #4f46e5; transform: scale(1.05); }
        .btn-send svg { fill: #fff; width: 20px; height: 20px; }

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
        }
    </style>
</head>
<body>
    <!-- Top nav -->
    <nav class="top-nav">
        <div class="brand">💬 ChatApp</div>
        <div class="user-info">
            <span class="user-name">{{ Auth::user()->name }}</span>
            <form method="POST" action="/logout" style="margin:0">
                @csrf
                <button type="submit" class="btn-logout">Logout</button>
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

                <div class="msg-input-bar">
                    <input type="text" id="msg-input" placeholder="Type a message..." maxlength="2000" autocomplete="off">
                    <button class="btn-send" onclick="sendMessage()">
                        <svg viewBox="0 0 24 24"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
                    </button>
                </div>
            </div>
        </main>
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
                    // If this conversation is open, add message
                    if (data.sender_id === selectedUserId) {
                        appendMsg(data.message, data.timestamp, false);
                        scrollBottom();
                        // Mark as read
                        fetch('/chat/messages/' + data.sender_id);
                    }
                    // Update contacts sidebar
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
                    msgs.forEach(m => appendMsg(m.message, m.timestamp, m.sender_id === ME.id));
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
        function appendMsg(text, timestamp, isSent) {
            const container = document.getElementById('messages-container');
            // Remove "no messages" placeholder
            const placeholder = container.querySelector('div[style*="text-align:center"]');
            if (placeholder) placeholder.remove();

            const row = document.createElement('div');
            row.className = 'msg-row ' + (isSent ? 'sent' : 'received');
            const time = timestamp ? new Date(timestamp).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : '';
            row.innerHTML = `<div class="msg-bubble">${esc(text)}</div><div class="msg-time">${time}</div>`;
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

        // Enter to send
        document.getElementById('msg-input').addEventListener('keydown', e => {
            if (e.key === 'Enter') { e.preventDefault(); sendMessage(); }
        });
    </script>
</body>
</html>
