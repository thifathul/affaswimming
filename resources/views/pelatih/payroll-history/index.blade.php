<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-slate-800 tracking-tight">
            {{ __('Riwayat Penggajian') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100 p-6">
                <h3 class="text-lg font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">Data Penggajian Anda</h3>
                
                @if($histories->isEmpty())
                    <div class="text-center py-10">
                        <i class="fa-solid fa-money-check-dollar text-4xl text-slate-300 mb-3"></i>
                        <p class="text-slate-500 font-medium">Belum ada riwayat penggajian untuk Anda.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50 border-y border-slate-100">
                                    <th class="py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Tanggal Pembayaran</th>
                                    <th class="py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Keterangan</th>
                                    <th class="py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Total (Rp)</th>
                                    <th class="py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Bukti</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($histories as $history)
                                    <tr class="hover:bg-slate-50/50 transition-colors">
                                        <td class="py-3 px-4 text-sm text-slate-700 whitespace-nowrap">
                                            {{ \Carbon\Carbon::parse($history->expense_date)->format('d M Y') }}
                                        </td>
                                        <td class="py-3 px-4 text-sm text-slate-600">
                                            {{ $history->description }}
                                        </td>
                                        <td class="py-3 px-4 text-sm font-bold text-emerald-600 whitespace-nowrap">
                                            {{ number_format($history->amount, 0, ',', '.') }}
                                        </td>
                                        <td class="py-3 px-4 text-sm text-center">
                                            @if($history->proof_file)
                                                <a href="{{ asset('storage/' . $history->proof_file) }}" target="_blank" class="text-blue-600 hover:text-blue-800" title="Lihat Bukti">
                                                    <i class="fa-solid fa-file-invoice"></i>
                                                </a>
                                            @else
                                                <span class="text-slate-300">-</span>
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
</x-app-layout>
