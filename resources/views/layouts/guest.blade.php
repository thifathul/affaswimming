<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'AFFA Swimming') }}</title>

        <!-- Google Fonts matching welcome page -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        
        <!-- FontAwesome for Premium Icons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Custom Vanilla CSS for Login/Register Pages -->
        <style>
            body {
                font-family: 'Plus Jakarta Sans', sans-serif !important;
                background: #030712 !important;
                color: #f8fafc !important;
                overflow-x: hidden;
                position: relative;
                min-height: 100vh;
            }

            /* Animated Background Glow Blobs */
            .bg-glow-container {
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                width: 100%;
                height: 100%;
                overflow: hidden;
                z-index: 0;
                pointer-events: none;
            }

            .glow-blob-1 {
                position: absolute;
                top: 15%;
                left: 10%;
                width: 350px;
                height: 350px;
                background: radial-gradient(circle, rgba(12, 74, 184, 0.22) 0%, rgba(0, 0, 0, 0) 70%);
                border-radius: 50%;
                filter: blur(40px);
                animation: float-blob-1 12s infinite alternate ease-in-out;
            }

            .glow-blob-2 {
                position: absolute;
                bottom: 15%;
                right: 10%;
                width: 400px;
                height: 400px;
                background: radial-gradient(circle, rgba(212, 175, 55, 0.12) 0%, rgba(0, 0, 0, 0) 70%);
                border-radius: 50%;
                filter: blur(50px);
                animation: float-blob-2 15s infinite alternate ease-in-out;
            }

            @keyframes float-blob-1 {
                0% { transform: translate(0, 0) scale(1); }
                100% { transform: translate(40px, 30px) scale(1.1); }
            }

            @keyframes float-blob-2 {
                0% { transform: translate(0, 0) scale(1); }
                100% { transform: translate(-30px, -40px) scale(1.05); }
            }

            .login-container {
                position: relative;
                z-index: 10;
            }

            /* Custom Premium Card with Fade-in Slide-up Animation */
            .glass-card {
                background: rgba(10, 20, 38, 0.7) !important;
                border: 1.5px solid rgba(212, 175, 55, 0.25) !important;
                backdrop-filter: blur(20px) !important;
                -webkit-backdrop-filter: blur(20px) !important;
                box-shadow: 0 25px 60px rgba(0, 0, 0, 0.75), inset 0 0 30px rgba(212, 175, 55, 0.03) !important;
                border-radius: 1.5rem !important;
                animation: card-appear 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            }

            @keyframes card-appear {
                0% {
                    opacity: 0;
                    transform: translateY(30px);
                }
                100% {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            /* Style input labels */
            label, .text-gray-600, .text-gray-700, span.text-sm {
                color: #e2e8f0 !important;
                font-weight: 500 !important;
                font-size: 0.85rem !important;
                letter-spacing: 0.5px !important;
            }

            /* Custom Input Fields Wrapper with Icons */
            .input-group {
                position: relative;
            }

            .input-group i {
                position: absolute;
                left: 1.25rem;
                top: 50%;
                transform: translateY(-50%);
                color: #64748b;
                transition: color 0.3s ease;
                z-index: 10;
                font-size: 1.1rem;
            }

            /* Target text inputs and select */
            input[type="email"], input[type="password"], input[type="text"], input[type="number"], input[type="date"], select {
                background: rgba(5, 11, 20, 0.8) !important;
                border: 1.5px solid rgba(255, 255, 255, 0.08) !important;
                color: #f8fafc !important;
                border-radius: 12px !important;
                padding-left: 3rem !important;
                padding-right: 1.25rem !important;
                height: 3.5rem !important;
                font-size: 0.95rem !important;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
                box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.3) !important;
                width: 100% !important;
                appearance: none;
                -webkit-appearance: none;
            }

            input[type="date"]::-webkit-calendar-picker-indicator {
                filter: invert(1);
                opacity: 0.5;
                cursor: pointer;
            }

            input[type="email"]:focus, input[type="password"]:focus, input[type="text"]:focus, input[type="number"]:focus, input[type="date"]:focus, select:focus {
                border-color: #D4AF37 !important;
                box-shadow: 0 0 15px rgba(212, 175, 55, 0.25), inset 0 2px 4px rgba(0, 0, 0, 0.2) !important;
                background: rgba(5, 11, 20, 0.95) !important;
            }
            
            select option {
                background-color: #050b14;
                color: #f8fafc;
            }

            /* Icon focus effect */
            .input-group:focus-within i {
                color: #D4AF37 !important;
            }

            /* Primary Gold Button */
            button[type="submit"], button.btn-gold {
                background: linear-gradient(135deg, #ffe8a3 0%, #D4AF37 50%, #aa8411 100%) !important;
                color: #050b14 !important;
                font-weight: 700 !important;
                border: none !important;
                box-shadow: 0 4px 20px rgba(212, 175, 55, 0.25) !important;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
                text-transform: uppercase !important;
                letter-spacing: 1.5px !important;
                cursor: pointer !important;
                border-radius: 12px !important;
                height: 3.5rem !important;
                width: 100% !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                font-size: 0.9rem !important;
            }

            button[type="submit"]:hover, button.btn-gold:hover {
                transform: translateY(-2px) !important;
                box-shadow: 0 8px 25px rgba(212, 175, 55, 0.4) !important;
                filter: brightness(1.05) !important;
            }

            button[type="submit"]:active, button.btn-gold:active {
                transform: translateY(0) !important;
            }

            /* Link customizations */
            a.text-gray-600, a.text-sm, a.underline {
                color: #94a3b8 !important;
                transition: color 0.2s ease;
                text-decoration: none !important;
                font-size: 0.85rem !important;
            }
            a.text-gray-600:hover, a.text-sm:hover, a.underline:hover {
                color: #ffe8a3 !important;
                text-decoration: underline !important;
            }

            /* Logo wrapper */
            .logo-wrapper {
                position: relative;
                padding: 4px;
                border-radius: 50%;
                background: linear-gradient(135deg, rgba(212, 175, 55, 0.5) 0%, rgba(212, 175, 55, 0.1) 100%);
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);
                display: inline-block;
            }
        </style>
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <!-- Background Glowing Blobs -->
        <div class="bg-glow-container">
            <div class="glow-blob-1"></div>
            <div class="glow-blob-2"></div>
        </div>

        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 login-container">
            <div class="mb-4 logo-wrapper">
                <a href="/">
                    <x-application-logo class="w-20 h-20" />
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-8 py-8 glass-card overflow-hidden sm:rounded-2xl">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
