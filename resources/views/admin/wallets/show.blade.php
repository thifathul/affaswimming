<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-slate-800 tracking-tight">
                {{ __('Detail E-Wallet: ' . $user->name) }}
            </h2>
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.wallets.index') }}" class="text-sm text-slate-500 hover:text-slate-800 transition-colors font-medium flex items-center gap-1.5">
                    <i class="fa-solid fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 bg-green-50 text-green-700 p-4 rounded-xl flex items-center border border-green-200 shadow-sm">
                    <i class="fa-solid fa-circle-check text-xl mr-3"></i>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-red-50 text-red-700 p-4 rounded-xl flex items-center border border-red-200 shadow-sm">
                    <i class="fa-solid fa-circle-exclamation text-xl mr-3"></i>
                    <span class="font-medium">{{ session('error') }}</span>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Info Saldo -->
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 flex flex-col justify-center items-center text-center">
                    <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-2">Total Saldo E-Wallet</h3>
                    @php
                        $balance = $user->wallet ? $user->wallet->balance : 0;
                        $isNegative = $balance < 0;
                    @endphp
                    <div class="text-4xl font-extrabold {{ $isNegative ? 'text-red-600' : 'text-slate-800' }} mb-2">
                        @if($isNegative)
                            - Rp {{ number_format(abs($balance), 0, ',', '.') }}
                        @else
                            Rp {{ number_format($balance, 0, ',', '.') }}
                        @endif
                    </div>
                    @if($isNegative)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-600 border border-red-100">
                            <i class="fa-solid fa-circle-exclamation mr-1.5"></i> Pinjaman / Kasbon
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-600 border border-emerald-100">
                            <i class="fa-solid fa-piggy-bank mr-1.5"></i> Tabungan / Titipan
                        </span>
                    @endif
                    
                    <p class="text-sm text-slate-500 mt-4">Atas nama: <strong>{{ $user->name }}</strong> ({{ ucfirst($user->role) }})</p>
                </div>

                <!-- Form Transaksi -->
                <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                    <h3 class="text-lg font-bold text-slate-800 mb-4 border-b border-slate-100 pb-3"><i class="fa-solid fa-plus-circle text-blue-500 mr-2"></i> Tambah Transaksi</h3>
                    <form action="{{ route('admin.wallets.transaction', $user->id) }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="type" class="block text-sm font-semibold text-slate-700 mb-1">Jenis Transaksi</label>
                                <select name="type" id="type" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2 text-slate-800 focus:border-blue-500 focus:ring-blue-500/20" required>
                                    <option value="" disabled selected>Pilih Jenis...</option>
                                    <option value="deposit">Deposit (Titip Uang / Nabung)</option>
                                    <option value="withdraw">Withdraw (Tarik Tunai)</option>
                                    @if($user->role === 'pelatih')
                                        <option value="borrow">Kasbon (Pinjam Uang)</option>
                                        <option value="repay">Bayar Kasbon (Manual)</option>
                                    @endif
                                </select>
                                <x-input-error :messages="$errors->get('type')" class="mt-2" />
                            </div>

                            <div>
                                <label for="amount" class="block text-sm font-semibold text-slate-700 mb-1">Nominal (Rp)</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-500 font-bold">Rp</span>
                                    <input type="text" name="amount" id="amount" class="w-full bg-white border border-slate-200 rounded-xl pl-12 pr-4 py-2 text-slate-800 focus:border-blue-500 focus:ring-blue-500/20 format-rupiah" required placeholder="0" value="{{ old('amount') }}">
                                </div>
                                <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                            </div>

                            <div class="md:col-span-2">
                                <label for="description" class="block text-sm font-semibold text-slate-700 mb-1">Catatan / Keterangan</label>
                                <input type="text" name="description" id="description" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2 text-slate-800 focus:border-blue-500 focus:ring-blue-500/20" placeholder="Opsional, misal: Pinjaman untuk beli perlengkapan..." value="{{ old('description') }}">
                                <x-input-error :messages="$errors->get('description')" class="mt-2" />
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <button type="submit" class="px-5 py-2.5 bg-slate-800 text-white font-bold rounded-xl hover:bg-slate-900 transition-colors shadow-md">
                                <i class="fa-solid fa-save mr-1.5"></i> Proses Transaksi
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Riwayat Transaksi -->
            <div class="mt-6 bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100">
                    <h3 class="text-lg font-bold text-slate-800"><i class="fa-solid fa-clock-rotate-left text-slate-400 mr-2"></i> Riwayat Mutasi</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/75 border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                <th class="p-4 pl-6">Tanggal</th>
                                <th class="p-4">Jenis Transaksi</th>
                                <th class="p-4">Keterangan</th>
                                <th class="p-4 text-right">Nominal Masuk</th>
                                <th class="p-4 pr-6 text-right">Nominal Keluar</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @forelse($transactions as $trx)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="p-4 pl-6 text-slate-500 font-medium">
                                        {{ $trx->created_at->format('d M Y, H:i') }}
                                    </td>
                                    <td class="p-4">
                                        @if($trx->type === 'deposit')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold bg-emerald-100 text-emerald-700">Deposit / Nabung</span>
                                        @elseif($trx->type === 'repay')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold bg-blue-100 text-blue-700">Bayar Kasbon</span>
                                        @elseif($trx->type === 'withdraw')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold bg-orange-100 text-orange-700">Tarik Tunai</span>
                                        @elseif($trx->type === 'borrow')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold bg-red-100 text-red-700">Pinjam / Kasbon</span>
                                        @endif
                                    </td>
                                    <td class="p-4 text-slate-600">
                                        {{ $trx->description ?? '-' }}
                                    </td>
                                    <td class="p-4 text-right font-bold text-emerald-600">
                                        @if(in_array($trx->type, ['deposit', 'repay']))
                                            + Rp {{ number_format($trx->amount, 0, ',', '.') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="p-4 pr-6 text-right font-bold text-red-600">
                                        @if(in_array($trx->type, ['withdraw', 'borrow']))
                                            - Rp {{ number_format($trx->amount, 0, ',', '.') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-8 text-center text-slate-400">Belum ada riwayat transaksi.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t border-slate-100">
                    {{ $transactions->links() }}
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Format Rupiah
            const rupiahInputs = document.querySelectorAll('.format-rupiah');
            
            function formatRupiah(value) {
                let numberString = value.replace(/[^,\d]/g, '').toString();
                let split = numberString.split(',');
                let sisa = split[0].length % 3;
                let rupiah = split[0].substr(0, sisa);
                let ribuan = split[0].substr(sisa).match(/\d{3}/gi);
                
                if (ribuan) {
                    let separator = sisa ? '.' : '';
                    rupiah += separator + ribuan.join('.');
                }
                
                rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
                return rupiah;
            }

            rupiahInputs.forEach(function(input) {
                input.addEventListener('keyup', function(e) {
                    this.value = formatRupiah(this.value);
                });
            });
        });
    </script>
    @endpush
</x-app-layout>
