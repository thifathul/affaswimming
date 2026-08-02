<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-slate-800 tracking-tight">
                {{ __('Student Member Dashboard') }}
            </h2>
            <div class="flex items-center gap-3">
                <span class="px-4 py-2 rounded-full border border-slate-200 bg-slate-50 text-xs font-semibold text-slate-550 shadow-sm flex items-center gap-1.5">
                    <i class="fa-regular fa-calendar"></i>
                    {{ now()->format('l, d F Y') }}
                </span>
                <span class="info-badge"><i class="fa-solid fa-graduation-cap mr-1"></i> Hak Akses: Murid</span>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl p-8 border border-slate-100 relative">
                <div class="absolute top-0 right-0 p-8 opacity-5 text-amber-600 hidden md:block">
                    <i class="fa-solid fa-graduation-cap text-9xl"></i>
                </div>
                <div class="relative z-10">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-100 shadow-sm mb-4">
                        <i class="fa-solid fa-face-smile mr-1.5"></i> Selamat Datang Kembali
                    </span>
                    <h3 class="text-2xl font-bold text-slate-800">Halo, {{ Auth::user()->name }}!</h3>
                    <p class="text-sm text-slate-500 mt-2 max-w-xl leading-relaxed">
                        Anda login sebagai <strong class="text-amber-600 font-semibold">Murid (Siswa)</strong>. Pantau hasil latihan, jadwal berenang, dan pengumuman terbaru club AFFA Swimming di sini.
                    </p>

                    <!-- Inval Information Section -->
                    @if(isset($activeInvals) && $activeInvals->isNotEmpty())
                        <div class="mt-8 mb-6">
                            <h4 class="text-lg font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2"><i class="fa-solid fa-triangle-exclamation text-amber-500 mr-2"></i> Informasi Perubahan Jadwal (Inval)</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($activeInvals as $inval)
                                    <div class="bg-amber-50/50 border border-amber-200 rounded-xl p-5 relative overflow-hidden">
                                        <div class="absolute top-0 right-0 w-2 h-full bg-amber-400"></div>
                                        <h5 class="font-bold text-amber-800 text-base mb-1">
                                            Penggantian Pelatih
                                        </h5>
                                        <p class="text-sm text-amber-700 mb-3">
                                            Jadwal hari <strong>{{ $inval->schedule->day }}</strong> akan digantikan sementara.
                                        </p>
                                        <div class="space-y-2 text-sm text-slate-700">
                                            <p><i class="fa-regular fa-calendar text-amber-600 w-5 text-center"></i> <strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($inval->proposed_date)->format('d F Y') }}</p>
                                            <p><i class="fa-regular fa-clock text-amber-600 w-5 text-center"></i> <strong>Jam:</strong> {{ \Carbon\Carbon::parse($inval->proposed_start_time)->format('H:i') }}</p>
                                            <p><i class="fa-solid fa-location-dot text-amber-600 w-5 text-center"></i> <strong>Lokasi:</strong> {{ $inval->proposedPoolLocation->name ?? ($inval->schedule->poolLocation->name ?? 'Tetap') }}</p>
                                            <p><i class="fa-solid fa-person-chalkboard text-amber-600 w-5 text-center"></i> <strong>Pelatih Pengganti:</strong> {{ $inval->substituteCoach->name ?? 'Belum ditentukan' }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Classes & Coaches Section -->
                    <div class="mt-8">
                        <h4 class="text-lg font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2"><i class="fa-solid fa-water mr-2 text-blue-500"></i> Kelas Berenang Anda</h4>
                        
                        @if(!$student)
                            <div class="bg-amber-50/50 border border-amber-100 rounded-xl p-6 text-center">
                                <i class="fa-solid fa-circle-exclamation text-3xl text-amber-300 mb-3"></i>
                                <p class="text-sm text-amber-800 font-medium">Data murid Anda belum terhubung dengan akun ini.</p>
                                <p class="text-xs text-amber-500 mt-1">Silakan hubungi administrator untuk menghubungkan akun login Anda dengan data profil murid.</p>
                            </div>
                        @elseif($classes->isEmpty())
                            <div class="bg-blue-50/50 border border-blue-100 rounded-xl p-6 text-center">
                                <i class="fa-regular fa-folder-open text-3xl text-blue-300 mb-3"></i>
                                <p class="text-sm text-blue-800 font-medium">Anda belum didaftarkan ke kelas berenang mana pun.</p>
                                <p class="text-xs text-blue-500 mt-1">Silakan hubungi admin atau tunggu hingga pelatih memplot Anda ke dalam kelas.</p>
                            </div>
                        @else
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach($classes as $swimClass)
                                    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                                        <div class="bg-slate-50 border-b border-slate-100 px-5 py-4">
                                            <h5 class="font-bold text-slate-800 text-lg">{{ $swimClass->name }}</h5>
                                            <p class="text-xs text-slate-500 mt-1"><i class="fa-regular fa-clock mr-1"></i> Jadwal: <span class="font-semibold">{{ $swimClass->schedule ?? 'Belum ada jadwal' }}</span></p>
                                        </div>
                                        
                                        <div class="p-5">
                                            <h6 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Pelatih Pengampu</h6>
                                            @if($swimClass->coaches->isEmpty())
                                                <p class="text-sm text-slate-400 italic">Belum ada pelatih yang ditugaskan.</p>
                                            @else
                                                <ul class="space-y-3">
                                                    @foreach($swimClass->coaches as $coach)
                                                        <li class="flex items-center gap-3">
                                                            <div class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-sm border border-emerald-100">
                                                                {{ strtoupper(substr($coach->name, 0, 2)) }}
                                                            </div>
                                                            <div>
                                                                <p class="text-sm font-bold text-slate-800">{{ $coach->name }}</p>
                                                                <p class="text-[10px] text-slate-500">
                                                                    {{ $coach->position ? $coach->position->name : 'Pelatih' }}
                                                                </p>
                                                            </div>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    <div class="mt-12 pt-6 border-t border-slate-100">
                        <h4 class="text-lg font-bold text-slate-800 mb-4 pb-2 border-b border-slate-100"><i class="fa-regular fa-calendar-days mr-2 text-emerald-500"></i> Jadwal Latihan Anda</h4>
                        
                        @if(!$student)
                            <div class="bg-amber-50/50 border border-amber-100 rounded-xl p-6 text-center">
                                <p class="text-sm text-amber-800 font-medium">Data murid belum terhubung.</p>
                            </div>
                        @elseif($schedules->isEmpty())
                            <div class="bg-emerald-50/50 border border-emerald-100 rounded-xl p-6 text-center">
                                <i class="fa-regular fa-calendar-xmark text-3xl text-emerald-300 mb-3"></i>
                                <p class="text-sm text-emerald-800 font-medium">Belum ada jadwal latihan yang di-assign untuk Anda.</p>
                                <p class="text-xs text-emerald-500 mt-1">Silakan tunggu Admin atau Pelatih untuk mengatur jadwal Anda.</p>
                            </div>
                        @else
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach($schedules as $schedule)
                                    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-shadow relative">
                                        <div class="absolute top-0 right-0 w-1.5 h-full {{ $schedule->status === 'available' ? 'bg-emerald-400' : 'bg-blue-400' }}"></div>
                                        <div class="p-5">
                                            <div class="flex items-center gap-3 mb-3">
                                                <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 font-bold">
                                                    {{ substr($schedule->day, 0, 3) }}
                                                </div>
                                                <div>
                                                    <h5 class="font-bold text-slate-800 text-lg">{{ $schedule->day }}</h5>
                                                    <p class="text-xs font-semibold text-blue-600">
                                                        {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                                                    </p>
                                                </div>
                                            </div>
                                            
                                            <div class="space-y-2 mt-4 text-sm text-slate-600">
                                                <div class="flex items-start gap-2">
                                                    <i class="fa-solid fa-location-dot mt-1 text-slate-400 w-4 text-center"></i>
                                                    <div>
                                                        <span class="font-semibold text-slate-700">Lokasi Kolam:</span>
                                                        <p>{{ $schedule->poolLocation ? $schedule->poolLocation->name : 'Belum ditentukan' }}</p>
                                                    </div>
                                                </div>
                                                <div class="flex items-start gap-2">
                                                    <i class="fa-solid fa-person-chalkboard mt-1 text-slate-400 w-4 text-center"></i>
                                                    <div>
                                                        <span class="font-semibold text-slate-700">Pelatih:</span>
                                                        <p>{{ $schedule->coach ? $schedule->coach->name : 'Belum ada pelatih' }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    <div class="mt-12 pt-6 border-t border-slate-100 text-center text-xs text-slate-400 font-medium">
                        © 2026 AFFA SWIMMING CLUB.
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
