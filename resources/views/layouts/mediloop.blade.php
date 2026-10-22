<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ filled($title ?? null) ? $title.' - MediLoop' : 'MediLoop' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:wght@400;600;700;800&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        /* MediLoop Design System */
        :root {
            --ml-navy: #0B1F4B;
            --ml-primary: #1D4ED8;
            --ml-surface: #F8FAFC;
            --ml-card: #FFFFFF;
            --ml-border: #DBE5F5;
            --ml-text: #0B1F4B;
            --ml-muted: #52627F;
            --ml-subtle: #94A3B8;
            --ml-danger: #EF4444;
            --ml-warning: #F59E0B;
            --ml-success: #22C55E;
            --ml-orange: #F97316;
            --font-heading: 'Bricolage Grotesque', system-ui, sans-serif;
            --font-body: 'DM Sans', system-ui, sans-serif;
        }
        *, ::before, ::after { box-sizing: border-box; }
        body {
            font-family: var(--font-body);
            color: var(--ml-text);
            background: var(--ml-surface);
            margin: 0;
            min-height: 100vh;
        }
        h1,h2,h3,h4,h5,h6 {
            font-family: var(--font-heading);
            letter-spacing: -0.02em;
        }

        /* Sidebar */
        .ml-sidebar {
            position: fixed;
            top: 0; left: 0; bottom: 0;
            width: 220px;
            background: var(--ml-navy);
            display: flex;
            flex-direction: column;
            z-index: 40;
            flex-shrink: 0;
        }
        .ml-sidebar-logo {
            padding: 22px 20px 18px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }
        .ml-sidebar-logo span {
            font-family: var(--font-heading);
            font-size: 22px;
            font-weight: 800;
            color: #fff;
            letter-spacing: -0.03em;
        }
        .ml-sidebar-nav {
            padding: 14px 10px;
            display: flex;
            flex-direction: column;
            gap: 2px;
            flex: 1;
        }
        .ml-nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 12px;
            border-radius: 10px;
            font-size: 13.5px;
            font-weight: 500;
            color: rgba(255,255,255,0.65);
            text-decoration: none;
            transition: all 0.15s ease;
            cursor: pointer;
            border: none;
            background: transparent;
            font-family: var(--font-body);
            width: 100%;
            text-align: left;
        }
        .ml-nav-item:hover {
            background: rgba(255,255,255,0.08);
            color: rgba(255,255,255,0.9);
        }
        .ml-nav-item.active {
            background: var(--ml-primary);
            color: #fff;
            font-weight: 600;
        }
        .ml-nav-item svg { flex-shrink: 0; opacity: 0.8; }
        .ml-nav-item.active svg { opacity: 1; }

        /* Dropdowns & Popovers */
        .ml-menu-dropdown {
            position: absolute;
            background: #FFFFFF;
            border: 1px solid var(--ml-border);
            border-radius: 12px;
            box-shadow: 0 10px 25px -5px rgba(11, 31, 75, 0.15), 0 8px 10px -6px rgba(11, 31, 75, 0.1);
            z-index: 60;
            padding: 6px;
            animation: mlFadeIn 0.15s ease-out;
        }
        @keyframes mlFadeIn {
            from { opacity: 0; transform: translateY(-4px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Settings menu (upward popup from sidebar bottom) */
        .ml-settings-container {
            margin-top: auto;
            position: relative;
        }
        .ml-settings-menu {
            bottom: calc(100% + 8px);
            left: 0;
            right: 0;
            background: #0f275e;
            border: 1px solid rgba(255,255,255,0.12);
            box-shadow: 0 -8px 24px rgba(0,0,0,0.35);
        }
        .ml-settings-menu .ml-menu-header {
            padding: 8px 10px 6px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: rgba(255,255,255,0.45);
        }
        .ml-settings-menu .ml-menu-item {
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
            padding: 8px 10px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            color: rgba(255,255,255,0.85);
            text-decoration: none;
            background: transparent;
            border: none;
            cursor: pointer;
            font-family: var(--font-body);
            text-align: left;
            transition: all 0.12s ease;
        }
        .ml-settings-menu .ml-menu-item:hover {
            background: rgba(255,255,255,0.1);
            color: #fff;
        }
        .ml-settings-menu .ml-menu-item-danger {
            color: #FCA5A5;
        }
        .ml-settings-menu .ml-menu-item-danger:hover {
            background: rgba(239, 68, 68, 0.22);
            color: #F87171;
        }
        .ml-settings-menu .ml-menu-divider {
            height: 1px;
            background: rgba(255,255,255,0.08);
            margin: 4px 0;
        }

        /* Profile menu (downward popup in topbar right) */
        .ml-profile-container {
            position: relative;
        }
        .ml-profile-menu {
            top: calc(100% + 8px);
            right: 0;
            width: 250px;
        }
        .ml-profile-dropdown-user {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px;
        }
        .ml-avatar-lg {
            width: 38px;
            height: 38px;
            font-size: 13px;
            flex-shrink: 0;
        }
        .ml-profile-name {
            font-size: 13px;
            font-weight: 700;
            color: var(--ml-text);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .ml-profile-email {
            font-size: 11px;
            color: var(--ml-muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .ml-role-badge {
            display: inline-block;
            margin-top: 3px;
            padding: 2px 7px;
            background: #EFF6FF;
            color: var(--ml-primary);
            border-radius: 6px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }
        .ml-profile-menu .ml-menu-item {
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
            padding: 8px 10px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            color: var(--ml-text);
            text-decoration: none;
            background: transparent;
            border: none;
            cursor: pointer;
            font-family: var(--font-body);
            text-align: left;
            transition: all 0.12s ease;
        }
        .ml-profile-menu .ml-menu-item:hover {
            background: var(--ml-surface);
            color: var(--ml-primary);
        }
        .ml-profile-menu .ml-menu-item-danger {
            color: var(--ml-danger);
        }
        .ml-profile-menu .ml-menu-item-danger:hover {
            background: #FEF2F2;
            color: #DC2626;
        }
        .ml-profile-menu .ml-menu-divider {
            height: 1px;
            background: var(--ml-border);
            margin: 4px 0;
        }

        /* Main layout */
        .ml-main {
            margin-left: 220px;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Topbar */
        .ml-topbar {
            background: #fff;
            border-bottom: 1px solid var(--ml-border);
            padding: 12px 24px;
            display: flex;
            align-items: center;
            gap: 16px;
            position: sticky;
            top: 0;
            z-index: 30;
        }
        .ml-search {
            flex: 1;
            max-width: 360px;
            display: flex;
            align-items: center;
            gap: 8px;
            background: var(--ml-surface);
            border: 1px solid var(--ml-border);
            border-radius: 10px;
            padding: 8px 14px;
        }
        .ml-search input {
            border: none;
            background: transparent;
            outline: none;
            font-family: var(--font-body);
            font-size: 13.5px;
            color: var(--ml-muted);
            width: 100%;
        }
        .ml-search svg { color: var(--ml-subtle); flex-shrink: 0; }
        .ml-topbar-spacer { flex: 1; }
        .ml-topbar-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .ml-icon-btn {
            position: relative;
            width: 36px; height: 36px;
            border-radius: 9px;
            border: 1px solid var(--ml-border);
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
            background: #fff;
            color: var(--ml-muted);
            transition: all 0.15s;
        }
        .ml-icon-btn:hover { background: var(--ml-surface); }
        .ml-badge-dot {
            position: absolute;
            top: 6px; right: 6px;
            width: 8px; height: 8px;
            border-radius: 50%;
            background: var(--ml-orange);
            border: 2px solid #fff;
        }
        .ml-user-chip {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 5px 10px 5px 6px;
            border-radius: 12px;
            border: 1px solid var(--ml-border);
            background: #fff;
            cursor: pointer;
            transition: all 0.15s;
            outline: none;
        }
        .ml-user-chip:hover { background: var(--ml-surface); }
        .ml-avatar {
            width: 32px; height: 32px;
            border-radius: 9px;
            background: var(--ml-primary);
            display: flex; align-items: center; justify-content: center;
            color: #fff;
            font-size: 12px;
            font-weight: 700;
            overflow: hidden;
        }
        .ml-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .ml-user-info { line-height: 1.25; text-align: left; }
        .ml-user-name { font-size: 13px; font-weight: 600; color: var(--ml-text); }
        .ml-user-role { font-size: 11px; color: var(--ml-muted); }

        /* Content area */
        .ml-content { padding: 24px; flex: 1; }

        /* Stat Cards */
        .ml-stat-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 22px;
        }
        .ml-stat-card {
            background: #fff;
            border: 1px solid var(--ml-border);
            border-radius: 16px;
            padding: 18px 20px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.04);
        }
        .ml-stat-label {
            font-size: 12.5px;
            color: var(--ml-muted);
            font-weight: 500;
            margin-bottom: 8px;
        }
        .ml-stat-sublabel {
            font-size: 11px;
            color: var(--ml-subtle);
            margin-bottom: 6px;
        }
        .ml-stat-value {
            font-size: 28px;
            font-weight: 800;
            font-family: var(--font-heading);
            color: var(--ml-text);
            letter-spacing: -0.03em;
            line-height: 1;
        }
        .ml-stat-badge {
            display: inline-block;
            background: var(--ml-orange);
            color: #fff;
            font-size: 18px;
            font-weight: 800;
            font-family: var(--font-heading);
            padding: 4px 14px;
            border-radius: 8px;
            letter-spacing: -0.02em;
        }
        .ml-stat-badge-blue {
            display: inline-block;
            background: var(--ml-primary);
            color: #fff;
            font-size: 18px;
            font-weight: 800;
            font-family: var(--font-heading);
            padding: 4px 14px;
            border-radius: 8px;
            letter-spacing: -0.02em;
        }
        .ml-stat-sub {
            font-size: 11.5px;
            color: var(--ml-muted);
            margin-top: 6px;
        }
        .ml-stat-sub b { color: var(--ml-orange); }

        /* Two-col layout */
        .ml-two-col {
            display: grid;
            grid-template-columns: 1fr 340px;
            gap: 20px;
            align-items: start;
        }

        /* Section card */
        .ml-section {
            background: #fff;
            border: 1px solid var(--ml-border);
            border-radius: 16px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.04);
            overflow: hidden;
        }
        .ml-section-header {
            padding: 16px 20px;
            border-bottom: 1px solid var(--ml-border);
        }
        .ml-section-title {
            font-size: 15px;
            font-weight: 700;
            color: var(--ml-text);
            font-family: var(--font-heading);
        }

        /* Table */
        .ml-table { width: 100%; border-collapse: collapse; }
        .ml-table th {
            padding: 10px 16px;
            background: #F8FAFC;
            font-size: 11.5px;
            font-weight: 600;
            color: var(--ml-muted);
            text-align: left;
            border-bottom: 1px solid var(--ml-border);
        }
        .ml-table td {
            padding: 14px 16px;
            font-size: 13px;
            border-bottom: 1px solid var(--ml-border);
            vertical-align: middle;
        }
        .ml-table tr:last-child td { border-bottom: none; }
        .ml-table tr:hover td { background: #F8FAFC; }

        /* Risk badge */
        .ml-risk-high {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            background: #FFF4EC;
            color: var(--ml-orange);
            border: 1px solid #FDDCBB;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 700;
        }
        .ml-risk-medium {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            background: #FFFBEB;
            color: #D97706;
            border: 1px solid #FDE68A;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 700;
        }

        /* Days bar visual */
        .ml-days-wrap {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
        }
        .ml-days-pill {
            padding: 3px 9px;
            border-radius: 99px;
            font-weight: 600;
            font-size: 11.5px;
        }
        .ml-days-expiry { background: #FFF4EC; color: var(--ml-orange); }
        .ml-days-deplete { background: #EFF6FF; color: var(--ml-primary); }
        .ml-days-vs { font-size: 10px; color: var(--ml-subtle); font-weight: 600; }

        /* Find match button */
        .ml-btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 7px 14px;
            background: var(--ml-primary);
            color: #fff;
            border: none;
            border-radius: 9px;
            font-size: 12.5px;
            font-weight: 600;
            font-family: var(--font-body);
            cursor: pointer;
            transition: all 0.15s;
            text-decoration: none;
        }
        .ml-btn-primary:hover { background: #1e40af; transform: translateY(-1px); }

        .ml-btn-ghost {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 7px 14px;
            background: #fff;
            color: var(--ml-primary);
            border: 1.5px solid var(--ml-primary);
            border-radius: 9px;
            font-size: 12.5px;
            font-weight: 600;
            font-family: var(--font-body);
            cursor: pointer;
            transition: all 0.15s;
            text-decoration: none;
        }
        .ml-btn-ghost:hover { background: #EFF6FF; }

        /* Map panel */
        .ml-map-container {
            background: #E8F0F8;
            border-radius: 10px;
            height: 200px;
            position: relative;
            overflow: hidden;
            margin: 16px;
        }
        .ml-map-pin {
            position: absolute;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .ml-pin-dot {
            width: 12px; height: 12px;
            border-radius: 50%;
            border: 2px solid #fff;
            box-shadow: 0 2px 6px rgba(0,0,0,0.2);
            flex-shrink: 0;
        }
        .ml-pin-label {
            background: #fff;
            border-radius: 6px;
            padding: 3px 7px;
            font-size: 10.5px;
            font-weight: 600;
            color: var(--ml-text);
            box-shadow: 0 2px 6px rgba(0,0,0,0.12);
            white-space: nowrap;
        }
        .ml-pin-label small { display: block; color: var(--ml-muted); font-weight: 400; font-size: 9.5px; }
        .ml-map-route {
            position: absolute;
            top: 50%;
            left: 0; right: 0;
            height: 1px;
            border-top: 2px dashed #93C5FD;
        }
        .ml-risk-dot { background: var(--ml-orange); }
        .ml-dest-dot { background: var(--ml-primary); }

        /* Match suggestions */
        .ml-match-list { padding: 0 16px 16px; }
        .ml-match-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 0;
            border-bottom: 1px solid var(--ml-border);
        }
        .ml-match-item:last-child { border-bottom: none; }
        .ml-match-rank {
            width: 22px; height: 22px;
            border-radius: 6px;
            background: var(--ml-surface);
            border: 1px solid var(--ml-border);
            display: flex; align-items: center; justify-content: center;
            font-size: 11px;
            font-weight: 700;
            color: var(--ml-muted);
            flex-shrink: 0;
        }
        .ml-match-info { flex: 1; }
        .ml-match-name { font-size: 13px; font-weight: 600; color: var(--ml-text); }
        .ml-match-meta { font-size: 11px; color: var(--ml-muted); }

        /* ============================================================ */
        /* MOBILE & ANDROID RESPONSIVE DESIGN                           */
        /* ============================================================ */
        .ml-sidebar-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(11, 31, 75, 0.6);
            backdrop-filter: blur(2px);
            -webkit-backdrop-filter: blur(2px);
            z-index: 45;
            opacity: 0;
            transition: opacity 0.25s ease;
        }
        .ml-sidebar-backdrop.is-active {
            display: block;
            opacity: 1;
        }
        .ml-sidebar-close {
            display: none;
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background: rgba(255,255,255,0.12);
            border: none;
            color: #fff;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background 0.15s;
        }
        .ml-sidebar-close:hover {
            background: rgba(255,255,255,0.22);
        }
        .ml-hamburger-btn {
            display: none;
            flex-shrink: 0;
        }
        .ml-topbar-brand {
            display: none;
            align-items: center;
            gap: 6px;
        }
        .ml-bottom-nav {
            display: none;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 60px;
            background: #FFFFFF;
            border-top: 1px solid var(--ml-border);
            z-index: 50;
            box-shadow: 0 -4px 16px rgba(11, 31, 75, 0.08);
            padding: 0 4px env(safe-area-inset-bottom, 0px);
        }
        .ml-bottom-nav-inner {
            display: flex;
            align-items: center;
            justify-content: space-around;
            height: 100%;
            max-width: 500px;
            margin: 0 auto;
        }
        .ml-bottom-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 3px;
            flex: 1;
            height: 100%;
            min-height: 48px;
            color: var(--ml-muted);
            text-decoration: none;
            font-size: 10.5px;
            font-weight: 600;
            font-family: var(--font-body);
            transition: all 0.15s ease;
            position: relative;
        }
        .ml-bottom-item svg {
            width: 20px;
            height: 20px;
            transition: transform 0.15s ease;
        }
        .ml-bottom-item:active svg {
            transform: scale(0.9);
        }
        .ml-bottom-item.active {
            color: var(--ml-primary);
        }
        .ml-bottom-item.active::after {
            content: '';
            position: absolute;
            top: 0;
            left: 20%;
            right: 20%;
            height: 3px;
            background: var(--ml-primary);
            border-radius: 0 0 3px 3px;
        }
        .ml-table-responsive {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        /* Responsive Breakpoints */
        @media (max-width: 1023px) {
            .ml-sidebar {
                transform: translateX(-100%);
                transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
                width: 260px;
                box-shadow: 8px 0 28px rgba(0,0,0,0.3);
                z-index: 50;
            }
            .ml-sidebar.is-open {
                transform: translateX(0);
            }
            .ml-sidebar-close {
                display: flex;
            }
            .ml-main {
                margin-left: 0;
                padding-bottom: 74px; /* Room for bottom navigation bar */
            }
            .ml-hamburger-btn {
                display: flex;
            }
            .ml-topbar-brand {
                display: flex;
            }
            .ml-bottom-nav {
                display: block;
            }
            .ml-two-col {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            .ml-stat-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 14px;
            }
        }

        @media (max-width: 767px) {
            .ml-topbar {
                padding: 10px 14px;
                gap: 10px;
            }
            .ml-search {
                display: none;
            }
            .ml-content {
                padding: 16px 12px;
            }
            .ml-stat-value {
                font-size: 22px;
            }
            .ml-stat-badge, .ml-stat-badge-blue {
                font-size: 14px;
                padding: 3px 8px;
            }
            .ml-user-chip {
                padding: 4px;
            }
            .ml-user-chip .ml-user-info,
            .ml-user-chip svg {
                display: none;
            }
        }

        @media (max-width: 480px) {
            .ml-stat-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 8px;
            }
            .ml-stat-card {
                padding: 12px 10px;
            }
            .ml-stat-label {
                font-size: 11px;
                margin-bottom: 4px;
            }
            .ml-stat-value {
                font-size: 18px;
            }
            .ml-stat-sub {
                font-size: 10px;
            }
            .ml-section-header {
                padding: 12px 14px;
            }
        }
    </style>
</head>
<body>

{{-- ============================================================ --}}
{{-- BACKDROP FOR MOBILE DRAWER --}}
{{-- ============================================================ --}}
<div id="ml-sidebar-backdrop" class="ml-sidebar-backdrop" aria-hidden="true"></div>

{{-- ============================================================ --}}
{{-- SIDEBAR --}}
{{-- ============================================================ --}}
<aside class="ml-sidebar" id="ml-sidebar" role="dialog" aria-modal="true" aria-label="Menu Navigasi">
    <div class="ml-sidebar-logo" style="display: flex; align-items: center; justify-content: space-between;">
        <span>MediLoop</span>
        <button type="button" class="ml-sidebar-close" id="ml-sidebar-close" aria-label="Tutup Menu">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M18 6L6 18M6 6l12 12"/></svg>
        </button>
    </div>
    <nav class="ml-sidebar-nav">
        <a href="{{ route('dashboard') }}" class="ml-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/></svg>
            Dashboard
        </a>
        <a href="{{ route('batches.index') }}" class="ml-nav-item {{ request()->routeIs('batches.*') ? 'active' : '' }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            Stok &amp; Batch Obat
        </a>
        <a href="{{ route('analytics.index') }}" class="ml-nav-item {{ request()->routeIs('analytics.*') ? 'active' : '' }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3v18h18"/><path d="M18.7 8l-5.1 5.2-2.8-2.7L7 14.3"/></svg>
            Analitik &amp; Statistik
        </a>
        <a href="{{ route('map.index') }}" class="ml-nav-item {{ request()->routeIs('map.*') ? 'active' : '' }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"/><line x1="8" y1="2" x2="8" y2="18"/><line x1="16" y1="6" x2="16" y2="22"/></svg>
            Peta Distribusi Faskes
        </a>
        <a href="{{ route('transfers.index') }}" class="ml-nav-item {{ request()->routeIs('transfers.*') ? 'active' : '' }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M7 16V4m0 0L3 8m4-4 4 4M17 8v12m0 0 4-4m-4 4-4-4"/></svg>
            Transfers &amp; Alokasi
        </a>
        <a href="{{ route('audit.index') }}" class="ml-nav-item {{ request()->routeIs('audit.*') ? 'active' : '' }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 19v-6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2zm0 0V9a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v10m-6 0a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2m0 0V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-2a2 2 0 0 1-2-2z"/></svg>
            Pengawasan &amp; Audit
        </a>

        {{-- SETTINGS MENU WITH DROPDOWN --}}
        <div class="ml-settings-container">
            <button type="button" class="ml-nav-item" id="ml-settings-btn" aria-expanded="false" aria-haspopup="true">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg>
                <span>Settings</span>
                <svg class="ml-caret" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-left: auto; opacity: 0.6;"><path d="M6 9l6 6 6-6"/></svg>
            </button>

            <!-- Settings Popup Dropdown -->
            <div id="ml-settings-dropdown" class="ml-menu-dropdown ml-settings-menu" style="display: none;">
                <div class="ml-menu-header">Pengaturan Sistem</div>
                <a href="{{ route('profile.edit') }}" class="ml-menu-item">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    <span>Profil & Akun</span>
                </a>
                <a href="{{ route('appearance.edit') }}" class="ml-menu-item">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"/><path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/></svg>
                    <span>Tampilan</span>
                </a>
                <a href="{{ route('security.edit') }}" class="ml-menu-item">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    <span>Keamanan</span>
                </a>
                <div class="ml-menu-divider"></div>
                <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                    @csrf
                    <button type="submit" class="ml-menu-item ml-menu-item-danger">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                        <span>Keluar</span>
                    </button>
                </form>
            </div>
        </div>
    </nav>
</aside>

{{-- ============================================================ --}}
{{-- MAIN --}}
{{-- ============================================================ --}}
<div class="ml-main">
    {{-- TOPBAR --}}
    <header class="ml-topbar">
        {{-- MOBILE HAMBURGER TOGGLE --}}
        <button type="button" class="ml-icon-btn ml-hamburger-btn" id="ml-hamburger-btn" aria-label="Buka Menu Navigasi" aria-expanded="false" aria-controls="ml-sidebar">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>

        {{-- MOBILE BRAND --}}
        <div class="ml-topbar-brand">
            <span style="font-family: var(--font-heading); font-weight: 800; font-size: 18px; color: var(--ml-navy); letter-spacing: -0.02em;">MediLoop</span>
        </div>

        <div class="ml-search">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
            <input type="text" placeholder="Search">
        </div>
        <div class="ml-topbar-spacer"></div>
        <div class="ml-topbar-right">
            <div class="ml-icon-btn">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                <span class="ml-badge-dot"></span>
            </div>

            {{-- USER PROFILE CHIP WITH DROPDOWN --}}
            <div class="ml-profile-container">
                <div class="ml-user-chip" id="ml-profile-btn" role="button" tabindex="0" aria-expanded="false" aria-haspopup="true">
                    <div class="ml-avatar">
                        @auth
                            {{ auth()->user()->initials() }}
                        @else
                            PS
                        @endauth
                    </div>
                    <div class="ml-user-info">
                        <div class="ml-user-name">
                            @auth
                                {{ auth()->user()->facility?->name ?? auth()->user()->name }}
                            @else
                                Faskes Pengguna
                            @endauth
                        </div>
                        <div class="ml-user-role">
                            @auth
                                ({{ ucfirst(auth()->user()->role ?? 'Staff') }})
                            @else
                                (Farmasi)
                            @endauth
                        </div>
                    </div>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--ml-subtle); margin-left: 2px;"><path d="M6 9l6 6 6-6"/></svg>
                </div>

                <!-- Profile Popup Dropdown -->
                <div id="ml-profile-dropdown" class="ml-menu-dropdown ml-profile-menu" style="display: none;">
                    <div class="ml-profile-dropdown-user">
                        <div class="ml-avatar ml-avatar-lg">
                            @auth
                                {{ auth()->user()->initials() }}
                            @else
                                PS
                            @endauth
                        </div>
                        <div style="overflow: hidden; flex: 1;">
                            <div class="ml-profile-name">
                                @auth
                                    {{ auth()->user()->name }}
                                @else
                                    Tenaga Kefarmasian
                                @endauth
                            </div>
                            <div class="ml-profile-email">
                                @auth
                                    {{ auth()->user()->email }}
                                @else
                                    farmasi@faskes.id
                                @endauth
                            </div>
                            <span class="ml-role-badge">
                                @auth
                                    {{ strtoupper(auth()->user()->role ?? 'Staff') }}
                                @else
                                    FARMASI
                                @endauth
                            </span>
                        </div>
                    </div>
                    <div class="ml-menu-divider"></div>
                    <a href="{{ route('profile.edit') }}" class="ml-menu-item">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        <span>Edit Profil</span>
                    </a>
                    <a href="{{ route('security.edit') }}" class="ml-menu-item">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        <span>Keamanan Akun</span>
                    </a>
                    <div class="ml-menu-divider"></div>
                    <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                        @csrf
                        <button type="submit" class="ml-menu-item ml-menu-item-danger">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                            <span>Keluar</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    {{-- CONTENT --}}
    <main class="ml-content">
        @yield('content')
    </main>
</div>

{{-- BOTTOM NAVIGATION (Android / Mobile Native-Feel App Bar) --}}
<nav class="ml-bottom-nav" aria-label="Navigasi Bawah">
    <div class="ml-bottom-nav-inner">
        <a href="{{ route('dashboard') }}" class="ml-bottom-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="{{ request()->routeIs('dashboard') ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="{{ request()->routeIs('dashboard') ? '0' : '2' }}">
                <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/>
            </svg>
            <span>Dashboard</span>
        </a>
        <a href="{{ route('batches.index') }}" class="ml-bottom-item {{ request()->routeIs('batches.*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
            <span>Stok</span>
        </a>
        <a href="{{ route('analytics.index') }}" class="ml-bottom-item {{ request()->routeIs('analytics.*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 3v18h18"/><path d="M18.7 8l-5.1 5.2-2.8-2.7L7 14.3"/>
            </svg>
            <span>Analitik</span>
        </a>
        <a href="{{ route('map.index') }}" class="ml-bottom-item {{ request()->routeIs('map.*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"/><line x1="8" y1="2" x2="8" y2="18"/><line x1="16" y1="6" x2="16" y2="22"/>
            </svg>
            <span>Peta</span>
        </a>
        <a href="{{ route('transfers.index') }}" class="ml-bottom-item {{ request()->routeIs('transfers.*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M7 16V4m0 0L3 8m4-4 4 4M17 8v12m0 0 4-4m-4 4-4-4"/>
            </svg>
            <span>Alokasi</span>
        </a>
        <a href="{{ route('audit.index') }}" class="ml-bottom-item {{ request()->routeIs('audit.*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 19v-6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2zm0 0V9a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v10m-6 0a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2m0 0V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-2a2 2 0 0 1-2-2z"/>
            </svg>
            <span>Audit</span>
        </a>
    </div>
</nav>

{{-- Vanilla JS Dropdown & Mobile Drawer Interactions --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const settingsBtn = document.getElementById('ml-settings-btn');
        const settingsDropdown = document.getElementById('ml-settings-dropdown');
        const profileBtn = document.getElementById('ml-profile-btn');
        const profileDropdown = document.getElementById('ml-profile-dropdown');

        // Mobile Sidebar Drawer Elements
        const hamburgerBtn = document.getElementById('ml-hamburger-btn');
        const sidebar = document.getElementById('ml-sidebar');
        const sidebarCloseBtn = document.getElementById('ml-sidebar-close');
        const sidebarBackdrop = document.getElementById('ml-sidebar-backdrop');

        function openMobileSidebar() {
            if (sidebar && sidebarBackdrop) {
                sidebar.classList.add('is-open');
                sidebarBackdrop.classList.add('is-active');
                hamburgerBtn?.setAttribute('aria-expanded', 'true');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeMobileSidebar() {
            if (sidebar && sidebarBackdrop) {
                sidebar.classList.remove('is-open');
                sidebarBackdrop.classList.remove('is-active');
                hamburgerBtn?.setAttribute('aria-expanded', 'false');
                document.body.style.overflow = '';
            }
        }

        if (hamburgerBtn) {
            hamburgerBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                openMobileSidebar();
            });
        }

        if (sidebarCloseBtn) {
            sidebarCloseBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                closeMobileSidebar();
            });
        }

        if (sidebarBackdrop) {
            sidebarBackdrop.addEventListener('click', function () {
                closeMobileSidebar();
            });
        }

        function closeAllDropdowns() {
            if (settingsDropdown) {
                settingsDropdown.style.display = 'none';
                settingsBtn?.setAttribute('aria-expanded', 'false');
            }
            if (profileDropdown) {
                profileDropdown.style.display = 'none';
                profileBtn?.setAttribute('aria-expanded', 'false');
            }
        }

        if (settingsBtn && settingsDropdown) {
            settingsBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                const isOpen = settingsDropdown.style.display === 'block';
                closeAllDropdowns();
                if (!isOpen) {
                    settingsDropdown.style.display = 'block';
                    settingsBtn.setAttribute('aria-expanded', 'true');
                }
            });
        }

        if (profileBtn && profileDropdown) {
            profileBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                const isOpen = profileDropdown.style.display === 'block';
                closeAllDropdowns();
                if (!isOpen) {
                    profileDropdown.style.display = 'block';
                    profileBtn.setAttribute('aria-expanded', 'true');
                }
            });

            profileBtn.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    profileBtn.click();
                }
            });
        }

        document.addEventListener('click', function (e) {
            const isClickInsideSettings = settingsBtn?.contains(e.target) || settingsDropdown?.contains(e.target);
            const isClickInsideProfile = profileBtn?.contains(e.target) || profileDropdown?.contains(e.target);

            if (!isClickInsideSettings && !isClickInsideProfile) {
                closeAllDropdowns();
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeAllDropdowns();
                closeMobileSidebar();
            }
        });
    });
</script>

@livewireScripts
</body>
</html>
