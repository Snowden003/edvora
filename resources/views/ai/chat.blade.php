<!DOCTYPE html>
<html lang="fa" dir="rtl" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>دستیار هوشمند ادوُرا تِک (Edvora AI) — گفتگوی پیشرفته</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    <!-- Google Fonts: Vazirmatn & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Vazirmatn:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Markdown & Highlight.js -->
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

    <style>
        :root {
            --bg-body: #030712;
            --bg-sidebar: #071224;
            --bg-card: rgba(11, 23, 48, 0.7);
            --bg-input: rgba(15, 29, 60, 0.85);
            --border-color: rgba(6, 182, 212, 0.18);
            --border-hover: rgba(6, 182, 212, 0.4);
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --primary: #00f0ff;
            --primary-glow: rgba(0, 240, 255, 0.25);
            --accent: #6366f1;
            --bubble-user: linear-gradient(135deg, #0A58CA, #4f46e5);
            --bubble-ai: rgba(15, 23, 42, 0.85);
            --font-family: 'Vazirmatn', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }

        html:not(.dark) {
            --bg-body: #f8fafc;
            --bg-sidebar: #ffffff;
            --bg-card: #ffffff;
            --bg-input: #ffffff;
            --border-color: #e2e8f0;
            --border-hover: #cbd5e1;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --primary: #0284c7;
            --primary-glow: rgba(2, 132, 199, 0.2);
            --accent: #4f46e5;
            --bubble-user: linear-gradient(135deg, #0284c7, #2563eb);
            --bubble-ai: #ffffff;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            font-family: var(--font-family);
            background-color: var(--bg-body);
            color: var(--text-main);
            min-height: 100vh;
            height: 100vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        /* Ambient Background Glow */
        .ambient-glow {
            position: fixed;
            pointer-events: none;
            z-index: 0;
            border-radius: 50%;
            filter: blur(120px);
            opacity: 0.15;
            transition: all 0.5s ease;
        }
        .glow-1 { top: -100px; right: -50px; width: 450px; height: 450px; background: #00f0ff; }
        .glow-2 { bottom: -100px; left: -50px; width: 500px; height: 500px; background: #6366f1; }

        /* App Container */
        .ai-app-wrapper {
            position: relative;
            z-index: 10;
            display: flex;
            height: 100vh;
            width: 100vw;
            overflow: hidden;
        }

        /* Sidebar Styles */
        .ai-sidebar {
            width: 320px;
            max-width: 85vw;
            background: var(--bg-sidebar);
            border-left: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            height: 100%;
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), background-color 0.3s ease;
            position: relative;
            z-index: 40;
            backdrop-filter: blur(20px);
        }

        .ai-sidebar-header {
            padding: 1.25rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
        }

        .ai-brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            color: inherit;
        }

        .ai-brand-logo {
            width: 42px;
            height: 42px;
            border-radius: 14px;
            background: linear-gradient(135deg, rgba(0, 240, 255, 0.2), rgba(99, 102, 241, 0.25));
            border: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-size: 1.25rem;
            box-shadow: 0 0 20px var(--primary-glow);
            position: relative;
        }

        .ai-brand-logo::after {
            content: '';
            position: absolute;
            bottom: -2px;
            right: -2px;
            width: 10px;
            height: 10px;
            background: #10b981;
            border-radius: 50%;
            border: 2px solid var(--bg-sidebar);
            animation: pulse-dot 2s infinite;
        }

        @keyframes pulse-dot {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.2); opacity: 0.8; }
        }

        .ai-brand-info h1 {
            font-size: 0.95rem;
            font-weight: 800;
            color: var(--text-main);
            letter-spacing: -0.01em;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .ai-brand-info .badge-gemini {
            font-size: 0.65rem;
            padding: 2px 7px;
            border-radius: 999px;
            background: linear-gradient(135deg, rgba(0, 240, 255, 0.15), rgba(99, 102, 241, 0.2));
            color: var(--primary);
            border: 1px solid var(--border-color);
            font-weight: 700;
        }

        .ai-brand-info p {
            font-size: 0.72rem;
            color: var(--text-muted);
            margin-top: 2px;
        }

        .new-chat-btn-wrap {
            padding: 1rem 1.25rem 0.5rem;
        }

        .new-chat-btn {
            width: 100%;
            padding: 0.75rem 1rem;
            border-radius: 14px;
            background: linear-gradient(135deg, rgba(0, 240, 255, 0.15), rgba(99, 102, 241, 0.25));
            border: 1px solid var(--border-color);
            color: var(--text-main);
            font-family: inherit;
            font-size: 0.85rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            cursor: pointer;
            transition: all 0.25s ease;
            box-shadow: 0 4px 15px rgba(0, 240, 255, 0.08);
        }

        .new-chat-btn:hover {
            border-color: var(--primary);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px var(--primary-glow);
            color: var(--primary);
        }

        /* Topics Pills in Sidebar */
        .sidebar-section-title {
            font-size: 0.7rem;
            font-weight: 800;
            color: var(--text-muted);
            padding: 0.85rem 1.25rem 0.4rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .topics-scroll {
            padding: 0 1rem;
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
        }

        .topic-pill {
            padding: 0.5rem 0.75rem;
            border-radius: 10px;
            font-size: 0.78rem;
            font-weight: 600;
            color: var(--text-muted);
            background: transparent;
            border: 1px solid transparent;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s ease;
            text-align: right;
        }

        .topic-pill:hover,
        .topic-pill.active {
            background: rgba(0, 240, 255, 0.08);
            border-color: var(--border-color);
            color: var(--primary);
        }

        .topic-pill.active {
            font-weight: 700;
        }

        /* History List */
        .history-list {
            flex: 1;
            overflow-y: auto;
            padding: 0.5rem 1rem;
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
        }

        .history-item {
            padding: 0.6rem 0.75rem;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 500;
            color: var(--text-muted);
            background: transparent;
            border: 1px solid transparent;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.5rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .history-item:hover,
        .history-item.active {
            background: rgba(255, 255, 255, 0.05);
            border-color: var(--border-color);
            color: var(--text-main);
        }

        html:not(.dark) .history-item:hover,
        html:not(.dark) .history-item.active {
            background: rgba(0, 0, 0, 0.04);
            border-color: #cbd5e1;
        }

        .history-title {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            flex: 1;
        }

        .history-del-btn {
            opacity: 0;
            background: none;
            border: none;
            color: #ef4444;
            font-size: 0.85rem;
            cursor: pointer;
            padding: 2px 4px;
            border-radius: 6px;
            transition: opacity 0.2s;
        }

        .history-item:hover .history-del-btn {
            opacity: 0.85;
        }

        .history-del-btn:hover {
            opacity: 1 !important;
            background: rgba(239, 68, 68, 0.15);
        }

        /* Sidebar Footer */
        .ai-sidebar-footer {
            padding: 1rem 1.25rem;
            border-top: 1px solid var(--border-color);
            background: rgba(0, 0, 0, 0.15);
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .guest-limit-card {
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.12), rgba(239, 68, 68, 0.1));
            border: 1px solid rgba(245, 158, 11, 0.25);
            border-radius: 12px;
            padding: 0.75rem;
            font-size: 0.75rem;
        }

        .guest-limit-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: #f59e0b;
            font-weight: 700;
            margin-bottom: 0.35rem;
        }

        .guest-progress-bar {
            height: 5px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 999px;
            overflow: hidden;
            margin-bottom: 0.5rem;
        }

        .guest-progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #f59e0b, #ef4444);
            border-radius: 999px;
            transition: width 0.3s ease;
        }

        .guest-login-link {
            display: block;
            text-align: center;
            padding: 0.35rem;
            border-radius: 8px;
            background: rgba(245, 158, 11, 0.2);
            color: #f59e0b;
            text-decoration: none;
            font-weight: 700;
            font-size: 0.72rem;
            transition: background 0.2s;
        }

        .guest-login-link:hover {
            background: rgba(245, 158, 11, 0.35);
        }

        .user-profile-card {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            color: inherit;
        }

        .user-avatar-wrap {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: 1.5px solid var(--border-color);
            overflow: hidden;
            background: var(--bubble-user);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.85rem;
            color: #fff;
            flex-shrink: 0;
        }

        .user-avatar-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .user-details {
            min-width: 0;
            flex: 1;
        }

        .user-details h4 {
            font-size: 0.82rem;
            font-weight: 700;
            color: var(--text-main);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .user-details span {
            font-size: 0.7rem;
            color: var(--primary);
            display: block;
        }

        /* Main Chat Area */
        .ai-main-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            height: 100%;
            min-width: 0;
            position: relative;
            background: transparent;
        }

        /* Top Navbar */
        .ai-topbar {
            height: 64px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
            background: var(--bg-card);
            backdrop-filter: blur(20px);
            position: relative;
            z-index: 20;
            flex-shrink: 0;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .sidebar-toggle-btn {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            border: 1px solid var(--border-color);
            background: transparent;
            color: var(--text-main);
            display: none;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.2s;
        }

        .sidebar-toggle-btn:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        .back-dashboard-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.45rem 0.9rem;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-color);
            color: var(--text-main);
            text-decoration: none;
            font-size: 0.8rem;
            font-weight: 600;
            transition: all 0.2s;
        }

        .back-dashboard-btn:hover {
            border-color: var(--primary);
            color: var(--primary);
            transform: translateX(2px);
        }

        .topbar-center-status {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.78rem;
            font-weight: 600;
            color: var(--text-muted);
        }

        .topbar-center-status .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #10b981;
            box-shadow: 0 0 10px #10b981;
        }

        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .action-icon-btn {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            border: 1px solid var(--border-color);
            background: transparent;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 0.95rem;
        }

        .action-icon-btn:hover {
            border-color: var(--primary);
            color: var(--primary);
            background: rgba(0, 240, 255, 0.08);
        }

        /* Chat Scroll Feed */
        .chat-feed {
            flex: 1;
            overflow-y: auto;
            padding: 1.5rem 1rem 1rem;
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
            scroll-behavior: smooth;
        }

        .chat-feed-inner {
            max-width: 860px;
            width: 100%;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        /* Empty State */
        .empty-welcome-hero {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 2.5rem 1rem;
            margin: auto;
            max-width: 680px;
            animation: fadeIn 0.4s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .hero-orb {
            width: 80px;
            height: 80px;
            border-radius: 26px;
            background: linear-gradient(135deg, rgba(0, 240, 255, 0.2), rgba(99, 102, 241, 0.35));
            border: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-size: 2.5rem;
            box-shadow: 0 0 35px var(--primary-glow);
            margin-bottom: 1.5rem;
            animation: floatOrb 4s ease-in-out infinite;
        }

        @keyframes floatOrb {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }

        .empty-welcome-hero h2 {
            font-size: 1.6rem;
            font-weight: 900;
            color: var(--text-main);
            margin-bottom: 0.5rem;
            letter-spacing: -0.02em;
        }

        .empty-welcome-hero p {
            font-size: 0.9rem;
            color: var(--text-muted);
            line-height: 1.7;
            max-width: 520px;
            margin-bottom: 2rem;
        }

        .suggestion-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0.85rem;
            width: 100%;
        }

        @media (max-width: 640px) {
            .suggestion-grid { grid-template-columns: 1fr; }
        }

        .suggestion-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 1rem 1.1rem;
            text-align: right;
            cursor: pointer;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
        }

        .suggestion-card:hover {
            border-color: var(--primary);
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 240, 255, 0.12);
        }

        .suggestion-icon {
            font-size: 1.25rem;
            color: var(--primary);
            flex-shrink: 0;
            margin-top: 2px;
        }

        .suggestion-text {
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--text-main);
            line-height: 1.5;
        }

        /* Message Bubbles */
        .chat-message-row {
            display: flex;
            gap: 0.85rem;
            animation: messageEntry 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes messageEntry {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .chat-message-row.user {
            flex-direction: row-reverse;
        }

        .msg-avatar {
            width: 36px;
            height: 36px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.85rem;
            flex-shrink: 0;
            border: 1px solid var(--border-color);
        }

        .chat-message-row.user .msg-avatar {
            background: var(--bubble-user);
            color: #fff;
        }

        .chat-message-row.ai .msg-avatar {
            background: linear-gradient(135deg, rgba(0, 240, 255, 0.2), rgba(99, 102, 241, 0.3));
            color: var(--primary);
            box-shadow: 0 0 15px var(--primary-glow);
        }

        .msg-content-box {
            max-width: 82%;
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
        }

        .chat-message-row.user .msg-content-box {
            align-items: flex-end;
        }

        .msg-bubble {
            padding: 1rem 1.25rem;
            border-radius: 18px;
            font-size: 0.88rem;
            line-height: 1.7;
            position: relative;
            word-break: break-word;
        }

        .chat-message-row.user .msg-bubble {
            background: var(--bubble-user);
            color: #ffffff;
            border-bottom-left-radius: 4px;
            box-shadow: 0 4px 15px rgba(10, 88, 202, 0.25);
        }

        .chat-message-row.ai .msg-bubble {
            background: var(--bubble-ai);
            color: var(--text-main);
            border: 1px solid var(--border-color);
            border-bottom-right-radius: 4px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(16px);
        }

        /* Markdown Styling inside Bubble */
        .msg-bubble p {
            margin-bottom: 0.65rem;
        }
        .msg-bubble p:last-child {
            margin-bottom: 0;
        }
        .msg-bubble h1, .msg-bubble h2, .msg-bubble h3 {
            margin: 0.8rem 0 0.4rem;
            font-weight: 800;
            color: var(--primary);
        }
        .msg-bubble ul, .msg-bubble ol {
            padding-right: 1.5rem;
            margin-bottom: 0.65rem;
        }
        .msg-bubble li {
            margin-bottom: 0.35rem;
        }
        .msg-bubble pre {
            background: #060d1d;
            border: 1px solid rgba(0, 240, 255, 0.2);
            border-radius: 12px;
            padding: 0.85rem 1rem;
            overflow-x: auto;
            direction: ltr;
            text-align: left;
            margin: 0.75rem 0;
            font-family: 'Courier New', Courier, monospace;
            font-size: 0.82rem;
            position: relative;
        }
        .msg-bubble code {
            font-family: 'Courier New', Courier, monospace;
            background: rgba(0, 240, 255, 0.1);
            color: var(--primary);
            padding: 2px 6px;
            border-radius: 5px;
            font-size: 0.82rem;
            direction: ltr;
            display: inline-block;
        }
        .msg-bubble pre code {
            background: transparent;
            padding: 0;
            color: #e2e8f0;
        }
        .copy-code-btn {
            position: absolute;
            top: 6px;
            right: 6px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.15);
            color: #94a3b8;
            font-size: 0.7rem;
            padding: 3px 8px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .copy-code-btn:hover {
            color: #fff;
            background: var(--primary);
            color: #000;
        }

        .msg-actions-row {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0 0.4rem;
        }

        .msg-tool-btn {
            background: none;
            border: none;
            color: var(--text-muted);
            font-size: 0.76rem;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            cursor: pointer;
            padding: 2px 6px;
            border-radius: 6px;
            transition: color 0.15s;
        }

        .msg-tool-btn:hover {
            color: var(--primary);
        }

        .msg-time {
            font-size: 0.68rem;
            color: var(--text-muted);
        }

        /* Typing indicator */
        .typing-indicator {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 0.85rem 1.25rem;
            background: var(--bubble-ai);
            border: 1px solid var(--border-color);
            border-radius: 18px;
            width: fit-content;
        }

        .typing-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--primary);
            animation: typingBounce 1.4s infinite ease-in-out both;
        }
        .typing-dot:nth-child(1) { animation-delay: -0.32s; }
        .typing-dot:nth-child(2) { animation-delay: -0.16s; }

        @keyframes typingBounce {
            0%, 80%, 100% { transform: scale(0.6); opacity: 0.4; }
            40% { transform: scale(1.1); opacity: 1; }
        }

        /* Bottom Command Input Bar */
        .chat-input-bar-wrap {
            padding: 0.85rem 1rem 1.25rem;
            background: transparent;
            position: relative;
            z-index: 20;
            flex-shrink: 0;
        }

        .chat-input-bar {
            max-width: 860px;
            margin: 0 auto;
            background: var(--bg-input);
            border: 1.5px solid var(--border-color);
            border-radius: 22px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.35);
            backdrop-filter: blur(25px);
            padding: 0.4rem 0.6rem;
            display: flex;
            align-items: flex-end;
            gap: 0.5rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .chat-input-bar:focus-within {
            border-color: var(--primary);
            box-shadow: 0 0 25px var(--primary-glow);
        }

        .chat-textarea {
            flex: 1;
            background: transparent;
            border: none;
            outline: none;
            color: var(--text-main);
            font-family: inherit;
            font-size: 0.9rem;
            line-height: 1.6;
            resize: none;
            max-height: 160px;
            min-height: 42px;
            padding: 0.5rem 0.75rem;
            direction: auto;
        }

        .chat-textarea::placeholder {
            color: var(--text-muted);
            font-size: 0.85rem;
        }

        .chat-input-actions {
            display: flex;
            align-items: center;
            gap: 0.35rem;
            padding-bottom: 4px;
        }

        .mic-btn, .send-btn {
            width: 38px;
            height: 38px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .mic-btn {
            background: rgba(255, 255, 255, 0.06);
            color: var(--text-muted);
            border: 1px solid var(--border-color);
        }

        .mic-btn:hover {
            color: var(--primary);
            border-color: var(--primary);
        }

        .mic-btn.listening {
            background: rgba(239, 68, 68, 0.2);
            border-color: #ef4444;
            color: #ef4444;
            animation: pulse-mic 1.2s infinite;
        }

        @keyframes pulse-mic {
            0%, 100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.5); }
            50% { box-shadow: 0 0 0 8px rgba(239, 68, 68, 0); }
        }

        .send-btn {
            background: linear-gradient(135deg, #00f0ff, #4f46e5);
            color: #030712;
            font-weight: 800;
            font-size: 1.1rem;
            box-shadow: 0 0 15px var(--primary-glow);
        }

        .send-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 0 25px var(--primary-glow);
        }

        .send-btn:disabled {
            opacity: 0.45;
            cursor: not-allowed;
            transform: none !important;
            box-shadow: none !important;
        }

        .input-disclaimer {
            text-align: center;
            font-size: 0.68rem;
            color: var(--text-muted);
            margin-top: 0.4rem;
        }

        /* Mobile Sidebar Overlay */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.65);
            backdrop-filter: blur(4px);
            z-index: 35;
        }

        @media (max-width: 991px) {
            .ai-sidebar {
                position: fixed;
                top: 0;
                bottom: 0;
                right: 0;
                transform: translateX(100%);
            }
            .ai-sidebar.active {
                transform: translateX(0);
            }
            .sidebar-overlay.active {
                display: block;
            }
            .sidebar-toggle-btn {
                display: flex;
            }
            .topbar-center-status {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="ambient-glow glow-1"></div>
    <div class="ambient-glow glow-2"></div>

    <div class="ai-app-wrapper">
        <!-- Sidebar Backdrop on Mobile -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <!-- RIGHT SIDEBAR (Chat Management & Topics) -->
        <aside class="ai-sidebar" id="aiSidebar">
            <div class="ai-sidebar-header">
                <a href="{{ route('home') }}" class="ai-brand">
                    <div class="ai-brand-logo">
                        <i class="bi bi-robot"></i>
                    </div>
                    <div class="ai-brand-info">
                        <h1>
                            ادوُرا AI
                            <span class="badge-gemini">Gemini 2.5</span>
                        </h1>
                        <p>دستیار هوشمند یادگیری</p>
                    </div>
                </a>

                <button type="button" class="action-icon-btn d-lg-none" id="closeSidebarBtn" title="بستن سایدبار">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <!-- New Chat Button -->
            <div class="new-chat-btn-wrap">
                <button type="button" class="new-chat-btn" id="newChatBtn">
                    <i class="bi bi-plus-lg"></i>
                    گفتگوی جدید
                </button>
            </div>

            <!-- Topics Selector -->
            <div class="sidebar-section-title">
                <span>دسته‌بندی موضوعی</span>
                <i class="bi bi-sliders2"></i>
            </div>
            <div class="topics-scroll">
                <button type="button" class="topic-pill active" data-topic="all">
                    <i class="bi bi-globe2"></i> همه موضوعات پلتفرم
                </button>
                <button type="button" class="topic-pill" data-topic="courses">
                    <i class="bi bi-mortarboard-fill"></i> دوره‌ها و سرفصل‌ها
                </button>
                <button type="button" class="topic-pill" data-topic="coding">
                    <i class="bi bi-code-slash"></i> برنامه‌نویسی و وب
                </button>
                <button type="button" class="topic-pill" data-topic="learning_guidance">
                    <i class="bi bi-compass-fill"></i> مشاوره و نقشه راه
                </button>
                <button type="button" class="topic-pill" data-topic="platform_support">
                    <i class="bi bi-gear-wide-connected"></i> پشتیبانی پلتفرم ادورا
                </button>
            </div>

            <!-- Chat History -->
            <div class="sidebar-section-title mt-2">
                <span>تاریخچه گفتگوها</span>
                <button type="button" class="msg-tool-btn text-danger" id="clearAllHistoryBtn" title="پاکسازی همه">
                    <i class="bi bi-trash3"></i>
                </button>
            </div>
            <div class="history-list" id="chatHistoryList">
                <!-- Dynamically populated from localStorage -->
            </div>

            <!-- Footer: User / Guest Info -->
            <div class="ai-sidebar-footer">
                @if($isGuest)
                <div class="guest-limit-card" id="guestLimitBox">
                    <div class="guest-limit-header">
                        <span><i class="bi bi-shield-exclamation me-1"></i> سهمیه مهمان</span>
                        <span id="guestRemainingCount">{{ $guestRemaining }} از ۵ سوال</span>
                    </div>
                    <div class="guest-progress-bar">
                        <div class="guest-progress-fill" id="guestProgressFill" style="width: {{ ($guestUsed / 5) * 100 }}%;"></div>
                    </div>
                    <a href="{{ route('login') }}" class="guest-login-link">
                        <i class="bi bi-box-arrow-in-left me-1"></i> ورود برای چت نامحدود
                    </a>
                </div>
                @else
                <div class="user-profile-card">
                    <div class="user-avatar-wrap">
                        @if($user && $user->avatar)
                            <img src="{{ str_starts_with($user->avatar, 'http') ? $user->avatar : asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}">
                        @else
                            {{ mb_substr($user->name ?? 'U', 0, 1) }}
                        @endif
                    </div>
                    <div class="user-details">
                        <h4>{{ $user->name ?? 'کاربر ادورا' }}</h4>
                        <span><i class="bi bi-stars me-1"></i>اشتراک نامحدود</span>
                    </div>
                </div>
                @endif
            </div>
        </aside>

        <!-- MAIN CHAT CONTAINER -->
        <main class="ai-main-area">
            <!-- Topbar Navigation -->
            <header class="ai-topbar">
                <div class="topbar-right">
                    <button type="button" class="sidebar-toggle-btn" id="sidebarToggleBtn" aria-label="Toggle Navigation">
                        <i class="bi bi-list"></i>
                    </button>

                    @php
                        $backRoute = route('home');
                        $backLabel = 'صفحه اصلی';
                        if (auth()->check()) {
                            $u = auth()->user();
                            if ($u->role === 'teacher') {
                                $backRoute = route('teacher.dashboard');
                                $backLabel = 'پنل مدرسین';
                            } elseif ($u->role === 'admin') {
                                $backRoute = route('admin.dashboard');
                                $backLabel = 'پنل مدیریت';
                            } else {
                                $backRoute = route('student.dashboard');
                                $backLabel = 'داشبورد من';
                            }
                        }
                    @endphp
                    <a href="{{ $backRoute }}" class="back-dashboard-btn" title="بازگشت به پنل">
                        <i class="bi bi-arrow-right"></i>
                        <span>{{ $backLabel }}</span>
                    </a>
                </div>

                <div class="topbar-center-status">
                    <span class="dot"></span>
                    <span>Gemini 2.5 Pro Ultra • متصل به دیتابیس هوشمند ادورا تِک</span>
                </div>

                <div class="topbar-actions">
                    <button type="button" class="action-icon-btn" id="themeToggleBtn" title="تغییر تم">
                        <i class="bi bi-sun-fill" id="themeIcon"></i>
                    </button>

                    <button type="button" class="action-icon-btn text-danger" id="clearCurrentChatBtn" title="پاکسازی پیام‌های این گفتگو">
                        <i class="bi bi-trash3"></i>
                    </button>
                </div>
            </header>

            <!-- Message Feed -->
            <div class="chat-feed" id="chatFeed">
                <div class="chat-feed-inner" id="messagesContainer">
                    <!-- Welcome Hero State when empty -->
                    <div class="empty-welcome-hero" id="emptyWelcomeHero">
                        <div class="hero-orb">
                            <i class="bi bi-stars"></i>
                        </div>
                        <h2>سلام{{ auth()->check() ? ' ' . auth()->user()->name : '' }}! چطور می‌تونم کمکت کنم؟</h2>
                        <p>
                            من دستیار هوش مصنوعی ادوُرا تِک هستم؛ آماده پاسخ‌گویی به سوالات برنامه‌نویسی، معرفی دوره‌ها، بررسی سیلابس‌ها، راهنمایی ثبت‌نام و مشاوره تحصیلی.
                        </p>

                        <div class="suggestion-grid">
                            <div class="suggestion-card" onclick="sendSuggestion('بهترین مسیر برای یادگیری برنامه‌نویسی وب از صفر در ادورا چیه؟')">
                                <i class="bi bi-code-square suggestion-icon"></i>
                                <span class="suggestion-text">مسیر یادگیری برنامه‌نویسی وب از صفر در ادورا چیه؟</span>
                            </div>
                            <div class="suggestion-card" onclick="sendSuggestion('دوره‌های فعال و اساتید برتر ادورا تِک رو به من معرفی کن')">
                                <i class="bi bi-mortarboard suggestion-icon"></i>
                                <span class="suggestion-text">دوره‌های فعال و اساتید برتر ادورا تِک رو به من معرفی کن</span>
                            </div>
                            <div class="suggestion-card" onclick="sendSuggestion('چگونه پایتون را پروژه-محور و سریع یاد بگیرم؟')">
                                <i class="bi bi-terminal suggestion-icon"></i>
                                <span class="suggestion-text">چگونه پایتون را پروژه-محور و سریع یاد بگیرم؟</span>
                            </div>
                            <div class="suggestion-card" onclick="sendSuggestion('شرایط دریافت گواهینامه معتبر دوره و ثبت‌نام چگونه است؟')">
                                <i class="bi bi-award suggestion-icon"></i>
                                <span class="suggestion-text">شرایط دریافت گواهینامه معتبر دوره و ثبت‌نام چگونه است؟</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sticky Command Input Bar -->
            <div class="chat-input-bar-wrap">
                <div class="chat-input-bar">
                    <textarea
                        class="chat-textarea"
                        id="chatInput"
                        rows="1"
                        placeholder="سوال یا پیام خود را بنویسید... (Enter برای ارسال)"
                    ></textarea>

                    <div class="chat-input-actions">
                        <button type="button" class="mic-btn" id="voiceBtn" title="ورودی صوتی (فارسی / English)">
                            <i class="bi bi-mic-fill"></i>
                        </button>
                        <button type="button" class="send-btn" id="sendBtn" title="ارسال پیام">
                            <i class="bi bi-arrow-up-short"></i>
                        </button>
                    </div>
                </div>
                <div class="input-disclaimer">
                    هوش مصنوعی ادورا متصل به Gemini است. لطفاً برای تصمیم‌گیری‌های حیاتی اطلاعات را تطبیق دهید.
                </div>
            </div>
        </main>
    </div>

    <!-- Application Script -->
    <script>
    (function () {
        'use strict';

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
        const isGuest = {{ $isGuest ? 'true' : 'false' }};
        let guestRemaining = {{ (int)$guestRemaining }};

        let currentTopic = 'all';
        let currentSessionId = 'session_' + Date.now();
        let sessions = JSON.parse(localStorage.getItem('edvora_ai_chat_sessions') || '{}');
        let currentMessages = [];

        // DOM Elements
        const chatFeed = document.getElementById('chatFeed');
        const messagesContainer = document.getElementById('messagesContainer');
        const emptyWelcomeHero = document.getElementById('emptyWelcomeHero');
        const chatInput = document.getElementById('chatInput');
        const sendBtn = document.getElementById('sendBtn');
        const voiceBtn = document.getElementById('voiceBtn');
        const newChatBtn = document.getElementById('newChatBtn');
        const chatHistoryList = document.getElementById('chatHistoryList');
        const clearAllHistoryBtn = document.getElementById('clearAllHistoryBtn');
        const clearCurrentChatBtn = document.getElementById('clearCurrentChatBtn');
        const themeToggleBtn = document.getElementById('themeToggleBtn');
        const themeIcon = document.getElementById('themeIcon');
        const sidebarToggleBtn = document.getElementById('sidebarToggleBtn');
        const closeSidebarBtn = document.getElementById('closeSidebarBtn');
        const aiSidebar = document.getElementById('aiSidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        const guestRemainingCount = document.getElementById('guestRemainingCount');
        const guestProgressFill = document.getElementById('guestProgressFill');

        // Theme Setup
        function initTheme() {
            const savedTheme = localStorage.getItem('edvora_theme') || 'dark';
            if (savedTheme === 'light') {
                document.documentElement.classList.remove('dark');
                themeIcon.className = 'bi bi-moon-fill';
            } else {
                document.documentElement.classList.add('dark');
                themeIcon.className = 'bi bi-sun-fill';
            }
        }
        initTheme();

        themeToggleBtn.addEventListener('click', () => {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('edvora_theme', isDark ? 'dark' : 'light');
            themeIcon.className = isDark ? 'bi bi-sun-fill' : 'bi bi-moon-fill';
        });

        // Mobile Sidebar Controls
        function openSidebar() {
            aiSidebar.classList.add('active');
            sidebarOverlay.classList.add('active');
        }
        function closeSidebar() {
            aiSidebar.classList.remove('active');
            sidebarOverlay.classList.remove('active');
        }
        sidebarToggleBtn.addEventListener('click', openSidebar);
        if (closeSidebarBtn) closeSidebarBtn.addEventListener('click', closeSidebar);
        sidebarOverlay.addEventListener('click', closeSidebar);

        // Topic Pills selection
        document.querySelectorAll('.topic-pill').forEach(pill => {
            pill.addEventListener('click', () => {
                document.querySelectorAll('.topic-pill').forEach(p => p.classList.remove('active'));
                pill.classList.add('active');
                currentTopic = pill.dataset.topic || 'all';
                closeSidebar();
            });
        });

        // Auto-growing textarea
        chatInput.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 160) + 'px';
            sendBtn.disabled = this.value.trim().length === 0;
        });

        chatInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                submitUserMessage();
            }
        });

        sendBtn.addEventListener('click', submitUserMessage);

        window.sendSuggestion = function(text) {
            chatInput.value = text;
            chatInput.dispatchEvent(new Event('input'));
            submitUserMessage();
        };

        // Render Sessions in History List
        function renderHistoryList() {
            chatHistoryList.innerHTML = '';
            const sessionKeys = Object.keys(sessions).reverse();

            if (sessionKeys.length === 0) {
                chatHistoryList.innerHTML = '<div style="font-size:0.75rem;color:var(--text-muted);text-align:center;padding:1rem;">گفتگویی ثبت نشده است.</div>';
                return;
            }

            sessionKeys.forEach(sId => {
                const sData = sessions[sId];
                const item = document.createElement('div');
                item.className = 'history-item ' + (sId === currentSessionId ? 'active' : '');
                item.innerHTML = `
                    <span class="history-title" title="${escapeHtml(sData.title || 'گفتگو')}">
                        <i class="bi bi-chat-left-text me-1 text-primary"></i> ${escapeHtml(sData.title || 'گفتگو')}
                    </span>
                    <button type="button" class="history-del-btn" title="حذف">
                        <i class="bi bi-x"></i>
                    </button>
                `;

                item.querySelector('.history-title').addEventListener('click', () => {
                    loadSession(sId);
                    closeSidebar();
                });

                item.querySelector('.history-del-btn').addEventListener('click', (e) => {
                    e.stopPropagation();
                    deleteSession(sId);
                });

                chatHistoryList.appendChild(item);
            });
        }

        function saveCurrentSession() {
            if (currentMessages.length === 0) return;
            const firstUserMsg = currentMessages.find(m => m.role === 'user');
            const title = firstUserMsg ? firstUserMsg.text.slice(0, 32) : 'گفتگوی جدید';

            sessions[currentSessionId] = {
                title: title,
                timestamp: Date.now(),
                messages: currentMessages,
                topic: currentTopic
            };

            localStorage.setItem('edvora_ai_chat_sessions', JSON.stringify(sessions));
            renderHistoryList();
        }

        function loadSession(sId) {
            if (!sessions[sId]) return;
            currentSessionId = sId;
            currentMessages = sessions[sId].messages || [];
            currentTopic = sessions[sId].topic || 'all';

            // Update topic pill
            document.querySelectorAll('.topic-pill').forEach(p => {
                p.classList.toggle('active', p.dataset.topic === currentTopic);
            });

            renderMessagesFeed();
            renderHistoryList();
        }

        function deleteSession(sId) {
            delete sessions[sId];
            localStorage.setItem('edvora_ai_chat_sessions', JSON.stringify(sessions));
            if (currentSessionId === sId) {
                startNewChat();
            } else {
                renderHistoryList();
            }
        }

        function startNewChat() {
            currentSessionId = 'session_' + Date.now();
            currentMessages = [];
            renderMessagesFeed();
            renderHistoryList();
            chatInput.value = '';
            chatInput.style.height = 'auto';
            chatInput.focus();
        }

        newChatBtn.addEventListener('click', () => {
            startNewChat();
            closeSidebar();
        });

        clearAllHistoryBtn.addEventListener('click', () => {
            if (confirm('آیا از پاکسازی تمام تاریخچه گفتگوها اطمینان دارید؟')) {
                sessions = {};
                localStorage.removeItem('edvora_ai_chat_sessions');
                startNewChat();
            }
        });

        clearCurrentChatBtn.addEventListener('click', () => {
            if (currentMessages.length === 0) return;
            if (confirm('پیام‌های این گفتگو پاک شوند؟')) {
                currentMessages = [];
                delete sessions[currentSessionId];
                localStorage.setItem('edvora_ai_chat_sessions', JSON.stringify(sessions));
                renderMessagesFeed();
                renderHistoryList();
            }
        });

        function renderMessagesFeed() {
            messagesContainer.innerHTML = '';

            if (currentMessages.length === 0) {
                messagesContainer.appendChild(emptyWelcomeHero);
                return;
            }

            currentMessages.forEach(msg => {
                appendMessageToDOM(msg.role, msg.text, msg.time, false);
            });

            scrollToBottom();
        }

        function appendMessageToDOM(role, text, time, shouldScroll = true) {
            const isUser = role === 'user';
            const row = document.createElement('div');
            row.className = `chat-message-row ${isUser ? 'user' : 'ai'}`;

            const timeStr = time || new Date().toLocaleTimeString('fa-IR', { hour: '2-digit', minute: '2-digit' });

            let parsedContent = '';
            if (isUser) {
                parsedContent = `<p>${escapeHtml(text).replace(/\n/g, '<br>')}</p>`;
            } else {
                if (window.marked) {
                    try {
                        parsedContent = marked.parse(text);
                    } catch (e) {
                        parsedContent = `<p>${escapeHtml(text)}</p>`;
                    }
                } else {
                    parsedContent = `<p>${escapeHtml(text).replace(/\n/g, '<br>')}</p>`;
                }
            }

            row.innerHTML = `
                <div class="msg-avatar">
                    ${isUser ? '<i class="bi bi-person-fill"></i>' : '<i class="bi bi-stars"></i>'}
                </div>
                <div class="msg-content-box">
                    <div class="msg-bubble">
                        ${parsedContent}
                    </div>
                    <div class="msg-actions-row">
                        <span class="msg-time">${timeStr}</span>
                        ${!isUser ? `
                            <button type="button" class="msg-tool-btn copy-msg-btn" title="کپی پیام">
                                <i class="bi bi-clipboard"></i>
                            </button>
                            <button type="button" class="msg-tool-btn tts-msg-btn" title="پخش صوتی">
                                <i class="bi bi-volume-up"></i>
                            </button>
                        ` : ''}
                    </div>
                </div>
            `;

            // Setup copy code buttons inside pre blocks
            row.querySelectorAll('pre').forEach(pre => {
                const btn = document.createElement('button');
                btn.className = 'copy-code-btn';
                btn.innerHTML = '<i class="bi bi-clipboard"></i> کپی';
                btn.onclick = function() {
                    const code = pre.querySelector('code')?.innerText || pre.innerText;
                    navigator.clipboard.writeText(code).then(() => {
                        btn.innerHTML = '<i class="bi bi-check2"></i> کپی شد';
                        setTimeout(() => btn.innerHTML = '<i class="bi bi-clipboard"></i> کپی', 2000);
                    });
                };
                pre.appendChild(btn);
            });

            // Copy whole message button
            const copyBtn = row.querySelector('.copy-msg-btn');
            if (copyBtn) {
                copyBtn.onclick = function() {
                    navigator.clipboard.writeText(text).then(() => {
                        copyBtn.innerHTML = '<i class="bi bi-check2"></i>';
                        setTimeout(() => copyBtn.innerHTML = '<i class="bi bi-clipboard"></i>', 2000);
                    });
                };
            }

            // Text-to-speech button
            const ttsBtn = row.querySelector('.tts-msg-btn');
            if (ttsBtn) {
                ttsBtn.onclick = function() {
                    if (!window.speechSynthesis) return;
                    window.speechSynthesis.cancel();
                    const cleanText = text.replace(/[#*`_]/g, '');
                    const utterance = new SpeechSynthesisUtterance(cleanText);
                    utterance.lang = /[\u0600-\u06FF]/.test(text) ? 'fa-IR' : 'en-US';
                    window.speechSynthesis.speak(utterance);
                };
            }

            messagesContainer.appendChild(row);
            if (shouldScroll) scrollToBottom();
        }

        function scrollToBottom() {
            chatFeed.scrollTop = chatFeed.scrollHeight;
        }

        function showTypingIndicator() {
            const indicator = document.createElement('div');
            indicator.className = 'chat-message-row ai';
            indicator.id = 'typingIndicatorRow';
            indicator.innerHTML = `
                <div class="msg-avatar">
                    <i class="bi bi-stars"></i>
                </div>
                <div class="typing-indicator">
                    <span class="typing-dot"></span>
                    <span class="typing-dot"></span>
                    <span class="typing-dot"></span>
                </div>
            `;
            messagesContainer.appendChild(indicator);
            scrollToBottom();
        }

        function removeTypingIndicator() {
            const ind = document.getElementById('typingIndicatorRow');
            if (ind) ind.remove();
        }

        async function submitUserMessage() {
            const msg = chatInput.value.trim();
            if (!msg) return;

            // Remove welcome state if active
            if (currentMessages.length === 0 && emptyWelcomeHero.parentNode) {
                emptyWelcomeHero.remove();
            }

            const nowTime = new Date().toLocaleTimeString('fa-IR', { hour: '2-digit', minute: '2-digit' });
            currentMessages.push({ role: 'user', text: msg, time: nowTime });
            appendMessageToDOM('user', msg, nowTime, true);

            chatInput.value = '';
            chatInput.style.height = 'auto';
            sendBtn.disabled = true;

            showTypingIndicator();

            // Prepare history payload (last 6 messages)
            const historyPayload = currentMessages.slice(-6).map(m => ({
                role: m.role === 'user' ? 'user' : 'model',
                text: m.text
            }));

            try {
                const response = await fetch('{{ route("ai.chat") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        message: msg,
                        history: historyPayload,
                        topic: currentTopic,
                        language: 'auto'
                    })
                });

                removeTypingIndicator();

                const data = await response.json();

                if (response.status === 429 || data.limit_reached) {
                    appendMessageToDOM('ai', data.message || 'سقف ۵ سوال رایگان شما به پایان رسیده است. برای ادامه لطفاً وارد حساب خود شوید.', null, true);
                    updateGuestRemaining(0);
                    return;
                }

                if (!response.ok || !data.success) {
                    appendMessageToDOM('ai', data.message || 'متأسفانه در دریافت پاسخ خطایی رخ داد. لطفاً دوباره امتحان کنید.', null, true);
                    return;
                }

                const aiText = data.message || 'پاسخی دریافت نشد.';
                const aiTime = new Date().toLocaleTimeString('fa-IR', { hour: '2-digit', minute: '2-digit' });
                currentMessages.push({ role: 'model', text: aiText, time: aiTime });
                appendMessageToDOM('ai', aiText, aiTime, true);

                if (typeof data.remaining !== 'undefined') {
                    updateGuestRemaining(data.remaining);
                }

                saveCurrentSession();

            } catch (err) {
                removeTypingIndicator();
                appendMessageToDOM('ai', 'خطا در برقراری ارتباط با سرور. لطفاً اتصال اینترنت خود را بررسی نمایید.', null, true);
            }
        }

        function updateGuestRemaining(rem) {
            guestRemaining = rem;
            if (guestRemainingCount) {
                guestRemainingCount.textContent = `${rem} از ۵ سوال`;
            }
            if (guestProgressFill) {
                const used = 5 - rem;
                guestProgressFill.style.width = `${(used / 5) * 100}%`;
            }
        }

        // Voice Input (Web Speech Recognition)
        let recognition = null;
        if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
            const SpeechRec = window.SpeechRecognition || window.webkitSpeechRecognition;
            recognition = new SpeechRec();
            recognition.continuous = false;
            recognition.interimResults = false;
            recognition.lang = 'fa-IR';

            recognition.onstart = function() {
                voiceBtn.classList.add('listening');
                voiceBtn.title = 'در حال شنیدن... صحبت کنید';
            };

            recognition.onresult = function(event) {
                const transcript = event.results[0][0].transcript;
                chatInput.value += (chatInput.value ? ' ' : '') + transcript;
                chatInput.dispatchEvent(new Event('input'));
                voiceBtn.classList.remove('listening');
            };

            recognition.onerror = function() {
                voiceBtn.classList.remove('listening');
            };

            recognition.onend = function() {
                voiceBtn.classList.remove('listening');
                voiceBtn.title = 'ورودی صوتی (فارسی / English)';
            };

            voiceBtn.addEventListener('click', function() {
                if (voiceBtn.classList.contains('listening')) {
                    recognition.stop();
                } else {
                    recognition.start();
                }
            });
        } else {
            voiceBtn.style.display = 'none';
        }

        function escapeHtml(text) {
            if (!text) return '';
            return text
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        // Initialize history list on load
        renderHistoryList();
    })();
    </script>
</body>
</html>
