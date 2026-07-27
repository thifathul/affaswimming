<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.operations.recap') }}" class="text-slate-500 hover:text-slate-700 transition-colors bg-slate-100 hover:bg-slate-200 p-2 rounded-full w-8 h-8 flex items-center justify-center">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <h2 class="font-bold text-2xl text-slate-800 tracking-tight">
                {{ __('Detail Laporan Latihan') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Info Utama -->
                <div class="md:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-bold text-slate-800 border-l-4 border-blue-500 pl-3">Informasi Jadwal</h3>
                            @php
                                $isSubstitute = $trainingReport->coach_id && $trainingReport->coach_id !== $trainingReport->schedule->user_id;
                                $invalRequest = $isSubstitute ? $trainingReport->schedule->scheduleRequests->firstWhere(function($req) use ($trainingReport) {
                                    return $req->type === 'inval' && $req->substitute_coach_id === $trainingReport->coach_id && \Carbon\Carbon::parse($req->proposed_date)->format('Y-m-d') === \Carbon\Carbon::parse($trainingReport->training_date)->format('Y-m-d');
                                }) : null;
                                $locationName = $invalRequest && $invalRequest->proposed_pool_location_id ? $invalRequest->proposedPoolLocation->name : ($trainingReport->schedule->poolLocation->name ?? 'Lokasi Dihapus');
                                $timeString = $invalRequest && $invalRequest->proposed_start_time ? \Carbon\Carbon::parse($invalRequest->proposed_start_time)->format('H:i') : \Carbon\Carbon::parse($trainingReport->schedule->start_time)->format('H:i');
                            @endphp
                            <div class="text-sm font-semibold px-3 py-1 bg-slate-100 text-slate-600 rounded-lg border border-slate-200">
                                {{ \Carbon\Carbon::parse($trainingReport->training_date)->translatedFormat('l, d F Y') }}
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-6">
                            <div class="flex items-start">
                                <div class="bg-blue-50 text-blue-500 p-3 rounded-xl mr-4 flex-shrink-0">
                                    <i class="fa-solid fa-clock text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-400 uppercase font-semibold mb-1">Jam Latihan</p>
                                    <p class="text-slate-800 font-medium">{{ $timeString }} - Selesai</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start">
                                <div class="bg-rose-50 text-rose-500 p-3 rounded-xl mr-4 flex-shrink-0">
                                    <i class="fa-solid fa-location-dot text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-400 uppercase font-semibold mb-1">Lokasi Kolam</p>
                                    <p class="text-slate-800 font-medium">{{ $locationName }}</p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div class="bg-amber-50 text-amber-500 p-3 rounded-xl mr-4 flex-shrink-0">
                                    <i class="fa-solid fa-person-swimming text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-400 uppercase font-semibold mb-1">Pelatih yang Mengajar</p>
                                    <div class="flex items-center gap-2">
                                        <p class="text-slate-800 font-medium">{{ $trainingReport->coach->name ?? 'Pelatih dihapus' }}</p>
                                        @if($isSubstitute)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-700">Inval</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div class="bg-emerald-50 text-emerald-500 p-3 rounded-xl mr-4 flex-shrink-0">
                                    <i class="fa-solid fa-clipboard-user text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-400 uppercase font-semibold mb-1">Status Pelatih</p>
                                    @if($trainingReport->coach_attendance === 'Hadir')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-emerald-100 text-emerald-800">
                                            Hadir
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-red-100 text-red-800">
                                            Tidak Hadir
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Catatan Umum -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex flex-col">
                    <h3 class="text-lg font-bold text-slate-800 border-l-4 border-amber-400 pl-3 mb-4">Catatan Umum Pelatih</h3>
                    <div class="flex-1 bg-slate-50 border border-slate-200 rounded-xl p-4 text-sm text-slate-700 italic">
                        @if($trainingReport->report_note)
                            "{{ $trainingReport->report_note }}"
                        @else
                            <span class="text-slate-400 not-italic flex flex-col items-center justify-center h-full text-center py-4">
                                <i class="fa-regular fa-comment-dots text-3xl mb-2 text-slate-300"></i>
                                Tidak ada catatan umum yang ditambahkan oleh pelatih.
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Tabel Detail Murid -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100">
                <div class="p-6 border-b border-slate-100 bg-slate-50">
                    <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <i class="fa-solid fa-users text-blue-500"></i>
                        Detail Kehadiran & Penilaian Murid
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50/50">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Nama Murid</th>
                                <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Kehadiran</th>
                                <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Pertemuan Ke-</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Penilaian / Catatan Individu</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-200">
                            @forelse($trainingReport->studentAttendances as $attendance)
                                <tr class="hover:bg-slate-50/80 transition-colors {{ $attendance->status === 'Tidak Hadir' ? 'bg-rose-50/30' : '' }}">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-slate-800">
                                        {{ $attendance->student->name ?? 'Murid dihapus' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if($attendance->status === 'Hadir')
                                            <span class="inline-flex items-center justify-center px-3 py-1 rounded text-xs font-bold bg-emerald-100 text-emerald-700">
                                                <i class="fa-solid fa-check mr-1.5"></i> Hadir
                                            </span>
                                        @else
                                            <span class="inline-flex items-center justify-center px-3 py-1 rounded text-xs font-bold bg-red-500 text-white shadow-sm">
                                                <i class="fa-solid fa-xmark mr-1.5"></i> Tidak Hadir
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 text-blue-700 font-bold text-sm">
                                            {{ $attendance->meeting_number }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-600 max-w-md">
                                        @if($attendance->evaluation)
                                            <p class="leading-relaxed">{{ $attendance->evaluation }}</p>
                                        @else
                                            <span class="text-slate-400 italic">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-slate-500">
                                        Tidak ada data murid pada laporan ini.
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
