<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-slate-800 tracking-tight">
                {{ __('Manajemen Murid') }}
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

            <!-- Statistical Widgets -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Total Murid -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl p-6 border border-slate-100 flex items-center justify-between">
                    <div>
                        <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Murid</div>
                        <div class="mt-2 flex items-baseline gap-2">
                            <span class="text-3xl font-extrabold text-slate-800">{{ $students->count() }}</span>
                            <span class="text-xs text-slate-500 font-medium">Siswa Terdaftar</span>
                        </div>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-slate-50 flex items-center justify-center text-slate-500 border border-slate-100">
                        <i class="fa-solid fa-user-graduate text-xl"></i>
                    </div>
                </div>
                <!-- Murid dengan Akun (Aktif) -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl p-6 border border-slate-100 flex items-center justify-between">
                    <div>
                        <div class="text-xs font-bold text-blue-500 uppercase tracking-wider">Murid Dengan Akun</div>
                        <div class="mt-2 flex items-baseline gap-2">
                            <span class="text-3xl font-extrabold text-blue-600">{{ $students->whereNotNull('user_id')->where('status', 'aktif')->count() }}</span>
                            <span class="text-xs text-blue-500/80 font-medium">Aktif & Bisa Login</span>
                        </div>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-500 border border-blue-100">
                        <i class="fa-solid fa-shield-halved text-xl"></i>
                    </div>
                </div>
                <!-- Murid tanpa Akun (Atau Nonaktif) -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl p-6 border border-slate-100 flex items-center justify-between">
                    <div>
                        <div class="text-xs font-bold text-amber-500 uppercase tracking-wider">Murid Tanpa Akun / Nonaktif</div>
                        <div class="mt-2 flex items-baseline gap-2">
                            <span class="text-3xl font-extrabold text-amber-600">{{ $students->filter(fn($s) => is_null($s->user_id) || $s->status === 'nonaktif')->count() }}</span>
                            <span class="text-xs text-amber-500/80 font-medium">Tidak Bisa Login</span>
                        </div>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center text-amber-500 border border-amber-100">
                        <i class="fa-solid fa-circle-question text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Table Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl p-6 border border-slate-100">
                
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
                    <div>
                        <h3 class="text-xl font-bold text-slate-800"><i class="fa-solid fa-user-graduate mr-2 text-slate-500"></i> Daftar Murid</h3>
                        <p class="text-sm text-slate-500 mt-1">Kelola informasi murid, nama wali murid/orang tua, nomor kontak, serta hubungkan ke akun login.</p>
                    </div>
                    <div class="flex flex-wrap items-center justify-start md:justify-end gap-3 w-full md:w-auto mt-4 md:mt-0">
                        <form action="{{ route('master.students.import') }}" method="POST" enctype="multipart/form-data" class="flex items-center gap-2 bg-slate-50 border border-slate-200 rounded-xl p-1 pr-2">
                            @csrf
                            <input type="file" name="import_file" accept=".csv" required class="text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-white file:text-slate-700 file:shadow-sm hover:file:bg-slate-50 cursor-pointer w-[180px]">
                            <button type="submit" class="inline-flex items-center justify-center w-8 h-8 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition-all shadow-sm flex-shrink-0" title="Import Data">
                                <i class="fa-solid fa-upload"></i>
                            </button>
                        </form>
                        <a href="{{ route('master.students.import.template') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold text-sm rounded-xl transition-all shadow-sm" title="Download Template CSV">
                            <i class="fa-solid fa-file-csv text-blue-600"></i> Template
                        </a>
                        <a href="{{ route('master.students.create') }}" class="inline-flex items-center gap-2 px-5 py-2 bg-slate-800 hover:bg-slate-900 text-white font-bold text-sm rounded-xl transition-all duration-200 shadow-md shadow-slate-800/20 hover:shadow-lg hover:shadow-slate-800/30 active:scale-95">
                            <i class="fa-solid fa-user-plus text-xs"></i> Tambah Murid
                        </a>
                    </div>
                </div>
                
                <div class="overflow-x-auto rounded-xl border border-slate-100 bg-white">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-100 bg-slate-50/75 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                <th class="p-4 pl-6">Nama Siswa</th>
                                <th class="p-4">Tempat, Tgl Lahir</th>
                                <th class="p-4">Usia</th>
                                <th class="p-4">Sekolah</th>
                                <th class="p-4">Kelas Berenang</th>
                                <th class="p-4">Pelatih</th>
                                <th class="p-4">Status & Akun Login</th>
                                <th class="p-4 pr-6 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @forelse($students as $student)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <!-- Student Name Column -->
                                    <td class="p-4 pl-6">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-xs bg-blue-50 text-blue-600 border border-blue-100 shadow-sm">
                                                {{ strtoupper(substr($student->name, 0, 2)) }}
                                            </div>
                                            <div>
                                                <div class="flex items-center gap-2">
                                                    <p class="font-bold text-slate-800 leading-snug">{{ $student->name }}</p>
                                                    @if($student->gender)
                                                        <i class="fa-solid {{ $student->gender === 'Laki-laki' ? 'fa-mars text-blue-500' : 'fa-venus text-pink-500' }}" title="{{ $student->gender }}"></i>
                                                    @endif
                                                </div>
                                                <p class="text-[10px] text-slate-450 font-medium">ID Murid: AFFA-M-{{ str_pad($student->id, 4, '0', STR_PAD_LEFT) }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <!-- Tempat Tgl Lahir Column -->
                                    <td class="p-4 text-slate-700 font-medium">
                                        {{ $student->birth_place_date ?? '-' }}
                                    </td>
                                    <!-- Usia Column -->
                                    <td class="p-4 text-slate-600 font-semibold">
                                        {{ $student->age ? $student->age . ' Tahun' : '-' }}
                                    </td>
                                    <!-- Sekolah Column -->
                                    <td class="p-4 text-slate-500 text-xs max-w-xs truncate" title="{{ $student->school }}">
                                        {{ $student->school ?? '-' }}
                                    </td>
                                    <!-- Classes Column -->
                                    <td class="p-4">
                                        @if($student->swimClasses->count() > 0)
                                            <div class="flex flex-wrap gap-1 max-w-[180px]">
                                                @foreach($student->swimClasses as $class)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-cyan-50 text-cyan-700 border border-cyan-100" title="{{ $class->schedule ?? 'Belum ada jadwal' }}">
                                                        <i class="fa-solid fa-water mr-1"></i> {{ $class->name }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-slate-400 text-xs italic">Belum plotting kelas</span>
                                        @endif
                                    </td>
                                    <!-- Coach Column -->
                                    <td class="p-4">
                                        @if($student->schedules->count() > 0)
                                            <div class="flex flex-col gap-1 max-w-[180px]">
                                                @foreach($student->schedules->unique('user_id') as $schedule)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                                        <i class="fa-solid fa-person-swimming mr-1"></i> {{ $schedule->coach->name ?? 'N/A' }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-slate-400 text-xs italic">-</span>
                                        @endif
                                    </td>
                                    <!-- User Login & Status Column -->
                                    <td class="p-4">
                                        <div class="flex flex-col gap-1.5">
                                            @if($student->status === 'aktif')
                                                <span class="inline-flex items-center w-fit px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    <i class="fa-solid fa-check-circle mr-1"></i> Aktif
                                                </span>
                                            @else
                                                <span class="inline-flex items-center w-fit px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                                    <i class="fa-solid fa-ban mr-1"></i> Nonaktif
                                                </span>
                                            @endif

                                            @if($student->user)
                                                <div class="flex flex-col">
                                                    <span class="inline-flex items-center w-fit px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-100 mt-1">
                                                        <i class="fa-solid fa-link mr-1"></i> Tertaut
                                                    </span>
                                                    <span class="text-[10px] text-slate-400 mt-1 font-medium">{{ $student->user->email }}</span>
                                                </div>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-500 border border-slate-200 mt-1 w-fit">
                                                    Belum Ditautkan
                                                </span>
                                            @endif
                                            
                                            @if($student->schedules->count() > 0)
                                                <span class="inline-flex items-center w-fit px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200 mt-1">
                                                    <i class="fa-solid fa-calendar-check mr-1"></i> Di-assign
                                                </span>
                                            @else
                                                <span class="inline-flex items-center w-fit px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-500/10 text-red-700 border border-red-500/20 mt-1">
                                                    <i class="fa-solid fa-calendar-xmark mr-1"></i> Belum Di-assign
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <!-- Action Column -->
                                    <td class="p-4 pr-6">
                                        <div class="flex items-center justify-end gap-3.5">
                                            <form action="{{ route('master.students.toggle-status', $student->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="{{ $student->status === 'aktif' ? 'text-amber-500 hover:text-amber-700' : 'text-emerald-500 hover:text-emerald-700' }} transition-colors text-lg bg-transparent border-none p-0 cursor-pointer" title="{{ $student->status === 'aktif' ? 'Nonaktifkan Murid' : 'Aktifkan Murid' }}">
                                                    <i class="fa-solid {{ $student->status === 'aktif' ? 'fa-lock' : 'fa-lock-open' }}"></i>
                                                </button>
                                            </form>
                                            <a href="{{ route('master.students.edit', $student->id) }}" class="text-blue-500 hover:text-blue-700 transition-colors text-lg" title="Edit Data Murid">
                                                <i class="fa-regular fa-pen-to-square"></i>
                                            </a>
                                            <form action="{{ route('master.students.destroy', $student->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data murid ini?');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700 transition-colors text-lg bg-transparent border-none p-0 cursor-pointer" title="Hapus Data Murid">
                                                    <i class="fa-regular fa-trash-can"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="p-12 text-center">
                                        <div class="flex flex-col items-center justify-center text-slate-400">
                                            <i class="fa-solid fa-users-slash text-5xl mb-4 text-slate-300"></i>
                                            <p class="text-base font-semibold">Belum ada data murid</p>
                                            <p class="text-xs text-slate-400 mt-1">Silakan tambahkan data murid baru untuk memulai.</p>
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
