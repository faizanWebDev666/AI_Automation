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
            padding: 8px 10px;
            box-shadow: none;
        }

        /* Message actions + metadata */
        .msg-bubble { position: relative; }

        .msg-quote {
            font-size: 12px;
            line-height: 1.35;
            margin-bottom: 8px;
            padding: 6px 10px;
            border-radius: 14px;
            background: rgba(99,102,241,0.08);
            border-left: 3px solid rgba(99,102,241,0.7);
            color: var(--text-secondary);
        }

        .msg-row.sent .msg-quote {
            background: rgba(255,255,255,0.18);
            border-left-color: rgba(255,255,255,0.65);
            color: rgba(255,255,255,0.95);
        }

        .msg-quote .quote-label {
            font-weight: 600;
            margin-right: 6px;
        }

        .msg-forwarded-badge {
            font-size: 11px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 999px;
            background: rgba(148,163,184,0.16);
            color: var(--text-tertiary);
            margin-bottom: 8px;
        }

        .msg-row.sent .msg-forwarded-badge {
            background: rgba(255,255,255,0.18);
            color: rgba(255,255,255,0.95);
        }

        .msg-actions {
            position: absolute;
            top: -10px;
            right: 6px;
            z-index: 20;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.15s;
        }

        .msg-row:hover .msg-actions {
            opacity: 1;
            pointer-events: auto;
        }

        .msg-more {
            width: 30px;
            height: 30px;
            border-radius: 10px;
            border: 1px solid rgba(148,163,184,0.35);
            background: rgba(255,255,255,0.75);
            color: var(--text-secondary);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .msg-row.sent .msg-more {
            background: rgba(0,0,0,0.15);
            border-color: rgba(255,255,255,0.35);
            color: rgba(255,255,255,0.95);
        }

        .msg-more:hover { filter: brightness(0.95); }

        .msg-menu {
            position: absolute;
            top: 36px;
            right: 0;
            width: 190px;
            background: var(--bg-content);
            border: 1px solid var(--border-color);
            border-radius: 14px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
            padding: 6px;
            display: none;
        }

        .msg-actions.open .msg-menu { display: block; }

        .msg-menu button {
            width: 100%;
            border: none;
            background: transparent;
            color: var(--text-primary);
            font-family: 'Inter', sans-serif;
            font-size: 13px;
            font-weight: 500;
            padding: 10px 10px;
            border-radius: 10px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: background 0.15s;
            text-align: left;
        }

        .msg-menu button:hover { background: var(--bg-light); }
        .msg-menu button:disabled { opacity: 0.45; cursor: not-allowed; }
        .msg-menu .danger:hover { background: rgba(220,38,38,0.12); color: #dc2626; }

        /* Reply preview bar */
        .reply-preview-bar {
            display: none;
            align-items: center;
            gap: 10px;
            padding: 8px 20px;
            background: var(--bg-light);
            border-top: 1px solid var(--border-color);
        }

        .reply-preview-bar.show { display: flex; }

        .reply-preview-bar .reply-preview-meta {
            flex: 1;
            min-width: 0;
        }

        .reply-preview-title {
            font-size: 12px;
            font-weight: 600;
            color: var(--text-tertiary);
            margin-bottom: 2px;
        }

        .reply-preview-text {
            font-size: 13px;
            color: var(--text-secondary);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .reply-close {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: none;
            background: transparent;
            cursor: pointer;
            color: var(--text-secondary);
        }

        .reply-close:hover { background: var(--bg-content); color: #dc2626; }

        /* Modals */
        .modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.6);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            padding: 16px;
        }

        .modal-backdrop.show { display: flex; }

        .modal {
            width: 100%;
            max-width: 560px;
            background: var(--bg-content);
            border: 1px solid var(--border-color);
            border-radius: 18px;
            box-shadow: 0 18px 60px rgba(0,0,0,0.35);
            overflow: hidden;
        }

        .modal-header {
            padding: 14px 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            border-bottom: 1px solid var(--border-color);
        }

        .modal-header h3 {
            font-size: 14px;
            font-weight: 700;
            color: var(--text-primary);
        }

        .modal-body { padding: 16px 18px; }

        .modal-body label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-tertiary);
            margin-bottom: 8px;
        }

        .modal-body input[type="text"],
        .modal-body textarea,
        .modal-body select {
            width: 100%;
            background: var(--bg-light);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 12px 14px;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            color: var(--text-primary);
            outline: none;
            transition: border-color 0.15s, box-shadow 0.15s;
        }

        .modal-body textarea { min-height: 110px; resize: vertical; }

        .modal-body input[type="text"]:focus,
        .modal-body textarea:focus,
        .modal-body select:focus {
            border-color: var(--accent-primary);
            box-shadow: 0 0 0 3px rgba(99,102,241,0.08);
        }

        .modal-preview {
            padding: 12px 14px;
            border-radius: 14px;
            border: 1px dashed rgba(99,102,241,0.35);
            background: rgba(99,102,241,0.06);
            margin-bottom: 14px;
            color: var(--text-secondary);
            font-size: 13px;
        }

        .modal-actions {
            padding: 14px 18px;
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            border-top: 1px solid var(--border-color);
        }

        .btn-secondary {
            background: var(--bg-light);
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            padding: 10px 14px;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 600;
            font-size: 13px;
            transition: transform 0.15s, background 0.15s;
        }

        .btn-secondary:hover { background: var(--border-color); transform: translateY(-1px); }

        .btn-primary {
            background: var(--accent-primary);
            border: none;
            color: #fff;
            padding: 10px 14px;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 700;
            font-size: 13px;
            transition: transform 0.15s, background 0.15s;
        }

        .btn-primary:hover { background: var(--accent-primary-hover); transform: translateY(-1px); }

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

        /* Forward/Reply UI */
        .msg-bubble .msg-text { white-space: pre-wrap; }
        .msg-edited-badge { font-weight: 700; color: rgba(255,255,255,0.85); margin-left: 6px; }
        .msg-row.received .msg-edited-badge { color: var(--text-tertiary); }

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

                <!-- Reply preview bar -->
                <div class="reply-preview-bar" id="reply-preview-bar">
                    <div class="reply-preview-meta">
                        <div class="reply-preview-title">Replying to</div>
                        <div class="reply-preview-text" id="reply-preview-text"></div>
                    </div>
                    <button class="reply-close" type="button" onclick="clearReplyContext()" title="Cancel reply">✕</button>
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

    <!-- Edit modal -->
    <div class="modal-backdrop" id="edit-modal-backdrop" role="dialog" aria-modal="true">
        <div class="modal">
            <div class="modal-header">
                <h3>Edit message</h3>
                <button class="btn-secondary" type="button" onclick="closeEditModal()" style="padding:8px 12px;">Close</button>
            </div>
            <div class="modal-body">
                <label for="edit-textarea">Message</label>
                <textarea id="edit-textarea" maxlength="2000" placeholder="Edit your message..."></textarea>
            </div>
            <div class="modal-actions">
                <button class="btn-secondary" type="button" onclick="closeEditModal()">Cancel</button>
                <button class="btn-primary" type="button" onclick="saveEditMessage()">Save</button>
            </div>
        </div>
    </div>

    <!-- Delete confirm modal -->
    <div class="modal-backdrop" id="delete-modal-backdrop" role="dialog" aria-modal="true">
        <div class="modal">
            <div class="modal-header">
                <h3>Delete message</h3>
                <button class="btn-secondary" type="button" onclick="closeDeleteModal()" style="padding:8px 12px;">Close</button>
            </div>
            <div class="modal-body">
                <div class="modal-preview" id="delete-preview">
                    This action cannot be undone.
                </div>
            </div>
            <div class="modal-actions">
                <button class="btn-secondary" type="button" onclick="closeDeleteModal()">Cancel</button>
                <button class="btn-primary" type="button" onclick="confirmDeleteMessage()">Delete</button>
            </div>
        </div>
    </div>

    <!-- Forward modal -->
    <div class="modal-backdrop" id="forward-modal-backdrop" role="dialog" aria-modal="true">
        <div class="modal">
            <div class="modal-header">
                <h3>Forward message</h3>
                <button class="btn-secondary" type="button" onclick="closeForwardModal()" style="padding:8px 12px;">Close</button>
            </div>
            <div class="modal-body">
                <div class="modal-preview" id="forward-preview">Select a message to forward.</div>
                <label for="forward-recipient-select">Send to</label>
                <select id="forward-recipient-select"></select>
            </div>
            <div class="modal-actions">
                <button class="btn-secondary" type="button" onclick="closeForwardModal()">Cancel</button>
                <button class="btn-primary" type="button" onclick="submitForwardMessage()">Forward</button>
            </div>
        </div>
    </div>

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
        let replyContext = null; // { messageId, previewText }
        let editMessageId = null;
        let deleteMessageId = null;
        let forwardMessageId = null;

        // Init
        loadContacts();
        connectWebSocket();

        // Enable Pusher debug logging only if broadcasting is enabled
        if ('{{ env("BROADCAST_CONNECTION", "null") }}' !== 'null') {
            Pusher.logToConsole = true;
        } else {
            console.log('[Polling] Broadcasting disabled. Starting polling for real-time updates...');
            startPolling();
        }

        // ---- Polling (fallback when broadcasting is disabled) ----
        let pollingInterval = null;

        function startPolling() {
            // Poll every 2 seconds
            pollingInterval = setInterval(() => {
                if (selectedUserId) {
                    // Silently reload messages for current conversation
                    fetch('/chat/messages/' + selectedUserId)
                        .then(res => res.json())
                        .then(msgs => {
                            // Check if new messages arrived
                            const container = document.getElementById('messages-container');
                            const currentMsgIds = Array.from(container.querySelectorAll('[data-message-id]'))
                                .map(el => el.dataset.messageId)
                                .filter(id => id);
                            
                            // Check if we have new messages that aren't in the DOM yet
                            const newMessages = msgs.filter(m => m.id && !currentMsgIds.includes(String(m.id)));
                            
                            if (newMessages.length > 0) {
                                console.log('[Polling] Found', newMessages.length, 'new message(s)');
                                // Append new messages
                                newMessages.forEach(m => appendMsg(m));
                                scrollBottom();
                            }
                        })
                        .catch(err => console.log('[Polling] Error:', err.message));
                }
                
                // Always refresh contacts
                fetch('/chat/contacts')
                    .then(res => res.json())
                    .then(data => {
                        contactsData = data;
                        const q = document.getElementById('search-box').value.toLowerCase();
                        const filtered = q ? data.filter(c => c.name.toLowerCase().includes(q)) : data;
                        renderContacts(filtered);
                    })
                    .catch(err => console.log('[Polling] Error:', err.message));
            }, 2000);
        }

        function stopPolling() {
            if (pollingInterval) {
                clearInterval(pollingInterval);
                pollingInterval = null;
            }
        }

        // ---- WebSocket ----
        function connectWebSocket() {
            const broadcasterType = '{{ env("BROADCAST_CONNECTION", "null") }}';
            
            // Skip WebSocket connection if broadcaster is disabled (null)
            if (broadcasterType === 'null' || !broadcasterType) {
                console.log('[Echo] Broadcasting disabled. Real-time updates unavailable.');
                return;
            }
            
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
                        appendMsg(data);
                        scrollBottom();
                    }
                    loadContacts();
                });

                channel.listen('.message.edited', (data) => {
                    console.log('[Echo] ✏️ Message edited:', data);
                    if (data.sender_id === selectedUserId) {
                        updateMessageEdited(data);
                        loadContacts();
                    }
                });

                channel.listen('.message.deleted', (data) => {
                    console.log('[Echo] 🗑️ Message deleted:', data);
                    if (data.sender_id === selectedUserId) {
                        removeMessageById(data.id);
                        loadContacts();
                    }
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
            clearReplyContext();
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
                    msgs.forEach(m => appendMsg(m));
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

            const tempKey = 'tmp_' + Date.now() + '_' + Math.random().toString(16).slice(2);
            const replyToMessageId = replyContext?.messageId ? Number(replyContext.messageId) : null;

            // Show instantly (optimistic UI)
            appendMsg({
                id: null,
                sender_id: ME.id,
                receiver_id: selectedUserId,
                message: text,
                type: 'text',
                file_url: null,
                timestamp: new Date().toISOString(),
                reply_to_message_id: replyToMessageId,
                reply_to_message: replyContext?.previewText || null,
                forwarded_from_message_id: null,
                edited_at: null,
            }, tempKey);
            scrollBottom();

            try {
                input.value = '';
                input.focus();

                const res = await fetch('/chat/send', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                        'Accept': 'application/json',
                        'X-Socket-ID': echo ? echo.socketId() : '',
                    },
                    body: JSON.stringify({
                        receiver_id: selectedUserId,
                        message: text,
                        reply_to_message_id: replyToMessageId,
                    }),
                });

                if (!res.ok) throw new Error('Send failed');
                const saved = await res.json();
                updateMessageTempKey(tempKey, saved);
                clearReplyContext();
                // Refresh contacts to update preview
                loadContacts();
            } catch (err) {
                showToast('Failed to send message.', 'error');
                input.value = text;
                input.focus();
                removeTempMessage(tempKey);
            }
        }

        // ---- Helpers ----
        function appendMsg(msg, tempKey = null) {
            const container = document.getElementById('messages-container');
            const placeholder = container.querySelector('div[style*="text-align:center"]');
            if (placeholder) placeholder.remove();

            const isSent = (msg.sender_id === ME.id);
            const type = msg.type ?? 'text';
            const fileUrl = msg.file_url ?? null;
            const time = msg.timestamp ? new Date(msg.timestamp).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : '';

            const row = document.createElement('div');
            row.className = 'msg-row ' + (isSent ? 'sent' : 'received');

            const hasId = !!msg.id;
            row.dataset.messageId = hasId ? String(msg.id) : '';
            row.dataset.tempKey = tempKey || '';
            row.dataset.senderId = String(msg.sender_id ?? '');
            row.dataset.msgType = type;
            row.dataset.fileUrl = fileUrl || '';
            row.dataset.messagePreview = type === 'image' ? '📷 Photo' : String(msg.message ?? '');
            row.dataset.messageContent = type === 'image' ? '' : String(msg.message ?? '');

            const replyPreview = msg.reply_to_message || null;
            const forwarded = !!msg.forwarded_from_message_id;
            const editedAt = msg.edited_at || null;

            const replyHtml = replyPreview
                ? `<div class="msg-quote"><span class="quote-label">Reply:</span><span>${esc(replyPreview)}</span></div>`
                : '';

            const forwardedHtml = forwarded
                ? `<div class="msg-forwarded-badge">➡ Forwarded</div>`
                : '';

            const bubbleHtml = (type === 'image' && fileUrl)
                ? `<div class="msg-media"><img src="${esc(fileUrl)}" alt="Photo" onclick="openLightbox(this.src)" loading="lazy"></div>`
                : `<div class="msg-text">${esc(msg.message ?? '')}</div>`;

            const actionsDisabled = !hasId;

            const editButton = (isSent && type === 'text')
                ? `<button type="button" data-msg-action="edit" ${actionsDisabled ? 'disabled' : ''}>✏ Edit</button>`
                : '';

            const deleteButton = isSent
                ? `<button type="button" class="danger" data-msg-action="delete" ${actionsDisabled ? 'disabled' : ''}>🗑 Delete</button>`
                : '';

            row.innerHTML = `
                <div class="msg-bubble ${type === 'image' ? 'img-bubble' : ''}">
                    ${replyHtml}
                    ${forwardedHtml}
                    ${bubbleHtml}
                    <div class="msg-actions">
                        <button class="msg-more" type="button" data-msg-more aria-label="Message actions">⋯</button>
                        <div class="msg-menu">
                            <button type="button" data-msg-action="reply" ${actionsDisabled ? 'disabled' : ''}>↩ Reply</button>
                            <button type="button" data-msg-action="forward" ${actionsDisabled ? 'disabled' : ''}>⤴ Forward</button>
                            ${editButton}
                            ${deleteButton}
                        </div>
                    </div>
                </div>
                <div class="msg-time"><span class="msg-time-text">${time}</span>${editedAt ? '<span class="msg-edited-badge">(edited)</span>' : ''}</div>
            `;

            container.appendChild(row);
        }

        function removeTempMessage(tempKey) {
            if (!tempKey) return;
            const row = document.querySelector('.msg-row[data-temp-key="' + tempKey + '"]');
            if (row) row.remove();
        }

        function updateMessageTempKey(tempKey, saved) {
            const container = document.getElementById('messages-container');
            const selector = '.msg-row[data-temp-key="' + tempKey + '"]';
            const row = container.querySelector(selector);
            if (!row) return;

            row.dataset.messageId = String(saved.id);
            row.dataset.tempKey = '';
            row.dataset.msgType = saved.type ?? 'text';
            row.dataset.fileUrl = saved.file_url || '';
            row.dataset.messagePreview = (saved.type === 'image') ? '📷 Photo' : String(saved.message ?? '');
            row.dataset.messageContent = (saved.type === 'image') ? '' : String(saved.message ?? '');

            // Enable actions now that we have a persistent id.
            row.querySelectorAll('[data-msg-action]').forEach(btn => btn.disabled = false);

            const t = saved.timestamp ? new Date(saved.timestamp).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : '';
            const timeEl = row.querySelector('.msg-time-text');
            if (timeEl) timeEl.textContent = t;

            if ((saved.type ?? 'text') === 'image' && saved.file_url) {
                const img = row.querySelector('img');
                if (img) img.src = saved.file_url;
            } else {
                const textEl = row.querySelector('.msg-text');
                if (textEl) textEl.textContent = String(saved.message ?? '');
            }
        }

        function updateMessageEdited(data) {
            const row = document.querySelector('.msg-row[data-message-id="' + data.id + '"]');
            if (!row) return;
            if ((data.type ?? 'text') === 'text') {
                const textEl = row.querySelector('.msg-text');
                if (textEl) textEl.textContent = String(data.message ?? '');
            }

            const timeEl = row.querySelector('.msg-time');
            if (timeEl) {
                if (row.querySelector('.msg-edited-badge')) {
                    // Keep existing badge.
                } else {
                    timeEl.insertAdjacentHTML('beforeend', '<span class="msg-edited-badge">(edited)</span>');
                }
            }
        }

        function removeMessageById(id) {
            if (!id) return;
            const row = document.querySelector('.msg-row[data-message-id="' + id + '"]');
            if (row) row.remove();
        }

        function closeAllMsgMenus() {
            document.querySelectorAll('.msg-actions.open').forEach(el => el.classList.remove('open'));
        }

        function clearReplyContext() {
            replyContext = null;
            const bar = document.getElementById('reply-preview-bar');
            const textEl = document.getElementById('reply-preview-text');
            if (bar) bar.classList.remove('show');
            if (textEl) textEl.textContent = '';
        }

        function setReplyContextFromRow(row) {
            const id = row?.dataset?.messageId;
            if (!id) return;
            replyContext = { messageId: Number(id), previewText: row.dataset.messagePreview || '' };

            const bar = document.getElementById('reply-preview-bar');
            const textEl = document.getElementById('reply-preview-text');
            if (bar) bar.classList.add('show');
            if (textEl) textEl.textContent = replyContext.previewText;

            closeAllMsgMenus();
            document.getElementById('msg-input').focus();
        }

        function openEditModalFromRow(row) {
            const id = row?.dataset?.messageId;
            if (!id) return;
            editMessageId = Number(id);
            document.getElementById('edit-textarea').value = row.dataset.messageContent || '';
            document.getElementById('edit-modal-backdrop').classList.add('show');
            closeAllMsgMenus();
            setTimeout(() => document.getElementById('edit-textarea').focus(), 0);
        }

        function closeEditModal() {
            editMessageId = null;
            document.getElementById('edit-modal-backdrop').classList.remove('show');
        }

        async function saveEditMessage() {
            if (!editMessageId) return;
            const textarea = document.getElementById('edit-textarea');
            const newText = textarea.value.trim();
            if (!newText) return showToast('Message cannot be empty.', 'error');

            try {
                const res = await fetch('/chat/message/edit', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                        'Accept': 'application/json',
                        'X-Socket-ID': echo ? echo.socketId() : '',
                    },
                    body: JSON.stringify({ message_id: editMessageId, message: newText }),
                });
                if (!res.ok) throw new Error('Edit failed');

                const saved = await res.json();
                updateMessageEdited(saved);
                closeEditModal();
                showToast('Message updated.', 'success');
                loadContacts();
            } catch (err) {
                showToast('Failed to edit message.', 'error');
            }
        }

        function openDeleteModalFromRow(row) {
            const id = row?.dataset?.messageId;
            if (!id) return;
            deleteMessageId = Number(id);
            document.getElementById('delete-preview').textContent = 'Are you sure you want to delete this message?';
            document.getElementById('delete-modal-backdrop').classList.add('show');
            closeAllMsgMenus();
        }

        function closeDeleteModal() {
            deleteMessageId = null;
            document.getElementById('delete-modal-backdrop').classList.remove('show');
        }

        async function confirmDeleteMessage() {
            if (!deleteMessageId) return;
            try {
                const res = await fetch('/chat/message/' + deleteMessageId, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': CSRF,
                        'Accept': 'application/json',
                        'X-Socket-ID': echo ? echo.socketId() : '',
                    },
                });
                if (!res.ok) throw new Error('Delete failed');
                await res.json().catch(() => null);

                removeMessageById(deleteMessageId);
                closeDeleteModal();
                showToast('Message deleted.', 'success');
                loadContacts();
            } catch (err) {
                showToast('Failed to delete message.', 'error');
            }
        }

        function openForwardModalFromRow(row) {
            const id = row?.dataset?.messageId;
            if (!id) return;
            forwardMessageId = Number(id);

            const type = row.dataset.msgType;
            const fileUrl = row.dataset.fileUrl;
            const previewText = row.dataset.messagePreview || '';

            const previewEl = document.getElementById('forward-preview');
            if (type === 'image' && fileUrl) {
                previewEl.innerHTML = `<div style="display:flex;gap:12px;align-items:center;"><img src="${esc(fileUrl)}" alt="Forwarded photo" style="width:48px;height:48px;border-radius:10px;object-fit:cover;border:2px solid var(--accent-primary);"><div><div style="font-weight:700;color:var(--text-primary);margin-bottom:2px;">Photo</div><div>${esc(previewText)}</div></div></div>`;
            } else {
                previewEl.innerHTML = `<div style="font-weight:700;color:var(--text-primary);margin-bottom:6px;">Message</div><div>${esc(previewText)}</div>`;
            }

            const select = document.getElementById('forward-recipient-select');
            select.innerHTML = '';
            contactsData.forEach(c => {
                const opt = document.createElement('option');
                opt.value = c.id;
                opt.textContent = c.name;
                if (c.id === selectedUserId) opt.selected = true;
                select.appendChild(opt);
            });

            document.getElementById('forward-modal-backdrop').classList.add('show');
            closeAllMsgMenus();
        }

        function closeForwardModal() {
            forwardMessageId = null;
            document.getElementById('forward-modal-backdrop').classList.remove('show');
        }

        async function submitForwardMessage() {
            if (!forwardMessageId) return;
            const select = document.getElementById('forward-recipient-select');
            const destId = Number(select.value);
            if (!destId) return showToast('Select a recipient.', 'error');

            try {
                const res = await fetch('/chat/message/forward', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                        'Accept': 'application/json',
                        'X-Socket-ID': echo ? echo.socketId() : '',
                    },
                    body: JSON.stringify({ message_id: forwardMessageId, receiver_id: destId }),
                });
                if (!res.ok) throw new Error('Forward failed');
                await res.json().catch(() => null);

                closeForwardModal();
                showToast('Message forwarded.', 'success');
                loadContacts();
                if (destId === selectedUserId) openConversation(destId);
            } catch (err) {
                showToast('Failed to forward message.', 'error');
            }
        }

        // Close modals on backdrop click
        ['edit-modal-backdrop', 'delete-modal-backdrop', 'forward-modal-backdrop'].forEach((id) => {
            const el = document.getElementById(id);
            if (!el) return;
            el.addEventListener('click', (e) => {
                if (e.target !== el) return;
                if (id === 'edit-modal-backdrop') return closeEditModal();
                if (id === 'delete-modal-backdrop') return closeDeleteModal();
                if (id === 'forward-modal-backdrop') return closeForwardModal();
            });
        });

        // ---- Message action handling ----
        document.getElementById('messages-container').addEventListener('click', (e) => {
            const moreBtn = e.target.closest('[data-msg-more]');
            if (moreBtn) {
                e.stopPropagation();
                const actions = moreBtn.closest('.msg-actions');
                const isOpen = actions.classList.contains('open');
                closeAllMsgMenus();
                if (!isOpen) actions.classList.add('open');
                return;
            }

            const actionBtn = e.target.closest('[data-msg-action]');
            if (!actionBtn) return;

            const row = actionBtn.closest('.msg-row');
            if (!row) return;
            if (actionBtn.disabled) {
                showToast('This message is not ready yet.', 'error');
                return;
            }

            const action = actionBtn.dataset.msgAction;
            closeAllMsgMenus();

            if (action === 'reply') return setReplyContextFromRow(row);
            if (action === 'forward') return openForwardModalFromRow(row);
            if (action === 'edit') return openEditModalFromRow(row);
            if (action === 'delete') return openDeleteModalFromRow(row);
        });

        document.addEventListener('click', (e) => {
            if (!e.target.closest('.msg-actions')) closeAllMsgMenus();
        });

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

            const tempKey = 'tmp_' + Date.now() + '_' + Math.random().toString(16).slice(2);
            const replyToMessageId = replyContext?.messageId ? Number(replyContext.messageId) : null;

            const formData = new FormData();
            formData.append('receiver_id', selectedUserId);
            formData.append('image', pendingImage);
            if (replyToMessageId) formData.append('reply_to_message_id', String(replyToMessageId));

            // Show preview instantly
            const tempUrl = document.getElementById('preview-thumb').src;
            appendMsg({
                id: null,
                sender_id: ME.id,
                receiver_id: selectedUserId,
                message: '📷 Photo',
                type: 'image',
                file_url: tempUrl,
                timestamp: new Date().toISOString(),
                reply_to_message_id: replyToMessageId,
                reply_to_message: replyContext?.previewText || null,
                forwarded_from_message_id: null,
                edited_at: null,
            }, tempKey);
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
                const saved = await res.json();
                updateMessageTempKey(tempKey, saved);
                clearReplyContext();
                loadContacts();
                showToast('Photo sent!', 'success');
            } catch (err) {
                showToast('Failed to send photo.', 'error');
                removeTempMessage(tempKey);
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
