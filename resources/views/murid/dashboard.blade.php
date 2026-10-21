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

    <div class="py-10 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- High Urgency Unpaid Warning Banner (Wajib Dibaca) -->
            @if($student && $student->remaining_meetings <= 0)
                <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-red-50 via-rose-50 to-amber-50 border-2 border-red-500 p-6 sm:p-7 shadow-lg shadow-red-500/10" style="background-color: #fef2f2; border-color: #ef4444; border-left: 8px solid #dc2626;">
                    <!-- Background watermark icon -->
                    <div class="absolute -right-6 -bottom-6 text-red-500/10 text-9xl pointer-events-none select-none">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>

                    <div class="relative z-10">
                        <!-- Top Header: Badges & Remaining Meetings -->
                        <div class="flex flex-wrap items-center justify-between gap-3 pb-3 border-b border-red-200">
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black tracking-wider uppercase text-white shadow-sm" style="background-color: #dc2626;">
                                    <i class="fa-solid fa-triangle-exclamation animate-pulse"></i> PERINGATAN PENTING
                                </span>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold text-white uppercase tracking-wider" style="background-color: #d97706;">
                                    WAJIB DIBACA
                                </span>
                            </div>
                            <div class="text-xs font-bold px-3 py-1 rounded-lg border" style="background-color: #fee2e2; border-color: #fca5a5; color: #991b1b;">
                                Sisa Pertemuan: <span class="font-extrabold text-sm" style="color: #7f1d1d;">{{ $student->remaining_meetings }} Sesi (Habis)</span>
                            </div>
                        </div>

                        <!-- Main Content -->
                        <div class="mt-4 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
                            <div class="flex-1 space-y-3">
                                <div>
                                    <h3 class="text-xl sm:text-2xl font-black tracking-tight flex items-center gap-2" style="color: #7f1d1d;">
                                        Paket Latihan Renang Anda Telah Habis!
                                    </h3>
                                    <p class="text-sm font-medium mt-1 leading-relaxed" style="color: #374151;">
                                        Masa kuota paket latihan renang Anda telah habis (0 sesi). Harap segera perhatikan poin pemberitahuan di bawah ini:
                                    </p>
                                </div>

                                <!-- Key Points / Consequences Card Grid -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 pt-1">
                                    <div class="rounded-xl p-3 flex items-start gap-3 shadow-xs border" style="background-color: rgba(255, 255, 255, 0.9); border-color: #fecaca;">
                                        <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 text-sm font-bold" style="background-color: #fee2e2; color: #b91c1c;">
                                            <i class="fa-solid fa-user-slash"></i>
                                        </div>
                                        <div class="text-xs" style="color: #4b5563;">
                                            <strong class="block font-bold mb-0.5" style="color: #991b1b;">Risiko Penonaktifan Akun</strong>
                                            Akun Anda berstatus unpaid dan akan dinonaktifkan oleh admin jika tidak segera diperpanjang.
                                        </div>
                                    </div>

                                    <div class="rounded-xl p-3 flex items-start gap-3 shadow-xs border" style="background-color: rgba(255, 255, 255, 0.9); border-color: #fecaca;">
                                        <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 text-sm font-bold" style="background-color: #fee2e2; color: #b91c1c;">
                                            <i class="fa-solid fa-calendar-xmark"></i>
                                        </div>
                                        <div class="text-xs" style="color: #4b5563;">
                                            <strong class="block font-bold mb-0.5" style="color: #991b1b;">Pencabutan Jadwal & Pelatih</strong>
                                            Slot jadwal dan kuota pelatih Anda akan ditarik dan dialihkan kepada murid lain yang aktif.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Action CTA Buttons -->
                            <div class="w-full lg:w-auto flex flex-col sm:flex-row lg:flex-col gap-2.5 flex-shrink-0">
                                <a href="{{ route('student.payments.create') }}" class="px-6 py-3.5 text-white font-black text-sm rounded-xl shadow-md hover:shadow-lg transition-all duration-200 flex items-center justify-center gap-2 text-center" style="background-color: #dc2626;">
                                    <i class="fa-solid fa-wallet text-base"></i>
                                    <span>Perpanjang Paket Sekarang</span>
                                    <i class="fa-solid fa-arrow-right text-xs ml-1"></i>
                                </a>

                                <a href="https://wa.me/6282128199210?text={{ rawurlencode('Halo Admin AFFA Swimming, saya ingin konfirmasi perpanjangan paket renang atas nama ' . (Auth::user()->name ?? 'Murid')) }}" target="_blank" class="px-5 py-2.5 font-bold text-xs rounded-xl border transition-colors flex items-center justify-center gap-2 text-center shadow-xs" style="background-color: #ffffff; border-color: #fca5a5; color: #374151;">
                                    <i class="fa-brands fa-whatsapp text-emerald-600 text-sm"></i>
                                    <span>Konfirmasi ke Admin WA</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

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
