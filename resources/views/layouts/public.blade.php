<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ filled($title ?? null) ? $title.' — MediLoop' : 'MediLoop' }}</title>
    <meta name="description" content="{{ __('public.meta_description') }}">
    @include('partials.favicons')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script type="application/json" id="js-translations">@json(__('js'))</script>
    <style>
        :root {
            --pub-navy: #0B1F4B;
            --pub-primary: #1D4ED8;
            --pub-primary-strong: #1E40AF;
            --pub-surface: #F4F7FC;
            --pub-raised: #FFFFFF;
            --pub-border: #DBE5F5;
            --pub-border-strong: #8FA0B8;
            --pub-muted: #52627F;
            --pub-subtle: #6B7C96;
            --pub-risk: #B42318;
            --pub-heading: var(--font-sans);
            --pub-body: var(--font-sans);
            --pub-code: var(--font-mono);
            --pub-shadow-panel: 0 1px 2px rgba(11, 31, 75, 0.04), 0 18px 48px -32px rgba(11, 31, 75, 0.28);
        }
        *, *::before, *::after { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body {
            margin: 0;
            min-height: 100vh;
            background:
                radial-gradient(1100px 520px at 88% -12%, #E5EDFF 0%, rgba(229, 237, 255, 0) 68%),
                var(--pub-surface);
            color: var(--pub-navy);
            font-family: var(--pub-body);
            font-size: 14px;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
            text-rendering: optimizeLegibility;
        }
        a { color: inherit; }
        .pub-skip {
            position: absolute;
            left: -9999px;
            top: 12px;
            z-index: 100;
            background: #fff;
            padding: 8px 14px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            color: var(--pub-primary);
        }
        .pub-skip:focus { left: 12px; }
        .pub-header {
            position: sticky;
            top: 0;
            z-index: 40;
            background: rgba(255, 255, 255, 0.88);
            border-bottom: 1px solid rgba(219, 229, 245, 0.9);
            backdrop-filter: blur(14px);
        }
        .pub-header-inner {
            max-width: 1160px;
            margin: 0 auto;
            padding: 0 24px;
            height: 68px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }
        .pub-logo {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            font-family: var(--pub-heading);
            font-size: 19px;
            font-weight: 600;
            letter-spacing: -0.03em;
        }
        .pub-logo-mark {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            background: var(--pub-primary);
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 6px 14px -8px rgba(29, 78, 216, 0.8);
        }
        .pub-nav {
            display: flex;
            align-items: center;
            gap: 18px;
            font-size: 13px;
            font-weight: 500;
        }
        .pub-nav a { text-decoration: none; color: var(--pub-muted); }
        .pub-nav a:hover { color: var(--pub-primary); }
        .pub-nav a.pub-btn { color: var(--pub-navy); }
        .pub-nav a.pub-btn-primary { color: #fff; }
        .pub-main {
            max-width: 1160px;
            margin: 0 auto;
            padding: 44px 24px 72px;
        }
        .pub-footer {
            border-top: 1px solid var(--pub-border);
            padding: 20px 24px 32px;
            text-align: center;
            font-size: 11.5px;
            line-height: 1.6;
            color: var(--pub-muted);
        }
        .pub-page-narrow { max-width: 820px; margin: 0 auto; }
        .pub-eyebrow {
            margin: 0 0 10px;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--pub-primary);
        }
        .pub-title {
            margin: 0;
            font-family: var(--pub-heading);
            font-size: clamp(28px, 4vw, 38px);
            font-weight: 600;
            line-height: 1.13;
            letter-spacing: -0.035em;
        }
        .pub-sub {
            margin: 12px 0 0;
            font-size: 14px;
            line-height: 1.65;
            color: var(--pub-muted);
            max-width: 64ch;
        }
        .pub-card {
            margin-top: 24px;
            background: var(--pub-raised);
            border: 1px solid var(--pub-border);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: var(--pub-shadow-panel);
        }
        .pub-card + .pub-card { margin-top: 18px; }
        .pub-card-head {
            padding: 20px 24px;
            border-bottom: 1px solid var(--pub-border);
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 14px;
        }
        .pub-card-title {
            margin: 0;
            font-family: var(--pub-heading);
            font-size: 16px;
            font-weight: 600;
            letter-spacing: -0.015em;
        }
        .pub-card-sub {
            margin: 5px 0 0;
            font-size: 12.5px;
            line-height: 1.55;
            color: var(--pub-muted);
        }
        .pub-card-body { padding: 24px; }
        .pub-label {
            display: block;
            margin-bottom: 7px;
            font-size: 13px;
            font-weight: 600;
            color: var(--pub-navy);
        }
        .pub-required { color: var(--pub-risk); }
        .pub-input,
        .pub-select,
        .pub-textarea {
            width: 100%;
            min-height: 44px;
            padding: 10px 13px;
            border: 1px solid var(--pub-border-strong);
            border-radius: 10px;
            background: #fff;
            color: var(--pub-navy);
            font-family: var(--pub-body);
            font-size: 14px;
            transition: border-color 160ms ease, box-shadow 160ms ease;
        }
        .pub-input::placeholder,
        .pub-textarea::placeholder { color: #7A8AA3; }
        .pub-select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2352627F' stroke-width='2' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 14px center;
            padding-right: 38px;
        }
        .pub-textarea { min-height: 104px; resize: vertical; line-height: 1.55; }
        .pub-input:focus,
        .pub-select:focus,
        .pub-textarea:focus {
            outline: none;
            border-color: var(--pub-primary);
            box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.14);
        }
        .pub-input.is-invalid,
        .pub-select.is-invalid,
        .pub-textarea.is-invalid { border-color: #B42318; }
        .pub-input.is-invalid:focus,
        .pub-select.is-invalid:focus,
        .pub-textarea.is-invalid:focus {
            box-shadow: 0 0 0 3px rgba(180, 35, 24, 0.14);
        }
        .pub-field { margin-bottom: 18px; }
        .pub-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
        }
        .pub-grid .is-full { grid-column: 1 / -1; }
        .pub-help {
            margin-top: 6px;
            font-size: 12px;
            line-height: 1.5;
            color: var(--pub-muted);
        }
        .pub-error {
            margin-top: 6px;
            font-size: 12px;
            font-weight: 500;
            line-height: 1.5;
            color: var(--pub-risk);
        }
        .pub-error:empty { display: none; }
        .pub-checkbox {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: 13px;
            line-height: 1.55;
            color: var(--pub-muted);
        }
        .pub-checkbox input {
            width: 17px;
            height: 17px;
            margin-top: 1px;
            accent-color: var(--pub-primary);
            flex-shrink: 0;
        }
        .pub-actions {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
            padding-top: 20px;
            margin-top: 8px;
            border-top: 1px solid var(--pub-border);
        }
        .pub-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 44px;
            padding: 10px 18px;
            border-radius: 10px;
            border: 1px solid var(--pub-border-strong);
            background: #fff;
            color: var(--pub-navy);
            font-family: var(--pub-body);
            font-size: 13.5px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: background 160ms ease, border-color 160ms ease, color 160ms ease, transform 160ms ease;
        }
        .pub-btn:hover { background: #F5F8FE; border-color: #7C8FAB; }
        .pub-btn:active { transform: translateY(1px); }
        .pub-btn-primary {
            background: var(--pub-primary);
            border-color: var(--pub-primary);
            color: #fff;
            box-shadow: 0 8px 18px -12px rgba(29, 78, 216, 0.75);
        }
        .pub-btn-primary:hover { background: var(--pub-primary-strong); border-color: var(--pub-primary-strong); }
        .pub-btn:disabled { opacity: 0.55; cursor: not-allowed; }
        .pub-btn-block { width: 100%; }
        .pub-btn:focus-visible,
        .pub-input:focus-visible,
        .pub-select:focus-visible,
        .pub-textarea:focus-visible {
            outline: 2px solid var(--pub-primary);
            outline-offset: 2px;
        }
        .pub-alert {
            padding: 13px 16px;
            margin-bottom: 20px;
            border-radius: 10px;
            border: 1px solid var(--pub-border);
            font-size: 13px;
            line-height: 1.55;
        }
        .pub-alert-error {
            background: #FEF3F2;
            border-color: #FDA29B;
            color: #7A271A;
        }
        .pub-alert-info {
            background: #EFF4FF;
            border-color: #C7D7FE;
            color: #1E3A8A;
        }
        .pub-alert strong { display: block; margin-bottom: 2px; }
        .pub-alert ul { margin: 6px 0 0; padding-left: 18px; }
        .pub-map {
            height: 320px;
            border: 1px solid var(--pub-border-strong);
            border-radius: 12px;
            overflow: hidden;
            background: #EAF1FF;
        }
        .pub-map:focus-visible {
            outline: 2px solid var(--pub-primary);
            outline-offset: 2px;
        }
        .pub-map .leaflet-tile-pane { filter: grayscale(0.28) saturate(0.7); }
        .pub-map-meta {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            margin-top: 10px;
            font-size: 12px;
            color: var(--pub-muted);
        }
        .pub-map-meta code {
            padding: 3px 8px;
            border-radius: 6px;
            background: #F1F5F9;
            font-family: var(--pub-code);
            font-size: 11px;
            color: var(--pub-navy);
        }
        .pub-success {
            background: var(--pub-raised);
            border: 1px solid var(--pub-border);
            border-radius: 16px;
            padding: 40px 32px;
            text-align: center;
            box-shadow: var(--pub-shadow-panel);
        }
        .pub-success svg { color: var(--pub-primary); margin-bottom: 12px; }
        .pub-success h2 {
            margin: 0 0 8px;
            font-family: var(--pub-heading);
            font-size: 24px;
            font-weight: 600;
            letter-spacing: -0.03em;
        }
        .pub-success p {
            max-width: 54ch;
            margin: 0 auto 20px;
            font-size: 14px;
            line-height: 1.65;
            color: var(--pub-muted);
        }
        .pub-meta-list {
            display: grid;
            gap: 12px;
            margin: 0 0 20px;
            text-align: left;
        }
        .pub-meta-item {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--pub-border);
            font-size: 13px;
        }
        .pub-meta-item dt { color: var(--pub-muted); }
        .pub-meta-item dd { margin: 0; font-weight: 600; text-align: right; }

        /* Public onboarding layout */
        .pub-onboarding {
            display: grid;
            grid-template-columns: 320px minmax(0, 1fr);
            gap: 28px;
            align-items: start;
            max-width: 1120px;
            margin: 0 auto;
        }
        .pub-dossier {
            position: sticky;
            top: 96px;
            background: rgba(255, 255, 255, 0.72);
            border: 1px solid rgba(219, 229, 245, 0.9);
            border-radius: 16px;
            padding: 24px;
            backdrop-filter: blur(10px);
        }
        .pub-dossier h2 {
            margin: 0;
            font-family: var(--pub-heading);
            font-size: 19px;
            font-weight: 600;
            letter-spacing: -0.03em;
        }
        .pub-dossier > p {
            margin: 8px 0 22px;
            font-size: 12.5px;
            line-height: 1.6;
            color: var(--pub-muted);
        }
        .pub-steps {
            display: grid;
            gap: 0;
            margin: 0;
            padding: 0;
            list-style: none;
        }
        .pub-step {
            position: relative;
            display: flex;
            gap: 12px;
            padding-bottom: 20px;
        }
        .pub-step:last-child { padding-bottom: 0; }
        .pub-step::before {
            content: "";
            position: absolute;
            left: 13px;
            top: 30px;
            bottom: 2px;
            width: 1px;
            background: var(--pub-border);
        }
        .pub-step:last-child::before { display: none; }
        .pub-step-number {
            position: relative;
            z-index: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 27px;
            height: 27px;
            border-radius: 999px;
            border: 1px solid var(--pub-border-strong);
            background: #fff;
            color: var(--pub-muted);
            font-family: var(--pub-code);
            font-size: 10px;
            font-weight: 500;
            flex-shrink: 0;
        }
        .pub-step.is-current .pub-step-number {
            background: var(--pub-primary);
            border-color: var(--pub-primary);
            color: #fff;
        }
        .pub-step-title {
            display: block;
            padding-top: 3px;
            font-size: 13px;
            font-weight: 600;
        }
        .pub-step-sub {
            display: block;
            margin-top: 3px;
            font-size: 11.5px;
            line-height: 1.5;
            color: var(--pub-muted);
        }
        .pub-dossier-divider {
            height: 1px;
            margin: 22px 0;
            background: var(--pub-border);
        }
        .pub-trust-list {
            display: grid;
            gap: 12px;
            margin: 0;
            padding: 0;
            list-style: none;
        }
        .pub-trust-item {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: 12px;
            line-height: 1.5;
            color: var(--pub-muted);
        }
        .pub-trust-item svg {
            color: var(--pub-primary);
            flex-shrink: 0;
            margin-top: 1px;
        }
        .pub-form-panel {
            min-width: 0;
            background: var(--pub-raised);
            border: 1px solid var(--pub-border);
            border-radius: 18px;
            box-shadow: var(--pub-shadow-panel);
            overflow: hidden;
        }
        .pub-form-intro {
            padding: 30px 32px 26px;
            border-bottom: 1px solid var(--pub-border);
            background:
                linear-gradient(180deg, rgba(239, 244, 255, 0.55) 0%, rgba(255, 255, 255, 0) 100%);
        }
        .pub-form-intro .pub-title {
            max-width: 19ch;
            font-size: clamp(26px, 3.2vw, 34px);
        }
        .pub-form-intro .pub-sub { max-width: 56ch; }
        .pub-form-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 8px 18px;
            margin-top: 18px;
            font-size: 12px;
            color: var(--pub-muted);
        }
        .pub-form-meta span {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .pub-form-meta svg { color: var(--pub-primary); }
        .pub-section {
            padding: 28px 32px;
            border-bottom: 1px solid var(--pub-border);
            scroll-margin-top: 96px;
        }
        .pub-section:last-of-type { border-bottom: 0; }
        .pub-section-head {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            margin-bottom: 22px;
        }
        .pub-section-index {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            border-radius: 9px;
            background: #EFF4FF;
            color: var(--pub-primary);
            font-family: var(--pub-code);
            font-size: 11px;
            font-weight: 500;
            flex-shrink: 0;
        }
        .pub-section-title {
            margin: 1px 0 0;
            font-family: var(--pub-heading);
            font-size: 17px;
            font-weight: 600;
            letter-spacing: -0.02em;
        }
        .pub-section-sub {
            margin: 5px 0 0;
            font-size: 12.5px;
            line-height: 1.55;
            color: var(--pub-muted);
        }
        .pub-submitbar {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            padding: 20px 32px;
            background: #FBFCFE;
            border-top: 1px solid var(--pub-border);
        }
        .pub-submit-note {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            max-width: 46ch;
            font-size: 11.5px;
            line-height: 1.55;
            color: var(--pub-muted);
        }
        .pub-submit-note svg { color: var(--pub-primary); flex-shrink: 0; margin-top: 1px; }
        .pub-submit-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-left: auto;
        }
        .pub-success-shell {
            max-width: 860px;
            margin: 0 auto;
        }
        .pub-success-card {
            display: grid;
            grid-template-columns: minmax(0, 0.9fr) minmax(0, 1.1fr);
            gap: 0;
            background: var(--pub-raised);
            border: 1px solid var(--pub-border);
            border-radius: 18px;
            overflow: hidden;
            box-shadow: var(--pub-shadow-panel);
        }
        .pub-success-lead {
            padding: 40px 34px;
            border-right: 1px solid var(--pub-border);
            background: linear-gradient(180deg, #EFF4FF 0%, #FFFFFF 100%);
        }
        .pub-success-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 46px;
            height: 46px;
            margin-bottom: 20px;
            border-radius: 13px;
            background: var(--pub-primary);
            color: #fff;
        }
        .pub-success-lead h2 {
            margin: 0 0 10px;
            font-family: var(--pub-heading);
            font-size: 27px;
            font-weight: 600;
            letter-spacing: -0.03em;
        }
        .pub-success-lead p {
            margin: 0;
            font-size: 13.5px;
            line-height: 1.65;
            color: var(--pub-muted);
        }
        .pub-success-next {
            padding: 34px;
        }
        .pub-success-next h3 {
            margin: 0 0 18px;
            font-family: var(--pub-heading);
            font-size: 13px;
            font-weight: 600;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: var(--pub-muted);
        }
        .pub-success-steps {
            display: grid;
            gap: 18px;
            margin: 0 0 26px;
            padding: 0;
            list-style: none;
        }
        .pub-success-step {
            display: flex;
            gap: 12px;
        }
        .pub-success-step span:first-child {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 24px;
            height: 24px;
            border-radius: 999px;
            background: #EFF4FF;
            color: var(--pub-primary);
            font-family: var(--pub-code);
            font-size: 10px;
            flex-shrink: 0;
        }
        .pub-success-step strong {
            display: block;
            font-size: 13px;
            font-weight: 600;
        }
        .pub-success-step small {
            display: block;
            margin-top: 3px;
            font-size: 12px;
            line-height: 1.5;
            color: var(--pub-muted);
        }
        .pub-success-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        /* Invitation acceptance */
        .pub-invite-shell {
            display: grid;
            grid-template-columns: minmax(0, 0.92fr) minmax(0, 1.08fr);
            max-width: 980px;
            margin: 0 auto;
            overflow: hidden;
            border: 1px solid var(--pub-border);
            border-radius: 18px;
            background: var(--pub-raised);
            box-shadow: var(--pub-shadow-panel);
        }
        .pub-invite-aside {
            padding: 40px 36px;
            border-right: 1px solid var(--pub-border);
            background:
                radial-gradient(500px 300px at 10% 0%, #E5EDFF 0%, rgba(229, 237, 255, 0) 72%),
                #FBFCFE;
        }
        .pub-invite-aside h1 {
            margin: 0 0 14px;
            font-family: var(--pub-heading);
            font-size: clamp(26px, 3.4vw, 34px);
            font-weight: 600;
            line-height: 1.15;
            letter-spacing: -0.035em;
        }
        .pub-invite-aside p {
            margin: 0;
            color: var(--pub-muted);
            font-size: 13.5px;
            line-height: 1.65;
        }
        .pub-invite-facility {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 26px 0;
            padding: 14px;
            border: 1px solid var(--pub-border);
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.7);
        }
        .pub-invite-mark {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 11px;
            background: var(--pub-primary);
            color: #fff;
            font-family: var(--pub-heading);
            font-size: 16px;
            font-weight: 600;
            flex-shrink: 0;
        }
        .pub-invite-facility strong {
            display: block;
            font-size: 13px;
            font-weight: 600;
        }
        .pub-invite-facility small {
            display: block;
            margin-top: 2px;
            color: var(--pub-muted);
            font-size: 11.5px;
        }
        .pub-invite-meta {
            display: grid;
            gap: 12px;
            margin: 0;
            padding: 0;
            list-style: none;
        }
        .pub-invite-meta li {
            display: flex;
            justify-content: space-between;
            gap: 14px;
            padding-bottom: 11px;
            border-bottom: 1px solid var(--pub-border);
            font-size: 12.5px;
        }
        .pub-invite-meta li > span { color: var(--pub-muted); }
        .pub-invite-meta li > strong { font-weight: 600; text-align: right; overflow-wrap: anywhere; }
        .pub-invite-trust {
            display: flex;
            gap: 9px;
            margin-top: 26px;
            padding-top: 20px;
            border-top: 1px solid var(--pub-border);
            color: var(--pub-muted);
            font-size: 11.5px;
            line-height: 1.55;
        }
        .pub-invite-trust svg { color: var(--pub-primary); flex-shrink: 0; margin-top: 1px; }
        .pub-invite-form { padding: 40px 38px; }
        .pub-invite-form h2 {
            margin: 0 0 6px;
            font-family: var(--pub-heading);
            font-size: 22px;
            font-weight: 600;
            letter-spacing: -0.025em;
        }
        .pub-invite-form > p {
            margin: 0 0 24px;
            color: var(--pub-muted);
            font-size: 13px;
            line-height: 1.6;
        }
        .pub-invite-form .pub-actions {
            margin-top: 4px;
            padding-top: 18px;
        }
        .pub-invite-footnote {
            margin: 18px 0 0;
            color: var(--pub-muted);
            font-size: 11.5px;
            line-height: 1.55;
        }

        @media (max-width: 880px) {
            .pub-invite-shell { grid-template-columns: 1fr; max-width: 620px; }
            .pub-invite-aside { border-right: 0; border-bottom: 1px solid var(--pub-border); }
        }

        @media (max-width: 980px) {
            .pub-onboarding { grid-template-columns: 1fr; max-width: 760px; }
            .pub-dossier { position: static; }
            .pub-steps { grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 8px; }
            .pub-step { display: block; padding-bottom: 0; }
            .pub-step::before { display: none; }
            .pub-step-number { margin-bottom: 8px; }
            .pub-step-sub { display: none; }
            .pub-dossier-divider, .pub-trust-list { display: none; }
            .pub-success-card { grid-template-columns: 1fr; }
            .pub-success-lead { border-right: 0; border-bottom: 1px solid var(--pub-border); }
        }
        @media (max-width: 720px) {
            .pub-header-inner { padding: 0 16px; height: 62px; }
            .pub-main { padding: 26px 16px 52px; }
            .pub-title { font-size: 27px; }
            .pub-grid { grid-template-columns: 1fr; }
            .pub-card-head, .pub-card-body { padding: 20px; }
            .pub-nav { gap: 10px; }
            .pub-nav .pub-btn { padding: 8px 12px; }
            .pub-map { height: 260px; }
            .pub-dossier { padding: 18px; }
            .pub-steps { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; }
            .pub-form-intro, .pub-section, .pub-submitbar { padding: 22px 20px; }
            .pub-submitbar { position: sticky; bottom: 0; z-index: 20; box-shadow: 0 -10px 30px -24px rgba(11, 31, 75, 0.4); }
            .pub-submit-note { display: none; }
            .pub-submit-actions { width: 100%; }
            .pub-submit-actions .pub-btn { flex: 1; }
            .pub-success-lead, .pub-success-next { padding: 28px 22px; }
        }
        @media (prefers-reduced-motion: reduce) {
            html { scroll-behavior: auto; }
            * { transition: none !important; animation: none !important; }
        }
    </style>
    @yield('head')
</head>
<body>
    <a href="#main-content" class="pub-skip">{{ __('public.skip_to_content') }}</a>

    <header class="pub-header">
        <div class="pub-header-inner">
            <a href="{{ route('home') }}" class="pub-logo">
                <span class="pub-logo-mark" aria-hidden="true" style="background: transparent; box-shadow: none;">
                    <img src="{{ asset('images/logo.png') }}" alt="" style="width: 34px; height: 34px; object-fit: contain;" />
                </span>
                MediLoop
            </a>

            @if(! request()->routeIs('invitations.*'))
                <nav class="pub-nav" aria-label="{{ __('navigation.public') }}">

                    @auth
                        <a href="{{ route('dashboard') }}">{{ __('navigation.dashboard') }}</a>
                    @else
                        <a href="{{ route('login') }}">{{ __('navigation.sign_in') }}</a>
                        @if(! request()->routeIs('facility-requests.*'))
                            <a href="{{ route('facility-requests.create') }}" class="pub-btn pub-btn-primary">{{ __('public.request_facility') }}</a>
                        @endif
                    @endauth
                </nav>
            @endif
        </div>
    </header>

    <main id="main-content" class="pub-main">
        @yield('content')
    </main>

    <footer class="pub-footer">
        {{ __('public.footer') }}
    </footer>

    @yield('scripts')
</body>
</html>
