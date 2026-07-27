<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-slate-800 tracking-tight">
                {{ __('Master Owner Dashboard') }}
            </h2>
            <div class="flex items-center gap-3">
                <span class="px-4 py-2 rounded-full border border-slate-200 bg-slate-50 text-xs font-semibold text-slate-505 shadow-sm flex items-center gap-1.5">
                    <i class="fa-regular fa-calendar"></i>
                    {{ now()->format('l, d F Y') }}
                </span>
                <span class="info-badge"><i class="fa-solid fa-crown mr-1"></i> Hak Akses: Master</span>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl p-8 border border-slate-100 relative">
                <div class="absolute top-0 right-0 p-8 opacity-5 text-blue-600 hidden md:block">
                    <i class="fa-solid fa-crown text-9xl"></i>
                </div>
                <div class="relative z-10">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-100 shadow-sm mb-4">
                        <i class="fa-solid fa-face-smile mr-1.5"></i> Selamat Datang Kembali
                    </span>
                    <h3 class="text-2xl font-bold text-slate-800">Halo, {{ Auth::user()->name }}!</h3>
                    <p class="text-sm text-slate-500 mt-2 max-w-xl leading-relaxed mb-8">
                        Anda login sebagai <strong class="text-blue-600 font-semibold">Master Owner</strong>. Berikut adalah ringkasan performa finansial dan operasional bulan ini.
                    </p>
                    
                    <!-- Metrics Section -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                        <!-- Total Kas -->
                        <div class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl p-6 text-white shadow-lg shadow-emerald-200">
                            <div class="flex justify-between items-start mb-4">
                                <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center backdrop-blur-sm">
                                    <i class="fa-solid fa-wallet text-xl"></i>
                                </div>
                            </div>
                            <h4 class="text-emerald-50 text-sm font-semibold mb-1">Total Pendapatan Kas</h4>
                            <p class="text-2xl font-bold tracking-tight">Rp {{ number_format($totalCash, 0, ',', '.') }}</p>
                            <p class="text-xs text-emerald-100 mt-2 opacity-80">Bersih bulan berjalan</p>
                        </div>
                        
                        <!-- Total Pengeluaran -->
                        <div class="bg-gradient-to-br from-rose-500 to-red-600 rounded-2xl p-6 text-white shadow-lg shadow-rose-200">
                            <div class="flex justify-between items-start mb-4">
                                <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center backdrop-blur-sm">
                                    <i class="fa-solid fa-money-bill-transfer text-xl"></i>
                                </div>
                            </div>
                            <h4 class="text-rose-50 text-sm font-semibold mb-1">Total Pengeluaran</h4>
                            <p class="text-2xl font-bold tracking-tight">Rp {{ number_format($totalExpenses, 0, ',', '.') }}</p>
                            <p class="text-xs text-rose-100 mt-2 opacity-80" title="Gaji: Rp {{ number_format($coachSalaryExpenses,0,',','.') }} | Ops: Rp {{ number_format($operationalExpenses,0,',','.') }}">Hover untuk rincian</p>
                        </div>

                        <!-- Murid Aktif -->
                        <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl p-6 text-white shadow-lg shadow-blue-200">
                            <div class="flex justify-between items-start mb-4">
                                <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center backdrop-blur-sm">
                                    <i class="fa-solid fa-users text-xl"></i>
                                </div>
                            </div>
                            <h4 class="text-blue-50 text-sm font-semibold mb-1">Murid Aktif</h4>
                            <p class="text-2xl font-bold tracking-tight">{{ number_format($activeStudents) }} Murid</p>
                            <p class="text-xs text-blue-100 mt-2 opacity-80">Paket berjalan</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-10">
                        <!-- Distribusi Paket -->
                        <div class="lg:col-span-2 bg-white border border-slate-100 rounded-2xl shadow-sm p-6">
                            <h4 class="text-lg font-bold text-slate-800 mb-4 pb-2 border-b border-slate-50">Statistik Paket per Kolam</h4>
                            @if($packageDistribution->isEmpty())
                                <div class="text-center py-6">
                                    <p class="text-slate-400 text-sm">Belum ada data penjualan paket bulan ini.</p>
                                </div>
                            @else
                                <div class="overflow-x-auto">
                                    <table class="w-full text-left border-collapse">
                                        <thead>
                                            <tr class="bg-slate-50 text-slate-500 text-xs uppercase">
                                                <th class="py-3 px-4 font-bold rounded-l-lg">Lokasi Kolam</th>
                                                <th class="py-3 px-4 font-bold text-center">Jenis Paket</th>
                                                <th class="py-3 px-4 font-bold text-center rounded-r-lg">Total Terjual</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-50">
                                            @foreach($packageDistribution as $dist)
                                            <tr class="hover:bg-slate-50/50">
                                                <td class="py-3 px-4 text-sm font-medium text-slate-700">{{ $dist->pool_name }}</td>
                                                <td class="py-3 px-4 text-sm text-center">
                                                    <span class="px-2 py-1 bg-blue-50 text-blue-700 rounded-lg text-xs font-bold">{{ $dist->package_type }}x Pertemuan</span>
                                                </td>
                                                <td class="py-3 px-4 text-sm text-center font-bold text-emerald-600">{{ $dist->total }} Paket</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>

                    <h4 class="text-lg font-bold text-slate-800 mb-4 pb-2 border-b border-slate-50">Navigasi Master</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-4">
                        <a href="{{ route('master.articles.index') }}" class="p-5 rounded-2xl border border-slate-150 bg-slate-50/50 hover:bg-blue-50/30 hover:border-blue-200 transition-all duration-200 group flex items-start gap-4">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                                <i class="fa-solid fa-newspaper text-lg"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-800 text-sm">Kelola Artikel</h4>
                                <p class="text-xs text-slate-400 mt-1">Publikasi berita & pengumuman di website.</p>
                            </div>
                        </a>
                        <a href="{{ route('master.settings.landing') }}" class="p-5 rounded-2xl border border-slate-150 bg-slate-50/50 hover:bg-blue-50/30 hover:border-blue-200 transition-all duration-200 group flex items-start gap-4">
                            <div class="w-10 h-10 rounded-xl bg-pink-50 border border-pink-100 text-pink-600 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                                <i class="fa-solid fa-laptop-code text-lg"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-800 text-sm">Kelola Halaman Utama</h4>
                                <p class="text-xs text-slate-400 mt-1">Ubah teks sambutan Landing Page.</p>
                            </div>
                        </a>
                        <a href="{{ route('master.settings.pages') }}" class="p-5 rounded-2xl border border-slate-150 bg-slate-50/50 hover:bg-blue-50/30 hover:border-blue-200 transition-all duration-200 group flex items-start gap-4">
                            <div class="w-10 h-10 rounded-xl bg-orange-50 border border-orange-100 text-orange-600 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                                <i class="fa-solid fa-file-lines text-lg"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-800 text-sm">Kelola Halaman Statis</h4>
                                <p class="text-xs text-slate-400 mt-1">Ubah isi Tentang Kami & Kontak.</p>
                            </div>
                        </a>
                        <a href="{{ route('master.users') }}" class="p-5 rounded-2xl border border-slate-150 bg-slate-50/50 hover:bg-blue-50/30 hover:border-blue-200 transition-all duration-200 group flex items-start gap-4">
                            <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                                <i class="fa-solid fa-users-gear text-lg"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-800 text-sm">Manajemen User</h4>
                                <p class="text-xs text-slate-400 mt-1">Kelola data login & peran pengguna.</p>
                            </div>
                        </a>
                        <a href="{{ route('master.teams.index') }}" class="p-5 rounded-2xl border border-slate-150 bg-slate-50/50 hover:bg-blue-50/30 hover:border-blue-200 transition-all duration-200 group flex items-start gap-4">
                            <div class="w-10 h-10 rounded-xl bg-amber-50 border border-amber-100 text-amber-600 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                                <i class="fa-solid fa-people-group text-lg"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-800 text-sm">Manajemen Tim</h4>
                                <p class="text-xs text-slate-400 mt-1">Atur jajaran pengurus & pelatih renang.</p>
                            </div>
                        </a>
                        <a href="{{ route('master.students.index') }}" class="p-5 rounded-2xl border border-slate-150 bg-slate-50/50 hover:bg-blue-50/30 hover:border-blue-200 transition-all duration-200 group flex items-start gap-4">
                            <div class="w-10 h-10 rounded-xl bg-violet-50 border border-violet-100 text-violet-600 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                                <i class="fa-solid fa-user-graduate text-lg"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-800 text-sm">Manajemen Murid</h4>
                                <p class="text-xs text-slate-400 mt-1">Kelola data murid & wali/orang tua siswa.</p>
                            </div>
                        </a>
                        <a href="{{ route('admin.pool-locations.index') }}" class="p-5 rounded-2xl border border-slate-150 bg-slate-50/50 hover:bg-blue-50/30 hover:border-blue-200 transition-all duration-200 group flex items-start gap-4">
                            <div class="w-10 h-10 rounded-xl bg-cyan-50 border border-cyan-100 text-cyan-600 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                                <i class="fa-solid fa-water-ladder text-lg"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-800 text-sm">Master Paket & Kolam</h4>
                                <p class="text-xs text-slate-400 mt-1">Kelola paket, pertemuan, dan lokasi kolam.</p>
                            </div>
                        </a>
                        <a href="{{ route('finance.incomes.index') }}" class="p-5 rounded-2xl border border-slate-150 bg-slate-50/50 hover:bg-blue-50/30 hover:border-blue-200 transition-all duration-200 group flex items-start gap-4">
                            <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                                <i class="fa-solid fa-arrow-trend-up text-lg"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-800 text-sm">Pemasukan</h4>
                                <p class="text-xs text-slate-400 mt-1">Kelola data pemasukan lainnya.</p>
                            </div>
                        </a>
                        <a href="{{ route('finance.expenses.index') }}" class="p-5 rounded-2xl border border-slate-150 bg-slate-50/50 hover:bg-blue-50/30 hover:border-blue-200 transition-all duration-200 group flex items-start gap-4">
                            <div class="w-10 h-10 rounded-xl bg-rose-50 border border-rose-100 text-rose-600 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                                <i class="fa-solid fa-arrow-trend-down text-lg"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-800 text-sm">Pengeluaran</h4>
                                <p class="text-xs text-slate-400 mt-1">Kelola data pengeluaran operasional.</p>
                            </div>
                        </a>
                    </div>

                    <div class="mt-12 pt-6 border-t border-slate-100 text-center text-xs text-slate-400 font-medium">
                        © 2026 SMK Pasundan 1 Bandung.
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
