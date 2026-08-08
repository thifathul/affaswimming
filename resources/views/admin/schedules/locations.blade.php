<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-slate-800 tracking-tight">
                {{ __('Jadwal Lokasi') }}
            </h2>
            <div class="flex items-center gap-3">
                <span class="px-4 py-2 rounded-full border border-slate-200 bg-slate-50 text-xs font-semibold text-slate-500 shadow-sm flex items-center gap-1.5">
                    <i class="fa-regular fa-calendar"></i>
                    {{ now()->format('l, d F Y') }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6 bg-white p-4 rounded-2xl border border-slate-100 shadow-sm">
                <form method="GET" action="{{ route('admin.schedules.locations') }}" class="flex items-center gap-4">
                    <div class="flex-1 max-w-sm">
                        <label for="pool_location_id" class="block text-xs font-medium text-slate-500 mb-1">Filter Kolam / Lokasi</label>
                        <select name="pool_location_id" id="pool_location_id" class="block w-full rounded-lg border-slate-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Semua Lokasi</option>
                            @foreach($poolLocations as $loc)
                                <option value="{{ $loc->id }}" {{ request('pool_location_id') == $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex-1 max-w-sm">
                        <label for="day" class="block text-xs font-medium text-slate-500 mb-1">Filter Hari</label>
                        <select name="day" id="day" class="block w-full rounded-lg border-slate-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Semua Hari</option>
                            @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'] as $dayOption)
                                <option value="{{ $dayOption }}" {{ request('day') == $dayOption ? 'selected' : '' }}>{{ $dayOption }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end gap-2 pt-5">
                        <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors shadow-sm">
                            <i class="fa-solid fa-filter mr-1"></i> Filter
                        </button>
                        @if(request()->hasAny(['pool_location_id', 'day']) && (request('pool_location_id') || request('day')))
                            <a href="{{ route('admin.schedules.locations') }}" class="bg-slate-200 text-slate-700 px-5 py-2 rounded-lg text-sm font-medium hover:bg-slate-300 transition-colors">Reset</a>
                        @endif
                    </div>
                </form>
            </div>

            @if(isset($locationSummary) && $locationSummary->count() > 0)
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    @foreach($locationSummary as $poolName => $poolSummary)
                        <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm">
                            <div class="flex items-center gap-3 mb-4 border-b border-slate-50 pb-3">
                                <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-600">
                                    <i class="fa-solid fa-water"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-800">{{ $poolName }}</h4>
                                </div>
                            </div>
                            
                            <div class="max-h-[500px] overflow-y-auto pr-2 space-y-4">
                                @foreach($poolSummary as $day => $daySummary)
                                    <div class="bg-slate-50 rounded-xl p-3 border border-slate-100">
                                        <h5 class="font-bold text-slate-700 mb-2 border-b border-slate-200 pb-1">{{ $day }}</h5>
                                        <div class="space-y-2">
                                            @foreach($daySummary as $slot)
                                                <div class="bg-white p-3 rounded-lg border border-slate-200 shadow-sm text-sm space-y-3">
                                                    <div class="font-bold text-slate-800 border-b border-slate-100 pb-2">
                                                        <i class="fa-regular fa-clock text-slate-400 mr-1"></i> {{ $slot['time'] }}
                                                        <span class="ml-2 text-xs text-slate-500 font-normal">({{ $slot['coaches']->unique()->count() }} Pelatih, {{ $slot['student_count'] }} Murid)</span>
                                                    </div>
                                                    
                                                    <div class="space-y-2">
                                                        @foreach($slot['details'] as $detail)
                                                            <div class="bg-slate-50 p-2 rounded border border-slate-100">
                                                                <div class="font-bold text-xs text-slate-700">
                                                                    <i class="fa-solid fa-person-swimming text-slate-400 mr-1"></i> Pelatih: {{ $detail['coach_name'] }}
                                                                </div>
                                                                <div class="mt-1">
                                                                    @if(count($detail['students']) > 0)
                                                                        <ul class="list-disc ml-5 text-[11px] text-slate-600 space-y-0.5">
                                                                            @foreach($detail['students'] as $student)
                                                                                <li>{{ $student }}</li>
                                                                            @endforeach
                                                                        </ul>
                                                                    @else
                                                                        <p class="text-[11px] text-slate-400 italic mt-0.5 ml-1">Belum ada murid</p>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-2xl border border-slate-100 p-12 text-center shadow-sm">
                    <i class="fa-solid fa-calendar-xmark text-4xl text-slate-300 mb-4"></i>
                    <h3 class="text-lg font-bold text-slate-700 mb-1">Tidak Ada Jadwal</h3>
                    <p class="text-slate-500 text-sm">Tidak ada jadwal kelas yang ditemukan dengan filter yang dipilih.</p>
                </div>
            @endif
        </div>
    </div>

</x-app-layout>
