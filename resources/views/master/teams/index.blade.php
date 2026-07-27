<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-slate-800 tracking-tight">
                {{ __('Manajemen Tim') }}
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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl p-6 border border-slate-100">
                
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
                    <div>
                        <h3 class="text-xl font-bold text-slate-800"><i class="fa-solid fa-users-gear mr-2 text-slate-505"></i> Daftar Anggota Tim</h3>
                        <p class="text-sm text-slate-500 mt-1">Kelola data jajaran pengurus, pelatih, dan tim internal AFFA Swimming.</p>
                    </div>
                    <a href="{{ route('master.teams.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-bold text-sm rounded-xl transition-all duration-200 shadow-md shadow-slate-800/20 hover:shadow-lg hover:shadow-slate-800/30 active:scale-95">
                        <i class="fa-solid fa-plus text-xs"></i> Tambah Anggota Tim
                    </a>
                </div>
                
                <div class="overflow-x-auto rounded-xl border border-slate-100 bg-white">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-100 bg-slate-50/75 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                <th class="p-4 pl-6">Nama Anggota</th>
                                <th class="p-4">Jabatan</th>
                                <th class="p-4">No Telepon</th>
                                <th class="p-4">Alamat</th>
                                <th class="p-4 pr-6 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @forelse($teams as $team)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="p-4 pl-6 text-slate-800 font-bold">
                                        {{ $team->name }}
                                    </td>
                                    <td class="p-4">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-100 shadow-sm capitalize">
                                            {{ $team->position }}
                                        </span>
                                    </td>
                                    <td class="p-4 text-slate-600 font-medium">
                                        {{ $team->phone ?? '-' }}
                                    </td>
                                    <td class="p-4 text-slate-505 text-xs max-w-xs truncate">
                                        {{ $team->address ?? '-' }}
                                    </td>
                                    <td class="p-4 pr-6">
                                        <div class="flex items-center justify-end gap-3.5">
                                            <a href="{{ route('master.teams.edit', $team->id) }}" class="text-blue-500 hover:text-blue-700 transition-colors text-lg" title="Edit Anggota">
                                                <i class="fa-regular fa-pen-to-square"></i>
                                            </a>
                                            <form action="{{ route('master.teams.destroy', $team->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus anggota tim ini?');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700 transition-colors text-lg bg-transparent border-none p-0 cursor-pointer" title="Hapus Anggota">
                                                    <i class="fa-regular fa-trash-can"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-12 text-center">
                                        <div class="flex flex-col items-center justify-center text-slate-400">
                                            <i class="fa-solid fa-users-slash text-5xl mb-4 text-slate-300"></i>
                                            <p class="text-base font-semibold">Belum ada anggota tim</p>
                                            <p class="text-xs text-slate-400 mt-1">Silakan tambahkan anggota tim baru.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Footer Copyright from Screenshot -->
                <div class="mt-12 text-center text-xs text-slate-400 font-medium">
                    © 2026 SMK Pasundan 1 Bandung.
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
