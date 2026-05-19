{{--
    Layout Utama Aplikasi
    
    Template dasar yang digunakan oleh semua halaman.
    Menyediakan struktur HTML, meta tags, font, dan styling global.
    Halaman-halaman lain meng-extend layout ini melalui @extends('layouts.app').
--}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistem Pembimbing Penulisan Artikel Ilmiah — Analisis struktur, keterbacaan, dan tata bahasa artikel ilmiah Anda secara otomatis.">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Pembimbing Artikel Ilmiah')</title>

    {{-- Google Fonts: Inter untuk tipografi modern --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        /* ===== CSS Reset & Base ===== */
        *, *::before, *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            /* Identitas UNIMED */
            --unimed-green: #059669; /* Hijau modern UNIMED */
            --unimed-green-dark: #047857;
            --unimed-red: #dc2626;
            --unimed-white: #ffffff;

            /* Menggunakan warna UNIMED sebagai warna utama (primary) */
            --primary: var(--unimed-green);
            --primary-dark: var(--unimed-green-dark);
            --primary-light: #34d399;
            --primary-bg: #ecfdf5;
            
            --accent: var(--unimed-red);
            --accent-dark: #b91c1c;
            --success: #10b981;
            --success-bg: #ecfdf5;
            --warning: #f59e0b;
            --warning-bg: #fffbeb;
            --danger: var(--unimed-red);
            --danger-bg: #fef2f2;
            --info: #3b82f6;
            --info-bg: #eff6ff;
            --gray-50: #f8fafc;
            --gray-100: #f1f5f9;
            --gray-200: #e2e8f0;
            --gray-300: #cbd5e1;
            --gray-400: #94a3b8;
            --gray-500: #64748b;
            --gray-600: #475569;
            --gray-700: #334155;
            --gray-800: #1e293b;
            --gray-900: #0f172a;
            --radius: 12px;
            --radius-lg: 16px;
            --radius-xl: 24px;
            --shadow-sm: 0 1px 2px rgba(0,0,0,0.05);
            --shadow: 0 4px 6px -1px rgba(0,0,0,0.07), 0 2px 4px -2px rgba(0,0,0,0.05);
            --shadow-lg: 0 10px 15px -3px rgba(0,0,0,0.08), 0 4px 6px -4px rgba(0,0,0,0.04);
            --shadow-xl: 0 20px 25px -5px rgba(0,0,0,0.08), 0 8px 10px -6px rgba(0,0,0,0.04);
            --transition: 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: transparent;
            color: var(--gray-800);
            line-height: 1.6;
            min-height: 100vh;
        }

        /* ===== Background Video ===== */
        .video-bg {
            position: fixed;
            top: 50%;
            left: 50%;
            min-width: 100%;
            min-height: 100vh;
            width: auto;
            height: auto;
            transform: translateX(-50%) translateY(-50%);
            object-fit: cover;
            filter: blur(2px);
            z-index: -2;
        }

        /* ===== Video Overlay ===== */
        .video-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            background: linear-gradient(to bottom, rgba(0, 50, 0, 0.75), rgba(0, 0, 0, 0.7));
            z-index: -1;
            pointer-events: none;
        }

        /* ===== Content Wrapper ===== */
        .content-wrapper {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* ===== Header / Navbar ===== */
        .navbar {
            background: var(--unimed-green);
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .navbar-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 0.75rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            color: var(--unimed-white);
            font-weight: 700;
            font-size: 1.125rem;
        }

        .navbar-logo-img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            background-color: var(--unimed-white);
            padding: 2px;
        }

        .navbar-toggler {
            background: transparent;
            border: none;
            color: var(--unimed-white);
            font-size: 1.75rem;
            cursor: pointer;
            display: none;
        }

        .navbar-collapse {
            display: flex;
            align-items: center;
        }

        .navbar-nav {
            list-style: none;
            display: flex;
            gap: 1.5rem;
            margin: 0;
            padding: 0;
        }

        .nav-link {
            text-decoration: none;
            color: rgba(255, 255, 255, 0.85);
            font-weight: 500;
            font-size: 0.9375rem;
            transition: color var(--transition);
        }

        .nav-link:hover, .nav-link.active {
            color: var(--unimed-white);
        }

        /* ===== Main Container ===== */
        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 2rem 1.5rem 4rem;
        }

        /* ===== Hero Section ===== */
        .hero {
            text-align: center;
            padding: 2.5rem 0 2rem;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            background: var(--primary-bg);
            color: var(--primary-dark);
            font-size: 0.8125rem;
            font-weight: 600;
            padding: 0.375rem 0.875rem;
            border-radius: 100px;
            margin-bottom: 1rem;
            letter-spacing: 0.01em;
        }

        .hero h1 {
            font-size: 2.5rem;
            font-weight: 900;
            color: var(--unimed-white);
            letter-spacing: -0.025em;
            line-height: 1.2;
            margin-bottom: 0.625rem;
            text-shadow: 0 4px 16px rgba(0,0,0,0.8), 0 2px 4px rgba(0,0,0,0.6);
        }

        .hero h1 .gradient-text {
            background: linear-gradient(135deg, var(--primary-light), var(--unimed-white));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero p {
            font-size: 1.0625rem;
            color: rgba(255, 255, 255, 0.95);
            max-width: 560px;
            margin: 0 auto;
            line-height: 1.7;
            text-shadow: 0 2px 8px rgba(0,0,0,0.8);
        }

        /* ===== Card ===== */
        .card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.6);
            border-radius: var(--radius-lg);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .card-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--gray-100);
            display: flex;
            align-items: center;
            gap: 0.625rem;
        }

        .card-header-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }

        .card-header-icon.purple {
            background: var(--primary-bg);
            color: var(--primary);
        }

        .card-header-icon.teal {
            background: #ecfeff;
            color: var(--accent-dark);
        }

        .card-header-icon.green {
            background: var(--success-bg);
            color: var(--success);
        }

        .card-header-icon.amber {
            background: var(--warning-bg);
            color: var(--warning);
        }

        .card-header-icon.red {
            background: var(--danger-bg);
            color: var(--danger);
        }

        .card-header-icon.blue {
            background: var(--info-bg);
            color: var(--info);
        }

        .card-header h2 {
            font-size: 1rem;
            font-weight: 600;
            color: var(--gray-800);
        }

        .card-body {
            padding: 2rem;
        }

        /* ===== Form Elements ===== */
        label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--gray-700);
            margin-bottom: 0.5rem;
        }

        textarea {
            width: 100%;
            min-height: 220px;
            padding: 1rem;
            border: 1.5px solid var(--gray-200);
            border-radius: var(--radius);
            font-family: 'Inter', sans-serif;
            font-size: 0.9375rem;
            line-height: 1.75;
            color: var(--gray-800);
            background: var(--gray-50);
            resize: vertical;
            transition: border-color var(--transition), box-shadow var(--transition), background var(--transition);
        }

        textarea:focus {
            outline: none;
            border-color: var(--unimed-green);
            box-shadow: 0 0 0 4px rgba(5, 150, 105, 0.15);
            background: #fff;
        }

        textarea::placeholder {
            color: var(--gray-400);
        }

        .input-hint {
            font-size: 0.8125rem;
            color: var(--gray-400);
            margin-top: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.375rem;
        }

        /* ===== Buttons ===== */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem 1.75rem;
            border: none;
            border-radius: var(--radius);
            font-family: 'Inter', sans-serif;
            font-size: 0.9375rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #fff;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            border: 1px solid transparent;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(5, 150, 105, 0.4);
            background: linear-gradient(135deg, var(--primary-dark), #064e3b);
        }

        .btn-primary:active {
            transform: translateY(0);
            box-shadow: 0 2px 8px rgba(5, 150, 105, 0.2);
        }

        .btn-secondary {
            background: #fff;
            color: var(--gray-700);
            border: 1.5px solid var(--gray-200);
            box-shadow: var(--shadow-sm);
        }

        .btn-secondary:hover {
            background: var(--gray-50);
            border-color: var(--gray-300);
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }

        .btn-group {
            display: flex;
            gap: 0.75rem;
            margin-top: 1.25rem;
            flex-wrap: wrap;
        }

        /* ===== Error Alert ===== */
        .alert {
            padding: 0.875rem 1.125rem;
            border-radius: var(--radius);
            font-size: 0.875rem;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: flex-start;
            gap: 0.5rem;
        }

        .alert-danger {
            background: var(--danger-bg);
            color: #b91c1c;
            border: 1px solid #fecaca;
        }

        .alert-danger i {
            margin-top: 1px;
        }

        /* ===== Toast Notifications ===== */
        #toast-container {
            position: fixed;
            top: 24px;
            right: 24px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 12px;
            pointer-events: none;
        }

        .toast {
            min-width: 250px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            padding: 14px 18px;
            display: flex;
            align-items: center;
            gap: 12px;
            transform: translateX(120%);
            opacity: 0;
            transition: all 0.4s cubic-bezier(0.68, -0.55, 0.27, 1.55);
            pointer-events: auto;
        }

        .toast.show {
            transform: translateX(0);
            opacity: 1;
        }

        .toast-icon { font-size: 1.25rem; }
        .toast-success { border-left: 4px solid var(--success); }
        .toast-success .toast-icon { color: var(--success); }
        .toast-error { border-left: 4px solid var(--danger); }
        .toast-error .toast-icon { color: var(--danger); }
        .toast-warning { border-left: 4px solid var(--warning); }
        .toast-warning .toast-icon { color: var(--warning); }

        .toast-content {
            flex: 1;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--gray-800);
        }

        /* ===== Results Section ===== */
        .results-section {
            margin-top: 2rem;
        }

        .results-section .section-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .results-grid {
            display: grid;
            gap: 1.25rem;
        }

        /* ===== Stat Cards ===== */
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 0.875rem;
        }

        .stat-card {
            background: var(--gray-50);
            border: 1px solid var(--gray-100);
            border-radius: var(--radius);
            padding: 1rem;
            text-align: center;
            transition: all var(--transition);
        }

        .stat-card:hover {
            border-color: var(--primary-light);
            background: var(--primary-bg);
        }

        .stat-value {
            font-size: 1.75rem;
            font-weight: 800;
            color: var(--primary);
            letter-spacing: -0.02em;
        }

        .stat-label {
            font-size: 0.8125rem;
            color: var(--gray-500);
            font-weight: 500;
            margin-top: 0.125rem;
        }

        /* ===== Structure Check List ===== */
        .check-list {
            list-style: none;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 0.5rem;
        }

        .check-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.625rem 0.875rem;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 500;
            transition: background var(--transition);
        }

        .check-item:hover {
            background: var(--gray-50);
        }

        .check-item.found {
            color: #047857;
        }

        .check-item.found i {
            color: var(--success);
        }

        .check-item.missing {
            color: #b91c1c;
        }

        .check-item.missing i {
            color: var(--danger);
        }

        /* ===== Readability Meter ===== */
        .readability-meter {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            flex-wrap: wrap;
        }

        .readability-score {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: 800;
            color: #fff;
            flex-shrink: 0;
        }

        .readability-score.easy { background: linear-gradient(135deg, #10b981, #059669); }
        .readability-score.medium { background: linear-gradient(135deg, #f59e0b, #d97706); }
        .readability-score.hard { background: linear-gradient(135deg, #ef4444, #dc2626); }

        .readability-info {
            flex: 1;
        }

        .readability-level {
            font-size: 1.125rem;
            font-weight: 700;
            color: var(--gray-800);
        }

        .readability-detail {
            font-size: 0.875rem;
            color: var(--gray-500);
            margin-top: 0.25rem;
        }

        /* ===== Suggestion Items ===== */
        .suggestion-list {
            display: grid;
            gap: 0.625rem;
        }

        .suggestion-item {
            display: flex;
            align-items: flex-start;
            gap: 0.625rem;
            padding: 0.875rem 1rem;
            border-radius: var(--radius);
            font-size: 0.875rem;
            line-height: 1.6;
        }

        .suggestion-item i {
            margin-top: 2px;
            flex-shrink: 0;
            font-size: 1rem;
        }

        .suggestion-item.warning {
            background: var(--warning-bg);
            color: #92400e;
            border: 1px solid #fde68a;
        }

        .suggestion-item.suggestion {
            background: var(--info-bg);
            color: #1e40af;
            border: 1px solid #bfdbfe;
        }

        .suggestion-item.info {
            background: #f0fdf4;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .suggestion-item.grammar,
        .suggestion-item.formatting,
        .suggestion-item.language {
            background: var(--danger-bg);
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .suggestion-item.error {
            background: var(--danger-bg);
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        /* ===== Score Display ===== */
        .score-display {
            display: flex;
            align-items: center;
            gap: 1.75rem;
            flex-wrap: wrap;
        }

        .score-circle {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            color: #fff;
            box-shadow: 0 4px 16px rgba(0,0,0,0.15);
        }

        .score-circle.easy { background: linear-gradient(135deg, #10b981, #059669); }
        .score-circle.medium { background: linear-gradient(135deg, #f59e0b, #d97706); }
        .score-circle.hard { background: linear-gradient(135deg, #ef4444, #dc2626); }

        .score-number {
            font-size: 2rem;
            font-weight: 800;
            line-height: 1;
        }

        .score-max {
            font-size: 0.75rem;
            font-weight: 500;
            opacity: 0.8;
        }

        .score-info {
            flex: 1;
            min-width: 200px;
        }

        .score-label {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--gray-800);
        }

        .score-detail {
            font-size: 0.875rem;
            color: var(--gray-500);
            margin-top: 0.25rem;
        }

        .score-bar-container {
            width: 100%;
            height: 8px;
            background: var(--gray-100);
            border-radius: 100px;
            margin-top: 0.75rem;
            overflow: hidden;
        }

        .score-bar {
            height: 100%;
            border-radius: 100px;
            transition: width 1s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .score-bar.easy { background: linear-gradient(90deg, #10b981, #059669); }
        .score-bar.medium { background: linear-gradient(90deg, #f59e0b, #d97706); }
        .score-bar.hard { background: linear-gradient(90deg, #ef4444, #dc2626); }

        /* ===== Badges ===== */
        .badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 22px;
            height: 22px;
            padding: 0 0.375rem;
            border-radius: 100px;
            font-size: 0.75rem;
            font-weight: 700;
            margin-left: auto;
        }

        .badge-red {
            background: var(--danger-bg);
            color: var(--danger);
            border: 1px solid #fecaca;
        }

        .badge-amber {
            background: var(--warning-bg);
            color: #b45309;
            border: 1px solid #fde68a;
        }

        /* ===== Empty State ===== */
        .empty-state {
            text-align: center;
            padding: 1.5rem 1rem;
            color: var(--gray-400);
            font-size: 0.875rem;
        }

        .empty-state i {
            font-size: 1.5rem;
            display: block;
            margin-bottom: 0.5rem;
        }

        /* ===== AI Improvement Card ===== */
        .ai-notice {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            padding: 1.25rem;
            background: linear-gradient(135deg, #f8fafc, #eef2ff);
            border: 1px dashed var(--gray-300);
            border-radius: var(--radius);
        }

        .ai-notice-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: var(--primary-bg);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.125rem;
            flex-shrink: 0;
        }

        .ai-notice-content {
            flex: 1;
        }

        .ai-notice-title {
            font-size: 0.9375rem;
            font-weight: 600;
            color: var(--gray-800);
            margin-bottom: 0.375rem;
        }

        .ai-notice-text {
            font-size: 0.875rem;
            color: var(--gray-500);
            line-height: 1.6;
        }

        .ai-code-block {
            background: var(--gray-800);
            color: #a5f3fc;
            padding: 0.625rem 0.875rem;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 0.8125rem;
            margin-top: 0.625rem;
            overflow-x: auto;
        }

        .ai-code-block code {
            background: none;
            padding: 0;
            color: inherit;
        }

        .ai-improved-text {
            background: linear-gradient(135deg, #f0fdf4, #ecfdf5);
            border: 1px solid #bbf7d0;
            border-left: 4px solid var(--success);
            border-radius: var(--radius);
            padding: 1.25rem;
            font-size: 0.9375rem;
            line-height: 1.85;
            color: var(--gray-800);
            white-space: pre-wrap;
        }

        .ai-actions {
            margin-top: 0.875rem;
            display: flex;
            gap: 0.5rem;
        }

        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.8125rem;
        }

        .btn-sm.copied {
            background: var(--success-bg);
            border-color: var(--success);
            color: #047857;
        }

        .badge-green {
            background: var(--success-bg);
            color: var(--success);
            border: 1px solid #bbf7d0;
        }

        /* ===== Footer ===== */
        .footer {
            background: var(--gray-900);
            color: var(--gray-400);
            text-align: center;
            padding: 2rem 1.5rem;
            font-size: 0.875rem;
            margin-top: auto;
        }

        .footer-container {
            max-width: 900px;
            margin: 0 auto;
        }

        .footer p {
            margin: 0;
        }

        /* ===== Animations ===== */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-in {
            animation: fadeInUp 0.5s ease-out both;
        }

        .animate-in:nth-child(1) { animation-delay: 0s; }
        .animate-in:nth-child(2) { animation-delay: 0.08s; }
        .animate-in:nth-child(3) { animation-delay: 0.16s; }
        .animate-in:nth-child(4) { animation-delay: 0.24s; }
        .animate-in:nth-child(5) { animation-delay: 0.32s; }

        /* ===== Responsive ===== */
        @media (max-width: 640px) {
            .hero h1 { font-size: 1.5rem; }
            .hero p { font-size: 0.9375rem; }
            .container { padding: 1.25rem 1rem 3rem; }
            .stat-grid { grid-template-columns: repeat(2, 1fr); }
            .check-list { grid-template-columns: 1fr; }
            .readability-meter { flex-direction: column; text-align: center; }
            .score-display { flex-direction: column; text-align: center; }
            .score-info { min-width: unset; }
            
            /* Navbar Mobile */
            .navbar-toggler {
                display: block;
            }
            .navbar-collapse {
                display: none;
                width: 100%;
                margin-top: 1rem;
                padding-top: 1rem;
                border-top: 1px solid rgba(255,255,255,0.1);
            }
            .navbar-collapse.show {
                display: block;
            }
            .navbar-nav {
                flex-direction: column;
                gap: 1rem;
            }
        }
    </style>
</head>
<body>
    {{-- Background Video (Z-Index: -2) --}}
    <video autoplay loop muted playsinline preload="auto" class="video-bg">
        <source src="{{ asset('images/unimed.mp4') }}" type="video/mp4">
    </video>

    {{-- Overlay Gelap (Z-Index: -1) --}}
    <div class="video-overlay"></div>

    {{-- Content Wrapper (Z-Index: 1) --}}
    <div class="content-wrapper">
        {{-- Navbar --}}
        <nav class="navbar">
            <div class="navbar-container">
                <a href="{{ route('home') }}" class="navbar-brand">
                    <img src="{{ asset('images/logo-unimed.png') }}" alt="Logo UNIMED" class="navbar-logo-img">
                    <span class="navbar-title">Sistem Pembimbing Artikel</span>
                </a>
                
                <button class="navbar-toggler" id="navbar-toggler" aria-label="Toggle navigation">
                    <i class="bi bi-list"></i>
                </button>

                <div class="navbar-collapse" id="navbar-collapse">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">Beranda</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('petunjuk') }}" class="nav-link {{ request()->routeIs('petunjuk') ? 'active' : '' }}">Petunjuk</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('tentang') }}" class="nav-link {{ request()->routeIs('tentang') ? 'active' : '' }}">Tentang</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        {{-- Konten halaman (di-inject oleh child view) --}}
        <main>
            @yield('content')
        </main>

        {{-- Footer --}}
        <footer class="footer">
            <div class="footer-container">
                <p>&copy; {{ date('Y') }} Universitas Negeri Medan. Semua Hak Cipta Dilindungi.</p>
            </div>
        </footer>
    </div>

    {{-- Container for Toasts --}}
    <div id="toast-container"></div>

    {{-- Global Scripts --}}
    <script>
        // Hamburger Menu
        document.getElementById('navbar-toggler').addEventListener('click', function() {
            document.getElementById('navbar-collapse').classList.toggle('show');
        });

        // Toast Notification System
        window.showToast = function(message, type = 'success') {
            const container = document.getElementById('toast-container');
            if (!container) return;

            const toast = document.createElement('div');
            toast.className = `toast toast-${type}`;
            
            let iconClass = 'bi-check-circle-fill';
            if (type === 'error') iconClass = 'bi-exclamation-circle-fill';
            if (type === 'warning') iconClass = 'bi-exclamation-triangle-fill';

            toast.innerHTML = `
                <div class="toast-icon"><i class="bi ${iconClass}"></i></div>
                <div class="toast-content">${message}</div>
            `;

            container.appendChild(toast);

            // Animate in
            requestAnimationFrame(() => {
                setTimeout(() => toast.classList.add('show'), 10);
            });

            // Auto remove after 3 seconds
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 400);
            }, 3000);
        };

        // UI Sound Feedback (Web Audio API)
        window.playClickSound = function() {
            try {
                const AudioContext = window.AudioContext || window.webkitAudioContext;
                if (!AudioContext) return;
                const ctx = new AudioContext();
                const osc = ctx.createOscillator();
                const gainNode = ctx.createGain();
                
                osc.type = 'sine';
                osc.frequency.setValueAtTime(600, ctx.currentTime);
                osc.frequency.exponentialRampToValueAtTime(300, ctx.currentTime + 0.1);
                
                gainNode.gain.setValueAtTime(0.1, ctx.currentTime);
                gainNode.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.1);
                
                osc.connect(gainNode);
                gainNode.connect(ctx.destination);
                osc.start();
                osc.stop(ctx.currentTime + 0.1);
            } catch (e) {
                console.error("Audio feedback failed:", e);
            }
        };
    </script>
</body>
</html>
