<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-slate-800 tracking-tight">
            {{ __('Pengeluaran Operasional') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
            <div class="mb-4 bg-emerald-50 text-emerald-700 p-4 rounded-xl border border-emerald-100 flex items-center gap-3">
                <i class="fa-solid fa-circle-check text-emerald-500 text-xl"></i>
                <p class="font-medium text-sm">{{ session('success') }}</p>
            </div>
            @endif

            <!-- Ringkasan Pemasukan per Kolam -->
            <div class="mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100 p-6">
                    <div class="flex flex-col md:flex-row md:items-center justify-between mb-4 border-b border-slate-100 pb-4">
                        <h3 class="text-lg font-bold text-slate-800">Sisa Saldo Pemasukan per Lokasi Kolam</h3>
                    </div>
                    @if($poolSummaries->isEmpty())
                        <div class="text-center py-6">
                            <p class="text-slate-500 font-medium">Belum ada data pemasukan.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-slate-50 border-y border-slate-100">
                                        <th class="py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Lokasi Kolam</th>
                                        <th class="py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Total Pemasukan (Harga)</th>
                                        <th class="py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Total Fee Pelatih</th>
                                        <th class="py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Total Kas</th>
                                        <th class="py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Total Tiket</th>
                                        <th class="py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Total Keuntungan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($poolSummaries as $summary)
                                        <tr class="hover:bg-slate-50/50 transition-colors">
                                            <td class="py-3 px-4 text-sm font-semibold text-slate-800 whitespace-nowrap">
                                                {{ $summary->pool_name }}
                                            </td>
                                            <td class="py-3 px-4 text-sm font-semibold text-slate-700 text-right whitespace-nowrap">
                                                Rp {{ number_format($summary->total_amount, 0, ',', '.') }}
                                            </td>
                                            <td class="py-3 px-4 text-sm text-right whitespace-nowrap {{ $summary->net_coach_fee < 0 ? 'text-red-500 font-bold' : 'text-blue-600' }}">
                                                Rp {{ number_format($summary->net_coach_fee, 0, ',', '.') }}
                                            </td>
                                            <td class="py-3 px-4 text-sm text-right whitespace-nowrap {{ $summary->net_cash < 0 ? 'text-red-500 font-bold' : 'text-amber-600' }}">
                                                Rp {{ number_format($summary->net_cash, 0, ',', '.') }}
                                            </td>
                                            <td class="py-3 px-4 text-sm text-right whitespace-nowrap {{ $summary->net_ticket < 0 ? 'text-red-500 font-bold' : 'text-rose-500' }}">
                                                Rp {{ number_format($summary->net_ticket, 0, ',', '.') }}
                                            </td>
                                            <td class="py-3 px-4 text-sm font-bold text-right whitespace-nowrap {{ $summary->net_profit < 0 ? 'text-red-600' : 'text-emerald-600' }}">
                                                Rp {{ number_format($summary->net_profit, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 gap-8">
                <!-- Daftar Pengeluaran -->
                <div class="md:col-span-1">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100 p-6">
                        <div class="flex flex-col md:flex-row md:items-center justify-between mb-4 border-b border-slate-100 pb-4">
                            <div class="flex items-center gap-3">
                                <h3 class="text-lg font-bold text-slate-800">Riwayat Pengeluaran</h3>
                                <button onclick="document.getElementById('addExpenseModal').classList.remove('hidden')" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm rounded-lg transition-all shadow-sm flex items-center gap-2">
                                    <i class="fa-solid fa-plus text-xs"></i> Buat Pengeluaran
                                </button>
                            </div>
                            <form action="{{ route('finance.expenses.index') }}" method="GET" class="mt-3 md:mt-0 flex gap-2">
                                <input type="month" name="month" value="{{ request('month') }}" class="rounded-xl border-slate-200 text-sm focus:ring-blue-500 focus:border-blue-500">
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari keterangan atau kolam..." class="rounded-xl border-slate-200 text-sm focus:ring-blue-500 focus:border-blue-500 min-w-[250px]">
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white font-semibold rounded-xl text-sm hover:bg-blue-700 transition">
                                    <i class="fa-solid fa-filter mr-1"></i> Filter
                                </button>
                                @if(request()->filled('search') || request()->filled('month'))
                                    <a href="{{ route('finance.expenses.index') }}" class="px-4 py-2 bg-slate-100 text-slate-600 font-semibold rounded-xl text-sm hover:bg-slate-200 transition">
                                        Reset
                                    </a>
                                @endif
                            </form>
                        </div>
                        
                        @if($expenses->isEmpty())
                            <div class="text-center py-10">
                                <i class="fa-regular fa-folder-open text-4xl text-slate-300 mb-3"></i>
                                <p class="text-slate-500 font-medium">Belum ada catatan pengeluaran.</p>
                            </div>
                        @else
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse">
                                    <thead>
                                        <tr class="bg-slate-50 border-y border-slate-100">
                                            <th class="py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Tanggal</th>
                                            <th class="py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Kategori</th>
                                            <th class="py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Kolam</th>
                                            <th class="py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Keterangan</th>
                                            <th class="py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Jumlah (Rp)</th>
                                            <th class="py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Bukti</th>
                                            @if(auth()->user()->role === 'master' || auth()->user()->role === 'admin')
                                                <th class="py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Aksi</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @foreach($expenses as $exp)
                                            <tr class="hover:bg-slate-50/50 transition-colors">
                                                <td class="py-3 px-4 text-sm text-slate-700 whitespace-nowrap">
                                                    {{ \Carbon\Carbon::parse($exp->expense_date)->format('d M Y') }}
                                                </td>
                                                <td class="py-3 px-4 text-sm">
                                                    <span class="px-2 py-1 bg-slate-100 text-slate-600 rounded-md text-xs font-semibold uppercase">{{ $exp->keyword }}</span>
                                                </td>
                                                <td class="py-3 px-4 text-sm text-slate-700 whitespace-nowrap">
                                                    {{ $exp->poolLocation->name ?? '-' }}
                                                </td>
                                                <td class="py-3 px-4 text-sm text-slate-600">
                                                    {{ Str::limit($exp->description, 50) }}
                                                </td>
                                                <td class="py-3 px-4 text-sm font-semibold text-slate-700 whitespace-nowrap">
                                                    {{ number_format($exp->amount, 0, ',', '.') }}
                                                </td>
                                                <td class="py-3 px-4 text-sm text-center">
                                                    @if($exp->proof_file)
                                                        <a href="{{ asset('storage/' . $exp->proof_file) }}" target="_blank" class="text-blue-600 hover:text-blue-800" title="Lihat Bukti">
                                                            <i class="fa-solid fa-file-invoice"></i>
                                                        </a>
                                                    @else
                                                        <span class="text-slate-300">-</span>
                                                    @endif
                                                </td>
                                                @if(auth()->user()->role === 'master' || auth()->user()->role === 'admin')
                                                    <td class="py-3 px-4 text-sm text-right">
                                                        <button type="button" onclick="openEditExpenseModal({{ $exp->id }}, '{{ \Carbon\Carbon::parse($exp->expense_date)->format('Y-m-d') }}', '{{ $exp->pool_location_id }}', '{{ $exp->keyword }}', {{ $exp->amount }}, '{{ addslashes($exp->description) }}')" class="text-blue-500 hover:text-blue-700 transition mr-2" title="Edit">
                                                            <i class="fa-solid fa-pen-to-square"></i>
                                                        </button>
                                                        @if(auth()->user()->role === 'master')
                                                            <form action="{{ route('finance.expenses.destroy', $exp) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus catatan pengeluaran ini?');" class="inline-block">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="text-rose-500 hover:text-rose-700 transition" title="Hapus">
                                                                    <i class="fa-solid fa-trash-can"></i>
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </td>
                                                @endif
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
    </div>

    <!-- Modal Catat Pengeluaran -->
    <div id="addExpenseModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-slate-900 bg-opacity-75 transition-opacity backdrop-blur-sm" aria-hidden="true" onclick="document.getElementById('addExpenseModal').classList.add('hidden')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-100">
                <form action="{{ route('finance.expenses.store') }}" method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                                <i class="fa-solid fa-money-bill-transfer text-blue-600"></i>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-bold text-slate-900" id="modal-title">
                                    Catat Pengeluaran
                                </h3>
                                
                                <div class="mt-4 space-y-4">
                                    <div>
                                        <label class="block text-sm font-bold text-slate-700 mb-2">Tanggal Pengeluaran <span class="text-red-500">*</span></label>
                                        <input type="date" name="expense_date" required class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-sm">
                                        @error('expense_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-bold text-slate-700 mb-2">Lokasi Kolam (Opsional)</label>
                                        <select name="pool_location_id" class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-sm">
                                            <option value="">-- Tidak Spesifik / Umum --</option>
                                            @foreach($poolLocations as $pool)
                                                <option value="{{ $pool->id }}">{{ $pool->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('pool_location_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-bold text-slate-700 mb-2">Keyword/Kategori <span class="text-red-500">*</span></label>
                                        <select name="keyword" required class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-sm">
                                            <option value="">-- Pilih Kategori --</option>
                                            <option value="gaji">Gaji</option>
                                            <option value="tiket">Tiket</option>
                                            <option value="kas">Kas</option>
                                            <option value="lainnya">Lainnya</option>
                                        </select>
                                        @error('keyword') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-bold text-slate-700 mb-2">Jumlah (Rp) <span class="text-red-500">*</span></label>
                                        <input type="text" id="amount" name="amount" required placeholder="Contoh: 150.000" class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-sm">
                                        @error('amount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-bold text-slate-700 mb-2">Keterangan <span class="text-red-500">*</span></label>
                                        <textarea name="description" required rows="3" class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-sm" placeholder="Detail pengeluaran..."></textarea>
                                        @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-slate-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-slate-100">
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                            Simpan Data
                        </button>
                        <button type="button" onclick="document.getElementById('addExpenseModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-lg border border-slate-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Modal Edit Pengeluaran -->
    <div id="editExpenseModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-slate-900 bg-opacity-75 transition-opacity backdrop-blur-sm" aria-hidden="true" onclick="document.getElementById('editExpenseModal').classList.add('hidden')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-100">
                <form id="editExpenseForm" action="" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                                <i class="fa-solid fa-pen-to-square text-blue-600"></i>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-bold text-slate-900" id="modal-title">
                                    Edit Pengeluaran
                                </h3>
                                
                                <div class="mt-4 space-y-4">
                                    <div>
                                        <label class="block text-sm font-bold text-slate-700 mb-2">Tanggal Pengeluaran <span class="text-red-500">*</span></label>
                                        <input type="date" id="edit_expense_date" name="expense_date" required class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-sm">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-bold text-slate-700 mb-2">Lokasi Kolam (Opsional)</label>
                                        <select id="edit_pool_location_id" name="pool_location_id" class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-sm">
                                            <option value="">-- Tidak Spesifik / Umum --</option>
                                            @foreach($poolLocations as $pool)
                                                <option value="{{ $pool->id }}">{{ $pool->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-bold text-slate-700 mb-2">Keyword/Kategori <span class="text-red-500">*</span></label>
                                        <select id="edit_keyword" name="keyword" required class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-sm">
                                            <option value="">-- Pilih Kategori --</option>
                                            <option value="gaji">Gaji</option>
                                            <option value="tiket">Tiket</option>
                                            <option value="kas">Kas</option>
                                            <option value="lainnya">Lainnya</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-bold text-slate-700 mb-2">Jumlah (Rp) <span class="text-red-500">*</span></label>
                                        <input type="text" id="edit_amount" name="amount" required class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-sm">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-bold text-slate-700 mb-2">Keterangan <span class="text-red-500">*</span></label>
                                        <textarea id="edit_description" name="description" required rows="3" class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-sm"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-slate-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-slate-100">
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                            Simpan Perubahan
                        </button>
                        <button type="button" onclick="document.getElementById('editExpenseModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-lg border border-slate-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openEditExpenseModal(id, date, poolId, keyword, amount, desc) {
            document.getElementById('editExpenseForm').action = `/finance/expenses/${id}`;
            document.getElementById('edit_expense_date').value = date;
            document.getElementById('edit_pool_location_id').value = poolId;
            document.getElementById('edit_keyword').value = keyword;
            document.getElementById('edit_amount').value = formatRupiah(amount.toString());
            document.getElementById('edit_description').value = desc;
            
            document.getElementById('editExpenseModal').classList.remove('hidden');
        }

        function formatRupiah(angka) {
            var number_string = angka.replace(/[^,\d]/g, '').toString(),
                split = number_string.split(','),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
            return rupiah;
        }

        document.addEventListener('DOMContentLoaded', function() {
            var amountInput = document.getElementById('amount');
            if(amountInput) {
                amountInput.addEventListener('keyup', function(e) {
                    this.value = formatRupiah(this.value);
                });
            }

            var editAmountInput = document.getElementById('edit_amount');
            if(editAmountInput) {
                editAmountInput.addEventListener('keyup', function(e) {
                    this.value = formatRupiah(this.value);
                });
            }
        });
    </script>
</x-app-layout>
