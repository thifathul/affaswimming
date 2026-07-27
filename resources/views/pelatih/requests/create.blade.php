<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-slate-800 tracking-tight">
            {{ __('Pengajuan Reschedule / Inval') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-slate-50/50 min-h-screen">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100 p-8">
                <div class="mb-6 pb-6 border-b border-slate-100">
                    <h3 class="text-lg font-bold text-slate-800 mb-2">Jadwal Asli</h3>
                    <div class="grid grid-cols-2 gap-4 text-sm text-slate-600">
                        <div><strong>Hari, Jam:</strong> {{ $schedule->day }}, {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}</div>
                        <div><strong>Lokasi:</strong> {{ $schedule->poolLocation->name ?? 'Belum ditentukan' }}</div>
                    </div>
                </div>

                <form action="{{ route('pelatih.requests.store', $schedule) }}" method="POST" id="requestForm">
                    @csrf
                    
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Tipe Pengajuan</label>
                        <select name="type" id="type" required class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" onchange="toggleInval(this.value)">
                            <option value="">-- Pilih Tipe --</option>
                            <option value="reschedule">Reschedule (Ganti Hari/Jam, Saya yang mengajar)</option>
                            <option value="inval">Inval (Ganti Pelatih)</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="proposed_date" class="block text-sm font-medium text-slate-700 mb-1">Usulan Tanggal Pengganti</label>
                            <input type="date" name="proposed_date" id="proposed_date" required class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="proposed_start_time" class="block text-sm font-medium text-slate-700 mb-1">Usulan Jam Mulai</label>
                            <input type="time" name="proposed_start_time" id="proposed_start_time" required class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        </div>
                    </div>

                    <div class="mb-6" id="coachSelectContainer" style="display: none;">
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label for="substitute_coach_id" class="block text-sm font-medium text-slate-700 mb-2">Pelatih Pengganti (Khusus Inval)</label>
                                <select name="substitute_coach_id" id="substitute_coach_id" class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                    <option value="">-- Pilih Pelatih --</option>
                                    @foreach($coaches as $coach)
                                        <option value="{{ $coach->id }}">{{ $coach->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="proposed_pool_location_id" class="block text-sm font-medium text-slate-700 mb-2">Pindah Kolam (Opsional)</label>
                                <select name="proposed_pool_location_id" id="proposed_pool_location_id" class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                    <option value="">-- Lokasi Tetap --</option>
                                    @foreach($poolLocations as $pool)
                                        <option value="{{ $pool->id }}">{{ $pool->name }}</option>
                                    @endforeach
                                </select>
                                <p class="mt-1 text-xs text-slate-500">Kosongkan jika latihan inval tetap di kolam yang sama.</p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label for="reason" class="block text-sm font-medium text-slate-700 mb-1">Alasan Pengajuan</label>
                        <textarea name="reason" id="reason" rows="3" required class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" placeholder="Jelaskan alasan berhalangan hadir..."></textarea>
                    </div>

                    <div class="flex justify-end gap-3 mt-8">
                        <a href="{{ route('pelatih.schedules.index') }}" class="px-4 py-2 border border-slate-300 rounded-lg shadow-sm text-sm font-medium text-slate-700 bg-white hover:bg-slate-50">
                            Batal
                        </a>
                        <button type="submit" class="px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                            Kirim Pengajuan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleInval(val) {
            const container = document.getElementById('coachSelectContainer');
            const select = document.getElementById('substitute_coach_id');
            if (val === 'inval') {
                container.style.display = 'block';
                select.required = true;
            } else {
                container.style.display = 'none';
                select.required = false;
                select.value = '';
            }
        }
    </script>
</x-app-layout>
