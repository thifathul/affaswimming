<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-slate-800 tracking-tight">
                {{ __('Detail Jadwal Latihan') }}
            </h2>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.schedules.index') }}" class="px-4 py-2 rounded-lg bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 transition-colors font-medium text-sm">
                    <i class="fa-solid fa-arrow-left mr-2"></i> Kembali
                </a>
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
            @if(session('error'))
                <div class="mb-6 p-4 bg-rose-50 border-l-4 border-rose-500 rounded-r-lg text-rose-700 font-medium text-sm flex items-center">
                    <i class="fa-solid fa-circle-xmark mr-3 text-lg"></i>
                    {{ session('error') }}
                </div>
            @endif
            @if($errors->any())
                <div class="mb-6 p-4 bg-rose-50 border-l-4 border-rose-500 rounded-r-lg text-rose-700 font-medium text-sm flex items-start">
                    <i class="fa-solid fa-circle-exclamation mt-0.5 mr-3 text-lg"></i>
                    <div>
                        <strong>Terjadi kesalahan:</strong>
                        <ul class="list-disc ml-5 mt-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <div class="mb-6 flex flex-col md:flex-row gap-6">
                <!-- Info Pelatih -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100 flex-1 p-6">
                    <h3 class="text-lg font-bold text-slate-800">Pelatih: {{ $coach->name }}</h3>
                    <p class="text-sm text-slate-500 mb-4">Hari: <span class="font-bold text-slate-700">{{ $day }}</span></p>
                    <div class="bg-blue-50 text-blue-800 p-3 rounded-lg border border-blue-100 text-sm flex gap-3 items-start">
                        <i class="fa-solid fa-circle-info mt-1"></i>
                        <p>Daftar di bawah ini adalah blok waktu kosong yang diberikan oleh pelatih. Anda dapat memecahnya menjadi beberapa sesi kelas dengan mengklik tombol <strong>Tambah Jadwal Kelas</strong>.</p>
                    </div>
                </div>

                <!-- Daftar Blok Ketersediaan -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100 flex-1 flex flex-col">
                    <div class="p-4 border-b border-slate-100 bg-slate-50 font-bold text-slate-800">
                        Blok Waktu Tersedia
                    </div>
                    <div class="p-4 flex-1 flex flex-col gap-3 overflow-y-auto max-h-48">
                        @forelse($availabilities as $availability)
                            <div class="flex items-center justify-between p-3 bg-white border border-slate-200 rounded-xl shadow-sm">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center">
                                        <i class="fa-regular fa-clock"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-800 text-sm">
                                            {{ \Carbon\Carbon::parse($availability->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($availability->end_time)->format('H:i') }}
                                        </div>
                                        <div class="text-xs text-slate-500">Blok Waktu Pelatih</div>
                                    </div>
                                </div>
                                <button type="button" onclick="openCreateModal({{ $availability->id }}, '{{ \Carbon\Carbon::parse($availability->start_time)->format('H:i') }}', '{{ \Carbon\Carbon::parse($availability->end_time)->format('H:i') }}')" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors shadow-sm">
                                    <i class="fa-solid fa-plus mr-1"></i> Sesi Kelas
                                </button>
                            </div>
                        @empty
                            <div class="text-center py-6 text-slate-500 text-sm">
                                Pelatih belum memberikan jadwal kosong untuk hari ini.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Tabel Sesi Kelas -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100">
                <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                    <div>
                        <h3 class="text-lg font-bold text-slate-800">Sesi Kelas yang Diatur</h3>
                        <p class="text-sm text-slate-500">Terdapat {{ $schedules->count() }} sesi kelas yang di-assign untuk pelatih ini.</p>
                    </div>
                </div>

                <div class="p-0 overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Jam Kelas</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Lokasi / Kolam</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Daftar Murid</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-200">
                            @forelse($schedules as $schedule)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-slate-900 font-semibold">
                                            {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-slate-800">
                                            @if($schedule->poolLocation)
                                                <i class="fa-solid fa-location-dot text-slate-400 mr-1 text-xs"></i> {{ $schedule->poolLocation->name }}
                                            @else
                                                <span class="text-slate-400 italic text-xs">Belum ada lokasi</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($schedule->students->count() > 0)
                                            <ul class="text-xs text-slate-600 space-y-1">
                                                @foreach($schedule->students as $student)
                                                    <li class="flex items-center gap-2">
                                                        <span class="flex items-center before:content-['•'] before:mr-2">{{ $student->name }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <span class="text-xs text-slate-400 italic">Belum ada murid di-assign</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end gap-2">
                                            <button type="button" onclick='openEditModal(@json($schedule), @json($schedule->students->pluck("id")))' class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition-colors border border-blue-200">
                                                <i class="fa-solid fa-pen-to-square mr-1"></i> Edit
                                            </button>
                                            <form action="{{ route('admin.schedules.destroy', $schedule) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus sesi kelas ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg transition-colors border border-red-200">
                                                    <i class="fa-solid fa-trash mr-1"></i> Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-slate-500">
                                        <div class="flex flex-col items-center justify-center">
                                            <i class="fa-regular fa-calendar-xmark text-4xl mb-3 text-slate-300"></i>
                                            <p>Belum ada sesi kelas yang dibuat untuk pelatih ini.</p>
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

    <!-- Modal Tambah Jadwal (Create) -->
    <div id="createModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-slate-900 bg-opacity-75 transition-opacity backdrop-blur-sm" aria-hidden="true" onclick="closeCreateModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-slate-100">
                <form id="createForm" method="POST" action="{{ route('admin.schedules.store') }}">
                    @csrf
                    <input type="hidden" name="coach_id" value="{{ $coach->id }}">
                    <input type="hidden" name="day" value="{{ $day }}">
                    <input type="hidden" name="coach_availability_id" id="create_availability_id">

                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                                <i class="fa-solid fa-plus text-blue-600"></i>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-bold text-slate-900" id="modal-title">
                                    Tambah Sesi Kelas
                                </h3>
                                <p class="text-sm text-slate-500 mt-1">Buat sesi kelas dan assign murid di dalam blok <span id="create_block_text" class="font-bold text-slate-700"></span>.</p>
                                
                                <div class="mt-6 space-y-5">
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label for="create_start_time" class="block text-sm font-medium text-slate-700">Jam Mulai</label>
                                            <input type="time" name="start_time" id="create_start_time" required class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                        </div>
                                        <div>
                                            <label for="create_end_time" class="block text-sm font-medium text-slate-700">Jam Selesai</label>
                                            <input type="time" name="end_time" id="create_end_time" required class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label for="create_pool_location_id" class="block text-sm font-medium text-slate-700">Lokasi Latihan</label>
                                        <select id="create_pool_location_id" name="pool_location_id" required class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                            <option value="">-- Pilih Lokasi Kolam --</option>
                                            @foreach($poolLocations as $location)
                                                <option value="{{ $location->id }}">{{ $location->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-2">Assign Murid (Bisa pilih lebih dari satu)</label>
                                        <div class="max-h-48 overflow-y-auto border border-slate-200 rounded-lg p-3 bg-slate-50">
                                            @foreach($students as $student)
                                                <div class="flex items-center mb-2 last:mb-0 bg-white p-2 border border-slate-100 rounded-md hover:bg-blue-50/50 transition-colors">
                                                    <input id="c_student_{{ $student->id }}" name="student_ids[]" type="checkbox" value="{{ $student->id }}" class="w-4 h-4 text-blue-600 bg-slate-100 border-slate-300 rounded focus:ring-blue-500 focus:ring-2">
                                                    <label for="c_student_{{ $student->id }}" class="ms-2 text-sm font-medium text-slate-700 flex-1 cursor-pointer">
                                                        {{ $student->name }} <span class="text-xs text-slate-500 font-normal ml-1">({{ $student->school ?? '-' }})</span>
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-slate-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-slate-100 rounded-b-2xl">
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                            Buat Sesi Kelas
                        </button>
                        <button type="button" onclick="closeCreateModal()" class="mt-3 w-full inline-flex justify-center rounded-lg border border-slate-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Jadwal -->
    <div id="editModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-slate-900 bg-opacity-75 transition-opacity backdrop-blur-sm" aria-hidden="true" onclick="closeEditModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-slate-100">
                <form id="editForm" method="POST" action="">
                    @csrf
                    @method('PUT')
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                                <i class="fa-solid fa-pen-to-square text-blue-600"></i>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-bold text-slate-900" id="modal-title">
                                    Edit Sesi Kelas
                                </h3>
                                <p class="text-sm text-slate-500 mt-1">Ubah rentang waktu, lokasi, dan murid yang ditugaskan.</p>
                                
                                <div class="mt-6 space-y-5">
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
                                    
                                    <div>
                                        <label for="pool_location_id" class="block text-sm font-medium text-slate-700">Lokasi Latihan</label>
                                        <select id="pool_location_id" name="pool_location_id" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                            <option value="">-- Pilih Lokasi Kolam --</option>
                                            @foreach($poolLocations as $location)
                                                <option value="{{ $location->id }}">{{ $location->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-2">Murid (Bisa pilih lebih dari satu)</label>
                                        <div class="max-h-48 overflow-y-auto border border-slate-200 rounded-lg p-3 bg-slate-50">
                                            @foreach($students as $student)
                                                <div class="flex items-center mb-2 last:mb-0 bg-white p-2 border border-slate-100 rounded-md hover:bg-blue-50/50 transition-colors">
                                                    <input id="student_{{ $student->id }}" name="student_ids[]" type="checkbox" value="{{ $student->id }}" data-name="{{ $student->name }}" class="student-checkbox w-4 h-4 text-blue-600 bg-slate-100 border-slate-300 rounded focus:ring-blue-500 focus:ring-2">
                                                    <label for="student_{{ $student->id }}" class="ms-2 text-sm font-medium text-slate-700 flex-1 cursor-pointer">
                                                        {{ $student->name }} <span class="text-xs text-slate-500 font-normal ml-1">({{ $student->school ?? '-' }})</span>
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-slate-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-slate-100 rounded-b-2xl">
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                            Simpan Perubahan
                        </button>
                        <button type="button" onclick="closeEditModal()" class="mt-3 w-full inline-flex justify-center rounded-lg border border-slate-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        window.originalStudentIds = [];
        
        function openCreateModal(availabilityId, start, end) {
            document.getElementById('create_availability_id').value = availabilityId;
            document.getElementById('create_block_text').textContent = start + ' - ' + end;
            
            // Set defaults to block bounds
            document.getElementById('create_start_time').value = start;
            document.getElementById('create_end_time').value = end;
            
            // Uncheck all
            document.querySelectorAll('#createForm input[type="checkbox"]').forEach(cb => {
                cb.checked = false;
            });
            
            document.getElementById('createModal').classList.remove('hidden');
        }

        function closeCreateModal() {
            document.getElementById('createModal').classList.add('hidden');
        }

        function openEditModal(schedule, studentIds) {
            document.getElementById('editForm').action = '/admin/schedules/' + schedule.id;
            
            // Format time correctly to HH:MM for input type="time"
            document.getElementById('start_time').value = schedule.start_time.substring(0, 5);
            document.getElementById('end_time').value = schedule.end_time.substring(0, 5);
            
            document.getElementById('pool_location_id').value = schedule.pool_location_id || '';
            
            window.originalStudentIds = studentIds;
            
            // Uncheck all first
            document.querySelectorAll('#editForm .student-checkbox').forEach(cb => {
                cb.checked = false;
            });
            
            // Check the assigned ones
            studentIds.forEach(id => {
                const cb = document.getElementById('student_' + id);
                if (cb) cb.checked = true;
            });
            
            document.getElementById('editModal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }
        
        document.getElementById('editForm').addEventListener('submit', function(e) {
            if (window.originalStudentIds.length > 0) {
                let newStudents = [];
                document.querySelectorAll('#editForm .student-checkbox:checked').forEach(cb => {
                    if (!window.originalStudentIds.includes(parseInt(cb.value))) {
                        newStudents.push(cb.getAttribute('data-name'));
                    }
                });
                
                if (newStudents.length > 0) {
                    let msg = 'Apakah Anda yakin untuk menyatukan jadwal ini dengan ' + newStudents.join(', ') + '?\n\n(Jadwal ini sudah terisi oleh murid lain sebelumnya)';
                    if (!confirm(msg)) {
                        e.preventDefault();
                    }
                }
            }
        });
    </script>
</x-app-layout>
