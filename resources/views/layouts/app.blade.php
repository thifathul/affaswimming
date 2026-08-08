<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'AFFA Swimming') }}</title>

        <!-- Favicon -->
        <link rel="icon" href="{{ asset('affa_logo.jpg') }}" type="image/jpeg">

        <!-- Google Fonts matching welcome page -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        
        <!-- FontAwesome for Dashboard Icons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Custom Light Theme Dashboard CSS -->
        <style>
            body {
                font-family: 'Plus Jakarta Sans', sans-serif !important;
                background: #f8fafc !important;
                color: #334155 !important;
            }

            .min-h-screen {
                background: #f8fafc !important;
            }

            /* Custom Nav Override */
            nav {
                background-color: #ffffff !important;
                border-bottom: 1px solid #e2e8f0 !important;
                backdrop-filter: none !important;
                -webkit-backdrop-filter: none !important;
                z-index: 50 !important;
            }

            /* Dropdown and buttons in Nav */
            nav button, nav .inline-flex {
                color: #475569 !important;
            }
            nav button:hover {
                color: #2563eb !important;
            }

            /* Specific style override for navbar buttons & dropdown triggers */
            nav button {
                background-color: transparent !important;
                border: none !important;
                box-shadow: none !important;
            }
            nav button:hover {
                background-color: rgba(0, 0, 0, 0.02) !important;
            }

            /* Active link in nav */
            nav .inline-flex.items-center {
                border-bottom: 2px solid transparent !important; /* By default transparent */
            }
            nav .inline-flex.items-center[class*="text-gray-900"] {
                border-bottom: 2px solid #2563eb !important;
                color: #2563eb !important;
            }

            /* Dropdown card content */
            div[class*="origin-top-right"] > div, .origin-top-right div {
                background-color: #ffffff !important;
                border: 1px solid #e2e8f0 !important;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important;
            }

            div[class*="origin-top-right"] a, .origin-top-right a {
                color: #475569 !important;
                background-color: transparent !important;
            }
            div[class*="origin-top-right"] a:hover, .origin-top-right a:hover {
                color: #2563eb !important;
                background-color: #f1f5f9 !important;
            }

            /* Page header */
            header.bg-white, .bg-white.shadow {
                background: #ffffff !important;
                border-bottom: 1px solid #e2e8f0 !important;
                box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05) !important;
            }

            h2, h3 {
                color: #0f172a !important;
            }

            /* General Cards */
            .glass-card, .bg-white.overflow-hidden.shadow-sm {
                background: #ffffff !important;
                border: 1px solid #e2e8f0 !important;
                backdrop-filter: none !important;
                -webkit-backdrop-filter: none !important;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.02) !important;
                border-radius: 1rem !important;
            }

            .text-gray-900 {
                color: #1e293b !important;
            }

            .text-gray-500, .text-gray-600 {
                color: #64748b !important;
            }

            /* Dashboard Info Badges/Items */
            .info-badge {
                background: #eff6ff !important;
                border: 1px solid #bfdbfe !important;
                color: #2563eb !important;
                border-radius: 9999px !important;
                padding: 0.25rem 0.75rem !important;
                font-size: 0.85rem !important;
                display: inline-block !important;
            }

            .action-btn-gold {
                background: #2563eb !important;
                color: #ffffff !important;
                font-weight: 700 !important;
                border: none !important;
                box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2) !important;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
                border-radius: 8px !important;
                padding: 0.5rem 1.25rem !important;
                display: inline-flex !important;
                align-items: center !important;
                gap: 0.5rem !important;
                text-transform: uppercase !important;
                font-size: 0.85rem !important;
                letter-spacing: 0.5px !important;
                text-decoration: none !important;
            }

            .action-btn-gold:hover {
                background: #1d4ed8 !important;
                transform: translateY(-1.5px) !important;
                box-shadow: 0 6px 16px rgba(37, 99, 235, 0.3) !important;
            }

            /* Fallback colors for uncompiled tailwind classes */
            .bg-blue-600 { background-color: #2563eb !important; }
            .hover\:bg-blue-700:hover { background-color: #1d4ed8 !important; }
            .bg-emerald-600 { background-color: #059669 !important; }
            .hover\:bg-emerald-700:hover { background-color: #047857 !important; }
            .bg-emerald-100 { background-color: #d1fae5 !important; }
            .text-emerald-700 { color: #047857 !important; }
            .bg-red-600 { background-color: #dc2626 !important; }
            .hover\:bg-red-700:hover { background-color: #b91c1c !important; }
            .bg-red-100 { background-color: #fee2e2 !important; }
            .text-red-700 { color: #b91c1c !important; }
            .bg-slate-800 { background-color: #1e293b !important; }
            .hover\:bg-slate-900:hover { background-color: #0f172a !important; }
            .text-white { color: #ffffff !important; }

            /* --- SIDEBAR STYLING --- */
            .sidebar {
                width: 260px;
                background: #ffffff !important;
                border-right: 1px solid #e2e8f0 !important;
                backdrop-filter: none;
                -webkit-backdrop-filter: none;
                transition: all 0.3s ease;
                display: flex;
                flex-direction: column;
                z-index: 40;
                min-height: calc(100vh - 64px);
            }

            .sidebar-menu {
                padding: 1.5rem 1rem;
                display: flex;
                flex-direction: column;
                gap: 0.5rem;
                flex: 1;
            }

            .sidebar-link {
                display: flex;
                align-items: center;
                gap: 0.75rem;
                padding: 0.75rem 1rem;
                border-radius: 8px;
                color: #475569 !important;
                font-size: 0.9rem;
                font-weight: 500;
                transition: all 0.2s ease;
                text-decoration: none !important;
            }

            .sidebar-link i {
                font-size: 1.1rem;
                width: 20px;
                text-align: center;
                color: #64748b;
            }

            .sidebar-link:hover, .sidebar-link.active {
                color: #2563eb !important;
                background: #eff6ff !important;
                border-left: 3px solid #2563eb !important;
            }
            
            .sidebar-link:hover i, .sidebar-link.active i {
                color: #2563eb !important;
            }

            .main-content {
                flex: 1;
                display: flex;
                flex-direction: column;
                min-width: 0;
            }
        </style>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen flex flex-col" x-data="{ sidebarOpen: window.innerWidth >= 768 }" @resize.window="sidebarOpen = window.innerWidth >= 768" @toggle-sidebar.window="sidebarOpen = !sidebarOpen">
            <!-- Top Navbar (User settings dropdown, etc.) -->
            @include('layouts.navigation')

            <!-- Main Layout with Sidebar + Content -->
            <div class="flex-1 flex flex-row min-h-0 relative">
                <!-- Left Sidebar -->
                <aside class="sidebar shrink-0" x-show="sidebarOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-x-full" x-transition:enter-end="opacity-100 translate-x-0" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 -translate-x-full">
                    <div class="sidebar-menu">
                        <div class="text-[10px] text-gray-500 uppercase tracking-widest font-semibold px-4 mb-2">Menu Utama</div>
                        
                        @if(auth()->user()->role === 'master')
                            <a href="{{ route('master.dashboard') }}" class="sidebar-link {{ request()->routeIs('master.dashboard') ? 'active' : '' }}">
                                <i class="fa-solid fa-chart-line"></i> Dashboard
                            </a>
                            <a href="{{ route('master.positions.index') }}" class="sidebar-link {{ request()->routeIs('master.positions*') ? 'active' : '' }}">
                                <i class="fa-solid fa-briefcase"></i> Manajemen Jabatan
                            </a>
                            <a href="{{ route('master.users') }}" class="sidebar-link {{ request()->routeIs('master.users*') ? 'active' : '' }}">
                                <i class="fa-solid fa-users-gear"></i> Manajemen Pengguna
                            </a>
                            <a href="{{ route('master.teams.index') }}" class="sidebar-link {{ request()->routeIs('master.teams*') ? 'active' : '' }}">
                                <i class="fa-solid fa-people-group"></i> Manajemen Tim
                            </a>
                            <a href="{{ route('master.swim-classes.index') }}" class="sidebar-link {{ request()->routeIs('master.swim-classes*') ? 'active' : '' }}">
                                <i class="fa-solid fa-water"></i> Manajemen Kelas
                            </a>
                            <a href="{{ route('master.students.index') }}" class="sidebar-link {{ request()->routeIs('master.students*') ? 'active' : '' }}">
                                <i class="fa-solid fa-graduation-cap"></i> Manajemen Murid
                            </a>
                            <a href="{{ route('admin.pool-locations.index') }}" class="sidebar-link {{ request()->routeIs('admin.pool-locations*') ? 'active' : '' }}">
                                <i class="fa-solid fa-water-ladder"></i> Master Paket & Kolam
                            </a>
                            <a href="{{ route('admin.schedules.index') }}" class="sidebar-link {{ request()->routeIs('admin.schedules.index') || request()->routeIs('admin.schedules.showDay') ? 'active' : '' }}">
                                <i class="fa-solid fa-calendar-days"></i> Manajemen Jadwal
                            </a>
                            <a href="{{ route('admin.schedules.locations') }}" class="sidebar-link {{ request()->routeIs('admin.schedules.locations') ? 'active' : '' }}">
                                <i class="fa-solid fa-map-location-dot"></i> Jadwal Lokasi
                            </a>
                            <a href="{{ route('master.articles.index') }}" class="sidebar-link {{ request()->routeIs('master.articles*') ? 'active' : '' }}">
                                <i class="fa-solid fa-newspaper"></i> Manajemen Artikel
                            </a>
                            <div class="px-4 py-2 mt-2 text-xs font-semibold text-slate-400 uppercase tracking-wider">
                                Operasional
                            </div>
                            <a href="{{ route('admin.trials.index') }}" class="sidebar-link {{ request()->routeIs('admin.trials.*') ? 'active' : '' }}">
                                <i class="fa-solid fa-person-swimming"></i> Trial Renang
                            </a>
                            <a href="{{ route('admin.operations.recap') }}" class="sidebar-link {{ request()->routeIs('admin.operations.recap') ? 'active' : '' }}">
                                <i class="fa-solid fa-list-check"></i> Rekap Kehadiran
                            </a>
                            <a href="{{ route('master.schedule-deletions.index') }}" class="sidebar-link {{ request()->routeIs('master.schedule-deletions.*') ? 'active' : '' }}">
                                <i class="fa-solid fa-calendar-xmark"></i> Hapus Jadwal (Req)
                            </a>
                            <div class="px-4 py-2 mt-2 text-xs font-semibold text-slate-400 uppercase tracking-wider">
                                Keuangan
                            </div>
                            <a href="{{ route('finance.payments.index') }}" class="sidebar-link {{ request()->routeIs('finance.payments.index') ? 'active' : '' }}">
                                <i class="fa-solid fa-file-invoice-dollar"></i> Pembayaran Paket
                            </a>
                            <a href="{{ route('admin.wallets.index') }}" class="sidebar-link {{ request()->routeIs('admin.wallets.*') ? 'active' : '' }}">
                                <i class="fa-solid fa-wallet"></i> E-Wallet
                            </a>
                            <a href="{{ route('finance.unpaid.index') }}" class="sidebar-link {{ request()->routeIs('finance.unpaid.index') ? 'active' : '' }}">
                                <i class="fa-solid fa-triangle-exclamation"></i> Daftar Unpaid
                            </a>
                            <a href="{{ route('finance.incomes.index') }}" class="sidebar-link {{ request()->routeIs('finance.incomes.*') ? 'active' : '' }}">
                                <i class="fa-solid fa-arrow-trend-up"></i> Pemasukan
                            </a>
                            <a href="{{ route('finance.expenses.index') }}" class="sidebar-link {{ request()->routeIs('finance.expenses.*') ? 'active' : '' }}">
                                <i class="fa-solid fa-arrow-trend-down"></i> Pengeluaran
                            </a>
                        @elseif(auth()->user()->role === 'admin')
                            <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                                <i class="fa-solid fa-table-columns"></i> Dashboard
                            </a>
                            <a href="{{ route('master.users') }}" class="sidebar-link {{ request()->routeIs('master.users*') ? 'active' : '' }}">
                                <i class="fa-solid fa-users-gear"></i> Manajemen Pengguna
                            </a>
                            <a href="{{ route('master.teams.index') }}" class="sidebar-link {{ request()->routeIs('master.teams*') ? 'active' : '' }}">
                                <i class="fa-solid fa-people-group"></i> Manajemen Tim
                            </a>
                            <a href="{{ route('master.articles.index') }}" class="sidebar-link {{ request()->routeIs('master.articles*') ? 'active' : '' }}">
                                <i class="fa-solid fa-newspaper"></i> Manajemen Artikel
                            </a>
                            <a href="{{ route('master.students.index') }}" class="sidebar-link {{ request()->routeIs('master.students*') ? 'active' : '' }}">
                                <i class="fa-solid fa-graduation-cap"></i> Manajemen Murid
                            </a>
                            <a href="{{ route('master.swim-classes.index') }}" class="sidebar-link {{ request()->routeIs('master.swim-classes*') ? 'active' : '' }}">
                                <i class="fa-solid fa-water"></i> Manajemen Kelas
                            </a>
                            <a href="{{ route('admin.pool-locations.index') }}" class="sidebar-link {{ request()->routeIs('admin.pool-locations*') ? 'active' : '' }}">
                                <i class="fa-solid fa-location-dot"></i> Master Paket
                            </a>
                            <a href="{{ route('admin.schedules.index') }}" class="sidebar-link {{ request()->routeIs('admin.schedules.index') || request()->routeIs('admin.schedules.showDay') ? 'active' : '' }}">
                                <i class="fa-regular fa-calendar-days"></i> Manajemen Jadwal
                            </a>
                            <a href="{{ route('admin.schedules.locations') }}" class="sidebar-link {{ request()->routeIs('admin.schedules.locations') ? 'active' : '' }}">
                                <i class="fa-solid fa-map-location-dot"></i> Jadwal Lokasi
                            </a>
                            <div class="px-4 py-2 mt-2 text-xs font-semibold text-slate-400 uppercase tracking-wider">
                                Operasional
                            </div>
                            <a href="{{ route('admin.trials.index') }}" class="sidebar-link {{ request()->routeIs('admin.trials.*') ? 'active' : '' }}">
                                <i class="fa-solid fa-person-swimming"></i> Trial Renang
                            </a>
                            <a href="{{ route('admin.operations.recap') }}" class="sidebar-link {{ request()->routeIs('admin.operations.recap') ? 'active' : '' }}">
                                <i class="fa-solid fa-list-check"></i> Rekap Kehadiran
                            </a>
                            <a href="{{ route('admin.operations.approvals') }}" class="sidebar-link {{ request()->routeIs('admin.operations.approvals') || request()->routeIs('admin.operations.updateApproval') ? 'active' : '' }}">
                                <i class="fa-solid fa-calendar-check"></i> Approval Reschedule
                            </a>
                            <a href="{{ route('admin.report-cards.index') }}" class="sidebar-link {{ request()->routeIs('admin.report-cards*') ? 'active' : '' }}">
                                <i class="fa-solid fa-clipboard-list"></i> Manajemen Raport
                            </a>
                            <div class="px-4 py-2 mt-2 text-xs font-semibold text-slate-400 uppercase tracking-wider">
                                Keuangan
                            </div>
                            <a href="{{ route('finance.payments.index') }}" class="sidebar-link {{ request()->routeIs('finance.payments.index') ? 'active' : '' }}">
                                <i class="fa-solid fa-file-invoice-dollar"></i> Pembayaran Paket
                            </a>
                            <a href="{{ route('admin.wallets.index') }}" class="sidebar-link {{ request()->routeIs('admin.wallets.*') ? 'active' : '' }}">
                                <i class="fa-solid fa-wallet"></i> E-Wallet
                            </a>
                            <a href="{{ route('finance.unpaid.index') }}" class="sidebar-link {{ request()->routeIs('finance.unpaid.index') ? 'active' : '' }}">
                                <i class="fa-solid fa-triangle-exclamation"></i> Daftar Unpaid
                            </a>
                            <a href="{{ route('finance.incomes.index') }}" class="sidebar-link {{ request()->routeIs('finance.incomes.*') ? 'active' : '' }}">
                                <i class="fa-solid fa-arrow-trend-up"></i> Pemasukan
                            </a>
                            <a href="{{ route('finance.expenses.index') }}" class="sidebar-link {{ request()->routeIs('finance.expenses.*') ? 'active' : '' }}">
                                <i class="fa-solid fa-arrow-trend-down"></i> Pengeluaran
                            </a>
                        @elseif(auth()->user()->role === 'pelatih')
                            <a href="{{ route('pelatih.dashboard') }}" class="sidebar-link {{ request()->routeIs('pelatih.dashboard') ? 'active' : '' }}">
                                <i class="fa-solid fa-stopwatch"></i> Dashboard
                            </a>
                            <a href="{{ route('pelatih.schedules.index') }}" class="sidebar-link {{ request()->routeIs('pelatih.schedules.index') ? 'active' : '' }}">
                                <i class="fa-regular fa-calendar-check"></i> Jadwal Saya
                            </a>
                            <a href="{{ route('pelatih.all-schedules.index') }}" class="sidebar-link {{ request()->routeIs('pelatih.all-schedules.index') ? 'active' : '' }}">
                                <i class="fa-solid fa-users"></i> Jadwal Pelatih Lain
                            </a>
                            <a href="{{ route('pelatih.trials.index') }}" class="sidebar-link {{ request()->routeIs('pelatih.trials.*') ? 'active' : '' }}">
                                <i class="fa-solid fa-person-swimming"></i> Jadwal Trial
                            </a>
                            <a href="{{ route('pelatih.reports.index') }}" class="sidebar-link {{ request()->routeIs('pelatih.reports.index') ? 'active' : '' }}">
                                <i class="fa-solid fa-list-check"></i> Daftar Laporan
                            </a>
                            <a href="{{ route('pelatih.requests.index') }}" class="sidebar-link {{ request()->routeIs('pelatih.requests.index') ? 'active' : '' }}">
                                <i class="fa-solid fa-clock-rotate-left"></i> Status Pengajuan
                            </a>

                            <a href="{{ route('pelatih.payroll.history') }}" class="sidebar-link {{ request()->routeIs('pelatih.payroll.history') ? 'active' : '' }}">
                                <i class="fa-solid fa-money-check-dollar"></i> Riwayat Penggajian
                            </a>
                        @elseif(auth()->user()->role === 'murid')
                            <div class="px-4 py-2 mt-2 text-xs font-semibold text-slate-400 uppercase tracking-wider">
                                Akademik
                            </div>
                            <a href="{{ route('murid.dashboard') }}" class="sidebar-link {{ request()->routeIs('murid.dashboard') ? 'active' : '' }}">
                                <i class="fa-solid fa-house-user"></i> Dashboard & Jadwal
                            </a>
                            <a href="{{ route('student.reports.index') }}" class="sidebar-link {{ request()->routeIs('student.reports.index') ? 'active' : '' }}">
                                <i class="fa-solid fa-clipboard-list"></i> Laporan Latihan
                            </a>

                            <div class="px-4 py-2 mt-2 text-xs font-semibold text-slate-400 uppercase tracking-wider">
                                Pembayaran
                            </div>
                            <a href="{{ route('student.payments.create') }}" class="sidebar-link {{ request()->routeIs('student.payments.create') ? 'active' : '' }}">
                                <i class="fa-solid fa-file-invoice-dollar"></i> Beli Paket (Top-up)
                            </a>
                            <a href="{{ route('student.payments.index') }}" class="sidebar-link {{ request()->routeIs('student.payments.index') ? 'active' : '' }}">
                                <i class="fa-solid fa-clock-rotate-left"></i> Riwayat Transaksi
                            </a>
                        @endif
                        
                        <!-- General Settings for all roles -->
                        <div class="text-[10px] text-gray-500 uppercase tracking-widest font-semibold px-4 mt-6 mb-2">Pengaturan</div>
                        <a href="{{ route('profile.edit') }}" class="sidebar-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
                            <i class="fa-solid fa-user-gear"></i> Kelola Profile
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="w-full">
                            @csrf
                            <button type="submit" class="sidebar-link w-full text-left bg-transparent border-none">
                                <i class="fa-solid fa-arrow-right-from-bracket"></i> Keluar (Logout)
                            </button>
                        </form>
                    </div>
                </aside>

                <!-- Page Content Area -->
                <main class="main-content">
                    @isset($header)
                        <header class="bg-white shadow">
                            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                                {{ $header }}
                            </div>
                        </header>
                    @endisset

                    <div class="flex-1">
                        {{ $slot }}
                    </div>

                    <!-- Footer -->
                    <footer class="bg-white border-t border-slate-200 mt-auto">
                        <div class="w-full mx-auto max-w-screen-xl p-4 flex flex-col md:flex-row items-center justify-between text-sm text-slate-500">
                            <span class="sm:text-center mb-2 md:mb-0">
                                &copy; {{ date('Y') }} <a href="{{ route('dashboard') }}" class="hover:underline font-semibold text-slate-700">{{ config('app.name', 'Affa Swimming') }}</a>. All Rights Reserved.
                            </span>
                            <ul class="flex flex-wrap items-center mt-3 sm:mt-0 gap-4 md:gap-6 font-medium">
                                <li>
                                    <a href="#" class="hover:text-blue-600 transition-colors">Tentang Kami</a>
                                </li>
                                <li>
                                    <a href="#" class="hover:text-blue-600 transition-colors">Bantuan</a>
                                </li>
                                <li>
                                    <a href="#" class="hover:text-blue-600 transition-colors">Kontak</a>
                                </li>
                            </ul>
                        </div>
                    </footer>
                </main>
            </div>
        </div>
    </body>
</html>
