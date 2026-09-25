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

            <div class="mb-6 flex flex-col gap-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="font-medium text-slate-700 hidden sm:block">Filter Laporan</div>
                    <a href="{{ route('admin.operations.createManualRecap') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg font-semibold text-sm hover:bg-blue-700 transition shadow-sm flex items-center justify-center gap-2 w-full sm:w-auto">
                        <i class="fa-solid fa-plus"></i> Buat Laporan Manual
                    </a>
                </div>
                
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
                    <form action="{{ route('admin.operations.recap') }}" method="GET" class="flex flex-col sm:flex-row gap-4 items-end">
                        
                        <div class="w-full sm:w-auto flex-1">
                            <label for="date" class="block text-sm font-medium text-slate-700 mb-1">Tanggal (Opsional)</label>
                            <input type="date" name="date" id="date" value="{{ $date }}" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <p class="text-[10px] text-slate-500 mt-1">Mengabaikan filter bulan & tahun jika diisi.</p>
                        </div>

                        <div class="w-full sm:w-auto flex-1">
                            <label for="month" class="block text-sm font-medium text-slate-700 mb-1">Bulan</label>
                            <select name="month" id="month" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <option value="">Semua Bulan</option>
                                @foreach(range(1, 12) as $m)
                                    <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}" {{ $month == str_pad($m, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="w-full sm:w-auto flex-1">
                            <label for="year" class="block text-sm font-medium text-slate-700 mb-1">Tahun</label>
                            <select name="year" id="year" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <option value="">Semua Tahun</option>
                                @foreach(range(date('Y') - 2, date('Y') + 1) as $y)
                                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="w-full sm:w-auto flex-1">
                            <label for="coach_id" class="block text-sm font-medium text-slate-700 mb-1">Pelatih</label>
                            <select name="coach_id" id="coach_id" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <option value="">Semua Pelatih</option>
                                @foreach($coaches as $coach)
                                    <option value="{{ $coach->id }}" {{ $coach_id == $coach->id ? 'selected' : '' }}>{{ $coach->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex gap-2 w-full sm:w-auto mt-2 sm:mt-0 pb-[2px]">
                            <button type="submit" class="w-full sm:w-auto bg-slate-800 hover:bg-slate-900 text-white px-6 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm flex items-center justify-center gap-2">
                                <i class="fa-solid fa-filter"></i> Filter
                            </button>
                            @if(request()->hasAny(['date', 'month', 'year', 'coach_id']))
                                <a href="{{ route('admin.operations.recap') }}" class="w-full sm:w-auto bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center justify-center">
                                    Reset
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100 p-6">
                <h3 class="text-lg font-bold text-slate-800 mb-6">
                    @if($date)
                        Laporan Latihan: {{ \Carbon\Carbon::parse($date)->format('d F Y') }}
                    @elseif($month && $year)
                        Laporan Latihan: {{ \Carbon\Carbon::create()->month((int)$month)->translatedFormat('F') }} {{ $year }}
                    @else
                        Seluruh Laporan Latihan
                    @endif
                    @if($coach_id)
                        @php
                            $selectedCoach = $coaches->firstWhere('id', $coach_id);
                        @endphp
                        @if($selectedCoach)
                            <span class="text-sm font-normal text-slate-500 ml-2 block sm:inline mt-1 sm:mt-0">(Pelatih: {{ $selectedCoach->name }})</span>
                        @endif
                    @endif
                </h3>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Pelatih & Lokasi</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Jam Jadwal Asli</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Kehadiran Coach</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Alasan (Jika Absen)</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Kehadiran Murid</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Catatan / Penilaian</th>
                                <th class="px-6 py-3 text-center text-xs font-bold text-slate-500 uppercase">Pertemuan Ke-</th>
                                <th class="px-6 py-3 text-center text-xs font-bold text-slate-500 uppercase">Sisa Billing</th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-slate-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-200">
                            @forelse($reports as $report)
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
                                    
                                    $rowspan = max(1, $report->studentAttendances->count());
                                    $attendances = $report->studentAttendances;
                                @endphp

                                @for($i = 0; $i < $rowspan; $i++)
                                    @php
                                        $attendance = $attendances->isEmpty() ? null : $attendances[$i];
                                    @endphp
                                    <tr class="hover:bg-slate-50 transition-colors {{ $i === $rowspan - 1 ? 'border-b-2 border-slate-200' : '' }}">
                                        @if($i === 0)
                                            <td rowspan="{{ $rowspan }}" class="px-6 py-4 align-top whitespace-nowrap border-r border-slate-100">
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
                                            <td rowspan="{{ $rowspan }}" class="px-6 py-4 align-top whitespace-nowrap text-sm text-slate-700 border-r border-slate-100">
                                                <div class="font-medium text-blue-600">{{ \Carbon\Carbon::parse($report->training_date)->format('d M Y') }}</div>
                                                <div class="text-xs text-slate-500">{{ $report->schedule->day }}, {{ $timeString }}</div>
                                            </td>
                                            <td rowspan="{{ $rowspan }}" class="px-6 py-4 align-top whitespace-nowrap border-r border-slate-100">
                                                @if($report->coach_attendance === 'Hadir')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">Hadir</span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-rose-100 text-rose-800">Tidak Hadir</span>
                                                @endif
                                            </td>
                                            <td rowspan="{{ $rowspan }}" class="px-6 py-4 align-top text-sm text-slate-600 border-r border-slate-100 max-w-[200px]" title="{{ $report->report_note }}">
                                                @if($report->coach_attendance === 'Tidak Hadir')
                                                    <span class="block">{{ $report->report_note ?: '-' }}</span>
                                                @else
                                                    <span class="text-slate-400">-</span>
                                                @endif
                                            </td>
                                        @endif

                                        @if($attendance)
                                            <td class="px-6 py-4 align-top text-sm text-slate-700 border-r border-slate-100 {{ $i !== $rowspan - 1 ? 'border-b border-slate-100' : '' }}">
                                                <div class="flex items-center justify-between gap-4">
                                                    <span class="font-medium">
                                                        {{ $attendance->student->name ?? 'Murid dihapus' }}
                                                    </span>
                                                    @if($attendance->status === 'Hadir')
                                                        <span class="inline-flex items-center justify-center px-2.5 py-1 rounded text-[10px] font-bold bg-emerald-100 text-emerald-700 min-w-max">
                                                            Hadir
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center justify-center px-2.5 py-1 rounded text-[10px] font-bold bg-red-600 text-white min-w-max">
                                                            Tidak Hadir
                                                        </span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 align-top text-sm text-slate-600 border-r border-slate-100 whitespace-normal min-w-[250px] max-w-[300px] {{ $attendance->status === 'Tidak Hadir' ? 'bg-rose-50/50' : '' }} {{ $i !== $rowspan - 1 ? 'border-b border-slate-100' : '' }}">
                                                <div class="leading-relaxed">{{ $attendance->evaluation ?: '-' }}</div>
                                            </td>
                                            <td class="px-6 py-4 align-top text-center text-sm font-bold text-slate-700 border-r border-slate-100 {{ $attendance->status === 'Tidak Hadir' ? 'bg-rose-50/50' : '' }} {{ $i !== $rowspan - 1 ? 'border-b border-slate-100' : '' }}">
                                                {{ $attendance->meeting_number > 0 ? $attendance->meeting_number : '-' }}
                                            </td>
                                            <td class="px-6 py-4 align-top text-center text-sm font-bold border-r border-slate-100 {{ $attendance->status === 'Tidak Hadir' ? 'bg-rose-50/50 text-slate-400' : ($attendance->student->remaining_meetings <= 0 ? 'text-red-600 bg-red-50/50' : 'text-blue-600') }} {{ $i !== $rowspan - 1 ? 'border-b border-slate-100' : '' }}">
                                                {{ $attendance->student->remaining_meetings ?? '-' }}
                                            </td>
                                        @else
                                            <td colspan="4" class="px-6 py-4 text-center text-slate-400 text-sm italic border-r border-slate-100">
                                                Tidak ada data murid
                                            </td>
                                        @endif

                                        @if($i === 0)
                                            <td rowspan="{{ $rowspan }}" class="px-6 py-4 align-top whitespace-nowrap text-right text-sm font-medium">
                                                <div class="flex items-center justify-end gap-3">
                                                    <a href="{{ route('admin.operations.showRecap', $report->id) }}" class="text-blue-600 hover:text-blue-800 transition-colors" title="Lihat Detail">
                                                        <i class="fa-regular fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.operations.editRecap', $report->id) }}" class="text-amber-500 hover:text-amber-700 transition-colors" title="Edit Laporan">
                                                        <i class="fa-solid fa-pen-to-square"></i>
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
                                        @endif
                                    </tr>
                                @endfor
                            @empty
                                <tr>
                                    <td colspan="9" class="px-6 py-12 text-center text-slate-500">
                                        <i class="fa-regular fa-folder-open text-3xl mb-3 text-slate-300 block"></i>
                                        Belum ada laporan latihan{{ $date ? ' untuk tanggal ini' : '' }}.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($reports->hasPages())
                    <div class="mt-6 border-t border-slate-100 pt-4">
                        {{ $reports->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
