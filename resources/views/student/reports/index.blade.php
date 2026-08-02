<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-slate-800 tracking-tight">
            {{ __('Laporan Latihan') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100">
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                        <h3 class="text-lg font-bold text-slate-800">Riwayat Laporan Latihan</h3>
                        @php
                            $remaining = auth()->user()->student->remaining_meetings ?? 0;
                            $isUnpaid = $remaining <= 0;
                        @endphp
                        
                        <div class="{{ $isUnpaid ? 'bg-red-50 border-red-200' : 'bg-blue-50 border-blue-200' }} border px-4 py-3 rounded-xl flex items-center gap-3 shadow-sm">
                            <div class="p-2 {{ $isUnpaid ? 'bg-red-100 text-red-600' : 'bg-blue-100 text-blue-600' }} rounded-lg flex-shrink-0">
                                <i class="fa-solid fa-ticket-simple text-lg"></i>
                            </div>
                            <div>
                                <div class="flex items-center gap-2 mb-0.5">
                                    <p class="text-xs {{ $isUnpaid ? 'text-red-600/80' : 'text-blue-600/80' }} font-bold uppercase tracking-wider">Sisa Billing Latihan</p>
                                    @if($isUnpaid)
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-red-200 text-red-800 uppercase">Unpaid</span>
                                    @endif
                                </div>
                                <p class="text-lg font-bold {{ $isUnpaid ? 'text-red-800' : 'text-blue-800' }} leading-none">
                                    {{ $remaining }} <span class="text-sm font-medium {{ $isUnpaid ? 'text-red-600' : 'text-blue-600' }}">Pertemuan</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    @if($attendances->isEmpty())
                        <div class="text-center py-10 bg-slate-50 rounded-xl border border-slate-100">
                            <i class="fa-solid fa-clipboard-list text-4xl text-slate-300 mb-3"></i>
                            <p class="text-slate-500 font-medium">Belum ada laporan latihan.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left text-slate-600">
                                <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-200">
                                    <tr>
                                        <th scope="col" class="px-6 py-4 font-semibold">Tanggal</th>
                                        <th scope="col" class="px-6 py-4 font-semibold">Pelatih</th>
                                        <th scope="col" class="px-6 py-4 font-semibold">Lokasi / Kelas</th>
                                        <th scope="col" class="px-6 py-4 font-semibold text-center">Pertemuan Ke-</th>
                                        <th scope="col" class="px-6 py-4 font-semibold">Catatan Evaluasi</th>
                                        <th scope="col" class="px-6 py-4 font-semibold text-center">Status Kehadiran</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($attendances as $att)
                                        <tr class="hover:bg-slate-50/50 transition-colors">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="font-medium text-slate-800">{{ \Carbon\Carbon::parse($att->trainingReport->training_date)->translatedFormat('d F Y') }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xs border border-blue-100">
                                                        {{ strtoupper(substr($att->trainingReport->coach->name ?? '?', 0, 2)) }}
                                                    </div>
                                                    <div class="font-semibold text-slate-800">{{ $att->trainingReport->coach->name ?? 'Pelatih' }}</div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center justify-center bg-slate-100 text-slate-600 text-[10px] font-bold px-2 py-1 rounded">
                                                    {{ $att->trainingReport->schedule->poolLocation->name ?? 'Lokasi Dihapus' }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                @if($att->status == 'Hadir')
                                                    <span class="inline-flex items-center justify-center bg-blue-100 text-blue-700 text-xs font-bold w-6 h-6 rounded-full">
                                                        {{ $att->meeting_number }}
                                                    </span>
                                                @else
                                                    <span class="text-slate-400">-</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4">
                                                @if($att->evaluation)
                                                    <p class="text-slate-700 text-sm whitespace-pre-line">{{ $att->evaluation }}</p>
                                                @else
                                                    <p class="text-sm text-slate-400 italic">Tidak ada catatan evaluasi.</p>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                @if($att->status == 'Hadir')
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                        <i class="fa-solid fa-check mr-1"></i> Hadir
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-red-100 text-red-800 border border-red-200">
                                                        <i class="fa-solid fa-xmark mr-1"></i> Absen
                                                    </span>
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
