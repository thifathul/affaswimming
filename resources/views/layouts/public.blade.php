<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Affa Swimming</title>

        <!-- Favicon -->
        <link rel="icon" href="{{ asset('affa_logo.jpg') }}" type="image/jpeg">

        <!-- Google Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,600;0,700;1,400&display=swap" rel="stylesheet">

        <!-- FontAwesome Icons for modern aesthetic -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <!-- Custom Vanilla CSS -->
        <style>
            :root {
                --bg-primary: #050b14;
                --bg-secondary: #0c1a30;
                --bg-tertiary: #081222;
                --text-main: #f8fafc;
                --text-muted: #94a3b8;
                --gold-main: #D4AF37;
                --gold-light: #f6e09c;
                --gold-dark: #aa8411;
                --gold-gradient: linear-gradient(135deg, #ffe8a3 0%, #D4AF37 50%, #aa8411 100%);
                --blue-gradient: linear-gradient(135deg, #0c1a30 0%, #050b14 100%);
                --glass-bg: rgba(255, 255, 255, 0.03);
                --glass-border: rgba(255, 255, 255, 0.06);
                --glass-glow: rgba(212, 175, 55, 0.1);
            }

            * {
                box-sizing: border-box;
                margin: 0;
                padding: 0;
                scroll-behavior: smooth;
            }

            body {
                font-family: 'Plus Jakarta Sans', sans-serif;
                background: linear-gradient(135deg, var(--bg-primary) 0%, var(--bg-secondary) 50%, var(--bg-tertiary) 100%);
                color: var(--text-main);
                min-height: 100vh;
                overflow-x: hidden;
                position: relative;
            }

            /* Custom Background Elements for Luxury Depth */
            body::before {
                content: '';
                position: absolute;
                width: 500px;
                height: 500px;
                border-radius: 50%;
                background: radial-gradient(circle, rgba(212, 175, 55, 0.08) 0%, transparent 70%);
                top: -150px;
                left: -150px;
                pointer-events: none;
                z-index: 0;
            }

            body::after {
                content: '';
                position: absolute;
                width: 600px;
                height: 600px;
                border-radius: 50%;
                background: radial-gradient(circle, rgba(12, 26, 48, 0.5) 0%, transparent 75%);
                bottom: -200px;
                right: -200px;
                pointer-events: none;
                z-index: 0;
            }

            .container {
                max-width: 1200px;
                margin: 0 auto;
                padding: 0 2rem;
                position: relative;
                z-index: 10;
            }

            /* NAVIGATION HEADER */
            header {
                padding: 1.5rem 0;
                border-bottom: 1px solid var(--glass-border);
                background: rgba(5, 11, 20, 0.7);
                backdrop-filter: blur(12px);
                -webkit-backdrop-filter: blur(12px);
                position: sticky;
                top: 0;
                z-index: 100;
                transition: all 0.3s ease;
            }

            .navbar {
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .brand {
                display: flex;
                align-items: center;
                gap: 0.75rem;
                text-decoration: none;
            }

            .brand-logo {
                width: 46px;
                height: 46px;
                border-radius: 50%;
                overflow: hidden;
                display: flex;
                align-items: center;
                justify-content: center;
                box-shadow: 0 0 15px rgba(212, 175, 55, 0.3);
                transition: all 0.5s ease;
                border: 1.5px solid var(--gold-main);
                background: #050b14;
            }

            .brand-logo img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            .brand:hover .brand-logo {
                transform: rotate(360deg);
                box-shadow: 0 0 25px rgba(212, 175, 55, 0.5);
            }

            .brand-logo i {
                color: #050b14;
                font-size: 1.25rem;
            }

            .brand-text {
                font-family: 'Playfair Display', serif;
                font-size: 1.5rem;
                font-weight: 700;
                letter-spacing: 1px;
                background: var(--gold-gradient);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                text-transform: uppercase;
                text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
            }

            .nav-actions {
                display: flex;
                align-items: center;
                gap: 1.5rem;
            }

            .nav-menu {
                display: flex;
                gap: 2rem;
                list-style: none;
            }

            .nav-link {
                color: var(--text-muted);
                text-decoration: none;
                font-size: 0.95rem;
                font-weight: 500;
                transition: all 0.3s ease;
                position: relative;
                padding: 0.5rem 0;
            }

            .nav-link:hover, .nav-link.active {
                color: var(--text-main);
            }

            .nav-link::after {
                content: '';
                position: absolute;
                bottom: 0;
                left: 0;
                width: 0;
                height: 2px;
                background: var(--gold-gradient);
                transition: width 0.3s ease;
            }

            .nav-link:hover::after, .nav-link.active::after {
                width: 100%;
            }

            /* Premium Authentication Buttons */
            .auth-buttons {
                display: flex;
                align-items: center;
                gap: 1rem;
            }

            .btn-login {
                padding: 0.6rem 1.5rem;
                color: var(--text-main);
                text-decoration: none;
                font-size: 0.9rem;
                font-weight: 600;
                border: 1px solid var(--glass-border);
                border-radius: 8px;
                background: var(--glass-bg);
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
            }

            .btn-login:hover {
                background: rgba(255, 255, 255, 0.08);
                border-color: var(--text-muted);
                transform: translateY(-2px);
            }

            .btn-register {
                padding: 0.6rem 1.5rem;
                color: #050b14;
                text-decoration: none;
                font-size: 0.9rem;
                font-weight: 700;
                border-radius: 8px;
                background: var(--gold-gradient);
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                box-shadow: 0 4px 15px rgba(212, 175, 55, 0.2);
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
            }

            .btn-register:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(212, 175, 55, 0.4);
                filter: brightness(1.05);
            }

            /* HERO SECTION */
            .hero-section {
                padding: 8rem 0 10rem;
                display: flex;
                align-items: center;
                min-height: calc(100vh - 120px);
                position: relative;
            }

            .hero-grid {
                display: grid;
                grid-template-columns: 1.2fr 1fr;
                gap: 4rem;
                align-items: center;
            }

            /* Hero Text Info */
            .hero-content {
                display: flex;
                flex-direction: column;
                gap: 1.75rem;
            }

            .badge {
                align-self: flex-start;
                padding: 0.5rem 1.2rem;
                background: var(--glass-bg);
                border: 1px solid rgba(212, 175, 55, 0.2);
                border-radius: 100px;
                color: var(--gold-light);
                font-size: 0.85rem;
                font-weight: 600;
                letter-spacing: 1.5px;
                text-transform: uppercase;
                display: flex;
                align-items: center;
                gap: 0.5rem;
                box-shadow: inset 0 0 10px rgba(212, 175, 55, 0.05);
            }

            .badge i {
                color: var(--gold-main);
                animation: pulse 2s infinite;
            }

            .hero-title {
                font-family: 'Playfair Display', serif;
                font-size: 3.5rem;
                font-weight: 700;
                line-height: 1.15;
                color: var(--text-main);
            }

            .hero-title span {
                background: var(--gold-gradient);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                display: block;
                margin-top: 0.5rem;
            }

            .hero-desc {
                font-size: 1.1rem;
                color: var(--text-muted);
                line-height: 1.7;
            }

            .hero-cta {
                display: flex;
                align-items: center;
                gap: 1.5rem;
                margin-top: 1rem;
            }

            .btn-primary {
                padding: 1rem 2.2rem;
                background: var(--gold-gradient);
                color: #050b14;
                text-decoration: none;
                font-weight: 700;
                font-size: 1rem;
                border-radius: 10px;
                box-shadow: 0 10px 25px rgba(212, 175, 55, 0.25);
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                display: inline-flex;
                align-items: center;
                gap: 0.75rem;
            }

            .btn-primary:hover {
                transform: translateY(-3px);
                box-shadow: 0 15px 30px rgba(212, 175, 55, 0.4);
                filter: brightness(1.05);
            }

            .btn-secondary {
                padding: 1rem 2.2rem;
                background: transparent;
                color: var(--text-main);
                text-decoration: none;
                font-weight: 600;
                font-size: 1rem;
                border-radius: 10px;
                border: 1px solid var(--glass-border);
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                display: inline-flex;
                align-items: center;
                gap: 0.75rem;
            }

            .btn-secondary:hover {
                background: var(--glass-bg);
                border-color: var(--gold-main);
                color: var(--gold-light);
                transform: translateY(-3px);
            }

            /* Stats Counter Row */
            .stats-container {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 2rem;
                margin-top: 2rem;
                padding-top: 2rem;
                border-top: 1px solid var(--glass-border);
            }

            .stat-card {
                display: flex;
                flex-direction: column;
                gap: 0.25rem;
            }

            .stat-number {
                font-size: 2.25rem;
                font-weight: 700;
                font-family: 'Playfair Display', serif;
                background: var(--gold-gradient);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
            }

            .stat-label {
                font-size: 0.85rem;
                color: var(--text-muted);
                text-transform: uppercase;
                letter-spacing: 1px;
            }

            /* Hero Image Showcase Area */
            .hero-media {
                position: relative;
                display: flex;
                justify-content: center;
                align-items: center;
            }

            .image-wrapper {
                width: 100%;
                max-width: 440px;
                border-radius: 24px;
                overflow: hidden;
                border: 1px solid var(--glass-border);
                background: var(--glass-bg);
                padding: 0.75rem;
                backdrop-filter: blur(10px);
                -webkit-backdrop-filter: blur(10px);
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5), 0 0 40px var(--glass-glow);
                position: relative;
                z-index: 5;
                animation: float 6s ease-in-out infinite;
            }

            .image-wrapper img {
                width: 100%;
                height: auto;
                border-radius: 16px;
                display: block;
                object-fit: cover;
                filter: brightness(0.9) contrast(1.05);
                transition: all 0.5s ease;
            }

            .image-wrapper:hover img {
                filter: brightness(1) contrast(1.05);
                transform: scale(1.02);
            }

            /* Background Glowing Aura */
            .media-glow {
                position: absolute;
                width: 80%;
                height: 80%;
                background: radial-gradient(circle, rgba(212, 175, 55, 0.15) 0%, transparent 60%);
                filter: blur(40px);
                z-index: 1;
                pointer-events: none;
            }

            /* Animated Floating Water Waves Background SVG */
            .waves-container {
                position: absolute;
                bottom: 0;
                left: 0;
                width: 100%;
                line-height: 0;
                z-index: 1;
            }

            .waves-svg {
                position: relative;
                width: 100%;
                height: 120px;
                margin-bottom: -1px;
                min-height: 100px;
                max-height: 150px;
            }

            /* ANIMATIONS */
            @keyframes float {
                0% {
                    transform: translateY(0px);
                }
                50% {
                    transform: translateY(-15px);
                }
                100% {
                    transform: translateY(0px);
                }
            }

            @keyframes pulse {
                0% {
                    transform: scale(0.95);
                    opacity: 0.5;
                }
                50% {
                    transform: scale(1.1);
                    opacity: 1;
                }
                100% {
                    transform: scale(0.95);
                    opacity: 0.5;
                }
            }

            /* RESPONSIVE LAYOUTS */
            @media (max-width: 991px) {
                .hero-grid {
                    grid-template-columns: 1fr;
                    gap: 3rem;
                    text-align: center;
                }

                .badge {
                    align-self: center;
                }

                .hero-cta {
                    justify-content: center;
                }

                .image-wrapper {
                    max-width: 380px;
                }

                .stats-container {
                    justify-content: center;
                }
            }

            @media (max-width: 768px) {
                .navbar {
                    flex-direction: column;
                    gap: 1.5rem;
                }

                .nav-actions {
                    flex-direction: column;
                    width: 100%;
                    gap: 1rem;
                }

                .nav-menu {
                    justify-content: center;
                    width: 100%;
                }

                .auth-buttons {
                    width: 100%;
                    justify-content: center;
                }

                .hero-title {
                    font-size: 2.75rem;
                }

                .hero-section {
                    padding: 3rem 0;
                }
            }

            /* TEAM / ABOUT SECTION */
            .team-section {
                padding: 6rem 0;
                background: linear-gradient(180deg, var(--bg-tertiary) 0%, var(--bg-primary) 100%);
                position: relative;
                z-index: 10;
                border-top: 1px solid rgba(255, 255, 255, 0.02);
            }

            .team-grid {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 2rem;
                margin-top: 4rem;
                max-width: 1000px;
                margin-left: auto;
                margin-right: auto;
            }

            .team-card {
                position: relative;
                aspect-ratio: 9/16;
                display: flex;
                align-items: flex-end;
                justify-content: center;
                overflow: hidden;
            }

            .team-card:hover .team-avatar {
                transform: scale(1.05);
            }

            .team-avatar {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                object-fit: cover;
                z-index: 1;
                transition: transform 0.5s ease;
            }
            
            .team-avatar-placeholder {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(12, 26, 48, 0.8);
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 5rem;
                color: rgba(255, 255, 255, 0.2);
                z-index: 1;
            }

            .team-info {
                position: relative;
                z-index: 2;
                background: #092626; /* Dark teal color matching screenshot */
                width: 88%;
                margin-bottom: 1.5rem;
                padding: 1.25rem 1rem;
                border-radius: 8px;
                text-align: center;
                box-shadow: 0 10px 20px rgba(0, 0, 0, 0.4);
                transition: background 0.3s ease;
            }

            .team-info h4 {
                color: #ffffff;
                font-size: 1.25rem;
                font-weight: 700;
                margin-bottom: 0.25rem;
            }

            .team-info p.position {
                color: #61898b; /* Muted teal for position */
                font-size: 0.95rem;
                font-weight: 500;
                margin: 0;
            }

            @media (max-width: 991px) {
                .team-grid {
                    grid-template-columns: repeat(2, 1fr);
                }
            }
            
            @media (max-width: 576px) {
                .team-grid {
                    grid-template-columns: 1fr;
                    max-width: 340px;
                }
            }

            /* NEWS & ANNOUNCEMENT SECTION */
            .news-section {
                background: linear-gradient(180deg, var(--bg-primary) 0%, var(--bg-tertiary) 50%, var(--bg-secondary) 100%);
                padding: 6rem 0;
                position: relative;
                z-index: 10;
                border-top: 1px solid rgba(255, 255, 255, 0.02);
            }

            .section-header {
                text-align: center;
                margin-bottom: 4rem;
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 1rem;
            }

            .section-title {
                font-family: 'Playfair Display', serif;
                font-size: 2.75rem;
                font-weight: 700;
                color: var(--text-main);
            }

            .section-title span {
                background: var(--gold-gradient);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
            }

            .section-divider {
                width: 80px;
                height: 3px;
                background: var(--gold-gradient);
                border-radius: 10px;
                margin-top: 0.5rem;
            }

            .news-grid {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 2.5rem;
            }

            .news-card {
                background: rgba(12, 26, 48, 0.45);
                border: 1px solid rgba(255, 255, 255, 0.05);
                border-radius: 20px;
                overflow: hidden;
                backdrop-filter: blur(12px);
                -webkit-backdrop-filter: blur(12px);
                box-shadow: 0 15px 35px rgba(0, 0, 0, 0.4);
                transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
                display: flex;
                flex-direction: column;
                height: 100%;
            }

            .news-card:hover {
                transform: translateY(-8px);
                border-color: rgba(212, 175, 55, 0.3);
                box-shadow: 0 20px 45px rgba(0, 0, 0, 0.6), 0 0 20px rgba(212, 175, 55, 0.1);
            }

            .news-image-wrapper {
                position: relative;
                width: 100%;
                height: 220px;
                overflow: hidden;
                border-bottom: 1.5px solid rgba(212, 175, 55, 0.15);
            }

            .news-image {
                width: 100%;
                height: 100%;
                object-fit: cover;
                transition: transform 0.5s ease;
                filter: brightness(0.85);
            }

            .news-card:hover .news-image {
                transform: scale(1.06);
                filter: brightness(0.95);
            }

            .category-badge {
                position: absolute;
                top: 1rem;
                left: 1rem;
                padding: 0.4rem 1rem;
                border-radius: 100px;
                font-size: 0.75rem;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 1px;
                backdrop-filter: blur(8px);
                -webkit-backdrop-filter: blur(8px);
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
                z-index: 10;
            }

            .badge-prestasi {
                background: var(--gold-gradient);
                color: #050b14;
                border: 1px solid rgba(255, 255, 255, 0.2);
            }

            .badge-pengumuman {
                background: rgba(12, 74, 184, 0.85);
                color: #f8fafc;
                border: 1px solid rgba(255, 255, 255, 0.15);
            }

            .badge-tips {
                background: rgba(13, 148, 136, 0.85);
                color: #f8fafc;
                border: 1px solid rgba(255, 255, 255, 0.15);
            }

            .news-content {
                padding: 1.75rem;
                display: flex;
                flex-direction: column;
                gap: 1rem;
                flex-grow: 1;
            }

            .news-date {
                font-size: 0.8rem;
                color: var(--text-muted);
                display: flex;
                align-items: center;
                gap: 0.5rem;
            }

            .news-card-title {
                font-family: 'Plus Jakarta Sans', sans-serif;
                font-size: 1.25rem;
                font-weight: 700;
                line-height: 1.4;
                color: var(--text-main);
                transition: color 0.3s ease;
            }

            .news-card:hover .news-card-title {
                color: var(--gold-light);
            }

            .news-excerpt {
                font-size: 0.9rem;
                color: var(--text-muted);
                line-height: 1.6;
            }

            .news-footer {
                padding: 0 1.75rem 1.75rem;
                margin-top: auto;
            }

            .btn-readmore {
                color: var(--gold-main);
                text-decoration: none;
                font-size: 0.9rem;
                font-weight: 700;
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                transition: all 0.3s ease;
            }

            .btn-readmore i {
                font-size: 0.8rem;
                transition: transform 0.3s ease;
            }

            .news-card:hover .btn-readmore {
                color: var(--gold-light);
            }

            .news-card:hover .btn-readmore i {
                transform: translateX(5px);
            }

            /* RESPONSIVE NEWS GRID */
            @media (max-width: 991px) {
                .news-grid {
                    grid-template-columns: repeat(2, 1fr);
                    gap: 2rem;
                }
                .section-title {
                    font-size: 2.35rem;
                }
            }

            @media (max-width: 768px) {
                .news-grid {
                    grid-template-columns: 1fr;
                    gap: 2rem;
                }
                .news-section {
                    padding: 4rem 0;
                }
            }
        </style>
    </head>
    <body>

        <!-- Navigation Header -->
        <header>
            <div class="container navbar">
                <a href="#" class="brand">
                    <div class="brand-logo">
                        <img src="{{ asset('affa_logo.jpg') }}" alt="AFFA Swimming Logo" />
                    </div>
                    <span class="brand-text">AFFA Swimming</span>
                </a>

                <div class="nav-actions">
                    <ul class="nav-menu">
                        <li><a href="{{ url('/') }}" class="nav-link {{ request()->is('/') ? 'active' : '' }}">Beranda</a></li>
                        <li><a href="{{ url('/#program') }}" class="nav-link">Program</a></li>
                        <li><a href="{{ url('/tentang-kami') }}" class="nav-link {{ request()->is('tentang-kami') ? 'active' : '' }}">Tentang Kami</a></li>
                        <li><a href="{{ url('/kontak') }}" class="nav-link {{ request()->is('kontak') ? 'active' : '' }}">Kontak</a></li>
                        @if (Route::has('login'))
                            @auth
                                <li><a href="{{ url('/dashboard') }}" class="nav-link">Dashboard</a></li>
                            @else
                                <li><a href="{{ route('login') }}" class="nav-link">Login</a></li>
                            @endauth
                        @else
                            <li><a href="/login" class="nav-link">Login</a></li>
                        @endif
                    </ul>

                    <div class="auth-buttons">
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}" class="btn-register">
                                    <i class="fa-solid fa-gauge"></i> Dashboard
                                </a>
                            @else
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="btn-register">
                                        <i class="fa-solid fa-user-plus"></i> Gabung Club
                                    </a>
                                @endif
                            @endauth
                        @else
                            <a href="/register" class="btn-register">
                                <i class="fa-solid fa-user-plus"></i> Gabung Club
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content Yield -->
        <main>
            @yield('content')
        </main>

        <!-- Floating Water Wave Divider at the bottom for modern look -->
        <div class="waves-container">
            <svg class="waves-svg" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 24 150 28" preserveAspectRatio="none" shape-rendering="auto">
                <defs>
                    <path id="gentle-wave" d="M-160 44c30 0 58-18 88-18s58 18 88 18 58-18 88-18 58 18 88 18v44h-352z" />
                </defs>
                <g class="parallax">
                    <use xlink:href="#gentle-wave" x="48" y="0" fill="rgba(12, 26, 48, 0.3)" />
                    <use xlink:href="#gentle-wave" x="48" y="3" fill="rgba(212, 175, 55, 0.05)" />
                    <use xlink:href="#gentle-wave" x="48" y="5" fill="rgba(8, 18, 34, 0.6)" />
                    <use xlink:href="#gentle-wave" x="48" y="7" fill="var(--bg-primary)" />
                </g>
            </svg>
        </div>

    </body>
</html>
