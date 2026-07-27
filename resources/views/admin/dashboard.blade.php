<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-slate-800 tracking-tight">
                {{ __('Admin Management Dashboard') }}
            </h2>
            <div class="flex items-center gap-3">
                <span class="px-4 py-2 rounded-full border border-slate-200 bg-slate-50 text-xs font-semibold text-slate-505 shadow-sm flex items-center gap-1.5">
                    <i class="fa-regular fa-calendar"></i>
                    {{ now()->format('l, d F Y') }}
                </span>
                <span class="info-badge"><i class="fa-solid fa-user-tie mr-1"></i> Hak Akses: Admin</span>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl p-8 border border-slate-100 relative">
                <div class="absolute top-0 right-0 p-8 opacity-5 text-blue-600 hidden md:block">
                    <i class="fa-solid fa-user-tie text-9xl"></i>
                </div>
                <div class="relative z-10">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-100 shadow-sm mb-4">
                        <i class="fa-solid fa-face-smile mr-1.5"></i> Selamat Datang Kembali
                    </span>
                    <h3 class="text-2xl font-bold text-slate-800">Halo, {{ Auth::user()->name }}!</h3>
                    <p class="text-sm text-slate-500 mt-2 max-w-xl leading-relaxed">
                        Anda login sebagai <strong class="text-blue-600 font-semibold">Admin (Pengelola)</strong>. Gunakan panel kendali ini untuk memantau data operasional dan administrasi club renang AFFA Swimming secara berkala.
                    </p>

                    <div class="mt-12">
                        <h4 class="text-lg font-bold text-slate-800 mb-4 pb-2 border-b border-slate-50">Menu Administrasi</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            <!-- Administrasi -->
                            <a href="{{ route('admin.pool-locations.index') }}" class="p-5 rounded-2xl border border-slate-150 bg-slate-50/50 hover:bg-blue-50/30 transition-all duration-200 group flex items-start gap-4">
                                <div class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                                    <i class="fa-solid fa-location-dot text-lg"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-800 text-sm">Lokasi Kolam</h4>
                                    <p class="text-xs text-slate-400 mt-1">Kelola daftar kolam renang & biaya.</p>
                                </div>
                            </a>

                            <a href="{{ route('admin.schedules.index') }}" class="p-5 rounded-2xl border border-slate-150 bg-slate-50/50 hover:bg-blue-50/30 transition-all duration-200 group flex items-start gap-4">
                                <div class="w-10 h-10 rounded-xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                                    <i class="fa-solid fa-calendar-alt text-lg"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-800 text-sm">Jadwal Kelas</h4>
                                    <p class="text-xs text-slate-400 mt-1">Plotting jadwal pelatih & murid.</p>
                                </div>
                            </a>

                            <a href="{{ route('admin.operations.recap') }}" class="p-5 rounded-2xl border border-slate-150 bg-slate-50/50 hover:bg-blue-50/30 transition-all duration-200 group flex items-start gap-4">
                                <div class="w-10 h-10 rounded-xl bg-purple-50 border border-purple-100 text-purple-600 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                                    <i class="fa-solid fa-clipboard-list text-lg"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-800 text-sm">Rekap Latihan</h4>
                                    <p class="text-xs text-slate-400 mt-1">Laporan harian & presensi latihan.</p>
                                </div>
                            </a>
                        </div>
                    </div>

                    <div class="mt-8">
                        <h4 class="text-lg font-bold text-slate-800 mb-4 pb-2 border-b border-slate-50">Menu Keuangan (Finance)</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            <!-- Finance -->
                            <a href="{{ route('finance.payments.index') }}" class="p-5 rounded-2xl border border-slate-150 bg-emerald-50/50 hover:bg-emerald-100/30 hover:border-emerald-200 transition-all duration-200 group flex items-start gap-4">
                                <div class="w-10 h-10 rounded-xl bg-emerald-100 border border-emerald-200 text-emerald-600 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                                    <i class="fa-solid fa-wallet text-lg"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-800 text-sm">Pembayaran Paket</h4>
                                    <p class="text-xs text-slate-400 mt-1">Approval pembayaran murid.</p>
                                </div>
                            </a>

                            <a href="{{ route('finance.profit') }}" class="p-5 rounded-2xl border border-slate-150 bg-emerald-50/50 hover:bg-emerald-100/30 hover:border-emerald-200 transition-all duration-200 group flex items-start gap-4">
                                <div class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                                    <i class="fa-solid fa-chart-line text-lg"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-800 text-sm">Profit</h4>
                                    <p class="text-xs text-slate-400 mt-1">Laporan laba/rugi & saldo akhir.</p>
                                </div>
                            </a>

                            <a href="{{ route('finance.payroll.index') }}" class="p-5 rounded-2xl border border-slate-150 bg-emerald-50/50 hover:bg-emerald-100/30 hover:border-emerald-200 transition-all duration-200 group flex items-start gap-4">
                                <div class="w-10 h-10 rounded-xl bg-amber-50 border border-amber-100 text-amber-600 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                                    <i class="fa-solid fa-money-check-dollar text-lg"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-800 text-sm">Perhitungan Gaji</h4>
                                    <p class="text-xs text-slate-400 mt-1">Gaji pelatih berdasarkan presensi.</p>
                                </div>
                            </a>
                        </div>
                    </div>

                    <div class="mt-12 pt-6 border-t border-slate-100 text-center text-xs text-slate-400 font-medium">
                        © 2026 AFFA Swimming Academy.
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
