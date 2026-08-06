<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-slate-800 tracking-tight">
            {{ __('Data Trial Renang') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-r-lg text-emerald-700 font-medium text-sm flex items-center">
                    <i class="fa-solid fa-circle-check mr-3 text-lg"></i>
                    {{ session('success') }}
                </div>
            @endif

            <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <form action="{{ route('admin.trials.index') }}" method="GET" class="flex flex-wrap items-center gap-3">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama/kontak..." class="rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    <select name="status" class="rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="hadir" {{ request('status') === 'hadir' ? 'selected' : '' }}>Hadir</option>
                        <option value="absen" {{ request('status') === 'absen' ? 'selected' : '' }}>Tidak Hadir</option>
                    </select>
                    <input type="date" name="date" value="{{ request('date') }}" class="rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        Filter
                    </button>
                    @if(request()->anyFilled(['search', 'status', 'date']))
                        <a href="{{ route('admin.trials.index') }}" class="text-sm font-medium text-slate-500 hover:text-slate-700">Reset</a>
                    @endif
                </form>

                <a href="{{ route('admin.trials.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg font-semibold text-sm hover:bg-blue-700 transition shadow-sm flex items-center justify-center gap-2">
                    <i class="fa-solid fa-plus"></i> Tambah Trial
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100 p-6">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Nama (Kontak)</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Jadwal & Lokasi</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Pelatih</th>
                                <th class="px-6 py-3 text-center text-xs font-bold text-slate-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Laporan</th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-slate-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-200">
                            @forelse($trials as $trial)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-medium text-slate-900">{{ $trial->name }}</div>
                                        <div class="text-xs text-slate-500">{{ $trial->age }} Tahun ({{ $trial->gender }})</div>
                                        @if($trial->contact_number)
                                            <div class="text-xs text-blue-500 mt-1"><i class="fa-brands fa-whatsapp"></i> {{ $trial->contact_number }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700">
                                        <div class="font-medium text-blue-600">{{ $trial->schedule_date->format('d M Y') }}</div>
                                        <div class="text-xs text-slate-500">{{ \Carbon\Carbon::parse($trial->schedule_time)->format('H:i') }} - {{ $trial->poolLocation->name ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700">
                                        {{ $trial->coach->name ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if($trial->status === 'hadir')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">Hadir</span>
                                        @elseif($trial->status === 'absen')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-rose-100 text-rose-800">Tidak Hadir</span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">Pending</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-700 max-w-xs truncate" title="{{ $trial->report_note }}">
                                        {{ $trial->report_note ?: '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end gap-3">
                                            <a href="{{ route('admin.trials.edit', $trial->id) }}" class="text-blue-600 hover:text-blue-800 transition-colors" title="Edit Data">
                                                <i class="fa-regular fa-pen-to-square"></i>
                                            </a>
                                            <form action="{{ route('admin.trials.destroy', $trial->id) }}" method="POST" class="inline m-0" onsubmit="return confirm('Yakin ingin menghapus data trial ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700 transition-colors text-base bg-transparent border-none p-0 cursor-pointer" title="Hapus Data">
                                                    <i class="fa-regular fa-trash-can"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                                        <i class="fa-regular fa-folder-open text-3xl mb-3 text-slate-300 block"></i>
                                        Belum ada data trial.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
