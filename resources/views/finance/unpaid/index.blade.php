<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-slate-800 tracking-tight">
            {{ __('Daftar Murid Unpaid') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
            <div class="mb-6 bg-emerald-50 text-emerald-700 p-4 rounded-xl border border-emerald-100 flex items-center gap-3 shadow-sm">
                <i class="fa-solid fa-circle-check text-emerald-500 text-xl"></i>
                <p class="font-medium text-sm">{{ session('success') }}</p>
            </div>
            @endif

            @if(session('error'))
            <div class="mb-6 bg-red-50 text-red-700 p-4 rounded-xl border border-red-100 flex items-center gap-3 shadow-sm">
                <i class="fa-solid fa-circle-exclamation text-red-500 text-xl"></i>
                <p class="font-medium text-sm">{{ session('error') }}</p>
            </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100 p-6">
                
                <!-- Header Section -->
                <div class="mb-6">
                    <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <i class="fa-solid fa-triangle-exclamation text-red-500"></i>
                        Tagihan Kosong / Minus
                    </h3>
                    <p class="text-sm text-slate-500 mt-1 max-w-3xl">Daftar murid di bawah ini adalah murid aktif yang kehabisan paket namun masih mengikuti latihan, atau sudah waktunya membeli paket baru.</p>
                </div>

                <!-- Toolbar Section -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4 p-4 bg-slate-50/50 rounded-xl border border-slate-100">
                    <div class="text-sm font-medium text-slate-500">
                        Total: <span class="text-slate-800 font-bold bg-white px-2 py-1 rounded-md border border-slate-200 shadow-sm ml-1">{{ $students->total() }}</span> Murid
                    </div>
                    
                    <form method="GET" action="{{ route('finance.unpaid.index') }}" class="w-full sm:w-auto flex flex-col sm:flex-row gap-2">
                        <div class="relative w-full sm:w-64">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fa-solid fa-magnifying-glass text-slate-400"></i>
                            </div>
                            <input type="text" name="search" value="{{ request('search') }}" class="block w-full pl-10 pr-3 py-2 border border-slate-200 rounded-lg text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all bg-white shadow-sm" placeholder="Cari nama murid...">
                        </div>
                        <div class="flex gap-2 w-full sm:w-auto">
                            <button type="submit" class="flex-1 sm:flex-none px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-lg text-sm font-semibold transition-colors shadow-sm flex items-center justify-center gap-2">
                                <i class="fa-solid fa-filter text-xs"></i> Filter
                            </button>
                            @if(request('search'))
                                <a href="{{ route('finance.unpaid.index') }}" class="flex-1 sm:flex-none px-4 py-2 bg-white hover:bg-slate-50 text-slate-600 border border-slate-200 rounded-lg text-sm font-semibold transition-colors flex items-center justify-center gap-2 shadow-sm">
                                    <i class="fa-solid fa-rotate-right text-xs"></i> Reset
                                </a>
                            @endif
                        </div>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200 bg-slate-50/50">
                                <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Nama Murid</th>
                                <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Sisa Pertemuan</th>
                                <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($students as $student)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 font-bold">
                                                {{ substr($student->user->name ?? 'M', 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="font-bold text-slate-800">{{ $student->user->name ?? $student->name }}</div>
                                                <div class="text-xs text-slate-500">Terakhir Latihan: {{ $student->attendances->first() ? \Carbon\Carbon::parse($student->attendances->first()->created_at)->diffForHumans() : 'Belum ada' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-sm font-bold bg-red-100 text-red-700 border border-red-200">
                                            {{ $student->remaining_meetings }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded text-xs font-bold bg-rose-100 text-rose-700 uppercase tracking-wider">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Unpaid
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            @php
                                                $rawPhone = $student->user->phone ?? '';
                                                $cleanPhone = preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $rawPhone));
                                                $displayName = $student->user->name ?? $student->name ?? 'Murid';
                                                $waMessage = "Halo Kak {$displayName}, kami dari AFFA Swimming menginformasikan bahwa paket sesi latihan renang Anda telah habis (Sisa Pertemuan: {$student->remaining_meetings}). Mohon konfirmasi apakah Kak {$displayName} ingin melanjutkan les renang dan melakukan perpanjangan paket? Terima kasih.";
                                            @endphp

                                            @if(!empty($cleanPhone))
                                                <a href="https://wa.me/{{ $cleanPhone }}?text={{ rawurlencode($waMessage) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg text-xs font-semibold transition-colors shadow-sm" title="Kirim Pengingat WhatsApp">
                                                    <i class="fa-brands fa-whatsapp text-sm"></i> <span>Hubungi WA</span>
                                                </a>
                                            @else
                                                <button type="button" disabled class="inline-flex items-center gap-1.5 px-3 py-2 bg-slate-100 text-slate-400 rounded-lg text-xs font-semibold cursor-not-allowed" title="Nomor WhatsApp tidak tersedia">
                                                    <i class="fa-brands fa-whatsapp text-sm"></i> <span>No WA Kosong</span>
                                                </button>
                                            @endif

                                            <button type="button" onclick="openDeactivateModal({{ $student->id }}, '{{ addslashes($displayName) }}', {{ $student->remaining_meetings }})" class="inline-flex items-center gap-1.5 px-3 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded-lg text-xs font-semibold transition-colors shadow-sm" title="Konfirmasi Tidak Lanjut">
                                                <i class="fa-solid fa-user-xmark text-sm"></i> <span>Tidak Lanjut</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-slate-500">
                                        <div class="w-16 h-16 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center mx-auto mb-3 text-2xl">
                                            <i class="fa-regular fa-face-smile"></i>
                                        </div>
                                        <p class="font-medium text-slate-600">Luar biasa! Tidak ada murid yang menunggak.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $students->links() }}
                </div>

            </div>
        </div>
    </div>

    <!-- Deactivate Student Confirmation Modal -->
    <div id="deactivateModal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black/50 backdrop-blur-sm transition-opacity opacity-0">
        <div class="bg-white rounded-2xl w-full max-w-lg p-6 shadow-2xl transform scale-95 transition-transform duration-300 mx-4" id="deactivateModalContent">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-2xl bg-rose-100 border border-rose-200 text-rose-600 flex items-center justify-center text-xl flex-shrink-0">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-bold text-slate-800">Konfirmasi Siswa Tidak Lanjut</h3>
                    <p class="text-sm text-slate-500 mt-1">
                        Apakah siswa ini telah dikonfirmasi <strong class="text-rose-600 font-semibold">TIDAK MELANJUTKAN</strong> les renang?
                    </p>
                </div>
            </div>

            <!-- Student Info Card -->
            <div class="mt-4 p-3.5 bg-slate-50 rounded-xl border border-slate-200/80 text-xs text-slate-700 space-y-1.5">
                <div class="flex justify-between">
                    <span class="text-slate-500">Nama Siswa:</span>
                    <span class="font-bold text-slate-800 text-sm" id="modalStudentName">-</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Sisa Pertemuan:</span>
                    <span class="font-bold text-rose-600" id="modalStudentRemaining">-</span>
                </div>
            </div>

            <!-- Impact warning list -->
            <div class="mt-4 p-3.5 bg-amber-50 rounded-xl border border-amber-200/80 text-xs text-amber-900 space-y-2">
                <div class="font-bold flex items-center gap-1.5 text-amber-800">
                    <i class="fa-solid fa-circle-info"></i> Tindakan yang akan otomatis diproses:
                </div>
                <ul class="list-disc list-inside space-y-1 text-slate-600">
                    <li><strong class="text-slate-800">Akun Siswa Dinonaktifkan:</strong> Siswa tidak akan bisa login kembali ke aplikasi.</li>
                    <li><strong class="text-slate-800">Ditarik dari Semua Jadwal:</strong> Siswa dilepas dari seluruh jadwal & kelas renang (pelatih tidak akan melihat siswa di presensi).</li>
                    <li><strong class="text-emerald-700">Riwayat Tetap Aman:</strong> Histori presensi & transaksi keuangan masa lalu tetap tersimpan utuh di sistem.</li>
                </ul>
            </div>

            <form id="deactivateForm" method="POST" action="" class="mt-6">
                @csrf
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeDeactivateModal()" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 font-semibold text-sm hover:bg-slate-50 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold text-sm rounded-xl transition shadow-sm flex items-center gap-2">
                        <i class="fa-solid fa-user-xmark"></i> Ya, Nonaktifkan & Tarik dari Jadwal
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const deactivateBaseUrl = "{{ route('finance.unpaid.deactivate', ':id') }}";

        function openDeactivateModal(studentId, studentName, remainingMeetings) {
            document.getElementById('modalStudentName').textContent = studentName;
            document.getElementById('modalStudentRemaining').textContent = remainingMeetings + ' sesi';
            document.getElementById('deactivateForm').action = deactivateBaseUrl.replace(':id', studentId);

            const modal = document.getElementById('deactivateModal');
            const content = document.getElementById('deactivateModalContent');

            modal.classList.remove('hidden');
            void modal.offsetWidth;
            modal.classList.remove('opacity-0');
            content.classList.remove('scale-95');
            content.classList.add('scale-100');
        }

        function closeDeactivateModal() {
            const modal = document.getElementById('deactivateModal');
            const content = document.getElementById('deactivateModalContent');

            modal.classList.add('opacity-0');
            content.classList.remove('scale-100');
            content.classList.add('scale-95');

            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

        // Close on backdrop click
        document.getElementById('deactivateModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeDeactivateModal();
            }
        });

        // Close on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !document.getElementById('deactivateModal')?.classList.contains('hidden')) {
                closeDeactivateModal();
            }
        });
    </script>
</x-app-layout>
