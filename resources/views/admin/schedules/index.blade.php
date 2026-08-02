<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-slate-800 tracking-tight">
                {{ __('Kelola Jadwal Latihan') }}
            </h2>
            <div class="flex items-center gap-3">
                <span class="px-4 py-2 rounded-full border border-slate-200 bg-slate-50 text-xs font-semibold text-slate-505 shadow-sm flex items-center gap-1.5">
                    <i class="fa-regular fa-calendar"></i>
                    {{ now()->format('l, d F Y') }}
                </span>
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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100">
                <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                    <div>
                        <h3 class="text-lg font-bold text-slate-800">Daftar Jadwal Seluruh Pelatih</h3>
                        <p class="text-sm text-slate-500">Assign murid ke jadwal pelatih yang berstatus "Available".</p>
                    </div>
                    <form method="GET" action="{{ route('admin.schedules.index') }}" class="flex items-center gap-2">
                        <select name="coach_id" class="rounded-lg border-slate-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Semua Pelatih</option>
                            @foreach($coaches as $coach)
                                <option value="{{ $coach->id }}" {{ request('coach_id') == $coach->id ? 'selected' : '' }}>{{ $coach->name }}</option>
                            @endforeach
                        </select>
                        <select name="day" class="rounded-lg border-slate-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Semua Hari</option>
                            @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'] as $day)
                                <option value="{{ $day }}" {{ request('day') == $day ? 'selected' : '' }}>{{ $day }}</option>
                            @endforeach
                        </select>
                        <select name="pool_location_id" class="rounded-lg border-slate-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500 max-w-[150px]">
                            <option value="">Semua Lokasi</option>
                            @foreach($poolLocations as $loc)
                                <option value="{{ $loc->id }}" {{ request('pool_location_id') == $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">Filter</button>
                        @if(request()->hasAny(['coach_id', 'day', 'pool_location_id']) && (request('coach_id') || request('day') || request('pool_location_id')))
                            <a href="{{ route('admin.schedules.index') }}" class="bg-slate-200 text-slate-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-slate-300 transition-colors">Reset</a>
                        @endif
                    </form>
                </div>

                <div class="p-0">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Pelatih</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Hari</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Blok Waktu Tersedia</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Lokasi Latihan</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Jumlah Sesi Latihan</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-200">
                            @forelse($groupedSchedules as $group)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-bold text-slate-900">{{ $group['coach_name'] }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-slate-700 font-semibold">{{ $group['day'] }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex flex-col gap-1 items-center justify-center">
                                            @foreach($group['availabilities'] as $avail)
                                                <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-medium bg-purple-50 text-purple-700 border border-purple-200">
                                                    <i class="fa-regular fa-clock mr-1 text-purple-400"></i>
                                                    {{ \Carbon\Carbon::parse($avail->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($avail->end_time)->format('H:i') }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex flex-wrap gap-1 justify-center max-w-[200px] mx-auto">
                                            @if(isset($group['locations']) && $group['locations']->count() > 0)
                                                @foreach($group['locations'] as $loc)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                        <i class="fa-solid fa-location-dot mr-1 text-emerald-400"></i>
                                                        {{ $loc }}
                                                    </span>
                                                @endforeach
                                            @else
                                                <span class="text-slate-400 italic text-xs">Belum ada lokasi</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200">
                                            {{ $group['count'] }} Sesi Kelas
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('admin.schedules.showDay', ['coach' => $group['coach_id'], 'day' => $group['day']]) }}" class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition-colors border border-blue-200">
                                            Lihat Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-slate-500">
                                        <div class="flex flex-col items-center justify-center">
                                            <i class="fa-regular fa-calendar-xmark text-4xl mb-3 text-slate-300"></i>
                                            <p>Belum ada jadwal pelatih yang tersedia.</p>
                                        </div>
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
