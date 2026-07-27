<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.operations.recap') }}" class="text-slate-500 hover:text-slate-700 transition">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <h2 class="font-bold text-2xl text-slate-800 tracking-tight">
                {{ __('Buat Rekap Manual') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12 bg-slate-50/50 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            @if($errors->any())
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg text-red-700 font-medium text-sm flex items-start gap-3">
                    <i class="fa-solid fa-circle-exclamation mt-0.5 text-lg"></i>
                    <div>
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <!-- Tahap 1: Pilih Jadwal -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100 p-6 mb-6">
                <h3 class="text-lg font-bold text-slate-800 mb-4 pb-3 border-b border-slate-100">Langkah 1: Pilih Jadwal & Tanggal</h3>
                <form action="{{ route('admin.operations.createManualRecap') }}" method="GET" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Jadwal Latihan</label>
                            <select name="schedule_id" required class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 text-sm">
                                <option value="">-- Pilih Jadwal Pelatih --</option>
                                @foreach($schedules as $schedule)
                                    <option value="{{ $schedule->id }}" {{ request('schedule_id') == $schedule->id ? 'selected' : '' }}>
                                        {{ $schedule->day }} ({{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}) - {{ $schedule->coach->name ?? 'N/A' }} - {{ $schedule->poolLocation->name ?? 'N/A' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tanggal Latihan</label>
                            <input type="date" name="training_date" value="{{ request('training_date', now()->format('Y-m-d')) }}" required class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 text-sm">
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="bg-blue-600 text-white px-5 py-2.5 rounded-xl font-medium text-sm hover:bg-blue-700 transition">
                            Tampilkan Data Murid
                        </button>
                    </div>
                </form>
            </div>

            <!-- Tahap 2: Isi Kehadiran (Hanya muncul jika jadwal dipilih) -->
            @if($selectedSchedule)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100 p-6 relative">
                    <h3 class="text-lg font-bold text-slate-800 mb-4 pb-3 border-b border-slate-100">Langkah 2: Isi Presensi</h3>
                    
                    <form action="{{ route('admin.operations.storeManualRecap') }}" method="POST">
                        @csrf
                        <input type="hidden" name="schedule_id" value="{{ $selectedSchedule->id }}">
                        <input type="hidden" name="training_date" value="{{ request('training_date') }}">

                        <!-- Kehadiran Pelatih -->
                        <div class="mb-6 bg-slate-50 p-4 rounded-xl border border-slate-100">
                            <h4 class="font-bold text-slate-700 mb-3 text-sm">Kehadiran Pelatih ({{ $selectedSchedule->coach->name ?? '-' }})</h4>
                            <div class="flex gap-4">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="coach_attendance" value="Hadir" checked class="text-emerald-500 focus:ring-emerald-500 w-4 h-4">
                                    <span class="text-sm font-medium text-slate-700">Hadir</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="coach_attendance" value="Tidak Hadir" class="text-red-500 focus:ring-red-500 w-4 h-4">
                                    <span class="text-sm font-medium text-slate-700">Tidak Hadir</span>
                                </label>
                            </div>
                        </div>

                        <!-- Laporan Umum -->
                        <div class="mb-6">
                            <label class="block text-sm font-bold text-slate-700 mb-2">Catatan/Laporan Umum</label>
                            <textarea name="report_note" rows="3" class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 text-sm" placeholder="Opsional: Tuliskan kondisi latihan secara umum..."></textarea>
                        </div>

                        <h4 class="font-bold text-slate-800 mb-4 text-sm uppercase tracking-wider">Presensi Murid</h4>
                        
                        @if($selectedSchedule->students->isEmpty())
                            <div class="p-4 bg-amber-50 text-amber-700 rounded-lg text-sm border border-amber-200 flex gap-2">
                                <i class="fa-solid fa-circle-info mt-0.5"></i>
                                Belum ada murid yang di-assign ke jadwal ini.
                            </div>
                        @else
                            <div class="space-y-4">
                                @foreach($selectedSchedule->students as $student)
                                    <div class="border border-slate-200 rounded-xl p-4 transition hover:border-blue-300">
                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold">
                                                    {{ substr($student->user->name ?? 'M', 0, 1) }}
                                                </div>
                                                <div>
                                                    <p class="font-bold text-slate-800">{{ $student->user->name ?? 'Murid' }}</p>
                                                    <p class="text-xs text-slate-500">Sisa Pertemuan: {{ $student->remaining_meetings }}</p>
                                                </div>
                                            </div>
                                            
                                            <div class="flex gap-4 bg-slate-50 px-4 py-2 rounded-lg border border-slate-100">
                                                <label class="flex items-center gap-2 cursor-pointer">
                                                    <input type="radio" name="student_attendance[{{ $student->id }}]" value="Hadir" checked class="text-emerald-500 focus:ring-emerald-500 w-4 h-4">
                                                    <span class="text-sm font-medium text-emerald-700">Hadir</span>
                                                </label>
                                                <label class="flex items-center gap-2 cursor-pointer">
                                                    <input type="radio" name="student_attendance[{{ $student->id }}]" value="Tidak Hadir" class="text-red-500 focus:ring-red-500 w-4 h-4">
                                                    <span class="text-sm font-medium text-red-700">Tidak Hadir</span>
                                                </label>
                                            </div>
                                        </div>

                                        <div class="pl-0 sm:pl-14">
                                            <textarea name="student_evaluations[{{ $student->id }}]" rows="2" class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 text-sm" placeholder="Catatan/Evaluasi untuk murid ini..."></textarea>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <div class="mt-8 flex justify-end gap-3 pt-4 border-t border-slate-100">
                            <a href="{{ route('admin.operations.recap') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-700 font-medium text-sm hover:bg-slate-50 transition">
                                Batal
                            </a>
                            <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-600 text-white font-medium text-sm hover:bg-blue-700 shadow-sm transition">
                                Simpan Rekap Latihan
                            </button>
                        </div>
                    </form>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
