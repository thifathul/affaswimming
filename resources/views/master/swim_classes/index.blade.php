<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-slate-800 tracking-tight">
                {{ __('Manajemen Kelas') }}
            </h2>
            <div class="flex items-center gap-3">
                <span class="px-4 py-2 rounded-full border border-slate-200 bg-slate-50 text-xs font-semibold text-slate-500 shadow-sm flex items-center gap-1.5">
                    <i class="fa-regular fa-calendar"></i>
                    {{ now()->format('l, d F Y') }}
                </span>
                <span class="info-badge"><i class="fa-solid fa-crown mr-1"></i> Hak Akses: {{ ucfirst(auth()->user()->role) }}</span>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-600 text-sm flex items-center gap-2 shadow-sm">
                    <i class="fa-solid fa-circle-check text-emerald-500"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-600 text-sm flex items-center gap-2 shadow-sm">
                    <i class="fa-solid fa-circle-exclamation text-rose-500"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- Statistical Widgets -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Total Kelas -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl p-6 border border-slate-100 flex items-center justify-between">
                    <div>
                        <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Kategori Kelas</div>
                        <div class="mt-2 flex items-baseline gap-2">
                            <span class="text-3xl font-extrabold text-slate-800">{{ $swimClasses->count() }}</span>
                            <span class="text-xs text-slate-500 font-medium">Kelas</span>
                        </div>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-slate-50 flex items-center justify-center text-slate-500 border border-slate-100">
                        <i class="fa-solid fa-water text-xl"></i>
                    </div>
                </div>
                <!-- Kelas Aktif -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl p-6 border border-slate-100 flex items-center justify-between">
                    <div>
                        <div class="text-xs font-bold text-emerald-500 uppercase tracking-wider">Kelas Aktif</div>
                        <div class="mt-2 flex items-baseline gap-2">
                            <span class="text-3xl font-extrabold text-emerald-600">{{ $swimClasses->where('status', 'aktif')->count() }}</span>
                            <span class="text-xs text-emerald-500/80 font-medium">Berjalan</span>
                        </div>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-500 border border-emerald-100">
                        <i class="fa-solid fa-check-circle text-xl"></i>
                    </div>
                </div>
                <!-- Total Murid Ditautkan -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl p-6 border border-slate-100 flex items-center justify-between">
                    <div>
                        <div class="text-xs font-bold text-blue-500 uppercase tracking-wider">Total Peserta</div>
                        <div class="mt-2 flex items-baseline gap-2">
                            <span class="text-3xl font-extrabold text-blue-600">{{ $swimClasses->sum('students_count') }}</span>
                            <span class="text-xs text-blue-500/80 font-medium">Murid Tergabung</span>
                        </div>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-500 border border-blue-100">
                        <i class="fa-solid fa-users text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Table Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl p-6 border border-slate-100">
                
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
                    <div>
                        <h3 class="text-xl font-bold text-slate-800"><i class="fa-solid fa-water mr-2 text-slate-500"></i> Daftar Kelas</h3>
                        <p class="text-sm text-slate-500 mt-1">Kelola data kelas berenang, pelatih yang ditugaskan, dan status aktifnya.</p>
                    </div>
                    <a href="{{ route('master.swim-classes.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-bold text-sm rounded-xl transition-all duration-200 shadow-md shadow-slate-800/20 hover:shadow-lg hover:shadow-slate-800/30 active:scale-95">
                        <i class="fa-solid fa-plus text-xs"></i> Tambah Kelas
                    </a>
                </div>
                
                <div class="overflow-x-auto rounded-xl border border-slate-100 bg-white">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-100 bg-slate-50/75 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                <th class="p-4 pl-6">Nama Kelas</th>
                                <th class="p-4">Pelatih Utama</th>
                                <th class="p-4">Jadwal</th>
                                <th class="p-4 text-center">Peserta</th>
                                <th class="p-4">Status</th>
                                <th class="p-4 pr-6 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @forelse($swimClasses as $swimClass)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="p-4 pl-6">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-xl flex items-center justify-center font-bold text-xs bg-blue-50 text-blue-600 border border-blue-100 shadow-sm">
                                                <i class="fa-solid fa-water"></i>
                                            </div>
                                            <div>
                                                <p class="font-bold text-slate-800 leading-snug">{{ $swimClass->name }}</p>
                                                @if($swimClass->description)
                                                    <p class="text-[10px] text-slate-500 max-w-[150px] truncate" title="{{ $swimClass->description }}">{{ $swimClass->description }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        @if($swimClass->coaches->count() > 0)
                                            <div class="flex flex-wrap gap-1 max-w-[200px]">
                                                @foreach($swimClass->coaches as $coach)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                                        <i class="fa-solid fa-user-tie mr-1"></i> {{ $coach->name }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-slate-400 text-xs italic">Belum ditentukan</span>
                                        @endif
                                    </td>
                                    <td class="p-4 text-slate-700 font-medium text-xs">
                                        @if($swimClass->schedule)
                                            <div class="flex items-center gap-1.5 bg-slate-100 w-fit px-2.5 py-1 rounded-lg border border-slate-200">
                                                <i class="fa-regular fa-clock text-slate-400"></i>
                                                <span>{{ $swimClass->schedule }}</span>
                                            </div>
                                        @else
                                            <span class="text-slate-400 italic">Belum diatur</span>
                                        @endif
                                    </td>
                                    <td class="p-4 text-center">
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-slate-100 text-slate-600 font-bold text-xs border border-slate-200" title="{{ $swimClass->students_count }} Murid di kelas ini">
                                            {{ $swimClass->students_count }}
                                        </span>
                                    </td>
                                    <td class="p-4">
                                        @if($swimClass->status === 'aktif')
                                            <span class="inline-flex items-center w-fit px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <i class="fa-solid fa-check mr-1"></i> Aktif
                                            </span>
                                        @else
                                            <span class="inline-flex items-center w-fit px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                                <i class="fa-solid fa-ban mr-1"></i> Nonaktif
                                            </span>
                                        @endif
                                    </td>
                                    <td class="p-4 pr-6">
                                        <div class="flex items-center justify-end gap-3.5">
                                            <form action="{{ route('master.swim-classes.toggle-status', $swimClass->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="{{ $swimClass->status === 'aktif' ? 'text-amber-500 hover:text-amber-700' : 'text-emerald-500 hover:text-emerald-700' }} transition-colors text-lg bg-transparent border-none p-0 cursor-pointer" title="{{ $swimClass->status === 'aktif' ? 'Nonaktifkan Kelas' : 'Aktifkan Kelas' }}">
                                                    <i class="fa-solid {{ $swimClass->status === 'aktif' ? 'fa-lock' : 'fa-lock-open' }}"></i>
                                                </button>
                                            </form>
                                            <a href="{{ route('master.swim-classes.edit', $swimClass->id) }}" class="text-blue-500 hover:text-blue-700 transition-colors text-lg" title="Edit Data Kelas">
                                                <i class="fa-regular fa-pen-to-square"></i>
                                            </a>
                                            <form action="{{ route('master.swim-classes.destroy', $swimClass->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data kelas ini? Pastikan tidak ada murid yang tersisa di dalam kelas ini.');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700 transition-colors text-lg bg-transparent border-none p-0 cursor-pointer" title="Hapus Data Kelas">
                                                    <i class="fa-regular fa-trash-can"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-12 text-center">
                                        <div class="flex flex-col items-center justify-center text-slate-400">
                                            <i class="fa-solid fa-water text-5xl mb-4 text-slate-300"></i>
                                            <p class="text-base font-semibold">Belum ada data kelas</p>
                                            <p class="text-xs text-slate-400 mt-1">Silakan tambahkan data kelas baru untuk memulai plotting murid.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-12 text-center text-xs text-slate-400 font-medium">
                    © 2026 SMK Pasundan 1 Bandung.
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
