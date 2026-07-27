<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-slate-800 tracking-tight">
            {{ __('Pemasukan Operasional') }}
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

            <div class="grid grid-cols-1 gap-8">
                <!-- Daftar Pemasukan -->
                <div class="md:col-span-1">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100 p-6">
                        <div class="flex flex-col md:flex-row md:items-center justify-between mb-4 border-b border-slate-100 pb-4">
                            <div class="flex items-center gap-3">
                                <h3 class="text-lg font-bold text-slate-800">Riwayat Pemasukan</h3>
                                <button onclick="document.getElementById('addIncomeModal').classList.remove('hidden')" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm rounded-lg transition-all shadow-sm flex items-center gap-2">
                                    <i class="fa-solid fa-plus text-xs"></i> Buat Pemasukan
                                </button>
                            </div>
                            <form action="{{ route('finance.incomes.index') }}" method="GET" class="mt-3 md:mt-0 flex gap-2">
                                <input type="month" name="month" value="{{ request('month') }}" class="rounded-xl border-slate-200 text-sm focus:ring-blue-500 focus:border-blue-500">
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau paket..." class="rounded-xl border-slate-200 text-sm focus:ring-blue-500 focus:border-blue-500 min-w-[250px]">
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white font-semibold rounded-xl text-sm hover:bg-blue-700 transition">
                                    <i class="fa-solid fa-filter mr-1"></i> Filter
                                </button>
                                @if(request()->filled('search') || request()->filled('month'))
                                    <a href="{{ route('finance.incomes.index') }}" class="px-4 py-2 bg-slate-100 text-slate-600 font-semibold rounded-xl text-sm hover:bg-slate-200 transition">
                                        Reset
                                    </a>
                                @endif
                            </form>
                        </div>
                        
                        @if($incomes->isEmpty())
                            <div class="text-center py-10">
                                <i class="fa-regular fa-folder-open text-4xl text-slate-300 mb-3"></i>
                                <p class="text-slate-500 font-medium">Belum ada catatan pemasukan.</p>
                            </div>
                        @else
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse">
                                    <thead>
                                        <tr class="bg-slate-50 border-y border-slate-100">
                                            <th class="py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Nama Murid</th>
                                            <th class="py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Tanggal</th>
                                            <th class="py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Paket</th>
                                            <th class="py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Harga</th>
                                            <th class="py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Fee Pelatih</th>
                                            <th class="py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Kas</th>
                                            <th class="py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Tiket</th>
                                            <th class="py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Keuntungan</th>
                                            @if(auth()->user()->role === 'master')
                                                <th class="py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Aksi</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @foreach($incomes as $inc)
                                            <tr class="hover:bg-slate-50/50 transition-colors">
                                                <td class="py-3 px-4 text-sm font-semibold text-slate-800 whitespace-nowrap">
                                                    {{ $inc->student_id ? ($inc->student->name ?? '-') : ($inc->manual_student_name ?? '-') }}
                                                </td>
                                                <td class="py-3 px-4 text-sm text-slate-700 whitespace-nowrap">
                                                    {{ \Carbon\Carbon::parse($inc->updated_at)->format('d M Y') }}
                                                </td>
                                                <td class="py-3 px-4 text-sm text-slate-600">
                                                    {{ $inc->poolLocation->package_name ?? '-' }} ({{ ucfirst(str_replace('_', ' ', $inc->class_type)) }})
                                                </td>
                                                <td class="py-3 px-4 text-sm font-semibold text-slate-700 text-right whitespace-nowrap">
                                                    {{ number_format($inc->amount, 0, ',', '.') }}
                                                </td>
                                                <td class="py-3 px-4 text-sm text-blue-600 text-right whitespace-nowrap">
                                                    {{ number_format($inc->coach_salary_cut, 0, ',', '.') }}
                                                </td>
                                                <td class="py-3 px-4 text-sm text-amber-600 text-right whitespace-nowrap">
                                                    {{ number_format($inc->cash_cut, 0, ',', '.') }}
                                                </td>
                                                <td class="py-3 px-4 text-sm text-rose-500 text-right whitespace-nowrap">
                                                    {{ number_format($inc->pool_ticket_cut, 0, ',', '.') }}
                                                </td>
                                                <td class="py-3 px-4 text-sm font-bold text-emerald-600 text-right whitespace-nowrap">
                                                    {{ number_format($inc->profit_cut, 0, ',', '.') }}
                                                </td>
                                                @if(auth()->user()->role === 'master')
                                                    <td class="py-3 px-4 text-sm text-center">
                                                        <form action="{{ route('finance.incomes.destroy', $inc) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus catatan pemasukan ini?');" class="inline-block">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-rose-500 hover:text-rose-700 transition" title="Hapus">
                                                                <i class="fa-solid fa-trash-can"></i>
                                                            </button>
                                                        </form>
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="bg-slate-50 border-t-2 border-slate-200">
                                        <tr>
                                            <th colspan="3" class="py-3 px-4 text-right text-sm font-bold text-slate-700">Total Keseluruhan</th>
                                            <th class="py-3 px-4 text-sm font-bold text-slate-800 text-right whitespace-nowrap">{{ number_format($incomes->sum('amount'), 0, ',', '.') }}</th>
                                            <th class="py-3 px-4 text-sm font-bold text-blue-700 text-right whitespace-nowrap">{{ number_format($incomes->sum('coach_salary_cut'), 0, ',', '.') }}</th>
                                            <th class="py-3 px-4 text-sm font-bold text-amber-700 text-right whitespace-nowrap">{{ number_format($incomes->sum('cash_cut'), 0, ',', '.') }}</th>
                                            <th class="py-3 px-4 text-sm font-bold text-rose-600 text-right whitespace-nowrap">{{ number_format($incomes->sum('pool_ticket_cut'), 0, ',', '.') }}</th>
                                            <th class="py-3 px-4 text-sm font-bold text-emerald-700 text-right whitespace-nowrap">{{ number_format($incomes->sum('profit_cut'), 0, ',', '.') }}</th>
                                            @if(auth()->user()->role === 'master')
                                                <th></th>
                                            @endif
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
        </div>
    </div>

    <!-- Modal Tambah Pemasukan -->
    <div id="addIncomeModal" class="fixed inset-0 z-50 hidden bg-slate-900/50 backdrop-blur-sm overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="relative bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-lg sm:w-full border border-slate-100">
                <form action="{{ route('finance.incomes.store') }}" method="POST">
                    @csrf
                    <div class="bg-white px-6 pt-6 pb-6">
                        <div class="flex justify-between items-center mb-5 pb-4 border-b border-slate-100">
                            <h3 class="text-lg font-bold text-slate-800" id="modal-title">Buat Pemasukan Manual</h3>
                            <button type="button" onclick="document.getElementById('addIncomeModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 transition-colors">
                                <i class="fa-solid fa-xmark text-xl"></i>
                            </button>
                        </div>
                        
                        <div class="space-y-4">
                            <div>
                                <label for="manual_student_name" class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Murid</label>
                                <input type="text" id="manual_student_name" name="manual_student_name" required placeholder="Contoh: Budi Santoso" class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 text-sm">
                            </div>
                            <div>
                                <label for="payment_date" class="block text-sm font-semibold text-slate-700 mb-1.5">Tanggal Pembayaran</label>
                                <input type="date" id="payment_date" name="payment_date" required value="{{ date('Y-m-d') }}" class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 text-sm">
                            </div>
                            <div>
                                <label for="practice_start_date" class="block text-sm font-semibold text-slate-700 mb-1.5">Tanggal Mulai</label>
                                <input type="date" id="practice_start_date" name="practice_start_date" required class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 text-sm">
                            </div>
                            <div>
                                <label for="pool_location_id" class="block text-sm font-semibold text-slate-700 mb-1.5">Paket</label>
                                <select id="pool_location_id" name="pool_location_id" required class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 text-sm">
                                    <option value="">-- Pilih Paket & Lokasi --</option>
                                    @foreach($poolLocations as $pool)
                                        <option value="{{ $pool->id }}">{{ $pool->package_name }} - {{ $pool->name }} (Rp{{ number_format($pool->price, 0, ',', '.') }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="amount" class="block text-sm font-semibold text-slate-700 mb-1.5">Harga / Total Pembayaran (Rp)</label>
                                <input type="number" id="amount" name="amount" required min="0" placeholder="Contoh: 350000" class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 text-sm">
                            </div>
                            <div>
                                <label for="payment_method" class="block text-sm font-semibold text-slate-700 mb-1.5">Metode Pembayaran</label>
                                <select id="payment_method" name="payment_method" required class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 text-sm">
                                    <option value="">-- Pilih Metode --</option>
                                    <option value="Bank BCAS">Bank BCAS</option>
                                    <option value="Bank BSI">Bank BSI</option>
                                    <option value="Cash">Cash (Tunai)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="bg-slate-50 px-6 py-4 flex justify-end gap-3 rounded-b-2xl border-t border-slate-100">
                        <button type="button" onclick="document.getElementById('addIncomeModal').classList.add('hidden')" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 font-semibold text-sm rounded-xl hover:bg-slate-50 transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 bg-blue-600 text-white font-semibold text-sm rounded-xl hover:bg-blue-700 transition-colors shadow-sm">
                            Simpan Pemasukan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
