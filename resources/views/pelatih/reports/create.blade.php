<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-slate-800 tracking-tight">
            {{ __('Presensi & Laporan Latihan') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-slate-50/50 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            @if ($errors->any())
                <div class="mb-6 p-4 bg-rose-50 border-l-4 border-rose-500 rounded-r-lg text-rose-700 font-medium text-sm">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100 p-8">
                <div class="mb-6 pb-6 border-b border-slate-100">
                    <h3 class="text-lg font-bold text-slate-800 mb-2">Informasi Jadwal</h3>
                    <div class="grid grid-cols-2 gap-4 text-sm text-slate-600">
                        @if(isset($isSubstitute) && $isSubstitute && isset($invalRequest))
                            <div><strong>Tanggal, Jam (Inval):</strong> {{ \Carbon\Carbon::parse($invalRequest->proposed_date)->format('d F Y') }}, {{ \Carbon\Carbon::parse($invalRequest->proposed_start_time)->format('H:i') }} - selesai</div>
                            <div><strong>Lokasi (Inval):</strong> {{ $invalRequest->proposed_pool_location_id ? $invalRequest->proposedPoolLocation->name : ($schedule->poolLocation->name ?? 'Belum ditentukan') }}</div>
                        @else
                            <div><strong>Hari, Jam:</strong> {{ $schedule->day }}, {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}</div>
                            <div><strong>Lokasi:</strong> {{ $schedule->poolLocation->name ?? 'Belum ditentukan' }}</div>
                        @endif
                    </div>
                </div>

                <form action="{{ route('pelatih.reports.store', $schedule) }}" method="POST">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="training_date" class="block text-sm font-medium text-slate-700 mb-1">Tanggal Latihan Aktual</label>
                            <input type="date" name="training_date" id="training_date" required max="{{ now()->format('Y-m-d') }}" class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm {{ isset($isSubstitute) && $isSubstitute ? 'bg-slate-100 cursor-not-allowed text-slate-500' : '' }}" value="{{ old('training_date', $defaultDate ?? now()->format('Y-m-d')) }}" {{ isset($isSubstitute) && $isSubstitute ? 'readonly' : '' }}>
                            @if(isset($isSubstitute) && $isSubstitute)
                                <p class="text-xs text-purple-600 mt-1 font-semibold">Tanggal laporan disesuaikan otomatis dengan jadwal inval.</p>
                            @else
                                <p class="text-xs text-slate-500 mt-1">Laporan tidak bisa disubmit jika lewat dari 7 hari.</p>
                            @endif
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Kehadiran Pelatih (Anda)</label>
                        <select name="coach_attendance" id="coach_attendance" required class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <option value="Hadir">Hadir</option>
                            <option value="Tidak Hadir">Tidak Hadir</option>
                        </select>
                    </div>

                    <div class="mb-6" id="student_attendance_section">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Kehadiran Murid</label>
                        <div class="bg-slate-50 rounded-lg p-4 border border-slate-200">
                            @foreach($schedule->students as $student)
                                <div class="py-3 {{ !$loop->last ? 'border-b border-slate-200' : '' }}">
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-2">
                                        <span class="text-sm font-medium text-slate-800">{{ $student->name }}</span>
                                        <div class="flex gap-4 mt-2 sm:mt-0">
                                            <label class="inline-flex items-center">
                                                <input type="radio" name="student_attendance[{{ $student->id }}]" value="Hadir" checked class="text-blue-600 focus:ring-blue-500 border-slate-300">
                                                <span class="ml-2 text-sm text-slate-700">Hadir</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="radio" name="student_attendance[{{ $student->id }}]" value="Tidak Hadir" class="text-rose-600 focus:ring-rose-500 border-slate-300">
                                                <span class="ml-2 text-sm text-slate-700">Tidak Hadir</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <input type="text" name="student_evaluations[{{ $student->id }}]" placeholder="Penilaian/catatan untuk {{ $student->name }} (opsional)" class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm text-slate-600">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mb-6">
                        <label for="report_note" id="report_note_label" class="block text-sm font-medium text-slate-700 mb-1">Catatan / Laporan Latihan</label>
                        <textarea name="report_note" id="report_note" rows="4" required class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" placeholder="Contoh: Fokus pernapasan gaya bebas, murid mengalami peningkatan..."></textarea>
                    </div>

                    <div class="flex justify-end gap-3 mt-8">
                        <a href="{{ route('pelatih.schedules.index') }}" class="px-4 py-2 border border-slate-300 rounded-lg shadow-sm text-sm font-medium text-slate-700 bg-white hover:bg-slate-50">
                            Batal
                        </a>
                        <button type="submit" class="px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 flex items-center gap-2">
                            <i class="fa-solid fa-paper-plane"></i> Submit Laporan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const coachAttendanceSelect = document.getElementById('coach_attendance');
        const studentAttendanceSection = document.getElementById('student_attendance_section');
        const reportNoteLabel = document.getElementById('report_note_label');
        const reportNoteTextarea = document.getElementById('report_note');

        function toggleStudentAttendance() {
            if (coachAttendanceSelect.value === 'Tidak Hadir') {
                studentAttendanceSection.style.display = 'none';
                reportNoteLabel.textContent = 'Alasan Tidak Hadir';
                reportNoteTextarea.placeholder = 'Contoh: Saya tidak bisa hadir karena sakit / ada keperluan keluarga...';
            } else {
                studentAttendanceSection.style.display = 'block';
                reportNoteLabel.textContent = 'Catatan / Laporan Latihan';
                reportNoteTextarea.placeholder = 'Contoh: Fokus pernapasan gaya bebas, murid mengalami peningkatan...';
            }
        }

        coachAttendanceSelect.addEventListener('change', toggleStudentAttendance);
        
        // Initialize on load
        toggleStudentAttendance();
    });
</script>
