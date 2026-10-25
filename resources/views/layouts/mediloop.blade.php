<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ filled($title ?? null) ? $title.' - MediLoop' : 'MediLoop' }}</title>
    @include('partials.favicons')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        /* MediLoop design system — legacy aliases map to app.css tokens. */
        :root {
            --ml-navy: var(--color-brand-navy);
            --ml-primary: var(--color-brand-primary);
            --ml-primary-strong: var(--color-brand-primary-strong);
            --ml-surface: var(--color-surface-canvas);
            --ml-card: var(--color-surface-raised);
            --ml-border: var(--color-border);
            --ml-text: var(--color-text);
            --ml-muted: var(--color-text-muted);
            --ml-subtle: var(--color-text-subtle);
            --ml-danger: #B91C1C;
            --ml-warning: var(--color-brand-warning);
            --ml-orange: #B45309;
            --ml-risk: var(--color-brand-risk);
            --font-heading: var(--font-sans);
            --font-body: var(--font-sans);
            --font-code: var(--font-mono);
            --ml-radius-sm: 8px;
            --ml-radius-md: 10px;
            --ml-radius-lg: 14px;
            --ml-shadow-panel: var(--shadow-panel);
            --ml-shadow-overlay: var(--shadow-overlay);
        }
        *, ::before, ::after { box-sizing: border-box; }

        /* Custom Auto-Hiding MediLoop Scrollbar */
        html, body, * {
            scrollbar-width: thin;
            scrollbar-color: transparent transparent;
            transition: scrollbar-color 0.3s ease;
        }
        html.is-scrolling, body.is-scrolling,
        html:hover, body:hover,
        .ml-table-responsive.is-scrolling,
        .ml-table-responsive:hover {
            scrollbar-color: rgba(82, 98, 127, 0.35) transparent;
        }

        ::-webkit-scrollbar {
            width: 7px;
            height: 7px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background-color: transparent;
            border-radius: 999px;
            border: 1px solid transparent;
            background-clip: padding-box;
            transition: background-color 0.25s ease;
        }

        /* Reveal thumb on hover or when actively scrolling */
        html:hover::-webkit-scrollbar-thumb,
        body:hover::-webkit-scrollbar-thumb,
        .ml-table-responsive:hover::-webkit-scrollbar-thumb,
        .ml-sidebar:hover::-webkit-scrollbar-thumb,
        html.is-scrolling::-webkit-scrollbar-thumb,
        body.is-scrolling::-webkit-scrollbar-thumb,
        .is-scrolling::-webkit-scrollbar-thumb {
            background-color: rgba(82, 98, 127, 0.35);
        }

        /* Active / Hovering directly on thumb */
        ::-webkit-scrollbar-thumb:hover {
            background-color: var(--ml-primary, #1D4ED8) !important;
        }
        ::-webkit-scrollbar-thumb:active {
            background-color: #1E40AF !important;
        }
        ::-webkit-scrollbar-corner {
            background: transparent;
        }

        body {
            font-family: var(--font-body);
            color: var(--ml-text);
            background: var(--ml-surface);
            margin: 0;
            min-height: 100vh;
            font-size: 14px;
            line-height: 1.5;
        }
        h1,h2,h3,h4,h5,h6 {
            font-family: var(--font-heading);
            font-weight: 600;
            letter-spacing: -0.025em;
        }
        code, kbd, samp {
            font-family: var(--font-code);
        }

        /* Sidebar */
        .ml-sidebar {
            position: fixed;
            top: 0; left: 0; bottom: 0;
            width: 232px;
            background: var(--ml-navy);
            display: flex;
            flex-direction: column;
            z-index: 40;
            flex-shrink: 0;
        }
        .ml-sidebar-logo {
            padding: 20px 18px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }
        .ml-sidebar-logo span {
            font-family: var(--font-heading);
            font-size: 20px;
            font-weight: 600;
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
            border-radius: 8px;
            font-size: 13.5px;
            font-weight: 500;
            color: rgba(255,255,255,0.65);
            text-decoration: none;
            transition: background 0.15s ease, color 0.15s ease;
            cursor: pointer;
            border: none;
            background: transparent;
            font-family: var(--font-body);
            width: 100%;
            text-align: left;
            min-height: 40px;
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
            border-radius: 10px;
            box-shadow: 0 4px 16px rgba(11, 31, 75, 0.10);
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
            transition: background-color 0.12s ease, color 0.12s ease;
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
            transition: background-color 0.12s ease, color 0.12s ease;
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
            margin-left: 232px;
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
            transition: background-color 0.15s ease, border-color 0.15s ease, color 0.15s ease;
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
            transition: background-color 0.15s ease, border-color 0.15s ease;
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
        .ml-content { padding: 28px 32px 36px; flex: 1; }

        /* Page header */
        .ml-page-head {
            display: flex;
            flex-wrap: wrap;
            align-items: flex-start;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 26px;
        }
        .ml-page-title-row {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }
        .ml-page-title {
            margin: 0;
            font-size: 22px;
            font-weight: 700;
            line-height: 1.25;
            letter-spacing: -0.02em;
            color: var(--ml-text);
            font-family: var(--font-heading);
        }
        .ml-page-sub {
            margin: 6px 0 0;
            font-size: 13px;
            line-height: 1.5;
            color: var(--ml-muted);
        }
        .ml-page-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
        }
        .ml-facility-chip {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 999px;
            font-size: 11.5px;
            font-weight: 600;
            background: #EAF1FF;
            color: var(--ml-primary);
            border: 1px solid var(--ml-border);
            white-space: nowrap;
        }
        .ml-sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }

        /* Stat Cards */
        .ml-stat-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 20px;
            margin-bottom: 24px;
        }
        .ml-stat-card {
            min-width: 0;
            background: #fff;
            border: 1px solid var(--ml-border);
            border-radius: 16px;
            padding: 20px 22px;
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

        /* Section card */
        .ml-section {
            background: #fff;
            border: 1px solid var(--ml-border);
            border-radius: 16px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.04);
            overflow: hidden;
        }
        .ml-section-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--ml-border);
        }
        .ml-section-title {
            margin: 0;
            font-size: 16px;
            font-weight: 700;
            color: var(--ml-text);
            font-family: var(--font-heading);
            letter-spacing: -0.01em;
        }
        .ml-section-sub {
            margin: 4px 0 0;
            font-size: 12.5px;
            line-height: 1.5;
            color: var(--ml-muted);
        }
        .ml-section-sub strong { color: var(--ml-text); font-weight: 600; }

        /* Table */
        .ml-table { width: 100%; border-collapse: collapse; }
        .ml-table th {
            padding: 12px 20px;
            background: #fff;
            font-size: 11.5px;
            font-weight: 600;
            letter-spacing: 0.02em;
            color: var(--ml-muted);
            text-align: left;
            border-bottom: 1px solid var(--ml-border);
            white-space: nowrap;
        }
        .ml-table td {
            padding: 18px 20px;
            font-size: 13px;
            border-bottom: 1px solid var(--ml-border);
            vertical-align: middle;
        }
        .ml-table th:first-child,
        .ml-table td:first-child { padding-left: 24px; }
        .ml-table th:last-child,
        .ml-table td:last-child { padding-right: 24px; }
        .ml-table tr:last-child td { border-bottom: none; }
        .ml-table tbody tr:hover td { background: #FAFCFF; }
        .ml-table-airy { min-width: 720px; }
        .ml-table .is-right { text-align: right; }

        /* Forecast cells */
        .ml-cell-title {
            font-size: 14px;
            font-weight: 600;
            line-height: 1.35;
            color: var(--ml-text);
        }
        .ml-cell-meta {
            margin-top: 3px;
            font-size: 12px;
            line-height: 1.45;
            color: var(--ml-muted);
        }
        .ml-cell-mono {
            font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
            font-size: 11px;
            letter-spacing: -0.01em;
        }
        .ml-cold-chain {
            display: inline-flex;
            align-items: center;
            vertical-align: -1px;
            margin-left: 4px;
            color: #0369A1;
        }
        .ml-cell-stock {
            text-align: right;
            white-space: nowrap;
            font-variant-numeric: tabular-nums;
        }
        .ml-stock-value {
            font-size: 15px;
            font-weight: 600;
            color: var(--ml-text);
        }
        .ml-stock-unit {
            margin-left: 4px;
            font-size: 12px;
            font-weight: 400;
            color: var(--ml-muted);
        }
        .ml-row-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
        }

        /* Runway: expiry vs depletion */
        .ml-runway-text {
            display: flex;
            flex-wrap: wrap;
            align-items: baseline;
            gap: 2px 6px;
            font-size: 12.5px;
            line-height: 1.45;
            color: var(--ml-muted);
        }
        .ml-runway-text .is-risk { color: #B45309; font-weight: 600; }
        .ml-runway-text .is-safe { color: var(--ml-text); font-weight: 600; }
        .ml-runway-sep { color: var(--ml-subtle); }
        .ml-runway {
            position: relative;
            width: 100%;
            max-width: 240px;
            height: 6px;
            margin-top: 8px;
            border-radius: 99px;
            background: #EAF1FF;
            overflow: hidden;
        }
        .ml-runway-fill {
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            border-radius: 99px;
            background: var(--ml-primary);
            opacity: 0.5;
        }
        .ml-runway-mark {
            position: absolute;
            top: 0;
            bottom: 0;
            width: 3px;
            margin-left: -1.5px;
            background: var(--ml-risk);
            border-radius: 2px;
        }

        /* Empty state */
        .ml-empty-state {
            max-width: 400px;
            margin: 0 auto;
            padding: 40px 20px;
            text-align: center;
            color: var(--ml-muted);
        }
        .ml-empty-state svg { color: var(--ml-subtle); margin-bottom: 10px; }
        .ml-empty-title {
            font-size: 14px;
            font-weight: 600;
            color: var(--ml-text);
            margin-bottom: 4px;
        }
        .ml-empty-copy {
            font-size: 12.5px;
            line-height: 1.55;
            margin: 0 0 16px;
        }

        /* Risk badge */
        .ml-risk {
            display: inline-flex;
            align-items: center;
            padding: 4px 9px;
            border: 1px solid transparent;
            border-radius: 999px;
            font-size: 10.5px;
            font-weight: 700;
            letter-spacing: 0.04em;
            white-space: nowrap;
        }
        .ml-risk-critical { background: #FEF2F2; color: #991B1B; border-color: #FECACA; }
        .ml-risk-high { background: #FFF7ED; color: #B45309; border-color: #FED7AA; }
        .ml-risk-medium { background: #FFFBEB; color: #92400E; border-color: #FDE68A; }
        .ml-risk-low { background: #F1F5F9; color: #475569; border-color: #E2E8F0; }

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
            transition: background-color 0.15s ease;
            text-decoration: none;
        }
        .ml-btn-primary:hover { background: #1e40af; }

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
            transition: background-color 0.15s ease, border-color 0.15s ease, color 0.15s ease;
            text-decoration: none;
        }
        .ml-btn-ghost:hover { background: #EFF6FF; }

        .ml-btn-quiet {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 14px;
            background: #fff;
            color: var(--ml-primary);
            border: 1px solid var(--ml-border);
            border-radius: 9px;
            font-size: 12.5px;
            font-weight: 600;
            font-family: var(--font-body);
            cursor: pointer;
            transition: background 0.15s, border-color 0.15s;
            text-decoration: none;
            white-space: nowrap;
        }
        .ml-btn-quiet:hover { background: #EAF1FF; border-color: #C7D7F5; }
        .ml-btn-sm {
            padding: 6px 11px;
            font-size: 12px;
        }
        .ml-btn-primary:focus-visible,
        .ml-btn-ghost:focus-visible,
        .ml-btn-quiet:focus-visible,
        .ml-nav-item:focus-visible,
        .ml-user-chip:focus-visible {
            outline: 2px solid var(--ml-primary);
            outline-offset: 2px;
        }

        /* Match recommendations */
        .ml-rec-band { margin-top: 24px; }
        .ml-rec-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 16px;
            padding: 20px 24px 8px;
        }
        .ml-rec-card {
            display: flex;
            flex-direction: column;
            gap: 12px;
            padding: 16px;
            background: var(--ml-surface);
            border: 1px solid #E6EEF9;
            border-radius: 12px;
        }
        .ml-rec-top {
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }
        .ml-rec-rank {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 24px;
            height: 24px;
            flex-shrink: 0;
            border: 1px solid var(--ml-border);
            border-radius: 8px;
            background: #fff;
            font-size: 11px;
            font-weight: 700;
            color: var(--ml-muted);
        }
        .ml-rec-body { flex: 1; min-width: 0; }
        .ml-rec-name {
            font-size: 13.5px;
            font-weight: 600;
            line-height: 1.4;
            color: var(--ml-text);
        }
        .ml-rec-meta {
            margin-top: 2px;
            font-size: 11.5px;
            color: var(--ml-muted);
        }
        .ml-rec-score {
            padding: 3px 8px;
            border-radius: 999px;
            background: #EAF1FF;
            color: var(--ml-primary);
            font-size: 11.5px;
            font-weight: 700;
            font-variant-numeric: tabular-nums;
            white-space: nowrap;
        }
        .ml-rec-facts {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            gap: 8px;
            padding-top: 10px;
            border-top: 1px solid #E6EEF9;
            font-size: 11.5px;
            color: var(--ml-muted);
        }
        .ml-rec-cta { align-self: flex-start; }
        .ml-rec-empty {
            grid-column: 1 / -1;
            padding: 20px 0;
            font-size: 12.5px;
            color: var(--ml-muted);
        }
        .ml-rec-note {
            margin: 0;
            padding: 4px 24px 20px;
            font-size: 11.5px;
            line-height: 1.5;
            color: var(--ml-muted);
        }

        /* ============================================================ */
        /* FACILITY NETWORK MAP                                         */
        /* ============================================================ */
        .ml-network-stats {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
            margin-bottom: 20px;
        }
        .ml-network-stat {
            display: flex;
            flex-direction: column;
            gap: 4px;
            padding: 18px 20px;
            background: #fff;
            border: 1px solid var(--ml-border);
            border-radius: 14px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
        }
        .ml-network-stat-label {
            font-size: 12px;
            font-weight: 500;
            color: var(--ml-muted);
        }
        .ml-network-stat-value {
            font-family: var(--font-heading);
            font-size: 26px;
            font-weight: 800;
            line-height: 1.1;
            letter-spacing: -0.03em;
            color: var(--ml-text);
            font-variant-numeric: tabular-nums;
        }
        .ml-network-stat-unit {
            margin-left: 2px;
            font-family: var(--font-body);
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0;
            color: var(--ml-muted);
        }
        .ml-network-stat-sub {
            font-size: 11.5px;
            color: var(--ml-subtle);
        }
        .ml-network-toolbar {
            display: flex;
            flex-wrap: wrap;
            align-items: flex-end;
            gap: 14px 18px;
            padding: 14px 18px;
            margin-bottom: 20px;
            background: #fff;
            border: 1px solid var(--ml-border);
            border-radius: 14px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
        }
        .ml-network-field {
            display: flex;
            flex-direction: column;
            gap: 4px;
            min-width: 170px;
        }
        .ml-network-field label {
            font-size: 11.5px;
            font-weight: 600;
            color: var(--ml-muted);
        }
        .ml-network-field select {
            min-height: 38px;
            padding: 7px 10px;
            font-family: var(--font-body);
            font-size: 12.5px;
            color: var(--ml-text);
            background: #fff;
            border: 1px solid var(--ml-border);
            border-radius: 9px;
        }
        .ml-network-field select:focus-visible,
        .ml-network-check input:focus-visible {
            outline: 2px solid var(--ml-primary);
            outline-offset: 2px;
        }
        .ml-network-check {
            display: flex;
            align-items: center;
            gap: 8px;
            min-height: 38px;
            font-size: 12.5px;
            font-weight: 500;
            color: var(--ml-text);
            cursor: pointer;
        }
        .ml-network-check input {
            width: 15px;
            height: 15px;
            accent-color: var(--ml-primary);
        }
        .ml-network-actions {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-left: auto;
        }
        .ml-network-layout {
            display: grid;
            grid-template-columns: minmax(0, 1.65fr) minmax(300px, 1fr);
            gap: 20px;
            align-items: start;
        }
        .ml-network-map-wrap {
            position: relative;
            padding: 20px 24px 0;
        }
        .ml-network-map {
            height: 480px;
            border: 1px solid var(--ml-border);
            border-radius: 12px;
            background: var(--ml-surface);
            position: relative;
            z-index: 0;
            isolation: isolate;
            overflow: hidden;
        }
        .ml-network-map:focus-visible {
            outline: 2px solid var(--ml-primary);
            outline-offset: 2px;
        }
        .ml-network-map .leaflet-container {
            height: 100%;
            width: 100%;
            border-radius: 12px;
            font-family: var(--font-body);
            background: #EAF1FF;
        }
        .ml-network-map-fallback {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            margin: 0;
            padding: 20px;
            text-align: center;
            font-size: 12.5px;
            color: var(--ml-muted);
        }
        .ml-network-noscript {
            margin: 8px 0 0;
            padding: 10px 12px;
            border-left: 3px solid var(--ml-warning);
            background: #FFFBEB;
            font-size: 12px;
            line-height: 1.5;
            color: #78350F;
        }
        .ml-network-legend {
            display: flex;
            flex-wrap: wrap;
            gap: 8px 18px;
            padding: 12px 24px 0;
        }
        .ml-network-legend-item {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            font-size: 11.5px;
            color: var(--ml-muted);
        }
        .ml-network-legend-dot {
            width: 11px;
            height: 11px;
            border-radius: 50%;
            flex-shrink: 0;
        }
        .ml-network-legend-dot.is-origin { background: var(--ml-navy); }
        .ml-network-legend-dot.is-rsud { background: var(--color-facility-rsud); }
        .ml-network-legend-dot.is-puskesmas { background: var(--color-facility-puskesmas); }
        .ml-network-legend-dot.is-klinik { background: var(--color-facility-klinik); }
        .ml-network-legend-dot.is-rs-swasta { background: var(--color-facility-rs-swasta); }
        .ml-network-legend-dot.is-other { background: var(--color-facility-other); }
        .ml-network-legend-dot.is-radius {
            background: transparent;
            border: 1.5px dashed var(--ml-primary);
        }
        .ml-network-source {
            margin: 0;
            padding: 10px 24px 18px;
            font-size: 11px;
            line-height: 1.5;
            color: var(--ml-subtle);
        }
        .ml-network-list {
            list-style: none;
            margin: 0;
            padding: 4px 0;
            max-height: 560px;
            overflow-y: auto;
        }
        .ml-network-item {
            border-bottom: 1px solid var(--ml-border);
        }
        .ml-network-item:last-child {
            border-bottom: none;
        }
        .ml-network-target {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            width: 100%;
            padding: 14px 20px;
            background: transparent;
            border: 0;
            text-align: left;
            cursor: pointer;
            font-family: var(--font-body);
            transition: background 0.15s ease;
        }
        .ml-network-target:hover {
            background: #FAFCFF;
        }
        .ml-network-target:focus-visible {
            outline: 2px solid var(--ml-primary);
            outline-offset: -2px;
        }
        .ml-network-target.is-active {
            background: #EAF1FF;
            box-shadow: inset 3px 0 0 var(--ml-primary);
        }
        .ml-network-rank {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 24px;
            height: 24px;
            flex-shrink: 0;
            border: 1px solid var(--ml-border);
            border-radius: 8px;
            background: #fff;
            font-size: 11px;
            font-weight: 700;
            color: var(--ml-muted);
            font-variant-numeric: tabular-nums;
        }
        .ml-network-body {
            display: flex;
            flex-direction: column;
            gap: 2px;
            flex: 1;
            min-width: 0;
        }
        .ml-network-name {
            font-size: 13px;
            font-weight: 600;
            line-height: 1.4;
            color: var(--ml-text);
        }
        .ml-network-meta {
            font-size: 11.5px;
            line-height: 1.45;
            color: var(--ml-muted);
        }
        .ml-network-cold {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            margin-top: 3px;
            font-size: 10.5px;
            font-weight: 600;
            color: #0369A1;
        }
        .ml-network-distance {
            flex-shrink: 0;
            text-align: right;
            font-size: 12.5px;
            font-weight: 700;
            color: var(--ml-primary);
            font-variant-numeric: tabular-nums;
            white-space: nowrap;
        }
        .ml-network-distance-unit {
            font-size: 10.5px;
            font-weight: 600;
            color: var(--ml-muted);
        }
        .ml-network-proximity {
            display: block;
            margin-top: 2px;
            font-size: 10px;
            font-weight: 500;
            color: var(--ml-subtle);
        }
        .ml-network-distance-missing {
            font-size: 10.5px;
            font-weight: 500;
            color: var(--ml-subtle);
            white-space: normal;
        }
        .ml-network-empty {
            padding: 28px 20px;
            text-align: center;
            font-size: 12.5px;
            color: var(--ml-muted);
        }
        .ml-network-empty p {
            margin: 0 0 12px;
        }
        .ml-map-marker-shell {
            background: transparent;
            border: none;
        }
        .ml-map-marker {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 26px;
            height: 26px;
            border: 3px solid var(--color-surface-raised);
            border-radius: 50%;
            color: var(--color-surface-raised);
            font-family: var(--font-heading);
            font-size: 10px;
            font-weight: 800;
            line-height: 1;
            box-shadow: 0 2px 6px rgba(11, 31, 75, 0.28);
        }
        .ml-map-marker-origin {
            width: 30px;
            height: 30px;
            background: var(--ml-navy);
        }
        .ml-map-marker-facility {
            background: var(--ml-primary);
        }
        .ml-map-marker-rsud {
            background: var(--color-facility-rsud);
        }
        .ml-map-marker-puskesmas {
            background: var(--color-facility-puskesmas);
        }
        .ml-map-marker-klinik {
            background: var(--color-facility-klinik);
        }
        .ml-map-marker-rs-swasta {
            background: var(--color-facility-rs-swasta);
        }
        .ml-map-marker-other {
            background: var(--color-facility-other);
        }
        .ml-map-hint {
            padding: 3px 8px;
            border: 1px solid var(--ml-border);
            border-radius: 6px;
            background: rgba(255, 255, 255, 0.92);
            font-family: var(--font-body);
            font-size: 10.5px;
            color: var(--ml-muted);
        }
        .ml-map-radius-btn {
            padding: 6px 10px;
            border: 1px solid var(--ml-border);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.96);
            box-shadow: 0 1px 4px rgba(11, 31, 75, 0.12);
            font-family: var(--font-body);
            font-size: 11px;
            font-weight: 600;
            color: var(--ml-primary);
            cursor: pointer;
        }
        .ml-map-radius-btn:hover {
            background: var(--ml-sky, #EAF1FF);
        }
        .ml-map-radius-btn:focus-visible {
            outline: 2px solid var(--ml-primary);
            outline-offset: 2px;
        }
        .ml-map-popup {
            font-family: var(--font-body);
            max-width: 240px;
        }
        .ml-map-popup p {
            margin: 0;
        }
        .ml-map-popup-title {
            font-size: 13px;
            font-weight: 700;
            line-height: 1.35;
            color: var(--ml-text);
        }
        .ml-map-popup-meta {
            margin-top: 3px !important;
            font-size: 11px;
            line-height: 1.45;
            color: var(--ml-muted);
        }
        .ml-map-popup-distance {
            margin-top: 6px !important;
            font-size: 11.5px;
            font-weight: 700;
            color: var(--ml-primary);
            font-variant-numeric: tabular-nums;
        }
        .ml-map-popup-cold {
            margin-top: 3px !important;
            font-size: 10.5px;
            font-weight: 600;
            color: #0369A1;
        }

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
            pointer-events: none; /* Tambahkan ini agar tidak menghalangi klik saat tidak aktif */
        }
        .ml-sidebar-backdrop.is-active {
            display: block;
            opacity: 1;
            pointer-events: auto; /* Aktifkan klik hanya saat sidebar terbuka */
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
            transition: background-color 0.15s ease, color 0.15s ease;
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
            .ml-content {
                padding: 24px;
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
            .ml-stat-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 14px;
            }
            .ml-table-airy { min-width: 0; }
            .ml-table-airy thead {
                position: absolute;
                width: 1px;
                height: 1px;
                overflow: hidden;
                clip: rect(0, 0, 0, 0);
                white-space: nowrap;
            }
            .ml-table-airy,
            .ml-table-airy tbody,
            .ml-table-airy tr,
            .ml-table-airy td {
                display: block;
                width: 100%;
            }
            .ml-table-airy tr {
                display: grid;
                grid-template-columns: minmax(0, 1fr) auto;
                gap: 10px 16px;
                padding: 18px 24px;
                border-bottom: 1px solid var(--ml-border);
            }
            .ml-table-airy tr:last-child { border-bottom: none; }
            .ml-table-airy tbody tr:hover td { background: transparent; }
            .ml-table-airy td,
            .ml-table-airy td:first-child,
            .ml-table-airy td:last-child {
                padding: 0;
                border: none;
            }
            .ml-table-airy td:first-child,
            .ml-table-airy td[colspan] { grid-column: 1 / -1; }
            .ml-table-airy td:nth-child(2) {
                grid-column: 2;
                grid-row: 2;
                align-self: start;
                padding-top: 1px;
            }
            .ml-table-airy td:nth-child(3) {
                grid-column: 1;
                grid-row: 2;
            }
            .ml-table-airy td:last-child {
                grid-column: 1 / -1;
                grid-row: 3;
            }
            .ml-table-airy td[colspan] {
                grid-row: 1;
            }
            .ml-table-registry td:nth-child(2) { grid-column: 2; grid-row: 2; }
            .ml-table-registry td:nth-child(3) { grid-column: 1; grid-row: 2; }
            .ml-table-registry td:nth-child(4) { grid-column: 1; grid-row: 3; }
            .ml-table-registry td:nth-child(5) { grid-column: 2; grid-row: 3; }
            .ml-table-registry td:nth-child(6) { grid-column: 1 / -1; grid-row: 4; }
            .ml-table-transfers td:nth-child(2) { grid-column: 1 / -1; grid-row: 2; }
            .ml-table-transfers td:nth-child(3) { grid-column: 1 / -1; grid-row: 3; }
            .ml-table-transfers td:nth-child(4) { grid-column: 1; grid-row: 4; }
            .ml-table-transfers td:nth-child(5) { grid-column: 2; grid-row: 4; }
            .ml-table-transfers td:nth-child(6) { grid-column: 1 / -1; grid-row: 5; }
            .ml-table-team td:nth-child(2) { grid-column: 1; grid-row: 2; }
            .ml-table-team td:nth-child(3) { grid-column: 2; grid-row: 2; }
            .ml-table-team td:nth-child(4) { grid-column: 1 / -1; grid-row: 3; }
            .ml-table-airy td[data-label]::before {
                content: attr(data-label);
                display: block;
                margin-bottom: 2px;
                font-size: 10.5px;
                font-weight: 600;
                letter-spacing: 0.04em;
                text-transform: uppercase;
                color: var(--ml-subtle);
            }
            .ml-row-actions { justify-content: flex-start; }
            .ml-runway { max-width: 100%; }
            .ml-network-layout { grid-template-columns: 1fr; }
            .ml-network-map { height: 380px; }
            .ml-network-list { max-height: none; }
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
            .ml-page-head {
                gap: 16px;
                margin-bottom: 20px;
            }
            .ml-page-title {
                font-size: 19px;
            }
            .ml-page-actions {
                width: 100%;
            }
            .ml-page-actions > a {
                flex: 1;
                justify-content: center;
            }
            .ml-filter-search,
            .ml-filter-status,
            .ml-filter-type,
            .ml-filter-status-wide {
                width: 100%;
                min-width: 0;
            }
            .ml-toolbar-actions {
                width: 100%;
            }
            .ml-toolbar-actions .ml-btn-primary,
            .ml-toolbar-actions .ml-btn-quiet {
                flex: 1;
                justify-content: center;
            }
            .ml-section-header {
                padding: 16px 18px;
            }
            .ml-table-airy tr {
                padding: 16px 18px;
            }
            .ml-rec-grid {
                gap: 12px;
                padding: 16px 18px 4px;
            }
            .ml-rec-note {
                padding: 4px 18px 16px;
            }
            .ml-network-stats {
                grid-template-columns: 1fr;
                gap: 10px;
            }
            .ml-network-stat {
                padding: 14px 16px;
            }
            .ml-network-toolbar {
                padding: 14px;
            }
            .ml-network-field {
                width: 100%;
            }
            .ml-network-actions {
                width: 100%;
                margin-left: 0;
            }
            .ml-network-actions .ml-btn-primary {
                flex: 1;
                justify-content: center;
            }
            .ml-network-map-wrap {
                padding: 16px 14px 0;
            }
            .ml-network-map {
                height: 320px;
            }
            .ml-network-legend {
                padding: 10px 14px 0;
            }
            .ml-network-source {
                padding: 8px 14px 14px;
            }
            .ml-network-target {
                padding: 13px 14px;
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
                grid-template-columns: repeat(2, minmax(0, 1fr));
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
            .ml-table-airy tr {
                padding: 14px;
            }
            .ml-rec-grid {
                padding: 16px 14px 4px;
            }
            .ml-rec-note {
                padding: 4px 14px 14px;
            }
            .ml-network-distance {
                font-size: 11.5px;
            }
            .ml-network-proximity {
                font-size: 9.5px;
            }
            /* Collapse filter grid to single column on very small screens */
            form[style*="grid-template-columns: repeat(12"] {
                display: flex !important;
                flex-direction: column;
            }
            form[style*="grid-template-columns: repeat(12"] > div {
                width: 100% !important;
            }
        }

        /* ============================================================
           ONBOARDING & ADMIN SURFACES
           ============================================================ */
        .ml-breadcrumb {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 6px;
            margin-bottom: 14px;
            font-size: 12px;
            color: var(--ml-muted);
        }
        .ml-breadcrumb a {
            color: var(--ml-primary);
            text-decoration: none;
            font-weight: 500;
        }
        .ml-breadcrumb a:hover { text-decoration: underline; }
        .ml-breadcrumb-sep { color: var(--ml-subtle); }

        .ml-summary-bar {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            padding: 14px 20px;
            margin-bottom: 20px;
            background: #fff;
            border: 1px solid var(--ml-border);
            border-radius: 12px;
        }
        .ml-summary-item {
            display: flex;
            align-items: baseline;
            gap: 8px;
            padding: 0 20px;
        }
        .ml-summary-item:first-child { padding-left: 0; }
        .ml-summary-item + .ml-summary-item { border-left: 1px solid var(--ml-border); }
        .ml-summary-value {
            font-family: var(--font-heading);
            font-size: 20px;
            font-weight: 700;
            line-height: 1;
            font-variant-numeric: tabular-nums;
        }
        .ml-summary-label {
            font-size: 12px;
            color: var(--ml-muted);
        }

        .ml-toolbar {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 10px;
            padding: 14px 20px;
            border-bottom: 1px solid var(--ml-border);
        }
        .ml-filter-search { flex: 1; min-width: 220px; }
        .ml-filter-status { width: 190px; }
        .ml-filter-type { width: 170px; }
        .ml-filter-status-wide { width: 220px; }
        .ml-table-team { min-width: 0; }

        .ml-label {
            display: block;
            margin-bottom: 6px;
            font-size: 12.5px;
            font-weight: 600;
            color: var(--ml-text);
        }
        .ml-required { color: var(--color-feedback-danger); }
        .ml-input,
        .ml-select,
        .ml-textarea {
            width: 100%;
            min-height: 40px;
            padding: 9px 12px;
            border: 1px solid var(--ml-border);
            border-radius: 9px;
            background: #fff;
            color: var(--ml-text);
            font-family: var(--font-body);
            font-size: 13px;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }
        .ml-textarea { min-height: 96px; resize: vertical; line-height: 1.5; }
        .ml-input.is-mono {
            font-family: var(--font-code);
            font-size: 12px;
        }
        .ml-input::placeholder,
        .ml-textarea::placeholder {
            color: var(--ml-muted);
            opacity: 1;
        }
        .ml-input:focus,
        .ml-select:focus,
        .ml-textarea:focus {
            outline: none;
            border-color: var(--ml-primary);
            box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.12);
        }
        .ml-input.is-invalid,
        .ml-select.is-invalid,
        .ml-textarea.is-invalid { border-color: var(--color-feedback-danger-border); }
        .ml-help {
            margin-top: 5px;
            font-size: 11.5px;
            line-height: 1.45;
            color: var(--ml-muted);
        }
        .ml-error-text {
            margin-top: 5px;
            font-size: 11.5px;
            line-height: 1.45;
            color: var(--color-feedback-danger);
        }
        .ml-field { margin-bottom: 16px; }
        .ml-form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
        }
        .ml-form-grid .is-full { grid-column: 1 / -1; }
        .ml-checkbox-row {
            display: flex;
            align-items: flex-start;
            gap: 9px;
            min-height: 32px;
            padding: 4px 0;
            font-size: 12.5px;
            line-height: 1.5;
            color: var(--ml-text);
            cursor: pointer;
        }
        .ml-checkbox-row input {
            margin-top: 2px;
            accent-color: var(--ml-primary);
            width: 16px;
            height: 16px;
            flex-shrink: 0;
        }
        .ml-checkbox-row input:focus-visible,
        .ml-form-toggle input:focus-visible {
            outline: 2px solid var(--ml-primary);
            outline-offset: 2px;
        }

        .ml-card {
            background: #fff;
            border: 1px solid var(--ml-border);
            border-radius: 12px;
            overflow: hidden;
        }
        .ml-card + .ml-card { margin-top: 20px; }
        .ml-card-head {
            padding: 18px 20px;
            border-bottom: 1px solid var(--ml-border);
        }
        .ml-card-body { padding: 20px; }
        .ml-card-title {
            margin: 0;
            font-family: var(--font-heading);
            font-size: 15px;
            font-weight: 700;
            letter-spacing: -0.01em;
        }
        .ml-card-sub {
            margin: 4px 0 0;
            font-size: 12px;
            line-height: 1.5;
            color: var(--ml-muted);
        }
        .ml-two-col {
            display: grid;
            grid-template-columns: minmax(0, 1.55fr) minmax(300px, 0.8fr);
            gap: 20px;
            align-items: start;
        }
        .ml-sticky { position: sticky; top: 84px; }

        .ml-alert {
            padding: 12px 16px;
            border: 1px solid var(--ml-border);
            border-radius: 10px;
            font-size: 12.5px;
            line-height: 1.55;
            margin-bottom: 18px;
        }
        .ml-alert strong { font-weight: 700; }
        .ml-alert-success { background: #EFF6FF; border-color: #BFDBFE; color: #1E3A8A; }
        .ml-alert-error { background: var(--color-feedback-danger-surface); border-color: var(--color-feedback-danger-surface-border); color: var(--color-feedback-danger-ink); }
        .ml-alert-info { background: #F8FAFC; border-color: var(--ml-border); color: var(--ml-muted); }
        .ml-alert-info strong { color: var(--ml-text); }
        .ml-alert ul { margin: 6px 0 0; padding-left: 18px; }
        .ml-alert a { color: inherit; font-weight: 600; text-underline-offset: 2px; }
        .ml-alert a:focus-visible { outline: 2px solid currentColor; outline-offset: 2px; }

        .ml-status {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 9px;
            border: 1px solid transparent;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 600;
            white-space: nowrap;
        }
        .ml-status::before {
            content: "";
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: currentColor;
            opacity: 0.7;
        }
        .ml-status-active { background: #EFF6FF; color: #1D4ED8; border-color: #BFDBFE; }
        .ml-status-pending { background: #FFFBEB; color: #92400E; border-color: #FDE68A; }
        .ml-status-suspended { background: #FEF2F2; color: #991B1B; border-color: #FECACA; }
        .ml-status-inactive { background: #F1F5F9; color: #475569; border-color: #E2E8F0; }

        .ml-detail-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px 24px;
        }
        .ml-detail-label {
            font-size: 11.5px;
            font-weight: 600;
            color: var(--ml-muted);
            margin-bottom: 4px;
        }
        .ml-detail-value {
            font-size: 13px;
            line-height: 1.5;
            color: var(--ml-text);
            overflow-wrap: break-word;
        }
        .ml-detail-value.is-mono {
            font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
            font-size: 12px;
        }

        .ml-map-picker {
            height: 340px;
            border: 1px solid var(--ml-border);
            border-radius: 10px;
            overflow: hidden;
            background: #EAF1FF;
        }
        .ml-map-coordinates {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-top: 10px;
            font-size: 11.5px;
            color: var(--ml-muted);
        }
        .ml-map-coordinates code {
            font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
            font-size: 11.5px;
            color: var(--ml-text);
            background: #F1F5F9;
            border-radius: 6px;
            padding: 3px 7px;
        }
        .ml-map-picker .leaflet-container { font-family: var(--font-body); cursor: crosshair; }
        .ml-map-picker.is-readonly .leaflet-container { cursor: grab; }

        .ml-page-narrow { max-width: 780px; margin: 0 auto; }
        .ml-page-admin { max-width: 1280px; }
        .ml-action-row {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
            padding-top: 18px;
            margin-top: 4px;
            border-top: 1px solid var(--ml-border);
        }
        .ml-link-action {
            font-size: 12.5px;
            font-weight: 600;
            color: var(--ml-primary);
            text-decoration: none;
            background: none;
            border: none;
            padding: 0;
            cursor: pointer;
            font-family: var(--font-body);
        }
        .ml-link-action:hover { text-decoration: underline; }
        .ml-link-danger { color: #B91C1C; }

        .ml-nav-label {
            padding: 16px 12px 6px;
            font-size: 10.5px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: rgba(255,255,255,0.56);
        }

        .ml-success-panel {
            background: #fff;
            border: 1px solid var(--ml-border);
            border-radius: 12px;
            padding: 32px 28px;
            text-align: center;
        }
        .ml-success-panel svg { color: var(--ml-primary); margin-bottom: 10px; }
        .ml-success-panel h2 {
            margin: 0 0 8px;
            font-size: 20px;
            font-weight: 700;
        }
        .ml-success-panel p {
            margin: 0 auto 18px;
            max-width: 520px;
            font-size: 13px;
            line-height: 1.6;
            color: var(--ml-muted);
        }

        /* Registry command surface */
        .ml-registry-tabs {
            display: flex;
            align-items: center;
            gap: 2px;
            margin-bottom: 18px;
            border-bottom: 1px solid var(--ml-border);
            overflow-x: auto;
            scrollbar-width: none;
        }
        .ml-registry-tabs::-webkit-scrollbar { display: none; }
        .ml-registry-tab {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 10px 12px 11px;
            border-bottom: 2px solid transparent;
            color: var(--ml-muted);
            font-size: 12.5px;
            font-weight: 500;
            text-decoration: none;
            white-space: nowrap;
            transition: color 0.15s ease, border-color 0.15s ease;
        }
        .ml-registry-tab:hover { color: var(--ml-text); }
        .ml-registry-tab.is-active {
            color: var(--ml-primary);
            border-bottom-color: var(--ml-primary);
            font-weight: 600;
        }
        .ml-tab-count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 20px;
            height: 19px;
            padding: 0 6px;
            border-radius: 999px;
            background: var(--ml-surface);
            color: var(--ml-muted);
            font-family: var(--font-code);
            font-size: 10px;
            line-height: 1;
        }
        .ml-registry-tab.is-active .ml-tab-count {
            background: #EFF4FF;
            color: var(--ml-primary);
        }
        .ml-registry-panel {
            background: var(--ml-card);
            border: 1px solid var(--ml-border);
            border-radius: 16px;
            box-shadow: var(--ml-shadow-panel);
            overflow: hidden;
        }
        .ml-registry-toolbar {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 16px 20px;
            border-bottom: 1px solid var(--ml-border);
        }
        .ml-registry-search {
            position: relative;
            flex: 1;
            min-width: 220px;
        }
        .ml-registry-search svg {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--ml-subtle);
            pointer-events: none;
        }
        .ml-registry-search .ml-input { padding-left: 36px; }
        .ml-registry-filter { width: 190px; flex-shrink: 0; }
        .ml-registry-toolbar-actions {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-left: auto;
        }
        .ml-facility-cell {
            display: flex;
            align-items: center;
            gap: 11px;
            min-width: 0;
        }
        .ml-facility-mark {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: #EFF4FF;
            color: var(--ml-primary);
            font-family: var(--font-heading);
            font-size: 15px;
            font-weight: 600;
            flex-shrink: 0;
        }
        .ml-facility-name {
            display: block;
            color: var(--ml-text);
            font-size: 13.5px;
            font-weight: 600;
            line-height: 1.35;
            text-decoration: none;
        }
        .ml-facility-name:hover { color: var(--ml-primary); }
        .ml-meta-dot { margin: 0 3px; color: var(--ml-subtle); }
        .ml-team-count {
            font-family: var(--font-code);
            font-size: 12px;
            color: var(--ml-text);
        }
        .ml-row-open {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 6px 9px;
            border-radius: 8px;
            color: var(--ml-primary);
            font-size: 12.5px;
            font-weight: 600;
            text-decoration: none;
            transition: background 0.15s ease;
        }
        .ml-row-open:hover { background: #EFF4FF; }
        .ml-row-open svg { transition: transform 0.15s ease; }
        .ml-row-open:hover svg { transform: translateX(2px); }
        .ml-pagination-wrap {
            padding: 16px 20px;
            border-top: 1px solid var(--ml-border);
        }
        .ml-table-registry tbody tr { transition: background 0.15s ease; }

        .ml-page-title {
            font-size: 24px;
            font-weight: 600;
        }
        .ml-page-sub { max-width: 68ch; }
        .ml-page-eyebrow {
            margin: 0 0 5px;
            color: var(--ml-muted);
            font-size: 10.5px;
            font-weight: 600;
            letter-spacing: 0.1em;
            text-transform: uppercase;
        }
        .ml-kpi-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
            margin-bottom: 22px;
        }
        .ml-kpi {
            padding: 18px;
            background: var(--ml-card);
            border: 1px solid var(--ml-border);
            border-radius: 14px;
            box-shadow: var(--ml-shadow-panel);
        }
        .ml-kpi-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }
        .ml-kpi-label {
            color: var(--ml-muted);
            font-size: 12px;
            font-weight: 500;
        }
        .ml-kpi-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            border-radius: 8px;
            background: var(--ml-surface);
            color: var(--ml-muted);
        }
        .ml-kpi-icon.is-risk { background: #FFF7ED; color: #B45309; }
        .ml-kpi-value {
            margin-top: 14px;
            font-family: var(--font-heading);
            font-size: 28px;
            font-weight: 600;
            line-height: 1;
            letter-spacing: -0.035em;
            font-variant-numeric: tabular-nums;
        }
        .ml-kpi-value span {
            color: var(--ml-muted);
            font-family: var(--font-body);
            font-size: 12px;
            font-weight: 500;
            letter-spacing: 0;
        }
        .ml-kpi-value-currency {
            font-size: 21px;
            line-height: 1.2;
        }
        .ml-kpi-meta {
            margin-top: 9px;
            color: var(--ml-muted);
            font-size: 11.5px;
            line-height: 1.4;
        }
        .ml-kpi-emphasis { color: #B45309; font-weight: 600; }
        .ml-alert-warning {
            background: #FFFBEB;
            border-color: #FDE68A;
            color: #78350F;
        }
        .ml-alert-warning p {
            margin: 4px 0 0;
            font-size: 12.5px;
            line-height: 1.55;
        }
        .ml-section-header-split {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
        }
        .ml-section-link {
            color: var(--ml-primary);
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            white-space: nowrap;
        }
        .ml-section-link:hover { text-decoration: underline; }

        @media (max-width: 1100px) {
            .ml-kpi-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        @media (max-width: 520px) {
            .ml-kpi-grid { gap: 10px; }
            .ml-kpi { padding: 14px; }
            .ml-kpi-value { font-size: 23px; }
            .ml-kpi-value-currency { font-size: 17px; }
            .ml-kpi-meta { font-size: 10.5px; }
            .ml-section-header-split { flex-direction: column; gap: 8px; }
        }
        .ml-input,
        .ml-select,
        .ml-textarea { border-color: var(--color-border-strong); }
        .ml-select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2352627F' stroke-width='2' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            padding-right: 36px;
        }
        .ml-map-picker .leaflet-tile-pane { filter: grayscale(0.28) saturate(0.7); }

        /* Facility passport */
        .ml-passport {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 24px;
            padding: 22px 24px;
            margin-bottom: 22px;
            background: var(--ml-card);
            border: 1px solid var(--ml-border);
            border-radius: 16px;
            box-shadow: var(--ml-shadow-panel);
        }
        .ml-passport-main {
            display: flex;
            align-items: center;
            gap: 16px;
            min-width: 0;
        }
        .ml-passport-mark {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 54px;
            height: 54px;
            border-radius: 14px;
            background: #EFF4FF;
            color: var(--ml-primary);
            font-family: var(--font-heading);
            font-size: 22px;
            font-weight: 600;
            flex-shrink: 0;
        }
        .ml-passport-copy { min-width: 0; }
        .ml-passport-eyebrow {
            margin: 0 0 4px;
            color: var(--ml-muted);
            font-size: 11.5px;
        }
        .ml-passport-title-row {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }
        .ml-passport-meta {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            margin: 6px 0 0;
            color: var(--ml-muted);
            font-size: 12px;
        }
        .ml-passport-actions {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-shrink: 0;
        }
        .ml-btn-danger-quiet {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 7px 14px;
            border: 1px solid #FECACA;
            border-radius: 9px;
            background: #fff;
            color: #B91C1C;
            font-family: var(--font-body);
            font-size: 12.5px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.15s ease;
        }
        .ml-btn-danger-quiet:hover { background: #FEF3F2; }
        .ml-btn-danger-quiet:focus-visible {
            outline: 2px solid #B91C1C;
            outline-offset: 2px;
        }
        .ml-card-head-split {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
        }
        .ml-card-index {
            font-family: var(--font-code);
            font-size: 10px;
            color: var(--ml-subtle);
        }
        .ml-inline-status {
            display: inline-flex;
            align-items: center;
            padding: 3px 8px;
            border-radius: 6px;
            font-size: 11.5px;
            font-weight: 600;
        }
        .ml-inline-status.is-on { background: #EFF4FF; color: var(--ml-primary); }
        .ml-inline-status.is-off { background: var(--ml-surface); color: var(--ml-muted); }

        /* Audit timeline */
        .ml-audit-list {
            margin: 0;
            padding: 20px 24px 8px;
            list-style: none;
        }
        .ml-audit-item {
            position: relative;
            display: grid;
            grid-template-columns: 12px minmax(0, 1fr);
            gap: 0 12px;
            padding-bottom: 22px;
        }
        .ml-audit-item::before {
            content: "";
            position: absolute;
            left: 5px;
            top: 14px;
            bottom: 0;
            width: 1px;
            background: var(--ml-border);
        }
        .ml-audit-item:last-child { padding-bottom: 12px; }
        .ml-audit-item:last-child::before { display: none; }
        .ml-audit-dot {
            grid-column: 1;
            grid-row: 1 / span 2;
            width: 11px;
            height: 11px;
            margin-top: 4px;
            border: 2px solid #C7D7FE;
            border-radius: 50%;
            background: var(--ml-primary);
            z-index: 1;
        }
        .ml-audit-time {
            grid-column: 2;
            margin-bottom: 3px;
            color: var(--ml-subtle);
            font-family: var(--font-code);
            font-size: 10.5px;
        }
        .ml-audit-body {
            grid-column: 2;
            color: var(--ml-muted);
            font-size: 12.5px;
            line-height: 1.55;
        }
        .ml-audit-body strong { color: var(--ml-text); font-weight: 600; }
        .ml-audit-body code {
            display: block;
            margin-top: 3px;
            color: var(--ml-subtle);
            font-family: var(--font-code);
            font-size: 10.5px;
        }

        /* Verification rail */
        .ml-verification-status {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--ml-border);
        }
        .ml-verification-date {
            color: var(--ml-muted);
            font-size: 11px;
        }
        .ml-checklist {
            display: grid;
            gap: 2px;
            margin: 16px 0;
            padding: 0;
            list-style: none;
        }
        .ml-checklist-item {
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 8px 0;
            color: var(--ml-muted);
            font-size: 12.5px;
        }
        .ml-checklist-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 22px;
            height: 22px;
            border-radius: 999px;
            background: var(--ml-surface);
            color: var(--ml-subtle);
            flex-shrink: 0;
        }
        .ml-checklist-item.is-complete { color: var(--ml-text); }
        .ml-checklist-item.is-complete .ml-checklist-icon {
            background: #EFF4FF;
            color: var(--ml-primary);
        }
        .ml-verification-meta {
            display: grid;
            gap: 14px;
            padding-top: 16px;
            border-top: 1px solid var(--ml-border);
        }
        .ml-verification-action { margin-top: 16px; }
        .ml-verification-action .ml-btn-primary { width: 100%; justify-content: center; }
        .ml-review-actions { display: grid; gap: 16px; }
        .ml-review-actions .ml-btn-primary { width: 100%; justify-content: center; }
        .ml-review-reject {
            padding-top: 16px;
            border-top: 1px solid var(--ml-border);
        }
        .ml-review-reject .ml-btn-danger-quiet { width: 100%; justify-content: center; }
        .ml-review-facility-link {
            width: 100%;
            justify-content: center;
            margin-top: 16px;
        }

        /* Team */
        .ml-member-list { display: grid; gap: 12px; }
        .ml-member-row {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .ml-member-avatar {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            border-radius: 9px;
            background: var(--ml-surface);
            color: var(--ml-muted);
            font-size: 11px;
            font-weight: 600;
            flex-shrink: 0;
        }
        .ml-member-copy { min-width: 0; flex: 1; }
        .ml-member-copy .ml-cell-title { display: block; font-size: 13px; }
        .ml-member-copy .ml-cell-meta { display: block; }
        .ml-inline-metrics {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
        }
        .ml-inline-metric {
            display: flex;
            align-items: baseline;
            gap: 8px;
            padding: 10px 14px;
            background: var(--ml-card);
            border: 1px solid var(--ml-border);
            border-radius: 10px;
        }
        .ml-inline-metric-value {
            font-family: var(--font-heading);
            font-size: 18px;
            font-weight: 600;
            font-variant-numeric: tabular-nums;
        }
        .ml-inline-metric-label {
            color: var(--ml-muted);
            font-size: 11.5px;
        }
        .ml-btn-block { width: 100%; justify-content: center; }

        /* Facility settings */
        .ml-settings-layout {
            display: grid;
            grid-template-columns: 300px minmax(0, 1fr);
            gap: 20px;
            align-items: start;
        }
        .ml-settings-aside { position: sticky; top: 84px; }
        .ml-settings-aside .ml-card-body { padding: 22px; }
        .ml-settings-mark {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            margin-bottom: 16px;
            border-radius: 12px;
            background: #EFF4FF;
            color: var(--ml-primary);
            font-family: var(--font-heading);
            font-size: 18px;
            font-weight: 600;
        }
        .ml-settings-list {
            display: grid;
            gap: 12px;
            margin: 20px 0 0;
        }
        .ml-settings-list > div {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            gap: 14px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--ml-border);
        }
        .ml-settings-list dt {
            color: var(--ml-muted);
            font-size: 11.5px;
        }
        .ml-settings-list dd {
            margin: 0;
            color: var(--ml-text);
            font-size: 12px;
            text-align: right;
            overflow-wrap: break-word;
        }
        .ml-settings-list dd.is-mono {
            font-family: var(--font-code);
            font-size: 10.5px;
        }
        .ml-settings-note {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            margin-top: 18px;
            padding: 10px 12px;
            border-radius: 9px;
            background: var(--ml-surface);
            color: var(--ml-muted);
            font-size: 11px;
            line-height: 1.5;
        }
        .ml-settings-note svg { color: var(--ml-primary); flex-shrink: 0; margin-top: 1px; }

        @media (max-width: 900px) {
            .ml-settings-layout { grid-template-columns: 1fr; }
            .ml-settings-aside { position: static; }
        }
        .ml-invitation-block {
            margin-top: 18px;
            padding-top: 16px;
            border-top: 1px solid var(--ml-border);
        }
        .ml-invitation-row {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            padding: 8px 0;
            align-items: flex-start;
        }
        .ml-invite-form {
            margin-top: 18px;
            padding-top: 16px;
            border-top: 1px solid var(--ml-border);
        }
        .ml-invite-form .ml-btn-quiet { width: 100%; justify-content: center; }

        @media (max-width: 900px) {
            .ml-passport { flex-direction: column; }
            .ml-passport-actions { width: 100%; flex-wrap: wrap; }
            .ml-passport-actions > a,
            .ml-passport-actions > form,
            .ml-passport-actions > form > button { width: 100%; }
            .ml-passport-actions > a,
            .ml-passport-actions > form > button { justify-content: center; }
            .ml-table-requests td:nth-child(2) { grid-column: 2; grid-row: 2; }
            .ml-table-requests td:nth-child(3) { grid-column: 1; grid-row: 2; }
            .ml-table-requests td:nth-child(4) { grid-column: 1; grid-row: 3; }
            .ml-table-requests td:nth-child(5) { grid-column: 1 / -1; grid-row: 4; }
        }
        @media (max-width: 720px) {
            .ml-passport { padding: 18px 16px; }
            .ml-passport-mark { width: 44px; height: 44px; border-radius: 12px; font-size: 18px; }
            .ml-audit-list { padding: 16px 18px 4px; }
        }

        /* Transfers */
        .ml-status-transit { background: #F5F3FF; color: #5B21B6; border-color: #DDD6FE; }
        .ml-transfer-code {
            color: var(--ml-text);
            font-family: var(--font-code);
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            white-space: nowrap;
        }
        .ml-transfer-code:hover { color: var(--ml-primary); }
        .ml-route {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 5px;
            font-size: 12px;
            line-height: 1.45;
        }
        .ml-route-name { color: var(--ml-text); font-weight: 600; }
        .ml-route-name.is-recipient { color: var(--ml-primary); }
        .ml-route-arrow { color: var(--ml-subtle); }
        .ml-score {
            color: var(--ml-primary);
            font-family: var(--font-code);
            font-size: 12.5px;
            font-weight: 600;
        }
        .ml-transfer-stages {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            margin: 0;
            padding: 0;
            list-style: none;
        }
        .ml-transfer-stage {
            position: relative;
            padding-right: 12px;
        }
        .ml-transfer-stage:not(:last-child)::before {
            content: "";
            position: absolute;
            top: 13px;
            left: 26px;
            right: 0;
            height: 2px;
            background: var(--ml-border);
        }
        .ml-transfer-stage.is-done:not(:last-child)::before { background: #BFDBFE; }
        .ml-transfer-stage-dot {
            position: relative;
            z-index: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 27px;
            height: 27px;
            border: 1px solid var(--color-border-strong);
            border-radius: 999px;
            background: var(--ml-card);
            color: var(--ml-subtle);
            font-family: var(--font-code);
            font-size: 10px;
        }
        .ml-transfer-stage.is-done .ml-transfer-stage-dot {
            border-color: var(--ml-primary);
            background: var(--ml-primary);
            color: #fff;
        }
        .ml-transfer-stage.is-current .ml-transfer-stage-dot {
            box-shadow: 0 0 0 4px #DBEAFE;
        }
        .ml-transfer-stage-copy { display: block; margin-top: 9px; }
        .ml-transfer-stage-label {
            display: block;
            color: var(--ml-muted);
            font-size: 12px;
            font-weight: 600;
            line-height: 1.35;
        }
        .ml-transfer-stage.is-done .ml-transfer-stage-label { color: var(--ml-text); }
        .ml-transfer-stage-time {
            display: block;
            margin-top: 3px;
            color: var(--ml-subtle);
            font-family: var(--font-code);
            font-size: 10px;
        }
        .ml-invariant-note { margin-bottom: 18px; }
        .ml-invariant-note p {
            margin: 4px 0 0;
            font-size: 12.5px;
            line-height: 1.55;
        }
        .ml-action-panel {
            border-color: #BFDBFE;
            box-shadow: 0 0 0 3px #EFF4FF, var(--ml-shadow-panel);
        }
        .ml-role-chip {
            padding: 4px 8px;
            border: 1px solid var(--ml-border);
            border-radius: 6px;
            background: var(--ml-surface);
            color: var(--ml-muted);
            font-family: var(--font-code);
            font-size: 10px;
            white-space: nowrap;
        }
        .ml-action-copy {
            margin: 0 0 16px;
            color: var(--ml-muted);
            font-size: 13px;
            line-height: 1.55;
        }
        .ml-action-note {
            margin: 0;
            padding: 10px 12px;
            border-radius: 8px;
            background: var(--ml-surface);
            color: var(--ml-muted);
            font-size: 12px;
            line-height: 1.5;
        }
        .ml-action-cancel {
            margin-top: 18px;
            padding-top: 14px;
            border-top: 1px solid var(--ml-border);
        }
        .ml-transit-banner {
            margin-bottom: 14px;
            padding: 13px 14px;
            border: 1px solid #DDD6FE;
            border-radius: 10px;
            background: #F5F3FF;
        }
        .ml-transit-meta {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 8px;
            margin-top: 4px;
            color: #4C1D95;
            font-size: 12px;
        }
        .ml-route-vertical {
            display: grid;
            gap: 0;
            padding-bottom: 4px;
        }
        .ml-route-line {
            display: block;
            width: 1px;
            height: 18px;
            margin: 7px 0 7px 9px;
            background: var(--ml-border);
        }
        .ml-score-list {
            display: grid;
            gap: 7px;
            margin-top: 22px;
            padding-top: 18px;
            border-top: 1px solid var(--ml-border);
        }
        .ml-score-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            color: var(--ml-muted);
            font-size: 11.5px;
        }
        .ml-score-row strong {
            color: var(--ml-text);
            font-family: var(--font-code);
            font-size: 11px;
        }
        .ml-score-bar {
            height: 5px;
            margin-bottom: 5px;
            overflow: hidden;
            border-radius: 999px;
            background: var(--ml-surface);
        }
        .ml-score-bar span {
            display: block;
            height: 100%;
            border-radius: inherit;
            background: var(--ml-primary);
        }
        .ml-score-total {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 10px;
            padding-top: 12px;
            border-top: 1px solid var(--ml-border);
            font-size: 12px;
            font-weight: 600;
        }
        .ml-score-total strong {
            color: var(--ml-primary);
            font-family: var(--font-code);
            font-size: 15px;
        }
        .ml-compliance-banner {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 20px;
            padding: 14px 16px;
            border-color: #C7D7FE;
            background: #EFF4FF;
            color: #1E3A8A;
        }
        .ml-compliance-banner svg { flex-shrink: 0; margin-top: 1px; }
        .ml-compliance-banner strong { display: block; font-size: 12.5px; }
        .ml-compliance-banner p {
            margin: 3px 0 0;
            font-size: 12px;
            line-height: 1.55;
        }
        .ml-metric-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 10px;
        }
        .ml-metric {
            padding: 12px;
            border-radius: 10px;
            background: var(--ml-surface);
        }
        .ml-metric.is-risk { background: #FFF7ED; }
        .ml-metric-label {
            display: block;
            color: var(--ml-muted);
            font-size: 11px;
        }
        .ml-metric-value {
            display: block;
            margin-top: 5px;
            color: var(--ml-text);
            font-family: var(--font-heading);
            font-size: 18px;
            font-weight: 600;
            line-height: 1.2;
        }
        .ml-metric.is-risk .ml-metric-value { color: #B45309; }
        .ml-metric-value small {
            color: var(--ml-muted);
            font-family: var(--font-body);
            font-size: 10.5px;
            font-weight: 500;
        }
        .ml-sort-row {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 6px;
            padding: 10px 20px;
            border-bottom: 1px solid var(--ml-border);
            background: #FBFCFE;
        }
        .ml-sort-pill {
            padding: 5px 10px;
            border: 1px solid var(--ml-border);
            border-radius: 999px;
            background: #fff;
            color: var(--ml-muted);
            font-size: 11.5px;
            font-weight: 500;
            text-decoration: none;
        }
        .ml-sort-pill.is-active {
            border-color: var(--ml-primary);
            background: var(--ml-primary);
            color: #fff;
        }
        .ml-sort-note {
            margin-left: auto;
            color: var(--ml-subtle);
            font-size: 10.5px;
        }
        .ml-candidate-list {
            display: grid;
            margin: 0;
            padding: 0;
            list-style: none;
        }
        .ml-candidate {
            display: grid;
            grid-template-columns: 26px minmax(0, 1fr) 112px;
            gap: 12px;
            align-items: center;
            padding: 16px 20px;
            border-bottom: 1px solid var(--ml-border);
        }
        .ml-candidate:last-child { border-bottom: 0; }
        .ml-candidate.is-top { background: linear-gradient(90deg, #EFF4FF 0%, #FFFFFF 62%); }
        .ml-candidate-rank {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 24px;
            height: 24px;
            border-radius: 999px;
            background: var(--ml-surface);
            color: var(--ml-muted);
            font-family: var(--font-code);
            font-size: 10px;
        }
        .ml-candidate.is-top .ml-candidate-rank { background: var(--ml-primary); color: #fff; }
        .ml-candidate-copy { min-width: 0; }
        .ml-candidate-name {
            color: var(--ml-text);
            font-size: 13.5px;
            font-weight: 600;
            line-height: 1.35;
        }
        .ml-candidate-metrics {
            display: flex;
            flex-wrap: wrap;
            gap: 4px 14px;
            margin-top: 8px;
            color: var(--ml-muted);
            font-size: 10.5px;
        }
        .ml-candidate-metrics strong { color: var(--ml-text); font-weight: 600; }
        .ml-candidate-side { text-align: right; }
        .ml-candidate-score {
            color: var(--ml-primary);
            font-family: var(--font-heading);
            font-size: 20px;
            font-weight: 600;
            line-height: 1;
            letter-spacing: -0.03em;
        }
        .ml-candidate-side .ml-btn-sm { margin-top: 7px; }

        @media (max-width: 900px) {
            .ml-metric-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .ml-candidate { grid-template-columns: 26px minmax(0, 1fr); }
            .ml-candidate-side {
                grid-column: 2;
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 10px;
                margin-top: 4px;
                text-align: left;
            }
        }
        @media (max-width: 720px) {
            .ml-transfer-stages { grid-template-columns: 1fr; gap: 15px; }
            .ml-transfer-stage { display: flex; gap: 10px; padding-right: 0; }
            .ml-transfer-stage:not(:last-child)::before { display: none; }
            .ml-transfer-stage-copy { margin-top: 2px; }
            .ml-sort-note { width: 100%; margin-left: 0; }
            .ml-candidate { padding: 14px 16px; }
            .ml-metric-grid { gap: 8px; }
        }

        @media (max-width: 900px) {
            .ml-two-col { grid-template-columns: 1fr; }
            .ml-sticky { position: static; }
            .ml-registry-toolbar { flex-wrap: wrap; }
            .ml-registry-filter { width: 160px; }
        }
        @media (max-width: 720px) {
            .ml-page-title { font-size: 20px; }
            .ml-content { padding-bottom: 88px; }
            .ml-registry-tabs { flex-wrap: wrap; overflow-x: visible; row-gap: 0; }
            .ml-registry-tab { padding: 9px 9px 10px; font-size: 12px; }
            .ml-registry-toolbar { padding: 14px 16px; }
            .ml-registry-search,
            .ml-registry-filter { width: 100%; min-width: 0; }
            .ml-registry-toolbar-actions { width: 100%; margin-left: 0; }
            .ml-registry-toolbar-actions .ml-btn-primary,
            .ml-registry-toolbar-actions .ml-btn-quiet { flex: 1; justify-content: center; }
            .ml-registry-panel { border-radius: 14px; }
            .ml-pagination-wrap { padding: 14px 16px; }
        }
        @media (max-width: 720px) {
            .ml-form-grid { grid-template-columns: 1fr; }
            .ml-detail-grid { grid-template-columns: 1fr; }
            .ml-summary-bar { padding: 12px 16px; }
            .ml-summary-item { padding: 0 14px; }
            .ml-summary-item:nth-child(3) { border-left: none; padding-left: 0; }
            .ml-map-picker { height: 300px; }
            .ml-toolbar { padding: 12px 16px; }
            .ml-card-head, .ml-card-body { padding: 16px; }
        }

        /* ============================================================
           INVENTORY LEDGER
           ============================================================ */
        .ml-alert-row {
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }
        .ml-alert-row > span {
            flex: 1;
            min-width: 0;
        }
        .ml-alert-icon {
            flex-shrink: 0;
            margin-top: 1px;
        }
        .ml-alert-close {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            min-width: 24px;
            min-height: 24px;
            padding: 0;
            border: 0;
            background: transparent;
            color: currentColor;
            font-size: 18px;
            line-height: 1;
            cursor: pointer;
            opacity: 0.7;
        }
        .ml-alert-close:hover { opacity: 1; }
        .ml-alert-close:focus-visible {
            outline: 2px solid currentColor;
            outline-offset: 2px;
            border-radius: 4px;
        }
        .ml-inventory-legend {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 6px;
        }
        .ml-recommendation-flag {
            display: inline-flex;
            align-items: center;
            margin-bottom: 6px;
            padding: 3px 8px;
            border: 1px solid var(--ml-border);
            border-radius: 999px;
            background: var(--ml-surface);
            color: var(--ml-primary);
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.05em;
        }
        .ml-rec-cta { margin-top: auto; }
        .ml-rec-facts { flex-wrap: wrap; row-gap: 4px; }
        .ml-batch-code {
            display: inline-block;
            max-width: 100%;
            padding: 3px 8px;
            border: 1px solid var(--ml-border);
            border-radius: 6px;
            background: var(--ml-surface);
            color: var(--ml-text);
            font-family: var(--font-code);
            font-size: 11px;
            font-weight: 600;
            line-height: 1.35;
            white-space: nowrap;
        }
        .ml-batch-cell {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 5px;
        }
        .ml-batch-cell .ml-batch-code {
            height: 24px;
            box-sizing: border-box;
            display: inline-flex;
            align-items: center;
        }
        .ml-batch-cell .ml-cell-meta {
            margin-top: 0;
            font-size: 12px;
            color: var(--ml-muted);
            line-height: 1.4;
        }
        .ml-stock-cell {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 2px;
        }
        .ml-stock-cell .ml-stock-value {
            font-size: 16px;
            font-weight: 700;
            color: var(--ml-text);
            font-variant-numeric: tabular-nums;
            line-height: 1.2;
        }
        .ml-stock-cell .ml-stock-unit {
            margin-left: 0;
            font-size: 12px;
            font-weight: 500;
            color: var(--ml-muted);
            line-height: 1.35;
            white-space: nowrap;
        }
        .ml-stock-burn {
            font-size: 11px;
            font-weight: 400;
            color: var(--ml-subtle);
            line-height: 1.35;
            white-space: nowrap;
        }
        .ml-inline-icon {
            margin-right: 2px;
            vertical-align: -2px;
        }
        .ml-runway-mark.is-urgent { background: var(--ml-danger); }
        .ml-simulator-toggle {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            min-height: 24px;
            margin-top: 4px;
            padding: 3px 0;
            border: 0;
            background: none;
            color: var(--ml-primary);
            font-family: var(--font-body);
            font-size: 11.5px;
            font-weight: 600;
            cursor: pointer;
        }
        .ml-simulator-toggle:hover { text-decoration: underline; }
        .ml-simulator-toggle:focus-visible {
            outline: 2px solid var(--ml-primary);
            outline-offset: 3px;
            border-radius: 4px;
        }
        .ml-waste-cell {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 3px;
        }
        .ml-waste-value {
            color: var(--ml-danger);
            font-family: var(--font-code);
            font-size: 13px;
            font-weight: 600;
            font-variant-numeric: tabular-nums;
        }
        .ml-waste-value.is-safe { color: var(--ml-text); }
        .ml-inventory-actions {
            display: flex;
            flex-wrap: nowrap;
            align-items: center;
            justify-content: flex-end;
            gap: 6px;
            white-space: nowrap;
        }
        .ml-inventory-actions form {
            display: inline-flex;
            margin: 0;
        }
        .ml-btn-quiet.is-danger {
            color: var(--ml-danger);
            border-color: var(--ml-danger);
        }
        .ml-btn-quiet.is-danger:hover { background: var(--ml-surface); }
        .ml-table-inventory { min-width: 1120px; }
        .ml-table-inventory td {
            vertical-align: top;
            padding: 16px 20px;
        }
        .ml-simulator-row[hidden] { display: none !important; }
        .ml-simulator-row > td {
            padding: 16px 24px;
            background: var(--ml-surface);
        }
        .ml-simulator {
            padding: 16px 18px;
            border: 1px solid var(--ml-border);
            border-radius: 12px;
            background: var(--ml-card);
        }
        .ml-simulator-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 16px;
        }
        .ml-simulator-title {
            margin: 2px 0 0;
            font-family: var(--font-heading);
            font-size: 15px;
            font-weight: 700;
            line-height: 1.35;
            color: var(--ml-text);
        }
        .ml-simulator-sub {
            margin: 4px 0 0;
            font-size: 12px;
            line-height: 1.5;
            color: var(--ml-muted);
        }
        .ml-simulator-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1.35fr);
            gap: 20px;
            align-items: center;
        }
        .ml-simulator-label {
            display: block;
            margin-bottom: 8px;
            color: var(--ml-text);
            font-size: 12px;
            font-weight: 600;
        }
        .ml-simulator-current {
            color: var(--ml-primary);
            font-family: var(--font-code);
            font-weight: 600;
        }
        .ml-range {
            width: 100%;
            cursor: pointer;
            accent-color: var(--ml-primary);
        }
        .ml-simulator-scale {
            display: flex;
            justify-content: space-between;
            gap: 8px;
            margin-top: 5px;
            color: var(--ml-subtle);
            font-size: 10.5px;
        }
        .ml-simulator-metrics {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 1px;
            overflow: hidden;
            border: 1px solid var(--ml-border);
            border-radius: 10px;
            background: var(--ml-border);
        }
        .ml-simulator-metric {
            display: flex;
            flex-direction: column;
            gap: 5px;
            min-width: 0;
            padding: 11px 12px;
            background: var(--ml-card);
        }
        .ml-simulator-metric-label {
            color: var(--ml-muted);
            font-size: 10.5px;
            line-height: 1.35;
        }
        .ml-simulator-metric-value {
            color: var(--ml-text);
            font-family: var(--font-code);
            font-size: 14px;
            font-weight: 600;
            font-variant-numeric: tabular-nums;
        }
        .ml-simulator-metric-value.is-risk { color: var(--ml-danger); }
        .ml-simulator-metric-value.is-safe { color: var(--ml-text); }

        @media (max-width: 1023px) {
            .ml-table-inventory { min-width: 0; }
            .ml-table-inventory td:nth-child(1) {
                grid-column: 1 / -1;
                grid-row: 1;
            }
            .ml-table-inventory td:nth-child(2) {
                grid-column: 1;
                grid-row: 2;
            }
            .ml-table-inventory td:nth-child(3) {
                grid-column: 2;
                grid-row: 2;
                align-self: start;
                padding-top: 1px;
                text-align: right;
            }
            .ml-table-inventory td:nth-child(3)[data-label]::before { text-align: right; }
            .ml-table-inventory td:nth-child(4) {
                grid-column: 1 / -1;
                grid-row: 3;
            }
            .ml-table-inventory td:nth-child(5) {
                grid-column: 1 / -1;
                grid-row: 4;
                text-align: left;
            }
            .ml-table-inventory td:nth-child(6) {
                grid-column: 1 / -1;
                grid-row: 5;
                text-align: left;
            }
            .ml-table-inventory .ml-waste-cell {
                align-items: flex-start;
                text-align: left;
            }
            .ml-table-inventory .ml-inventory-actions { justify-content: flex-start; }
            .ml-table-airy tr.ml-simulator-row {
                padding: 0;
                border-bottom: 1px solid var(--ml-border);
            }
            .ml-simulator-row > td {
                grid-column: 1 / -1;
                grid-row: 1;
                padding: 12px 14px;
            }
            .ml-simulator-grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 600px) {
            .ml-inventory-legend { width: 100%; }
            .ml-simulator { padding: 14px; }
            .ml-simulator-head { gap: 10px; }
            .ml-simulator-metrics { grid-template-columns: 1fr; }
            .ml-simulator-metric {
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
            }
            .ml-inventory-actions { width: 100%; }
            .ml-inventory-actions > .ml-btn-primary,
            .ml-inventory-actions > .ml-btn-quiet { flex: 1; justify-content: center; }
            .ml-inventory-actions form { flex: 1; }
            .ml-inventory-actions form .ml-btn-quiet { width: 100%; justify-content: center; }
        }
        /* ============================================================
           AUDIT REGISTER
           ============================================================ */
        .ml-audit-count {
            display: inline-flex;
            align-items: center;
            padding: 4px 9px;
            border: 1px solid var(--ml-border);
            border-radius: 999px;
            background: var(--ml-surface);
            color: var(--ml-muted);
            font-size: 11px;
            font-weight: 600;
            line-height: 1.35;
            white-space: nowrap;
        }
        .ml-audit-link {
            display: inline-block;
            max-width: 100%;
            color: var(--ml-primary);
            font-family: var(--font-code);
            font-size: 11.5px;
            font-weight: 600;
            line-height: 1.35;
            text-decoration: none;
            white-space: nowrap;
        }
        .ml-audit-link:hover { text-decoration: underline; }
        .ml-audit-link:focus-visible {
            outline: 2px solid var(--ml-primary);
            outline-offset: 3px;
            border-radius: 4px;
        }
        .ml-audit-amount {
            color: var(--ml-text);
            font-family: var(--font-code);
            font-size: 12px;
            font-weight: 600;
            font-variant-numeric: tabular-nums;
            white-space: nowrap;
        }
        .ml-audit-action {
            display: inline-block;
            max-width: 100%;
            padding: 3px 7px;
            border: 1px solid var(--ml-border);
            border-radius: 6px;
            background: var(--ml-surface);
            color: var(--ml-text);
            font-family: var(--font-code);
            font-size: 10.5px;
            font-weight: 600;
            line-height: 1.35;
            overflow-wrap: break-word;
        }
        .ml-audit-description {
            display: block;
            max-width: 460px;
            color: var(--ml-text);
            line-height: 1.55;
        }
        .ml-table-audit { min-width: 960px; }
        .ml-table-audit th,
        .ml-table-audit td {
            padding-left: 12px;
            padding-right: 12px;
        }
        .ml-table-audit th:first-child,
        .ml-table-audit td:first-child { padding-left: 18px; }
        .ml-table-audit th:last-child,
        .ml-table-audit td:last-child { padding-right: 18px; }
        .ml-table-responsive:focus-visible {
            outline: 2px solid var(--ml-primary);
            outline-offset: -2px;
        }
        /* ============================================================
           BATCH FORM
           ============================================================ */
        .ml-step-head {
            display: flex;
            flex-wrap: wrap;
            align-items: flex-start;
            justify-content: space-between;
            gap: 14px;
        }
        .ml-step-title {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            min-width: 0;
        }
        .ml-step-index {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 24px;
            width: 24px;
            height: 24px;
            border-radius: 7px;
            background: #EAF1FF;
            color: var(--ml-primary);
            font-family: var(--font-code);
            font-size: 11px;
            font-weight: 700;
        }
        .ml-step-title .ml-card-title { line-height: 1.3; }
        .ml-form-toggle {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            min-height: 32px;
            padding: 6px 0;
            color: var(--ml-text);
            font-size: 12px;
            font-weight: 600;
            line-height: 1.4;
            cursor: pointer;
        }
        .ml-form-toggle input {
            width: 16px;
            height: 16px;
            margin: 0;
            accent-color: var(--ml-primary);
            flex-shrink: 0;
        }
        .ml-form-stack {
            display: grid;
            gap: 18px;
        }
        .ml-form-grid.is-tight > .ml-field { margin-bottom: 0; }
        .ml-field.is-last { margin-bottom: 0; }
        .ml-optional {
            color: var(--ml-muted);
            font-weight: 500;
        }
        .ml-input.is-uppercase { text-transform: uppercase; }
        .ml-subpanel {
            padding: 16px;
            border: 1px solid var(--ml-border);
            border-radius: 10px;
            background: var(--ml-surface);
        }
        .ml-subpanel-head {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            margin-bottom: 14px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--ml-border);
        }
        .ml-subpanel-title {
            color: var(--ml-text);
            font-size: 12.5px;
            font-weight: 700;
        }
        .ml-alert-copy {
            margin: 3px 0 0;
            font-size: 12px;
            line-height: 1.55;
        }
        .ml-summary-medicine {
            color: var(--ml-text);
            font-family: var(--font-heading);
            font-size: 16px;
            font-weight: 600;
            line-height: 1.3;
            letter-spacing: -0.01em;
        }
        .ml-summary-details { margin-top: 16px; }
        .ml-forecast-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 8px;
        }
        .ml-forecast-cell {
            padding: 11px 12px;
            border-radius: 10px;
            background: var(--ml-surface);
        }
        .ml-forecast-cell.is-wide { grid-column: 1 / -1; }
        .ml-forecast-cell.is-wide .ml-forecast-value { font-size: 20px; }
        .ml-forecast-label {
            display: block;
            color: var(--ml-muted);
            font-size: 10.5px;
            line-height: 1.35;
        }
        .ml-forecast-value {
            display: block;
            margin-top: 5px;
            color: var(--ml-text);
            font-family: var(--font-heading);
            font-size: 18px;
            font-weight: 600;
            line-height: 1.2;
            font-variant-numeric: tabular-nums;
        }
        .ml-forecast-value small {
            color: var(--ml-muted);
            font-family: var(--font-body);
            font-size: 10.5px;
            font-weight: 500;
        }
        .ml-forecast-risk {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 8px;
            margin-top: 14px;
        }
        .ml-forecast-risk .ml-forecast-label {
            width: 100%;
            margin-bottom: -2px;
        }
        .ml-forecast-hint {
            width: 100%;
            margin: 0;
            color: var(--ml-muted);
            font-size: 11px;
            line-height: 1.45;
        }
        .ml-forecast-note {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            margin-top: 14px;
            padding-top: 14px;
            border-top: 1px solid var(--ml-border);
            color: var(--ml-muted);
            font-size: 11px;
            line-height: 1.5;
        }
        .ml-forecast-note svg {
            flex-shrink: 0;
            margin-top: 1px;
            color: var(--ml-subtle);
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
        <span style="display: inline-flex; align-items: center; gap: 8px;">
            <img src="{{ asset('images/logo.png') }}" alt="" style="width: 26px; height: 26px; object-fit: contain;" />
            MediLoop
        </span>
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
        <a href="{{ route('network.index') }}" class="ml-nav-item {{ request()->routeIs('network.*') ? 'active' : '' }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 21s-7-4.35-7-10a7 7 0 1114 0c0 5.65-7 10-7 10z"/><circle cx="12" cy="11" r="2.5"/></svg>
            Peta Jaringan
        </a>
        <a href="{{ route('transfers.index') }}" class="ml-nav-item {{ request()->routeIs('transfers.*') ? 'active' : '' }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M7 16V4m0 0L3 8m4-4 4 4M17 8v12m0 0 4-4m-4 4-4-4"/></svg>
            Transfers &amp; Alokasi
        </a>
        <a href="{{ route('audit.index') }}" class="ml-nav-item {{ request()->routeIs('audit.*') ? 'active' : '' }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 19v-6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2zm0 0V9a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v10m-6 0a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2m0 0V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-2a2 2 0 0 1-2-2z"/></svg>
            Pengawasan &amp; Audit
        </a>

        @auth
            @if(auth()->user()->isSysadmin())
                <div class="ml-nav-label">Administrasi</div>
                <a href="{{ route('admin.dashboard') }}" class="ml-nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19V9m6 10V5m6 14v-7m4 7H2"/></svg>
                    Ringkasan Admin
                </a>
                <a href="{{ route('admin.facilities.index') }}" class="ml-nav-item {{ request()->routeIs('admin.facilities.*') ? 'active' : '' }}">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18M5 21V7l7-4 7 4v14M9 21v-5h6v5"/><path d="M9 9h.01M15 9h.01M9 12h.01M15 12h.01"/></svg>
                    Registry Faskes
                </a>
                <a href="{{ route('admin.facility-requests.index') }}" class="ml-nav-item {{ request()->routeIs('admin.facility-requests.*') ? 'active' : '' }}">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 12h6m-6 4h6M7 3h10a2 2 0 0 1 2 2v14l-3-2-3 2-3-2-3 2V5a2 2 0 0 1 2-2z"/></svg>
                    Pengajuan Faskes
                </a>
                <a href="{{ route('admin.users.index') }}" class="ml-nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2"/><circle cx="10" cy="7" r="4"/><path d="M21 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    Pengguna
                </a>
            @endif

            @if(auth()->user()->isFacilityApprover())
                <div class="ml-nav-label">Fasilitas Anda</div>
                <a href="{{ route('facility.team.index') }}" class="ml-nav-item {{ request()->routeIs('facility.team.*') ? 'active' : '' }}">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2"/><circle cx="10" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    Tim Faskes
                </a>
                <a href="{{ route('facility.settings.edit') }}" class="ml-nav-item {{ request()->routeIs('facility.settings.*') ? 'active' : '' }}">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18M5 21V7l7-4 7 4v14M9 21v-5h6v5"/></svg>
                    Profil Faskes
                </a>
            @endif
        @endauth

        {{-- SETTINGS MENU WITH DROPDOWN --}}
        <div class="ml-settings-container">
            <button type="button" class="ml-nav-item" id="ml-settings-btn" aria-expanded="false" aria-haspopup="true">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg>
                <span>Pengaturan</span>
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
            <input type="text" placeholder="Cari cepat…" aria-label="Cari cepat">
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
                                ({{ auth()->user()->role_label ?? 'Staff' }})
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
                                    {{ auth()->user()->role_label ?? 'Staff' }}
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
            <span>Stok Obat</span>
        </a>
        <a href="{{ route('transfers.index') }}" class="ml-bottom-item {{ request()->routeIs('transfers.*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M7 16V4m0 0L3 8m4-4 4 4M17 8v12m0 0 4-4m-4 4-4-4"/>
            </svg>
            <span>Redistribusi</span>
        </a>
        <a href="{{ route('network.index') }}" class="ml-bottom-item {{ request()->routeIs('network.*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 21s-7-4.35-7-10a7 7 0 1114 0c0 5.65-7 10-7 10z"/>
                <circle cx="12" cy="11" r="2.5"/>
            </svg>
            <span>Peta</span>
        </a>
        <a href="{{ route('audit.index') }}" class="ml-bottom-item {{ request()->routeIs('audit.*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 19v-6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2zm0 0V9a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v10m-6 0a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2m0 0V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-2a2 2 0 0 1-2-2z"/>
            </svg>
            <span>Audit</span>
        </a>
    </div>
</nav>

@livewireScripts
@yield('scripts')
</body>
</html>
