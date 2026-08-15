<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-2xl text-slate-800 tracking-tight">
                <i class="fa-solid fa-chart-line text-blue-600 mr-2"></i> {{ __('Laporan Profit / Laba Rugi') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100 p-6">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-slate-800">Laporan Keuangan</h3>
                        <p class="text-sm text-slate-500">Rekapitulasi seluruh pemasukan, pengeluaran, dan saldo akhir operasional.</p>
                    </div>

                    <div class="flex items-center gap-2">
                        <form method="GET" action="{{ route('finance.profit') }}" class="flex items-center gap-2">
                            <input type="month" name="month" value="{{ $month }}" class="border-slate-200 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 transition">
                                Filter
                            </button>
                        </form>
                        
                        <a href="{{ route('finance.profit.export', ['month' => $month]) }}" class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-semibold hover:bg-emerald-700 transition inline-flex items-center gap-2">
                            <i class="fa-solid fa-file-excel"></i> Export Excel
                        </a>
                    </div>

                    <div class="text-right">
                        <p class="text-sm text-slate-500 font-medium">Total Saldo Akhir</p>
                        <p class="text-2xl font-bold {{ (isset($profit_data) && $profit_data->last() ? $profit_data->last()['balance'] : $previousBalance) >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                            Rp {{ number_format(isset($profit_data) && $profit_data->last() ? $profit_data->last()['balance'] : $previousBalance, 0, ',', '.') }}
                        </p>
                    </div>
                </div>

                @if(isset($spreadsheet_summary))
                <div class="mb-8">
                    <h3 class="text-lg font-bold text-slate-800 mb-4">Rekapitulasi Saldo</h3>
                    <div class="overflow-x-auto rounded-xl border border-slate-300 shadow-sm">
                        <table class="w-full text-center border-collapse">
                            <thead>
                                <tr>
                                    <th class="border border-slate-300 bg-white px-4 py-2 w-32"></th>
                                    <th class="border border-slate-300 bg-blue-100 text-slate-700 font-bold px-4 py-3">Pelatih</th>
                                    <th class="border border-slate-300 bg-yellow-100 text-slate-700 font-bold px-4 py-3">Keuntungan</th>
                                    <th class="border border-slate-300 bg-red-200 text-slate-700 font-bold px-4 py-3">Kas</th>
                                    @foreach($spreadsheet_summary['locations'] as $poolName => $locData)
                                        <th class="border border-slate-300 bg-orange-200 text-slate-700 font-bold px-4 py-3 uppercase">TIKET {{ $poolName }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="text-sm">
                                <!-- Terkumpul -->
                                <tr>
                                    <td class="border border-slate-300 font-bold bg-white text-left px-4 py-3 uppercase">Terkumpul</td>
                                    <td class="border border-slate-300 px-4 py-3 bg-white">Rp{{ number_format($spreadsheet_summary['pelatih']['terkumpul'], 0, ',', '.') }}</td>
                                    <td class="border border-slate-300 px-4 py-3 bg-white">Rp{{ number_format($spreadsheet_summary['keuntungan']['terkumpul'], 0, ',', '.') }}</td>
                                    <td class="border border-slate-300 px-4 py-3 bg-white">Rp{{ number_format($spreadsheet_summary['kas']['terkumpul'], 0, ',', '.') }}</td>
                                    @foreach($spreadsheet_summary['locations'] as $poolName => $locData)
                                        <td class="border border-slate-300 px-4 py-3 bg-white">Rp{{ number_format($locData['terkumpul'], 0, ',', '.') }}</td>
                                    @endforeach
                                </tr>
                                <!-- Terpakai -->
                                <tr>
                                    <td class="border border-slate-300 font-bold bg-white text-left px-4 py-3 uppercase">Terpakai</td>
                                    <td class="border border-slate-300 px-4 py-3 bg-white">
                                        {{ $spreadsheet_summary['pelatih']['terpakai'] != 0 ? 'Rp' . number_format($spreadsheet_summary['pelatih']['terpakai'], 0, ',', '.') : '' }}
                                    </td>
                                    <td class="border border-slate-300 px-4 py-3 bg-white">
                                        {{ $spreadsheet_summary['keuntungan']['terpakai'] != 0 ? 'Rp' . number_format($spreadsheet_summary['keuntungan']['terpakai'], 0, ',', '.') : '' }}
                                    </td>
                                    <td class="border border-slate-300 px-4 py-3 bg-white">
                                        {{ $spreadsheet_summary['kas']['terpakai'] != 0 ? 'Rp' . number_format($spreadsheet_summary['kas']['terpakai'], 0, ',', '.') : '' }}
                                    </td>
                                    @foreach($spreadsheet_summary['locations'] as $poolName => $locData)
                                        <td class="border border-slate-300 px-4 py-3 bg-white">
                                            {{ $locData['terpakai'] != 0 ? 'Rp' . number_format($locData['terpakai'], 0, ',', '.') : '' }}
                                        </td>
                                    @endforeach
                                </tr>
                                <!-- Saldo -->
                                <tr>
                                    <td class="border border-slate-300 font-bold bg-white text-left px-4 py-3 uppercase">Saldo</td>
                                    <td class="border border-slate-300 px-4 py-3 bg-white">Rp{{ number_format($spreadsheet_summary['pelatih']['saldo'], 0, ',', '.') }}</td>
                                    <td class="border border-slate-300 px-4 py-3 bg-white">Rp{{ number_format($spreadsheet_summary['keuntungan']['saldo'], 0, ',', '.') }}</td>
                                    <td class="border border-slate-300 px-4 py-3 bg-white">Rp{{ number_format($spreadsheet_summary['kas']['saldo'], 0, ',', '.') }}</td>
                                    @foreach($spreadsheet_summary['locations'] as $poolName => $locData)
                                        <td class="border border-slate-300 px-4 py-3 bg-white">Rp{{ number_format($locData['saldo'], 0, ',', '.') }}</td>
                                    @endforeach
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                <div class="mb-4">
                    <h3 class="text-lg font-bold text-slate-800">Buku Besar Transaksi</h3>
                    <p class="text-sm text-slate-500">Rincian mutasi (pemasukan & pengeluaran).</p>
                </div>

                @if($profit_data->isEmpty())
                    <div class="text-center py-10">
                        <i class="fa-solid fa-file-invoice text-4xl text-slate-300 mb-3"></i>
                        <p class="text-slate-500 font-medium">Belum ada catatan transaksi keuangan.</p>
                    </div>
                @else
                    <div class="overflow-x-auto rounded-xl border border-slate-200">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-200">
                                    <th class="py-3 px-4 text-xs font-bold text-slate-600 uppercase tracking-wider">Tanggal</th>
                                    <th class="py-3 px-4 text-xs font-bold text-slate-600 uppercase tracking-wider">Keterangan Transaksi</th>
                                    <th class="py-3 px-4 text-xs font-bold text-emerald-600 uppercase tracking-wider text-right">Pemasukan</th>
                                    <th class="py-3 px-4 text-xs font-bold text-rose-600 uppercase tracking-wider text-right">Pengeluaran</th>
                                    <th class="py-3 px-4 text-xs font-bold text-blue-600 uppercase tracking-wider text-right">Profit / Saldo Akhir</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @if(isset($month))
                                <tr class="bg-slate-50/50">
                                    <td colspan="4" class="py-3 px-4 text-sm font-semibold text-slate-600 text-right">
                                        Saldo Awal per {{ \Carbon\Carbon::parse($month.'-01')->format('M Y') }}
                                    </td>
                                    <td class="py-3 px-4 text-sm font-bold whitespace-nowrap text-right {{ $previousBalance >= 0 ? 'text-blue-600' : 'text-red-600' }}">
                                        Rp {{ number_format($previousBalance, 0, ',', '.') }}
                                    </td>
                                </tr>
                                @endif

                                @foreach($profit_data as $item)
                                    <tr class="hover:bg-slate-50/50 transition-colors">
                                        <td class="py-3 px-4 text-sm text-slate-700 whitespace-nowrap">
                                            {{ \Carbon\Carbon::parse($item['date'])->format('d M Y') }}
                                        </td>
                                        <td class="py-3 px-4 text-sm text-slate-700">
                                            <div class="flex items-center gap-2">
                                                @if($item['type'] === 'income')
                                                    <div class="w-6 h-6 rounded bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
                                                        <i class="fa-solid fa-arrow-down text-xs"></i>
                                                    </div>
                                                @else
                                                    <div class="w-6 h-6 rounded bg-rose-50 text-rose-600 flex items-center justify-center flex-shrink-0">
                                                        <i class="fa-solid fa-arrow-up text-xs"></i>
                                                    </div>
                                                @endif
                                                {{ $item['description'] }}
                                            </div>
                                        </td>
                                        <td class="py-3 px-4 text-sm font-semibold text-emerald-600 whitespace-nowrap text-right">
                                            {{ $item['income'] > 0 ? '+ ' . number_format($item['income'], 0, ',', '.') : '-' }}
                                        </td>
                                        <td class="py-3 px-4 text-sm font-semibold text-rose-600 whitespace-nowrap text-right">
                                            {{ $item['expense'] > 0 ? '- ' . number_format($item['expense'], 0, ',', '.') : '-' }}
                                        </td>
                                        <td class="py-3 px-4 text-sm font-bold whitespace-nowrap text-right {{ $item['balance'] >= 0 ? 'text-blue-600' : 'text-red-600' }}">
                                            Rp {{ number_format($item['balance'], 0, ',', '.') }}
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
