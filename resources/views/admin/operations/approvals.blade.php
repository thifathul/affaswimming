<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-slate-800 tracking-tight">
            {{ __('Approval Reschedule & Inval') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-r-lg text-emerald-700 font-medium text-sm flex items-center">
                    <i class="fa-solid fa-circle-check mr-3 text-lg"></i>
                    {{ session('success') }}
                </div>
            @endif

            <!-- Filter Form -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4 mb-6">
                <form action="{{ route('admin.operations.approvals') }}" method="GET" class="flex flex-col sm:flex-row gap-4 items-end">
                    <div class="flex-1 w-full">
                        <label for="type" class="block text-xs font-medium text-slate-500 mb-1">Tipe Pengajuan</label>
                        <select name="type" id="type" class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <option value="">Semua Tipe</option>
                            <option value="reschedule" {{ request('type') == 'reschedule' ? 'selected' : '' }}>Reschedule</option>
                            <option value="inval" {{ request('type') == 'inval' ? 'selected' : '' }}>Inval</option>
                        </select>
                    </div>
                    <div class="flex-1 w-full">
                        <label for="status" class="block text-xs font-medium text-slate-500 mb-1">Status</label>
                        <select name="status" id="status" class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <option value="">Semua Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu (Pending)</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                    </div>
                    <div>
                        <button type="submit" class="w-full sm:w-auto px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 flex items-center justify-center gap-2">
                            <i class="fa-solid fa-filter"></i> Filter
                        </button>
                    </div>
                    @if(request()->filled('type') || request()->filled('status'))
                        <div>
                            <a href="{{ route('admin.operations.approvals') }}" class="w-full sm:w-auto px-4 py-2 border border-slate-300 rounded-lg shadow-sm text-sm font-medium text-slate-700 bg-white hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 flex items-center justify-center">
                                Reset
                            </a>
                        </div>
                    @endif
                </form>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100 p-6">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Pelatih</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Tipe Pengajuan</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Detail Usulan</th>
                                <th class="px-6 py-3 text-center text-xs font-bold text-slate-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-slate-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-200">
                            @forelse($requests as $request)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-medium text-slate-900">{{ $request->schedule->coach->name ?? 'N/A' }}</div>
                                        <div class="text-xs text-slate-500">Jadwal Asli: {{ $request->schedule->day }}, {{ \Carbon\Carbon::parse($request->schedule->start_time)->format('H:i') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($request->type === 'reschedule')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-purple-100 text-purple-700">
                                                Reschedule
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-700">
                                                Inval
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700">
                                        <div><strong>Tgl:</strong> {{ \Carbon\Carbon::parse($request->proposed_date)->format('d M Y') }}</div>
                                        <div><strong>Jam:</strong> {{ \Carbon\Carbon::parse($request->proposed_start_time)->format('H:i') }}</div>
                                        @if($request->proposed_pool_location_id)
                                            <div class="text-xs mt-1 text-slate-800"><strong>Pindah Kolam:</strong> {{ $request->proposedPoolLocation->name ?? '-' }}</div>
                                        @else
                                            <div class="text-xs mt-1 text-slate-500"><strong>Kolam:</strong> Tetap ({{ $request->schedule->poolLocation->name ?? '-' }})</div>
                                        @endif
                                        @if($request->type === 'inval')
                                            <div class="text-xs mt-1 text-blue-600"><strong>Pengganti:</strong> {{ $request->substituteCoach->name ?? '-' }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if($request->status === 'pending')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700">Menunggu</span>
                                        @elseif($request->status === 'approved')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">Disetujui</span>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-700">Ditolak</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        @if($request->status === 'pending')
                                            <div class="flex items-center justify-end gap-3">
                                                <a href="{{ route('admin.operations.showApproval', $request->id) }}" class="text-blue-500 hover:text-blue-700 transition-colors text-base" title="Lihat Detail">
                                                    <i class="fa-regular fa-eye"></i>
                                                </a>
                                                <button onclick="openApprovalModal({{ $request->id }}, 'approved', '')" class="text-blue-600 hover:text-blue-900 font-medium">
                                                    Proses
                                                </button>
                                            </div>
                                        @else
                                            <div class="flex flex-col items-end justify-center">
                                                <div class="flex items-center gap-3">
                                                    <a href="{{ route('admin.operations.showApproval', $request->id) }}" class="text-blue-500 hover:text-blue-700 transition-colors text-base" title="Lihat Detail">
                                                        <i class="fa-regular fa-eye"></i>
                                                    </a>
                                                    <button onclick="openApprovalModal({{ $request->id }}, '{{ $request->status }}', '{{ addslashes($request->admin_note) }}')" class="text-orange-500 hover:text-orange-700 transition-colors text-base" title="Edit Status">
                                                        <i class="fa-regular fa-pen-to-square"></i>
                                                    </button>
                                                    <form action="{{ route('admin.operations.destroyApproval', $request->id) }}" method="POST" class="inline m-0" onsubmit="return confirm('Yakin ingin menghapus pengajuan ini secara permanen?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-500 hover:text-red-700 transition-colors text-base bg-transparent border-none p-0 cursor-pointer" title="Hapus Pengajuan">
                                                            <i class="fa-regular fa-trash-can"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-slate-500">
                                        Tidak ada pengajuan saat ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Approval -->
    <div id="approvalModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-slate-900 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeApprovalModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="approvalForm" method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-slate-900" id="modal-title">Proses Pengajuan</h3>
                                <div class="mt-4 space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-2">Keputusan</label>
                                        <select name="status" required class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                            <option value="approved">Approve (Setujui)</option>
                                            <option value="rejected">Reject (Tolak)</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-2">Catatan Admin (Opsional)</label>
                                        <textarea name="admin_note" rows="3" class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" placeholder="Tambahkan catatan jika perlu..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-slate-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse rounded-b-2xl">
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                            Simpan
                        </button>
                        <button type="button" onclick="closeApprovalModal()" class="mt-3 w-full inline-flex justify-center rounded-lg border border-slate-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-slate-700 hover:bg-slate-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openApprovalModal(requestId, currentStatus = 'approved', currentNote = '') {
            document.getElementById('approvalModal').classList.remove('hidden');
            document.getElementById('approvalForm').action = `/admin/operations/approvals/${requestId}`;
            
            // Set existing values if editing
            const statusSelect = document.querySelector('select[name="status"]');
            if(statusSelect) statusSelect.value = currentStatus;
            
            const noteTextarea = document.querySelector('textarea[name="admin_note"]');
            if(noteTextarea) noteTextarea.value = currentNote;
        }
        function closeApprovalModal() {
            document.getElementById('approvalModal').classList.add('hidden');
        }
    </script>
</x-app-layout>
