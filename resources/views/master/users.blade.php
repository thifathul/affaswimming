<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-slate-800 tracking-tight">
                {{ __('Management Users') }}
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
            
            <!-- Success Alert -->
            @if(session('success'))
                <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-600 text-sm flex items-center gap-2 shadow-sm">
                    <i class="fa-solid fa-circle-check text-emerald-500"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- Error Alert -->
            @if(session('error'))
                <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-600 text-sm flex items-center gap-2 shadow-sm">
                    <i class="fa-solid fa-circle-xmark text-red-500"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- Statistical Role Counter Widgets -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <!-- Total Pengguna -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl p-6 border border-slate-100 flex items-center justify-between">
                    <div>
                        <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Pengguna</div>
                        <div class="mt-2 flex items-baseline gap-2">
                            <span class="text-3xl font-extrabold text-slate-800">{{ $totalUsers }}</span>
                            <span class="text-xs text-slate-500 font-medium">Tergabung</span>
                        </div>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-slate-50 flex items-center justify-center text-slate-500 border border-slate-100">
                        <i class="fa-solid fa-users text-xl"></i>
                    </div>
                </div>
                <!-- Total Admin -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl p-6 border border-slate-100 flex items-center justify-between">
                    <div>
                        <div class="text-xs font-bold text-blue-500 uppercase tracking-wider">Total Admin</div>
                        <div class="mt-2 flex items-baseline gap-2">
                            <span class="text-3xl font-extrabold text-blue-600">{{ $totalAdmin }}</span>
                            <span class="text-xs text-blue-500/80 font-medium">Petugas</span>
                        </div>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-500 border border-blue-100">
                        <i class="fa-solid fa-user-tie text-xl"></i>
                    </div>
                </div>
                <!-- Total Pelatih -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl p-6 border border-slate-100 flex items-center justify-between">
                    <div>
                        <div class="text-xs font-bold text-emerald-500 uppercase tracking-wider">Total Pelatih</div>
                        <div class="mt-2 flex items-baseline gap-2">
                            <span class="text-3xl font-extrabold text-emerald-600">{{ $totalPelatih }}</span>
                            <span class="text-xs text-emerald-500/80 font-medium">Instruktur</span>
                        </div>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-500 border border-emerald-100">
                        <i class="fa-solid fa-person-swimming text-xl"></i>
                    </div>
                </div>
                <!-- Total Murid -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl p-6 border border-slate-100 flex items-center justify-between">
                    <div>
                        <div class="text-xs font-bold text-amber-500 uppercase tracking-wider">Total Murid</div>
                        <div class="mt-2 flex items-baseline gap-2">
                            <span class="text-3xl font-extrabold text-amber-600">{{ $totalMurid }}</span>
                            <span class="text-xs text-amber-500/80 font-medium">Siswa</span>
                        </div>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center text-amber-500 border border-amber-100">
                        <i class="fa-solid fa-graduation-cap text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Users Data Table Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl p-6 border border-slate-100">
                <!-- Table Action Bar -->
                <div class="flex flex-col xl:flex-row justify-between items-start xl:items-center gap-4 mb-8">
                    <div>
                        <h3 class="text-xl font-bold text-slate-800"><i class="fa-solid fa-users mr-2 text-slate-500"></i> Daftar Pengguna Sistem</h3>
                        <p class="text-sm text-slate-500 mt-1">Kelola data login, ubah hak akses, dan pantau status pendaftaran seluruh anggota club.</p>
                    </div>
                    <div class="flex flex-col md:flex-row gap-3 w-full xl:w-auto">
                        <form method="GET" action="{{ route('master.users') }}" class="flex items-center gap-2 w-full md:w-auto">
                            <select name="role" class="rounded-xl border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Semua Jabatan</option>
                                <option value="master" {{ request('role') == 'master' ? 'selected' : '' }}>Master</option>
                                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="pelatih" {{ request('role') == 'pelatih' ? 'selected' : '' }}>Pelatih</option>
                                <option value="murid" {{ request('role') == 'murid' ? 'selected' : '' }}>Murid</option>
                            </select>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama pengguna..." class="rounded-xl border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500 w-full md:w-64">
                            <button type="submit" class="px-4 py-2 bg-blue-50 text-blue-600 rounded-xl hover:bg-blue-100 transition-colors border border-blue-100 flex-shrink-0">
                                <i class="fa-solid fa-search"></i>
                            </button>
                        </form>
                        <a href="{{ route('master.users.create') }}" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-bold text-sm rounded-xl transition-all duration-200 shadow-md shadow-slate-800/20 hover:shadow-lg hover:shadow-slate-800/30 active:scale-95 whitespace-nowrap">
                            <i class="fa-solid fa-user-plus text-xs"></i> Tambah Pengguna Baru
                        </a>
                    </div>
                </div>

                <!-- Table Content -->
                <div class="overflow-x-auto rounded-xl border border-slate-100 bg-white">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-100 bg-slate-50/75 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                <th class="p-4 pl-6">Pengguna</th>
                                <th class="p-4">Email</th>
                                <th class="p-4">Hak Akses & Jabatan</th>
                                <th class="p-4">Tanggal Daftar</th>
                                <th class="p-4 pr-6 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @foreach($users as $user)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <!-- User Column (Avatar & Name) -->
                                    <td class="p-4 pl-6 flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm bg-blue-50 text-blue-600 border border-blue-100 shadow-sm">
                                            {{ strtoupper(substr($user->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <p class="font-bold text-slate-800">{{ $user->name }}</p>
                                            <p class="text-[10px] text-slate-400 font-medium">ID Anggota: AFFA-{{ str_pad($user->id, 4, '0', STR_PAD_LEFT) }}</p>
                                        </div>
                                    </td>
                                    
                                    <!-- Email Column -->
                                    <td class="p-4 text-slate-600 font-medium">
                                        {{ $user->email }}
                                    </td>
                                    
                                    <!-- Role Badge Column -->
                                    <td class="p-4">
                                        @if($user->role === 'master')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-100 shadow-sm">
                                                <i class="fa-solid fa-crown mr-1 text-rose-500"></i> Master
                                            </span>
                                        @elseif($user->role === 'admin')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-100 shadow-sm">
                                                <i class="fa-solid fa-user-tie mr-1 text-blue-500"></i> Admin
                                            </span>
                                        @elseif($user->role === 'pelatih')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100 shadow-sm">
                                                <i class="fa-solid fa-person-swimming mr-1 text-emerald-500"></i> Pelatih
                                            </span>
                                        @elseif($user->role === 'murid')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-100 shadow-sm">
                                                <i class="fa-solid fa-graduation-cap mr-1 text-amber-500"></i> Murid
                                            </span>
                                        @endif

                                        @if($user->position)
                                            <div class="mt-1.5">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                                    <i class="fa-solid fa-briefcase mr-1 text-slate-400"></i> {{ $user->position->name }}
                                                </span>
                                            </div>
                                        @endif
                                    </td>
                                    
                                    <!-- Created At Column -->
                                    <td class="p-4 text-slate-500">
                                        {{ $user->created_at->format('d M Y') }}
                                    </td>
                                    
                                    <!-- Action Buttons -->
                                    <td class="p-4 pr-6">
                                        <div class="flex items-center justify-end gap-3.5">
                                            <a href="{{ route('master.users.edit', $user->id) }}" class="text-blue-500 hover:text-blue-700 transition-colors text-lg" title="Edit Pengguna">
                                                <i class="fa-regular fa-pen-to-square"></i>
                                            </a>
                                            @if(Auth::id() !== $user->id)
                                                <form action="{{ route('master.users.destroy', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-500 hover:text-red-700 transition-colors text-lg" title="Hapus Pengguna">
                                                        <i class="fa-regular fa-trash-can"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-slate-350 cursor-not-allowed text-lg" title="Akun Anda Sedang Aktif">
                                                    <i class="fa-solid fa-lock"></i>
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-4">
                    {{ $users->links() }}
                </div>

                <!-- Footer Copyright from Screenshot -->
                <div class="mt-12 text-center text-xs text-slate-400 font-medium">
                    &copy; {{ date('Y') }} AFFA Swimming.
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
