<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-slate-800 tracking-tight">
            {{ __('Jadwal Trial Renang') }}
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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100 p-6">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Nama Murid</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Jadwal & Lokasi</th>
                                <th class="px-6 py-3 text-center text-xs font-bold text-slate-500 uppercase">Status Kehadiran</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Catatan Laporan</th>
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
                                        <a href="{{ route('pelatih.trials.report', $trial->id) }}" class="inline-flex items-center px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg font-medium hover:bg-blue-100 transition-colors">
                                            @if($trial->status === 'pending')
                                                <i class="fa-solid fa-file-pen mr-2"></i> Isi Laporan
                                            @else
                                                <i class="fa-solid fa-pen-to-square mr-2"></i> Edit Laporan
                                            @endif
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                                        <i class="fa-regular fa-calendar-xmark text-3xl mb-3 text-slate-300 block"></i>
                                        Tidak ada jadwal trial saat ini.
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
