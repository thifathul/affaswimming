<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-slate-800 tracking-tight">
            {{ __('Rekap Kehadiran Harian') }}
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
                <form action="{{ route('admin.operations.recap') }}" method="GET" class="flex items-center gap-3">
                    <label for="date" class="text-sm font-medium text-slate-700">Pilih Tanggal:</label>
                    <input type="date" name="date" id="date" value="{{ $date }}" class="rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        Filter
                    </button>
                    @if($date)
                        <a href="{{ route('admin.operations.recap') }}" class="text-sm font-medium text-slate-500 hover:text-slate-700">Reset</a>
                    @endif
                </form>

                <a href="{{ route('admin.operations.createManualRecap') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg font-semibold text-sm hover:bg-blue-700 transition shadow-sm flex items-center justify-center gap-2">
                    <i class="fa-solid fa-plus"></i> Buat Rekap Manual
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100 p-6">
                <h3 class="text-lg font-bold text-slate-800 mb-6">
                    @if($date)
                        Laporan Latihan: {{ \Carbon\Carbon::parse($date)->format('d F Y') }}
                    @else
                        Seluruh Laporan Latihan
                    @endif
                </h3>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Pelatih & Lokasi</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Jam Jadwal Asli</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Kehadiran Coach</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Kehadiran Murid</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Catatan / Penilaian</th>
                                <th class="px-6 py-3 text-center text-xs font-bold text-slate-500 uppercase">Pertemuan Ke-</th>
                                <th class="px-6 py-3 text-center text-xs font-bold text-slate-500 uppercase">Sisa Billing</th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-slate-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-200">
                            @forelse($reports as $report)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    @php
                                        $invalRequest = null;
                                        if($report->coach_id && $report->coach_id !== $report->schedule->user_id) {
                                            $invalRequest = $report->schedule->scheduleRequests->first(function($req) use ($report) {
                                                return $req->type === 'inval' 
                                                    && $req->substitute_coach_id === $report->coach_id 
                                                    && \Carbon\Carbon::parse($req->proposed_date)->format('Y-m-d') === \Carbon\Carbon::parse($report->training_date)->format('Y-m-d');
                                            });
                                        }
                                        $locationName = $invalRequest && $invalRequest->proposed_pool_location_id ? $invalRequest->proposedPoolLocation->name : ($report->schedule->poolLocation->name ?? 'Lokasi tidak diketahui');
                                        $timeString = $invalRequest && $invalRequest->proposed_start_time ? \Carbon\Carbon::parse($invalRequest->proposed_start_time)->format('H:i') : \Carbon\Carbon::parse($report->schedule->start_time)->format('H:i');
                                    @endphp
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($report->coach_id && $report->coach_id !== $report->schedule->user_id)
                                            <div class="font-medium text-slate-900 flex items-center gap-2">
                                                {{ $report->coach->name ?? 'N/A' }}
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-700">Inval</span>
                                            </div>
                                            <div class="text-[10px] text-slate-500 mt-0.5">Menggantikan: {{ $report->schedule->coach->name ?? 'N/A' }}</div>
                                        @else
                                            <div class="font-medium text-slate-900">{{ $report->schedule->coach->name ?? 'N/A' }}</div>
                                        @endif
                                        <div class="text-xs text-slate-500 mt-1">{{ $locationName }} @if($invalRequest && $invalRequest->proposed_pool_location_id) <span class="text-amber-600 font-medium">(Pindah Lokasi)</span> @endif</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700">
                                        <div class="font-medium text-blue-600">{{ \Carbon\Carbon::parse($report->training_date)->format('d M Y') }}</div>
                                        <div class="text-xs text-slate-500">{{ $report->schedule->day }}, {{ $timeString }}</div>
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
                                    <td class="p-0 align-top border-l border-slate-100">
                                        <div class="flex flex-col h-full min-h-full">
                                            @foreach($report->studentAttendances as $attendance)
                                                <div class="px-6 py-4 border-b border-slate-100 last:border-b-0 flex-1 text-center text-sm font-bold text-slate-700 flex items-center justify-center {{ $attendance->status === 'Tidak Hadir' ? 'bg-rose-50/50' : '' }}">
                                                    {{ $attendance->meeting_number > 0 ? $attendance->meeting_number : '-' }}
                                                </div>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="p-0 align-top border-l border-slate-100">
                                        <div class="flex flex-col h-full min-h-full">
                                            @foreach($report->studentAttendances as $attendance)
                                                <div class="px-6 py-4 border-b border-slate-100 last:border-b-0 flex-1 text-center text-sm font-bold flex items-center justify-center {{ $attendance->status === 'Tidak Hadir' ? 'bg-rose-50/50 text-slate-400' : ($attendance->student->remaining_meetings <= 0 ? 'text-red-600 bg-red-50/50' : 'text-blue-600') }}">
                                                    {{ $attendance->student->remaining_meetings ?? '-' }}
                                                </div>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium border-l border-slate-100">
                                        <div class="flex items-center justify-end gap-3">
                                            <a href="{{ route('admin.operations.showRecap', $report->id) }}" class="text-blue-600 hover:text-blue-800 transition-colors" title="Lihat Detail">
                                                <i class="fa-regular fa-eye"></i>
                                            </a>
                                            <form action="{{ route('admin.operations.destroyRecap', $report->id) }}" method="POST" class="inline m-0" onsubmit="return confirm('Yakin ingin menghapus laporan kehadiran ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700 transition-colors text-base bg-transparent border-none p-0 cursor-pointer" title="Hapus Laporan">
                                                    <i class="fa-regular fa-trash-can"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                                        <i class="fa-regular fa-folder-open text-3xl mb-3 text-slate-300 block"></i>
                                        Belum ada laporan latihan{{ $date ? ' untuk tanggal ini' : '' }}.
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
