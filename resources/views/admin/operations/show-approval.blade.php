<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.operations.approvals') }}" class="text-slate-500 hover:text-slate-700 transition-colors bg-slate-100 hover:bg-slate-200 p-2 rounded-full w-8 h-8 flex items-center justify-center">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <h2 class="font-bold text-2xl text-slate-800 tracking-tight">
                {{ __('Detail Pengajuan ' . ucfirst($scheduleRequest->type)) }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12 bg-slate-50/50 min-h-screen">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <!-- Info Jadwal Asli -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex flex-col relative overflow-hidden">
                    <div class="absolute top-0 right-0 bg-slate-100 px-4 py-1 text-xs font-bold text-slate-500 rounded-bl-xl border-b border-l border-slate-200">
                        Jadwal Asli
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 border-l-4 border-slate-400 pl-3 mb-6">Informasi Jadwal Awal</h3>
                    
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <div class="bg-slate-50 text-slate-500 p-3 rounded-xl mr-4 flex-shrink-0">
                                <i class="fa-solid fa-person-swimming text-xl"></i>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400 uppercase font-semibold mb-1">Pelatih Utama</p>
                                <p class="text-slate-800 font-medium">{{ $scheduleRequest->schedule->coach->name ?? 'Dihapus' }}</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="bg-slate-50 text-slate-500 p-3 rounded-xl mr-4 flex-shrink-0">
                                <i class="fa-solid fa-calendar-day text-xl"></i>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400 uppercase font-semibold mb-1">Hari & Jam</p>
                                <p class="text-slate-800 font-medium">{{ $scheduleRequest->schedule->day }}, {{ \Carbon\Carbon::parse($scheduleRequest->schedule->start_time)->format('H:i') }}</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="bg-slate-50 text-slate-500 p-3 rounded-xl mr-4 flex-shrink-0">
                                <i class="fa-solid fa-location-dot text-xl"></i>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400 uppercase font-semibold mb-1">Lokasi Kolam</p>
                                <p class="text-slate-800 font-medium">{{ $scheduleRequest->schedule->poolLocation->name ?? 'Dihapus' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detail Usulan Baru -->
                <div class="bg-white rounded-2xl shadow-sm border {{ $scheduleRequest->type === 'inval' ? 'border-amber-200' : 'border-purple-200' }} p-6 flex flex-col relative overflow-hidden">
                    <div class="absolute top-0 right-0 {{ $scheduleRequest->type === 'inval' ? 'bg-amber-100 text-amber-700 border-amber-200' : 'bg-purple-100 text-purple-700 border-purple-200' }} px-4 py-1 text-xs font-bold rounded-bl-xl border-b border-l">
                        Usulan Perubahan
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 border-l-4 {{ $scheduleRequest->type === 'inval' ? 'border-amber-400' : 'border-purple-400' }} pl-3 mb-6">Detail Pengajuan Baru</h3>
                    
                    <div class="space-y-4">
                        @if($scheduleRequest->type === 'inval')
                            <div class="flex items-start">
                                <div class="bg-amber-50 text-amber-500 p-3 rounded-xl mr-4 flex-shrink-0">
                                    <i class="fa-solid fa-user-plus text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-amber-600/70 uppercase font-semibold mb-1">Pelatih Pengganti (Inval)</p>
                                    <p class="text-slate-800 font-bold">{{ $scheduleRequest->substituteCoach->name ?? 'Tidak ada' }}</p>
                                </div>
                            </div>
                        @else
                            <div class="flex items-start">
                                <div class="bg-purple-50 text-purple-500 p-3 rounded-xl mr-4 flex-shrink-0">
                                    <i class="fa-solid fa-calendar-check text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-purple-600/70 uppercase font-semibold mb-1">Tipe Pengajuan</p>
                                    <p class="text-slate-800 font-bold">Reschedule Jadwal</p>
                                </div>
                            </div>
                        @endif

                        <div class="flex items-start">
                            <div class="bg-blue-50 text-blue-500 p-3 rounded-xl mr-4 flex-shrink-0">
                                <i class="fa-solid fa-clock text-xl"></i>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400 uppercase font-semibold mb-1">Tanggal & Jam Baru</p>
                                <p class="text-slate-800 font-medium">
                                    {{ \Carbon\Carbon::parse($scheduleRequest->proposed_date)->translatedFormat('l, d F Y') }} <br>
                                    Pukul {{ \Carbon\Carbon::parse($scheduleRequest->proposed_start_time)->format('H:i') }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="bg-rose-50 text-rose-500 p-3 rounded-xl mr-4 flex-shrink-0">
                                <i class="fa-solid fa-map-location-dot text-xl"></i>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400 uppercase font-semibold mb-1">Lokasi Kolam Usulan</p>
                                @if($scheduleRequest->proposed_pool_location_id)
                                    <p class="text-slate-800 font-medium">{{ $scheduleRequest->proposedPoolLocation->name ?? '-' }} <span class="text-xs bg-rose-100 text-rose-700 px-2 py-0.5 rounded ml-2 font-bold">Pindah Lokasi</span></p>
                                @else
                                    <p class="text-slate-800 font-medium">Tetap ({{ $scheduleRequest->schedule->poolLocation->name ?? '-' }})</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alasan & Status Area -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Alasan Pelatih -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                    <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2 mb-4">
                        <i class="fa-regular fa-comment-dots text-blue-500"></i>
                        Alasan Pengajuan
                    </h3>
                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 text-slate-700 leading-relaxed italic">
                        "{{ $scheduleRequest->reason }}"
                    </div>
                </div>

                <!-- Panel Keputusan Admin -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                    <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2 mb-4">
                        <i class="fa-solid fa-gavel text-slate-500"></i>
                        Keputusan Admin
                    </h3>
                    
                    <!-- Status Saat Ini -->
                    <div class="mb-6 flex items-center gap-4">
                        <span class="text-sm text-slate-500 font-medium">Status Saat Ini:</span>
                        @if($scheduleRequest->status === 'pending')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-700">
                                <i class="fa-regular fa-clock mr-1.5"></i> Menunggu
                            </span>
                        @elseif($scheduleRequest->status === 'approved')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">
                                <i class="fa-solid fa-check mr-1.5"></i> Disetujui
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700">
                                <i class="fa-solid fa-xmark mr-1.5"></i> Ditolak
                            </span>
                        @endif
                    </div>

                    <!-- Form Proses Keputusan -->
                    <form action="{{ route('admin.operations.updateApproval', $scheduleRequest->id) }}" method="POST" class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-slate-700 mb-2">Ubah Status</label>
                            <div class="flex gap-4" id="statusButtons">
                                <label class="flex-1 cursor-pointer">
                                    <input type="radio" name="status" value="approved" class="sr-only status-radio" {{ $scheduleRequest->status === 'approved' ? 'checked' : '' }} required>
                                    <div class="status-btn text-center px-4 py-2 rounded-lg border-2 border-slate-200 bg-white shadow-sm text-slate-600 font-medium transition-all" data-active-class="border-emerald-500 bg-emerald-50 text-emerald-700" data-inactive-class="border-slate-200 bg-white text-slate-600">
                                        <i class="fa-solid fa-check mr-2"></i> Approve
                                    </div>
                                </label>
                                <label class="flex-1 cursor-pointer">
                                    <input type="radio" name="status" value="rejected" class="sr-only status-radio" {{ $scheduleRequest->status === 'rejected' ? 'checked' : '' }} required>
                                    <div class="status-btn text-center px-4 py-2 rounded-lg border-2 border-slate-200 bg-white shadow-sm text-slate-600 font-medium transition-all" data-active-class="border-red-500 bg-red-50 text-red-700" data-inactive-class="border-slate-200 bg-white text-slate-600">
                                        <i class="fa-solid fa-xmark mr-2"></i> Reject
                                    </div>
                                </label>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-slate-700 mb-2">Catatan Admin (Opsional)</label>
                            <textarea name="admin_note" rows="3" class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" placeholder="Tulis catatan persetujuan atau alasan penolakan...">{{ $scheduleRequest->admin_note }}</textarea>
                        </div>

                        <button type="submit" class="w-full bg-slate-800 hover:bg-slate-900 text-white font-medium py-2 px-4 rounded-lg transition-colors shadow-sm">
                            Simpan Keputusan
                        </button>
                    </form>

                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const radios = document.querySelectorAll('.status-radio');
                            
                            function updateStyles() {
                                radios.forEach(radio => {
                                    const btn = radio.nextElementSibling;
                                    const activeClasses = btn.getAttribute('data-active-class').split(' ');
                                    const inactiveClasses = btn.getAttribute('data-inactive-class').split(' ');
                                    
                                    if (radio.checked) {
                                        btn.classList.remove(...inactiveClasses);
                                        btn.classList.add(...activeClasses);
                                    } else {
                                        btn.classList.remove(...activeClasses);
                                        btn.classList.add(...inactiveClasses);
                                    }
                                });
                            }
                            
                            radios.forEach(radio => {
                                radio.addEventListener('change', updateStyles);
                            });
                            
                            // Initialize on load
                            updateStyles();
                        });
                    </script>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
