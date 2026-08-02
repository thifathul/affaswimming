<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-slate-800 tracking-tight">
                {{ __('Jadwal Anda') }}
            </h2>
            <div class="flex items-center gap-3">
                <button onclick="openCreateModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold shadow-sm transition-colors flex items-center gap-2">
                    <i class="fa-solid fa-plus"></i> Input Jadwal Kosong
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-r-lg text-emerald-700 font-medium text-sm flex items-center">
                    <i class="fa-solid fa-circle-check mr-3 text-lg"></i>
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100 mb-8">
                @php
                    $daysMap = [
                        'Sunday' => 'Minggu',
                        'Monday' => 'Senin',
                        'Tuesday' => 'Selasa',
                        'Wednesday' => 'Rabu',
                        'Thursday' => 'Kamis',
                        'Friday' => 'Jumat',
                        'Saturday' => 'Sabtu',
                    ];
                    $todayName = $daysMap[now()->format('l')];
                    $todaySchedules = $schedules->filter(function($s) use ($todayName) {
                        return strtolower($s->day) === strtolower($todayName);
                    });
                @endphp

                <div class="p-6 border-b border-slate-100 bg-emerald-50/50">
                    <h3 class="text-lg font-bold text-emerald-800">Jadwal Hari Ini ({{ $todayName }})</h3>
                    <p class="text-sm text-emerald-600">Berikut adalah jadwal Anda untuk hari ini.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6 bg-slate-50/20">
                    @forelse($todaySchedules as $schedule)
                        <div class="bg-white border {{ $schedule->status === 'available' ? 'border-emerald-200' : 'border-blue-200' }} rounded-xl shadow-sm overflow-hidden hover:shadow-md transition-shadow relative">
                            <div class="{{ $schedule->status === 'available' ? 'bg-emerald-50 text-emerald-800' : 'bg-blue-50 text-blue-800' }} px-4 py-3 border-b {{ $schedule->status === 'available' ? 'border-emerald-100' : 'border-blue-100' }} flex justify-between items-center">
                                <span class="font-bold text-sm uppercase tracking-wide">{{ $schedule->day }}</span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold {{ $schedule->status === 'available' ? 'bg-emerald-200 text-emerald-800' : 'bg-blue-200 text-blue-800' }}">
                                    {{ ucfirst($schedule->status) }}
                                </span>
                                @if($schedule->user_id !== auth()->id() && $schedule->scheduleRequests->isNotEmpty())
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-purple-200 text-purple-800 ml-2">
                                        Inval ({{ \Carbon\Carbon::parse($schedule->scheduleRequests->first()->proposed_date)->format('d/m/Y') }})
                                    </span>
                                @endif
                            </div>
                            <div class="p-4">
                                @php
                                    $invalRequest = $schedule->user_id !== auth()->id() && $schedule->scheduleRequests->isNotEmpty() ? $schedule->scheduleRequests->first() : null;
                                @endphp
                                <div class="flex items-center text-slate-700 mb-3 font-medium">
                                    <i class="fa-regular fa-clock mr-2 text-slate-400"></i>
                                    @if($invalRequest && $invalRequest->proposed_start_time)
                                        <span class="text-amber-600">{{ \Carbon\Carbon::parse($invalRequest->proposed_start_time)->format('H:i') }} - selesai</span>
                                    @else
                                        {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                                    @endif
                                </div>
                                
                                @if($schedule->status === 'booked')
                                    <div class="mt-4 pt-4 border-t border-slate-100">
                                        <div class="flex items-start text-sm text-slate-600 mb-2">
                                            <i class="fa-solid fa-location-dot mt-1 mr-2 text-rose-500"></i>
                                            @php
                                                $locationName = $invalRequest && $invalRequest->proposed_pool_location_id ? $invalRequest->proposedPoolLocation->name : ($schedule->poolLocation->name ?? 'Lokasi Dihapus');
                                                $locationLabel = $invalRequest && $invalRequest->proposed_pool_location_id ? 'Pindah Lokasi Inval' : 'Lokasi';
                                            @endphp
                                            <span><strong>{{ $locationLabel }}:</strong> {{ $locationName }}</span>
                                        </div>
                                        <div class="flex items-start text-sm text-slate-600">
                                            <i class="fa-solid fa-users mt-1 mr-2 text-blue-500"></i>
                                            <div>
                                                <strong>Murid:</strong>
                                                <div class="flex flex-wrap gap-2 mt-2">
                                                    @foreach($schedule->students as $student)
                                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                                            <i class="fa-solid fa-child-reaching mr-1.5 text-indigo-400"></i>
                                                            {{ $student->name }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-4 pt-4 border-t border-slate-100 flex gap-2">
                                            @if(in_array($schedule->id, $submittedScheduleIds))
                                                <div class="flex-1 text-center bg-slate-100 text-slate-400 px-3 py-2 rounded-lg text-sm font-medium cursor-not-allowed border border-slate-200" title="Laporan sudah disubmit hari ini">
                                                    <i class="fa-solid fa-check-double mr-1"></i> Selesai
                                                </div>
                                            @else
                                                <a href="{{ route('pelatih.reports.create', $schedule) }}" class="flex-1 text-center bg-blue-50 hover:bg-blue-100 text-blue-700 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                                                    <i class="fa-solid fa-clipboard-check mr-1"></i> Laporan
                                                </a>
                                            @endif
                                            @if($schedule->user_id === auth()->id())
                                                @if(in_array($schedule->id, $submittedScheduleIds))
                                                    @php
                                                        $reportToday = $reportsToday->where('schedule_id', $schedule->id)->first();
                                                        $absentCount = $reportToday ? $reportToday->studentAttendances->where('status', 'Tidak Hadir')->count() : 0;
                                                    @endphp
                                                    @if($absentCount > 0)
                                                        <a href="{{ route('pelatih.requests.createAbsent', $schedule) }}" class="flex-1 text-center bg-orange-100 hover:bg-orange-200 text-orange-700 px-3 py-2 rounded-lg text-sm font-medium transition-colors" title="Reschedule khusus murid absen hari ini">
                                                            <i class="fa-solid fa-user-clock mr-1"></i> Ganti Jadwal Absen
                                                        </a>
                                                    @else
                                                        <div class="flex-1 text-center bg-slate-100 text-slate-400 px-3 py-2 rounded-lg text-sm font-medium cursor-not-allowed border border-slate-200" title="Tidak dapat reschedule setelah laporan disubmit">
                                                            <i class="fa-solid fa-calendar-xmark mr-1"></i> Reschedule
                                                        </div>
                                                    @endif
                                                @else
                                                    <a href="{{ route('pelatih.requests.create', $schedule) }}" class="flex-1 text-center bg-orange-50 hover:bg-orange-100 text-orange-700 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                                                        <i class="fa-solid fa-calendar-xmark mr-1"></i> Reschedule
                                                    </a>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <div class="mt-4 pt-4 border-t border-slate-100 text-center flex flex-col gap-2">
                                        <span class="text-xs text-slate-400 italic">Belum ada murid.</span>
                                        @if($schedule->user_id === auth()->id())
                                            <button type="button" onclick="openDeleteModal({{ $schedule->id }})" class="w-full text-center bg-red-50 hover:bg-red-100 text-red-700 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                                                <i class="fa-solid fa-trash mr-1"></i> Minta Hapus
                                            </button>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full bg-white border border-slate-200 rounded-xl p-8 text-center text-slate-500">
                            <i class="fa-regular fa-calendar text-4xl mb-3 text-slate-300"></i>
                            <p>Tidak ada jadwal untuk hari ini.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100 mb-8">
                <div class="p-6 border-b border-slate-100 bg-blue-50/50 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-bold text-blue-800">Jadwal Kosong Anda</h3>
                        <p class="text-sm text-blue-600">Berikut adalah blok waktu ketersediaan (jadwal kosong) yang telah Anda daftarkan.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6 bg-slate-50/20">
                    @forelse($availabilities as $avail)
                        <div class="bg-white border border-blue-200 rounded-xl shadow-sm overflow-hidden hover:shadow-md transition-shadow relative">
                            <div class="bg-blue-50 text-blue-800 px-4 py-3 border-b border-blue-100 flex justify-between items-center">
                                <span class="font-bold text-sm uppercase tracking-wide">{{ $avail->day }}</span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-blue-200 text-blue-800">
                                    Blok Waktu
                                </span>
                            </div>
                            <div class="p-4">
                                <div class="flex items-center text-slate-700 font-medium">
                                    <i class="fa-regular fa-clock mr-2 text-slate-400"></i>
                                    {{ \Carbon\Carbon::parse($avail->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($avail->end_time)->format('H:i') }}
                                </div>
                                <div class="mt-4 pt-4 border-t border-slate-100 flex flex-col gap-2 text-center">
                                    <span class="text-xs text-slate-400 italic">Menunggu admin mengatur sesi kelas pada jadwal ini.</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full bg-white border border-slate-200 rounded-xl p-8 text-center text-slate-500">
                            <i class="fa-regular fa-calendar-plus text-4xl mb-3 text-slate-300"></i>
                            <p>Anda belum menginput jadwal kosong.</p>
                            <button onclick="openCreateModal()" class="mt-4 text-blue-600 font-medium hover:underline">Input Jadwal Sekarang</button>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100">
                <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                    <h3 class="text-lg font-bold text-slate-800">Keseluruhan Jadwal</h3>
                    <p class="text-sm text-slate-500">Berikut adalah semua jadwal yang telah Anda input.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6 bg-slate-50/20">
                    @forelse($schedules as $schedule)
                        <div class="bg-white border {{ $schedule->status === 'available' ? 'border-emerald-200' : 'border-blue-200' }} rounded-xl shadow-sm overflow-hidden hover:shadow-md transition-shadow relative">
                            <div class="{{ $schedule->status === 'available' ? 'bg-emerald-50 text-emerald-800' : 'bg-blue-50 text-blue-800' }} px-4 py-3 border-b {{ $schedule->status === 'available' ? 'border-emerald-100' : 'border-blue-100' }} flex justify-between items-center">
                                <span class="font-bold text-sm uppercase tracking-wide">{{ $schedule->day }}</span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold {{ $schedule->status === 'available' ? 'bg-emerald-200 text-emerald-800' : 'bg-blue-200 text-blue-800' }}">
                                    {{ ucfirst($schedule->status) }}
                                </span>
                                @if($schedule->user_id !== auth()->id() && $schedule->scheduleRequests->isNotEmpty())
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-purple-200 text-purple-800 ml-2">
                                        Inval ({{ \Carbon\Carbon::parse($schedule->scheduleRequests->first()->proposed_date)->format('d/m/Y') }})
                                    </span>
                                @endif
                            </div>
                            <div class="p-4">
                                @php
                                    $invalRequest = $schedule->user_id !== auth()->id() && $schedule->scheduleRequests->isNotEmpty() ? $schedule->scheduleRequests->first() : null;
                                @endphp
                                <div class="flex items-center text-slate-700 mb-3 font-medium">
                                    <i class="fa-regular fa-clock mr-2 text-slate-400"></i>
                                    @if($invalRequest && $invalRequest->proposed_start_time)
                                        <span class="text-amber-600">{{ \Carbon\Carbon::parse($invalRequest->proposed_start_time)->format('H:i') }} - selesai</span>
                                    @else
                                        {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                                    @endif
                                </div>
                                
                                @if($schedule->status === 'booked')
                                    <div class="mt-4 pt-4 border-t border-slate-100">
                                        <div class="flex items-start text-sm text-slate-600 mb-2">
                                            <i class="fa-solid fa-location-dot mt-1 mr-2 text-rose-500"></i>
                                            @php
                                                $locationName = $invalRequest && $invalRequest->proposed_pool_location_id ? $invalRequest->proposedPoolLocation->name : ($schedule->poolLocation->name ?? 'Lokasi Dihapus');
                                                $locationLabel = $invalRequest && $invalRequest->proposed_pool_location_id ? 'Pindah Lokasi Inval' : 'Lokasi';
                                            @endphp
                                            <span><strong>{{ $locationLabel }}:</strong> {{ $locationName }}</span>
                                        </div>
                                        <div class="flex items-start text-sm text-slate-600">
                                            <i class="fa-solid fa-users mt-1 mr-2 text-blue-500"></i>
                                            <div>
                                                <strong>Murid:</strong>
                                                <div class="flex flex-wrap gap-2 mt-2">
                                                    @foreach($schedule->students as $student)
                                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                                            <i class="fa-solid fa-child-reaching mr-1.5 text-indigo-400"></i>
                                                            {{ $student->name }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-4 pt-4 border-t border-slate-100 flex gap-2">
                                            @if(in_array($schedule->id, $submittedScheduleIds))
                                                <div class="flex-1 text-center bg-slate-100 text-slate-400 px-3 py-2 rounded-lg text-sm font-medium cursor-not-allowed border border-slate-200" title="Laporan sudah disubmit hari ini">
                                                    <i class="fa-solid fa-check-double mr-1"></i> Selesai
                                                </div>
                                            @elseif(strtolower($schedule->day) !== strtolower($todayName))
                                                <div class="flex-1 text-center bg-slate-100 text-slate-400 px-3 py-2 rounded-lg text-sm font-medium cursor-not-allowed border border-slate-200" title="Laporan hanya bisa diisi pada hari {{ $schedule->day }}">
                                                    <i class="fa-solid fa-clipboard-check mr-1"></i> Laporan
                                                </div>
                                            @else
                                                <a href="{{ route('pelatih.reports.create', $schedule) }}" class="flex-1 text-center bg-blue-50 hover:bg-blue-100 text-blue-700 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                                                    <i class="fa-solid fa-clipboard-check mr-1"></i> Laporan
                                                </a>
                                            @endif
                                            @if($schedule->user_id === auth()->id())
                                                @if(in_array($schedule->id, $submittedScheduleIds))
                                                    <div class="flex-1 text-center bg-slate-100 text-slate-400 px-3 py-2 rounded-lg text-sm font-medium cursor-not-allowed border border-slate-200" title="Tidak dapat reschedule setelah laporan disubmit">
                                                        <i class="fa-solid fa-calendar-xmark mr-1"></i> Reschedule
                                                    </div>
                                                @else
                                                    <a href="{{ route('pelatih.requests.create', $schedule) }}" class="flex-1 text-center bg-orange-50 hover:bg-orange-100 text-orange-700 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                                                        <i class="fa-solid fa-calendar-xmark mr-1"></i> Reschedule
                                                    </a>
                                                @endif
                                                <button type="button" onclick="openDeleteModal({{ $schedule->id }})" class="flex-1 text-center bg-red-50 hover:bg-red-100 text-red-700 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                                                    <i class="fa-solid fa-trash mr-1"></i> Hapus
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <div class="mt-4 pt-4 border-t border-slate-100 text-center flex flex-col gap-2">
                                        <span class="text-xs text-slate-400 italic">Belum ada murid.</span>
                                        @if($schedule->user_id === auth()->id())
                                            <button type="button" onclick="openDeleteModal({{ $schedule->id }})" class="w-full text-center bg-red-50 hover:bg-red-100 text-red-700 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                                                <i class="fa-solid fa-trash mr-1"></i> Minta Hapus
                                            </button>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full bg-white border border-slate-200 rounded-xl p-8 text-center text-slate-500">
                            <i class="fa-regular fa-calendar-plus text-4xl mb-3 text-slate-300"></i>
                            <p>Anda belum menginput jadwal kosong.</p>
                            <button onclick="openCreateModal()" class="mt-4 text-blue-600 font-medium hover:underline">Input Jadwal Sekarang</button>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Input Jadwal -->
    <div id="createModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-slate-900 bg-opacity-75 transition-opacity backdrop-blur-sm" aria-hidden="true" onclick="closeCreateModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-100">
                <form method="POST" action="{{ route('pelatih.schedules.store') }}">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                                <i class="fa-regular fa-calendar-plus text-blue-600"></i>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-bold text-slate-900" id="modal-title">
                                    Input Jadwal Kosong
                                </h3>
                                <div class="mt-4 space-y-4">
                                    <div>
                                        <label for="day" class="block text-sm font-medium text-slate-700">Hari</label>
                                        <select id="day" name="day" required class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                            <option value="Senin">Senin</option>
                                            <option value="Selasa">Selasa</option>
                                            <option value="Rabu">Rabu</option>
                                            <option value="Kamis">Kamis</option>
                                            <option value="Jumat">Jumat</option>
                                            <option value="Sabtu">Sabtu</option>
                                            <option value="Minggu">Minggu</option>
                                        </select>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label for="start_time" class="block text-sm font-medium text-slate-700">Jam Mulai</label>
                                            <input type="time" name="start_time" id="start_time" required class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                        </div>
                                        <div>
                                            <label for="end_time" class="block text-sm font-medium text-slate-700">Jam Selesai</label>
                                            <input type="time" name="end_time" id="end_time" required class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-slate-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-slate-100">
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                            Simpan Jadwal
                        </button>
                        <button type="button" onclick="closeCreateModal()" class="mt-3 w-full inline-flex justify-center rounded-lg border border-slate-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Request Hapus Jadwal -->
    <div id="deleteModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-slate-900 bg-opacity-75 transition-opacity backdrop-blur-sm" aria-hidden="true" onclick="closeDeleteModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-100">
                <form id="deleteForm" method="POST" action="">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <i class="fa-solid fa-trash text-red-600"></i>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-bold text-slate-900" id="modal-title">
                                    Minta Hapus Jadwal
                                </h3>
                                <div class="mt-4 space-y-4">
                                    <div>
                                        <label for="reason" class="block text-sm font-medium text-slate-700">Alasan Penghapusan</label>
                                        <textarea id="reason" name="reason" rows="3" required class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm" placeholder="Jelaskan alasan mengapa jadwal ini ingin dihapus..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-slate-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-slate-100">
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                            Kirim Permintaan
                        </button>
                        <button type="button" onclick="closeDeleteModal()" class="mt-3 w-full inline-flex justify-center rounded-lg border border-slate-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openCreateModal() {
            document.getElementById('createModal').classList.remove('hidden');
        }

        function closeCreateModal() {
            document.getElementById('createModal').classList.add('hidden');
        }

        function openDeleteModal(scheduleId) {
            const form = document.getElementById('deleteForm');
            form.action = `/pelatih/schedules/${scheduleId}/request-delete`;
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }
    </script>
</x-app-layout>
