<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-slate-800 tracking-tight">
            {{ __('Riwayat Laporan Kehadiran Saya') }}
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
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Tanggal & Waktu</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Lokasi</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Kehadiran Saya</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Kehadiran Murid</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Penilaian Murid</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Catatan</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-200">
                            @forelse($reports as $report)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    @php
                                        $invalRequest = null;
                                        if($report->coach_id !== $report->schedule->user_id) {
                                            $invalRequest = $report->schedule->scheduleRequests->first(function($req) use ($report) {
                                                return $req->type === 'inval' 
                                                    && $req->substitute_coach_id === $report->coach_id 
                                                    && \Carbon\Carbon::parse($req->proposed_date)->format('Y-m-d') === \Carbon\Carbon::parse($report->training_date)->format('Y-m-d');
                                            });
                                        }
                                        $locationName = $invalRequest && $invalRequest->proposed_pool_location_id ? $invalRequest->proposedPoolLocation->name : ($report->schedule->poolLocation->name ?? 'Lokasi tidak diketahui');
                                        $timeString = $invalRequest && $invalRequest->proposed_start_time ? \Carbon\Carbon::parse($invalRequest->proposed_start_time)->format('H:i') : \Carbon\Carbon::parse($report->schedule->start_time)->format('H:i');
                                    @endphp
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700">
                                        <div class="font-medium text-blue-600">{{ \Carbon\Carbon::parse($report->training_date)->format('d M Y') }}</div>
                                        <div class="text-xs text-slate-500">{{ $report->schedule->day }}, {{ $timeString }}</div>
                                        @if($report->coach_id !== $report->schedule->user_id)
                                            <div class="mt-1 inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-700">Inval</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-slate-900">{{ $locationName }}</div>
                                        @if($invalRequest && $invalRequest->proposed_pool_location_id)
                                            <div class="text-[10px] text-amber-600 mt-1">Pindah Lokasi</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($report->coach_attendance === 'Hadir')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">Hadir</span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-rose-100 text-rose-800">Tidak Hadir</span>
                                        @endif
                                    </td>
                                    <td class="p-0 align-top border-l border-slate-100 min-w-[250px]">
                                        <div class="flex flex-col h-full min-h-full">
                                            @foreach($report->studentAttendances as $attendance)
                                                <div class="px-6 py-4 border-b border-slate-100 last:border-b-0 flex-1 whitespace-nowrap text-sm flex justify-between items-center text-slate-700">
                                                    <span class="font-medium flex items-center gap-2">
                                                        {{ $attendance->student->name ?? 'Murid dihapus' }}
                                                        <span class="text-xs text-slate-400 font-normal">
                                                            (Pertemuan {{ $attendance->meeting_number }})
                                                        </span>
                                                    </span>
                                                    @if($attendance->status === 'Hadir')
                                                        <span class="ml-4 inline-flex items-center justify-center px-2.5 py-1 rounded text-[10px] font-bold bg-emerald-100 text-emerald-700 min-w-max">
                                                            Hadir
                                                        </span>
                                                    @else
                                                        <span class="ml-4 inline-flex items-center justify-center px-2.5 py-1 rounded text-[10px] font-bold bg-red-600 text-white min-w-max">
                                                            Tidak Hadir
                                                        </span>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="p-0 align-top border-l border-slate-100">
                                        <div class="flex flex-col h-full min-h-full">
                                            @foreach($report->studentAttendances as $attendance)
                                                <div class="px-6 py-4 border-b border-slate-100 last:border-b-0 flex-1 text-sm text-slate-600 flex items-center max-w-[200px] {{ $attendance->status === 'Tidak Hadir' ? 'bg-rose-50/50' : '' }}" title="{{ $attendance->evaluation ?: '-' }}">
                                                    <span class="truncate">{{ $attendance->evaluation ?: '-' }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-600 max-w-xs truncate" title="{{ $report->report_note }}">
                                        {{ $report->report_note ?: '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                                        <i class="fa-regular fa-folder-open text-3xl mb-3 text-slate-300 block"></i>
                                        Anda belum pernah membuat laporan kehadiran.
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
