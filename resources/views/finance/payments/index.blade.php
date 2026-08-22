<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-slate-800 tracking-tight">
                {{ __('Daftar Pembayaran Murid') }}
            </h2>
            <a href="{{ route('finance.payments.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-xl font-semibold text-sm hover:bg-blue-700 transition shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Buat Pembelian
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
            <div class="mb-4 bg-emerald-50 text-emerald-700 p-4 rounded-xl border border-emerald-100 flex items-center gap-3">
                <i class="fa-solid fa-circle-check text-emerald-500 text-xl"></i>
                <p class="font-medium text-sm">{{ session('success') }}</p>
            </div>
            @endif

            @if(session('error'))
            <div class="mb-4 bg-red-50 text-red-700 p-4 rounded-xl border border-red-100 flex items-center gap-3">
                <i class="fa-solid fa-circle-exclamation text-red-500 text-xl"></i>
                <p class="font-medium text-sm">{{ session('error') }}</p>
            </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100">
                <div class="p-6">
                    
                    @if($transactions->isEmpty())
                        <div class="text-center py-10">
                            <i class="fa-regular fa-folder-open text-4xl text-slate-300 mb-3"></i>
                            <p class="text-slate-500 font-medium">Belum ada transaksi pembayaran.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-slate-50 border-y border-slate-100">
                                        <th class="py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Murid</th>
                                        <th class="py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Paket</th>
                                        <th class="py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Jumlah</th>
                                        <th class="py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Kredit</th>
                                        <th class="py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Bukti</th>
                                        <th class="py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Metode</th>
                                        <th class="py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                                        <th class="py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($transactions as $trx)
                                        <tr class="hover:bg-slate-50/50 transition-colors">
                                            <td class="py-3 px-4">
                                                <p class="text-sm font-bold text-slate-800">{{ $trx->student->name ?? 'Murid Dihapus' }}</p>
                                                <p class="text-xs text-slate-500">{{ \Carbon\Carbon::parse($trx->practice_start_date)->format('d M Y') }}</p>
                                            </td>
                                            <td class="py-3 px-4">
                                                <p class="text-sm text-slate-700">{{ $trx->poolLocation->name ?? '-' }}</p>
                                                <p class="text-xs text-slate-500">{{ $trx->package_type }} Pertemuan ({{ ucfirst($trx->class_type) }})</p>
                                            </td>
                                            <td class="py-3 px-4">
                                                <div class="text-sm font-semibold text-slate-700">
                                                    Rp {{ number_format($trx->amount, 0, ',', '.') }}
                                                </div>
                                            </td>
                                            <td class="py-3 px-4">
                                                @if($trx->credit > 0)
                                                    <div class="text-sm font-semibold text-red-600">
                                                        Rp {{ number_format($trx->credit, 0, ',', '.') }}
                                                    </div>
                                                @else
                                                    <span class="text-xs text-slate-400">-</span>
                                                @endif
                                            </td>
                                            <td class="py-3 px-4">
                                                <a href="{{ asset('storage/' . $trx->proof_of_payment) }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                                    Lihat Foto
                                                </a>
                                            </td>
                                            <td class="py-3 px-4">
                                                @if($trx->payment_method)
                                                    <span class="text-xs font-semibold text-slate-700 bg-slate-100 px-2 py-1 rounded">{{ $trx->payment_method }}</span>
                                                @else
                                                    <span class="text-xs text-slate-400">-</span>
                                                @endif
                                            </td>
                                            <td class="py-3 px-4">
                                                @if($trx->status === 'approved')
                                                    <span class="px-2 py-1 bg-emerald-100 text-emerald-700 text-xs font-bold rounded-full">Approved</span>
                                                @elseif($trx->status === 'rejected')
                                                    <span class="px-2 py-1 bg-red-100 text-red-700 text-xs font-bold rounded-full">Rejected</span>
                                                @else
                                                    <span class="px-2 py-1 bg-amber-100 text-amber-700 text-xs font-bold rounded-full">Pending</span>
                                                @endif
                                            </td>
                                            <td class="py-3 px-4 text-right">
                                                <div class="flex items-center justify-end gap-2">
                                                    @if($trx->status === 'pending')
                                                        <button type="button" onclick="openApproveModal({{ $trx->id }})" class="px-3 py-1.5 bg-emerald-600 text-white text-xs font-bold rounded-lg hover:bg-emerald-700 transition">
                                                            Approve
                                                        </button>
                                                        <form action="{{ route('finance.payments.reject', $trx->id) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" onclick="return confirm('Tolak pembayaran ini?')" class="px-3 py-1.5 bg-red-600 text-white text-xs font-bold rounded-lg hover:bg-red-700 transition">
                                                                Reject
                                                            </button>
                                                        </form>
                                                    @else
                                                        @if($trx->status === 'approved')
                                                            @if($trx->credit > 0)
                                                                <button type="button" onclick="openSettleModal({{ $trx->id }})" class="px-3 py-1.5 bg-blue-600 text-white text-xs font-bold rounded-lg hover:bg-blue-700 transition">
                                                                    Lunas
                                                                </button>
                                                            @endif
                                                            <a href="{{ route('student.payments.receipt', $trx->id) }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-xs font-bold">
                                                                Lihat Kuitansi
                                                            </a>
                                                        @else
                                                            <span class="text-xs text-slate-400">Selesai diproses</span>
                                                        @endif
                                                    @endif
                                                    
                                                    @php
                                                        $detailData = [
                                                            "student" => $trx->student?->user?->name ?? $trx->manual_student_name ?? '-',
                                                            "pool" => $trx->poolLocation?->name ?? "-",
                                                            "package" => $trx->package_type . " Pertemuan (" . ucfirst($trx->class_type) . ")",
                                                            "amount" => "Rp " . number_format($trx->amount, 0, ",", "."),
                                                            "credit" => $trx->credit > 0 ? "Rp " . number_format($trx->credit, 0, ",", ".") : "-",
                                                            "payment_method" => $trx->payment_method ?: "-",
                                                            "notes" => $trx->notes ?: "-",
                                                            "date" => \Carbon\Carbon::parse($trx->created_at)->format("d M Y H:i"),
                                                            "status" => ucfirst($trx->status)
                                                        ];
                                                    @endphp
                                                    <button type="button" onclick='openDetailModal(@json($detailData))' class="ml-2 px-2 py-1.5 bg-slate-100 text-slate-600 rounded hover:bg-slate-200 transition" title="Detail Lengkap">
                                                        <i class="fa-solid fa-circle-info"></i>
                                                    </button>

                                                    @if(auth()->user()->role === 'admin' || auth()->user()->role === 'master')
                                                        <a href="{{ route('finance.payments.edit', $trx->id) }}" class="ml-2 px-2 py-1.5 bg-blue-50 text-blue-600 rounded hover:bg-blue-100 transition" title="Edit Data">
                                                            <i class="fa-solid fa-pen-to-square"></i>
                                                        </a>
                                                    @endif

                                                    @if(auth()->user()->role === 'master' || auth()->user()->role === 'admin')
                                                        <form action="{{ route('finance.payments.destroy', $trx->id) }}" method="POST" class="ml-2">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" onclick="return confirm('Yakin ingin menghapus data pembayaran ini?')" class="px-2 py-1.5 bg-red-100 text-red-600 rounded hover:bg-red-200 transition" title="Hapus Data">
                                                                <i class="fa-solid fa-trash-can"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Approve Modal -->
    <div id="approveModal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black/50 backdrop-blur-sm transition-opacity opacity-0">
        <div class="bg-white rounded-2xl w-full max-w-md p-6 shadow-xl transform scale-95 transition-transform duration-300" id="approveModalContent">
            <h3 class="text-lg font-bold text-slate-800 mb-4">Konfirmasi Pembayaran</h3>
            <p class="text-sm text-slate-500 mb-5">Pilih metode pembayaran (rekening tujuan/cash) untuk mengkonfirmasi penerimaan dana.</p>
            
            <form id="approveForm" method="POST" action="">
                @csrf
                <div class="mb-5">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Metode Pembayaran</label>
                    <select name="payment_method" required class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-sm">
                        <option value="">-- Pilih Metode --</option>
                        <option value="Bank BCAS">Bank BCAS</option>
                        <option value="Bank BSI">Bank BSI</option>
                        <option value="Cash">Cash (Tunai)</option>
                    </select>
                </div>
                
                <div class="mb-5">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Kredit / Sisa Belum Bayar (Opsional)</label>
                    <input type="text" name="credit" id="approve_credit" class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-sm" placeholder="Contoh: 100.000">
                </div>

                <div class="mb-5">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Catatan (Opsional)</label>
                    <textarea name="notes" rows="2" class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-sm" placeholder="Tambahkan catatan approval jika ada..."></textarea>
                </div>
                
                <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                    <button type="button" onclick="closeApproveModal()" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 font-semibold text-sm hover:bg-slate-50 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 text-white font-semibold text-sm hover:bg-emerald-700 shadow-sm transition">
                        Konfirmasi & Approve
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openApproveModal(transactionId) {
            const modal = document.getElementById('approveModal');
            const form = document.getElementById('approveForm');
            const content = document.getElementById('approveModalContent');
            
            form.action = `/finance/payments/${transactionId}/approve`;
            
            modal.classList.remove('hidden');
            // Trigger reflow
            void modal.offsetWidth;
            modal.classList.remove('opacity-0');
            content.classList.remove('scale-95');
        }

        function closeApproveModal() {
            const modal = document.getElementById('approveModal');
            const content = document.getElementById('approveModalContent');
            
            modal.classList.add('opacity-0');
            content.classList.add('scale-95');
            
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

        function openSettleModal(transactionId) {
            const modal = document.getElementById('settleModal');
            const form = document.getElementById('settleForm');
            const content = document.getElementById('settleModalContent');
            
            form.action = `/finance/payments/${transactionId}/settle`;
            
            modal.classList.remove('hidden');
            void modal.offsetWidth;
            modal.classList.remove('opacity-0');
            content.classList.remove('scale-95');
        }

        function closeSettleModal() {
            const modal = document.getElementById('settleModal');
            const content = document.getElementById('settleModalContent');
            
            modal.classList.add('opacity-0');
            content.classList.add('scale-95');
            
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const creditInput = document.getElementById('approve_credit');
            if(creditInput) {
                creditInput.addEventListener('input', function(e) {
                    let value = this.value.replace(/[^0-9]/g, '');
                    if (value) {
                        this.value = parseInt(value, 10).toLocaleString('id-ID');
                    } else {
                        this.value = '';
                    }
                });
            }
        });

        function openDetailModal(data) {
            const modal = document.getElementById('detailModal');
            const content = document.getElementById('detailModalContent');
            
            document.getElementById('detail_student').textContent = data.student;
            document.getElementById('detail_pool').textContent = data.pool;
            document.getElementById('detail_package').textContent = data.package;
            document.getElementById('detail_amount').textContent = data.amount;
            document.getElementById('detail_credit').textContent = data.credit;
            document.getElementById('detail_method').textContent = data.payment_method;
            document.getElementById('detail_date').textContent = data.date;
            document.getElementById('detail_status').textContent = data.status;
            
            const notesEl = document.getElementById('detail_notes');
            notesEl.innerHTML = data.notes.replace(/\n/g, '<br>');
            
            modal.classList.remove('hidden');
            void modal.offsetWidth;
            modal.classList.remove('opacity-0');
            content.classList.remove('scale-95');
        }

        function closeDetailModal() {
            const modal = document.getElementById('detailModal');
            const content = document.getElementById('detailModalContent');
            
            modal.classList.add('opacity-0');
            content.classList.add('scale-95');
            
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }
    </script>

    <!-- Detail Modal -->
    <div id="detailModal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black/50 backdrop-blur-sm transition-opacity opacity-0">
        <div class="bg-white rounded-2xl w-full max-w-lg p-6 shadow-xl transform scale-95 transition-transform duration-300" id="detailModalContent">
            <div class="flex justify-between items-center mb-4 border-b border-slate-100 pb-3">
                <h3 class="text-lg font-bold text-slate-800">Detail Pembayaran</h3>
                <button type="button" onclick="closeDetailModal()" class="text-slate-400 hover:text-slate-600 transition">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            
            <div class="space-y-3 text-sm">
                <div class="flex border-b border-slate-50 pb-2">
                    <div class="w-1/3 font-semibold text-slate-500">Nama Murid</div>
                    <div class="w-2/3 font-medium text-slate-800" id="detail_student"></div>
                </div>
                <div class="flex border-b border-slate-50 pb-2">
                    <div class="w-1/3 font-semibold text-slate-500">Tanggal</div>
                    <div class="w-2/3 font-medium text-slate-800" id="detail_date"></div>
                </div>
                <div class="flex border-b border-slate-50 pb-2">
                    <div class="w-1/3 font-semibold text-slate-500">Kolam</div>
                    <div class="w-2/3 font-medium text-slate-800" id="detail_pool"></div>
                </div>
                <div class="flex border-b border-slate-50 pb-2">
                    <div class="w-1/3 font-semibold text-slate-500">Paket</div>
                    <div class="w-2/3 font-medium text-slate-800" id="detail_package"></div>
                </div>
                <div class="flex border-b border-slate-50 pb-2">
                    <div class="w-1/3 font-semibold text-slate-500">Jumlah Dibayar</div>
                    <div class="w-2/3 font-bold text-emerald-600" id="detail_amount"></div>
                </div>
                <div class="flex border-b border-slate-50 pb-2">
                    <div class="w-1/3 font-semibold text-slate-500">Sisa / Kredit</div>
                    <div class="w-2/3 font-bold text-red-600" id="detail_credit"></div>
                </div>
                <div class="flex border-b border-slate-50 pb-2">
                    <div class="w-1/3 font-semibold text-slate-500">Metode</div>
                    <div class="w-2/3 font-medium text-slate-800" id="detail_method"></div>
                </div>
                <div class="flex border-b border-slate-50 pb-2">
                    <div class="w-1/3 font-semibold text-slate-500">Status</div>
                    <div class="w-2/3 font-medium text-slate-800" id="detail_status"></div>
                </div>
                <div class="flex flex-col pt-2">
                    <div class="font-semibold text-slate-500 mb-1">Keterangan & Riwayat Pelunasan:</div>
                    <div class="font-medium text-slate-800 bg-slate-50 p-3 rounded-lg border border-slate-100" id="detail_notes"></div>
                </div>
            </div>
            
            <div class="mt-6 flex justify-end">
                <button type="button" onclick="closeDetailModal()" class="px-5 py-2 rounded-xl bg-slate-800 text-white font-semibold text-sm hover:bg-slate-900 shadow-sm transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- Settle Modal -->
    <div id="settleModal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black/50 backdrop-blur-sm transition-opacity opacity-0">
        <div class="bg-white rounded-2xl w-full max-w-md p-6 shadow-xl transform scale-95 transition-transform duration-300" id="settleModalContent">
            <h3 class="text-lg font-bold text-slate-800 mb-4">Pelunasan Hutang</h3>
            <p class="text-sm text-slate-500 mb-5">Masukkan detail pelunasan di bawah ini.</p>
            
            <form id="settleForm" method="POST" action="">
                @csrf
                <div class="mb-5">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Metode Pembayaran</label>
                    <select name="payment_method" required class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-sm">
                        <option value="">-- Pilih Metode --</option>
                        <option value="Bank BCAS">Bank BCAS</option>
                        <option value="Bank BSI">Bank BSI</option>
                        <option value="Cash">Cash (Tunai)</option>
                    </select>
                </div>

                <div class="mb-5">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Penerima / Catatan Tambahan</label>
                    <textarea name="settle_note" required rows="2" class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-sm" placeholder="Contoh: Diterima tunai oleh Budi..."></textarea>
                </div>
                
                <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                    <button type="button" onclick="closeSettleModal()" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 font-semibold text-sm hover:bg-slate-50 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-blue-600 text-white font-semibold text-sm hover:bg-blue-700 shadow-sm transition">
                        Konfirmasi Pelunasan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
