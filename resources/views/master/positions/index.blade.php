<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-slate-800 tracking-tight">
                {{ __('Manajemen Jabatan') }}
            </h2>
            <div class="flex items-center gap-3">
                <span class="px-4 py-2 rounded-full border border-slate-200 bg-slate-50 text-xs font-semibold text-slate-500 shadow-sm flex items-center gap-1.5">
                    <i class="fa-regular fa-calendar"></i>
                    {{ now()->format('l, d F Y') }}
                </span>
                <span class="info-badge"><i class="fa-solid fa-crown mr-1"></i> Hak Akses: Master</span>
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
                <!-- Total Jabatan -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl p-6 border border-slate-100 flex items-center justify-between">
                    <div>
                        <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Kategori Jabatan</div>
                        <div class="mt-2 flex items-baseline gap-2">
                            <span class="text-3xl font-extrabold text-slate-800">{{ $positions->count() }}</span>
                            <span class="text-xs text-slate-500 font-medium">Jabatan</span>
                        </div>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-slate-50 flex items-center justify-center text-slate-500 border border-slate-100">
                        <i class="fa-solid fa-briefcase text-xl"></i>
                    </div>
                </div>
                <!-- Jabatan Aktif -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl p-6 border border-slate-100 flex items-center justify-between">
                    <div>
                        <div class="text-xs font-bold text-emerald-500 uppercase tracking-wider">Jabatan Aktif</div>
                        <div class="mt-2 flex items-baseline gap-2">
                            <span class="text-3xl font-extrabold text-emerald-600">{{ $positions->where('status', 'aktif')->count() }}</span>
                            <span class="text-xs text-emerald-500/80 font-medium">Sedang Digunakan</span>
                        </div>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-500 border border-emerald-100">
                        <i class="fa-solid fa-check-circle text-xl"></i>
                    </div>
                </div>
                <!-- Total Tim Ditautkan -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl p-6 border border-slate-100 flex items-center justify-between">
                    <div>
                        <div class="text-xs font-bold text-blue-500 uppercase tracking-wider">Total Pekerja (Tim)</div>
                        <div class="mt-2 flex items-baseline gap-2">
                            <span class="text-3xl font-extrabold text-blue-600">{{ $positions->sum('teams_count') }}</span>
                            <span class="text-xs text-blue-500/80 font-medium">Anggota Tim</span>
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
                        <h3 class="text-xl font-bold text-slate-800"><i class="fa-solid fa-briefcase mr-2 text-slate-500"></i> Daftar Jabatan</h3>
                        <p class="text-sm text-slate-500 mt-1">Kelola data posisi jabatan, tingkat gaji, serta status penggunaannya.</p>
                    </div>
                    <a href="{{ route('master.positions.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-bold text-sm rounded-xl transition-all duration-200 shadow-md shadow-slate-800/20 hover:shadow-lg hover:shadow-slate-800/30 active:scale-95">
                        <i class="fa-solid fa-plus text-xs"></i> Tambah Jabatan
                    </a>
                </div>
                
                <div class="overflow-x-auto rounded-xl border border-slate-100 bg-white">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-100 bg-slate-50/75 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                <th class="p-4 pl-6">Nama Jabatan</th>
                                <th class="p-4">Deskripsi Tugas</th>
                                <th class="p-4">Gaji Pokok / Honor</th>
                                <th class="p-4 text-center">Anggota Tim</th>
                                <th class="p-4">Status</th>
                                <th class="p-4 pr-6 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @forelse($positions as $position)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="p-4 pl-6">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-xl flex items-center justify-center font-bold text-xs bg-blue-50 text-blue-600 border border-blue-100 shadow-sm">
                                                <i class="fa-solid fa-briefcase"></i>
                                            </div>
                                            <div>
                                                <p class="font-bold text-slate-800 leading-snug">{{ $position->name }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4 text-slate-500 text-xs max-w-xs truncate" title="{{ $position->description }}">
                                        {{ $position->description ?? '-' }}
                                    </td>
                                    <td class="p-4 text-slate-700 font-bold">
                                        @if($position->base_salary)
                                            Rp {{ number_format($position->base_salary, 0, ',', '.') }}
                                        @else
                                            <span class="text-slate-400 font-normal italic">Belum diatur</span>
                                        @endif
                                    </td>
                                    <td class="p-4 text-center">
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-slate-100 text-slate-600 font-bold text-xs border border-slate-200" title="{{ $position->teams_count }} Anggota Tim menggunakan jabatan ini">
                                            {{ $position->teams_count }}
                                        </span>
                                    </td>
                                    <td class="p-4">
                                        @if($position->status === 'aktif')
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
                                            <form action="{{ route('master.positions.toggle-status', $position->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="{{ $position->status === 'aktif' ? 'text-amber-500 hover:text-amber-700' : 'text-emerald-500 hover:text-emerald-700' }} transition-colors text-lg bg-transparent border-none p-0 cursor-pointer" title="{{ $position->status === 'aktif' ? 'Nonaktifkan Jabatan' : 'Aktifkan Jabatan' }}">
                                                    <i class="fa-solid {{ $position->status === 'aktif' ? 'fa-lock' : 'fa-lock-open' }}"></i>
                                                </button>
                                            </form>
                                            <a href="{{ route('master.positions.edit', $position->id) }}" class="text-blue-500 hover:text-blue-700 transition-colors text-lg" title="Edit Data Jabatan">
                                                <i class="fa-regular fa-pen-to-square"></i>
                                            </a>
                                            <form action="{{ route('master.positions.destroy', $position->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data jabatan ini?');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700 transition-colors text-lg bg-transparent border-none p-0 cursor-pointer" title="Hapus Data Jabatan">
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
                                            <i class="fa-solid fa-briefcase text-5xl mb-4 text-slate-300"></i>
                                            <p class="text-base font-semibold">Belum ada data jabatan</p>
                                            <p class="text-xs text-slate-400 mt-1">Silakan tambahkan data jabatan baru untuk memulai.</p>
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
