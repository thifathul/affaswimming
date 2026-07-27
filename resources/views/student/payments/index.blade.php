<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-slate-800 tracking-tight">
                {{ __('Pembayaran Paket') }}
            </h2>
            <div class="flex items-center gap-3">
                <a href="{{ route('student.payments.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 transition">
                    <i class="fa-solid fa-plus mr-1"></i> Upload Pembayaran
                </a>
            </div>
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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-slate-800 mb-4">Riwayat Pembayaran</h3>
                    
                    @if(empty($transactions) || $transactions->isEmpty())
                        <div class="text-center py-10">
                            <i class="fa-regular fa-folder-open text-4xl text-slate-300 mb-3"></i>
                            <p class="text-slate-500 font-medium">Belum ada riwayat pembayaran.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-slate-50 border-y border-slate-100">
                                        <th class="py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Tanggal Mulai</th>
                                        <th class="py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Lokasi Kolam</th>
                                        <th class="py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Paket</th>
                                        <th class="py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Jumlah (Rp)</th>
                                        <th class="py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                                        <th class="py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($transactions as $trx)
                                        <tr class="hover:bg-slate-50/50 transition-colors">
                                            <td class="py-3 px-4 text-sm text-slate-700 font-medium">{{ \Carbon\Carbon::parse($trx->practice_start_date)->format('d M Y') }}</td>
                                            <td class="py-3 px-4 text-sm text-slate-600">{{ $trx->poolLocation->name ?? '-' }}</td>
                                            <td class="py-3 px-4 text-sm text-slate-600">{{ $trx->package_type }} Pertemuan ({{ ucfirst(str_replace('_', ' ', $trx->class_type)) }})</td>
                                            <td class="py-3 px-4 text-sm font-semibold text-slate-700">{{ number_format($trx->amount, 0, ',', '.') }}</td>
                                            <td class="py-3 px-4">
                                                @if($trx->status === 'approved')
                                                    <span class="px-2 py-1 bg-emerald-100 text-emerald-700 text-xs font-bold rounded-full">Approved</span>
                                                @elseif($trx->status === 'rejected')
                                                    <span class="px-2 py-1 bg-red-100 text-red-700 text-xs font-bold rounded-full">Rejected</span>
                                                @else
                                                    <span class="px-2 py-1 bg-amber-100 text-amber-700 text-xs font-bold rounded-full">Pending</span>
                                                @endif
                                            </td>
                                            <td class="py-3 px-4">
                                                @if($trx->status === 'approved')
                                                    <a href="{{ route('student.payments.receipt', $trx->id) }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                                        <i class="fa-solid fa-download mr-1"></i> Kuitansi
                                                    </a>
                                                @else
                                                    <span class="text-slate-400 text-sm">-</span>
                                                @endif
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
</x-app-layout>
