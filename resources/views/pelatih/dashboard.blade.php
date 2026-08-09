<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-slate-800 tracking-tight">
                {{ __('Coach Training Dashboard') }}
            </h2>
            <div class="flex items-center gap-3">
                <span class="px-4 py-2 rounded-full border border-slate-200 bg-slate-50 text-xs font-semibold text-slate-505 shadow-sm flex items-center gap-1.5">
                    <i class="fa-regular fa-calendar"></i>
                    {{ now()->format('l, d F Y') }}
                </span>
                <span class="info-badge"><i class="fa-solid fa-person-swimming mr-1"></i> Hak Akses: Pelatih</span>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl p-8 border border-slate-100 relative">
                <div class="absolute top-0 right-0 p-8 opacity-5 text-emerald-600 hidden md:block">
                    <i class="fa-solid fa-person-swimming text-9xl"></i>
                </div>
                <div class="relative z-10">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-100 shadow-sm mb-4">
                        <i class="fa-solid fa-face-smile mr-1.5"></i> Selamat Datang Kembali
                    </span>
                    <h3 class="text-2xl font-bold text-slate-800">Halo, {{ Auth::user()->name }}!</h3>
                    <p class="text-sm text-slate-500 mt-2 max-w-xl leading-relaxed">
                        Anda login sebagai <strong class="text-emerald-600 font-semibold">Pelatih (Instruktur)</strong>. Pantau jadwal latihan, data kemajuan murid, dan agenda renang club Anda dengan efisien.
                    </p>

                    <!-- Classes & Students Section -->
                    <div class="mt-8">
                        <h4 class="text-lg font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2"><i class="fa-solid fa-users-rectangle mr-2 text-blue-500"></i> Kelas & Murid Bimbingan Anda</h4>
                        
                        @if($classes->isEmpty())
                            <div class="bg-blue-50/50 border border-blue-100 rounded-xl p-6 text-center">
                                <i class="fa-regular fa-folder-open text-3xl text-blue-300 mb-3"></i>
                                <p class="text-sm text-blue-800 font-medium">Anda belum ditugaskan untuk mengajar kelas mana pun.</p>
                                <p class="text-xs text-blue-500 mt-1">Silakan hubungi administrator jika ini adalah sebuah kesalahan.</p>
                            </div>
                        @else
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                @foreach($classes as $swimClass)
                                    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                                        <div class="bg-slate-50 border-b border-slate-100 px-5 py-4 flex justify-between items-center">
                                            <div>
                                                <h5 class="font-bold text-slate-800">{{ $swimClass->name }}</h5>
                                                <p class="text-xs text-slate-500 mt-0.5"><i class="fa-regular fa-clock mr-1"></i> {{ $swimClass->schedule ?? 'Belum ada jadwal' }}</p>
                                            </div>
                                            <span class="inline-flex items-center justify-center bg-blue-100 text-blue-700 text-xs font-bold px-2.5 py-1 rounded-full">
                                                {{ $swimClass->students->count() }} Murid
                                            </span>
                                        </div>
                                        
                                        <div class="p-5">
                                            @if($swimClass->students->isEmpty())
                                                <p class="text-sm text-slate-400 italic text-center py-4">Belum ada murid di kelas ini.</p>
                                            @else
                                                <ul class="divide-y divide-slate-100">
                                                    @foreach($swimClass->students as $student)
                                                        <li class="py-3 flex items-center justify-between first:pt-0 last:pb-0">
                                                            <div class="flex items-center gap-3">
                                                                <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center font-bold text-xs border border-slate-200">
                                                                    {{ strtoupper(substr($student->name, 0, 2)) }}
                                                                </div>
                                                                <div>
                                                                    <p class="text-sm font-semibold text-slate-800">{{ $student->name }}</p>
                                                                    <p class="text-[10px] text-slate-400">{{ $student->phone ?? 'Tidak ada kontak' }}</p>
                                                                </div>
                                                            </div>
                                                            @if($student->status === 'aktif')
                                                                <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 border border-emerald-100 px-2 py-0.5 rounded">Aktif</span>
                                                            @else
                                                                <span class="text-[10px] font-bold text-rose-600 bg-rose-50 border border-rose-100 px-2 py-0.5 rounded">Nonaktif</span>
                                                            @endif
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

                    <div class="mt-12 pt-6 border-t border-slate-100 text-center text-xs text-slate-400 font-medium">
                        &copy; {{ date('Y') }} AFFA Swimming.
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
