<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-slate-800 tracking-tight">
                {{ __('Jadwal Pelatih Lainnya') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filter Form -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4 mb-6">
                <form action="{{ route('pelatih.all-schedules.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4 items-end">
                    <div class="flex-1 w-full">
                        <label for="coach_id" class="block text-xs font-medium text-slate-500 mb-1">Pelatih</label>
                        <select name="coach_id" id="coach_id" class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <option value="">Semua Pelatih</option>
                            @foreach($coaches as $coach)
                                <option value="{{ $coach->id }}" {{ request('coach_id') == $coach->id ? 'selected' : '' }}>{{ $coach->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex-1 w-full">
                        <label for="day" class="block text-xs font-medium text-slate-500 mb-1">Hari</label>
                        <select name="day" id="day" class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <option value="">Semua Hari</option>
                            @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'] as $day)
                                <option value="{{ $day }}" {{ request('day') == $day ? 'selected' : '' }}>{{ $day }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex-1 w-full">
                        <label for="pool_location_id" class="block text-xs font-medium text-slate-500 mb-1">Lokasi Kolam</label>
                        <select name="pool_location_id" id="pool_location_id" class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <option value="">Semua Lokasi</option>
                            @foreach($poolLocations as $location)
                                <option value="{{ $location->id }}" {{ request('pool_location_id') == $location->id ? 'selected' : '' }}>{{ $location->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <button type="submit" class="w-full sm:w-auto px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 flex items-center justify-center gap-2">
                            <i class="fa-solid fa-filter"></i> Filter
                        </button>
                    </div>
                    @if(request()->filled('coach_id') || request()->filled('day') || request()->filled('pool_location_id'))
                        <div>
                            <a href="{{ route('pelatih.all-schedules.index') }}" class="w-full sm:w-auto px-4 py-2 border border-slate-300 rounded-lg shadow-sm text-sm font-medium text-slate-700 bg-white hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 flex items-center justify-center">
                                Reset
                            </a>
                        </div>
                    @endif
                </form>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100">
                <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                    <h3 class="text-lg font-bold text-slate-800">
                        <i class="fa-solid fa-users text-blue-500 mr-2"></i> Daftar Jadwal Semua Pelatih
                    </h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-slate-600">
                        <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-200">
                            <tr>
                                <th scope="col" class="px-6 py-4 font-semibold">Nama Pelatih</th>
                                <th scope="col" class="px-6 py-4 font-semibold">Hari & Jam</th>
                                <th scope="col" class="px-6 py-4 font-semibold">Lokasi</th>
                                <th scope="col" class="px-6 py-4 font-semibold">Murid</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($schedules as $schedule)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xs border border-blue-100">
                                                {{ strtoupper(substr($schedule->coach->name ?? '?', 0, 2)) }}
                                            </div>
                                            <div class="font-semibold text-slate-800">{{ $schedule->coach->name ?? 'Tanpa Pelatih' }}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-medium text-slate-800">{{ $schedule->day }}</div>
                                        <div class="text-xs text-slate-500">{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-slate-800 font-medium">{{ $schedule->poolLocation->name ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($schedule->students->isNotEmpty())
                                            <ul class="list-disc list-inside text-xs text-slate-600">
                                                @foreach($schedule->students as $student)
                                                    <li>{{ $student->name }}</li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <span class="text-xs text-slate-400 italic">Jadwal Kosong</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center text-slate-400">
                                            <i class="fa-regular fa-folder-open text-4xl mb-3"></i>
                                            <p class="text-base font-medium">Belum ada jadwal pelatih lain.</p>
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
