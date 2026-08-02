<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-bold text-2xl text-slate-800 tracking-tight">
                    <i class="fa-solid fa-clipboard-list text-blue-600 mr-2"></i> Detail Raport: {{ $student->user->name ?? 'Murid' }}
                </h2>
                <p class="text-sm text-slate-500 mt-1"> Kelas: {{ $student->swimClasses->pluck('name')->join(', ') ?: '-' }}</p>
            </div>
            <div>
                <a href="{{ route('admin.report-cards.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-300 rounded-lg text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                    <i class="fa-solid fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-slate-50/50 min-h-screen" x-data="{ 
        showModal: false, 
        formAction: '', 
        adminNote: '',
        openModal(action, note) {
            this.formAction = action;
            this.adminNote = note;
            this.showModal = true;
        }
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-6 flex items-center bg-white p-4 rounded-xl border border-blue-100 shadow-sm max-w-sm">
                <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-xl mr-4">
                    <i class="fa-solid fa-person-swimming"></i>
                </div>
                <div>
                    <p class="text-sm text-slate-500 font-medium">Total Latihan Diikuti</p>
                    <p class="text-2xl font-bold text-slate-800">{{ $totalTrainings }} <span class="text-base font-medium text-slate-500">Pertemuan</span></p>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100 p-6">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-slate-600">
                        <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-200">
                            <tr>
                                <th scope="col" class="px-6 py-4 font-semibold">Tanggal</th>
                                <th scope="col" class="px-6 py-4 font-semibold">Pelatih</th>
                                <th scope="col" class="px-6 py-4 font-semibold">Kelas</th>
                                <th scope="col" class="px-6 py-4 font-semibold text-center">Pertemuan Ke-</th>
                                <th scope="col" class="px-6 py-4 font-semibold">Catatan Evaluasi</th>
                                <th scope="col" class="px-6 py-4 font-semibold">Catatan Admin</th>
                                <th scope="col" class="px-6 py-4 font-semibold text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($evaluations as $eval)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-medium text-slate-800">{{ \Carbon\Carbon::parse($eval->trainingReport->training_date)->translatedFormat('d F Y') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xs border border-blue-100">
                                                {{ strtoupper(substr($eval->trainingReport->coach->name ?? '?', 0, 2)) }}
                                            </div>
                                            <div class="font-semibold text-slate-800">{{ $eval->trainingReport->coach->name ?? 'Pelatih' }}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center justify-center bg-slate-100 text-slate-600 text-[10px] font-bold px-2 py-1 rounded">
                                            {{ $eval->trainingReport->schedule->poolLocation->package_name ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center justify-center bg-blue-100 text-blue-700 text-xs font-bold w-6 h-6 rounded-full">
                                            {{ $eval->meeting_number }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="text-slate-700 text-sm whitespace-pre-line">{{ $eval->evaluation }}</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="text-slate-700 text-sm whitespace-pre-line">{{ $eval->admin_note ?? '-' }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <button @click="openModal('{{ route('admin.report-cards.update-admin-note', [$student->id, $eval->id]) }}', `{{ addslashes(str_replace(["\r", "\n"], [' ', ' '], $eval->admin_note ?? '')) }}`)" class="text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 px-3 py-1 rounded-md text-xs font-semibold transition-colors">
                                            <i class="fa-solid fa-pen mr-1"></i> Edit
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                                        <div class="flex flex-col items-center justify-center text-slate-400">
                                            <i class="fa-regular fa-clipboard text-4xl mb-3"></i>
                                            <p class="text-base font-medium">Belum ada rapor evaluasi.</p>
                                            <p class="text-sm mt-1">Pelatih akan memberikan penilaian setelah sesi latihan berlangsung.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal Edit Catatan Admin -->
        <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-slate-900 bg-opacity-50 transition-opacity" @click="showModal = false" aria-hidden="true"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form :action="formAction" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                    <h3 class="text-lg leading-6 font-semibold text-slate-800" id="modal-title">
                                        Edit Catatan Admin
                                    </h3>
                                    <div class="mt-4 w-full">
                                        <textarea name="admin_note" x-model="adminNote" rows="4" class="w-full border-slate-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" placeholder="Tambahkan catatan admin di sini..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-slate-50 px-4 py-3 sm:px-6 flex justify-end gap-2">
                            <button type="button" @click="showModal = false" class="px-4 py-2 bg-white border border-slate-300 rounded-lg text-sm font-semibold text-slate-700 hover:bg-slate-50">
                                Batal
                            </button>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 shadow-sm shadow-blue-500/30">
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
