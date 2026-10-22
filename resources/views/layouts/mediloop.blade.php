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
            --ml-risk: #D9480F;
            --font-heading: 'Bricolage Grotesque', system-ui, sans-serif;
            --font-body: 'DM Sans', system-ui, sans-serif;
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
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 24px;
        }
        .ml-stat-card {
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
            transition: all 0.15s;
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
            transition: all 0.15s;
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
        .ml-network-legend-dot.is-partner { background: var(--ml-primary); }
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
            width: 28px;
            height: 28px;
            border: 2px solid #fff;
            border-radius: 50%;
            color: #fff;
            font-family: var(--font-heading);
            font-size: 11px;
            font-weight: 800;
            font-variant-numeric: tabular-nums;
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
                grid-template-columns: repeat(2, 1fr);
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

        // Auto-hiding Scrollbar Listener
        let scrollTimeout;
        window.addEventListener('scroll', function (e) {
            document.documentElement.classList.add('is-scrolling');
            document.body.classList.add('is-scrolling');
            if (e.target && e.target.classList && e.target.classList.contains('ml-table-responsive')) {
                e.target.classList.add('is-scrolling');
            }
            clearTimeout(scrollTimeout);
            scrollTimeout = setTimeout(function () {
                document.documentElement.classList.remove('is-scrolling');
                document.body.classList.remove('is-scrolling');
                document.querySelectorAll('.is-scrolling').forEach(function (el) {
                    el.classList.remove('is-scrolling');
                });
            }, 900);
        }, { passive: true, capture: true });
    });
</script>

@livewireScripts
</body>
</html>
