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
                        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
                            @foreach($attendances as $att)
                                <div class="bg-white border {{ $att->status == 'Hadir' ? 'border-emerald-200' : 'border-red-200' }} rounded-xl p-5 shadow-sm hover:shadow-md transition-shadow">
                                    <div class="flex justify-between items-start mb-4 border-b border-slate-100 pb-4">
                                        <div>
                                            <p class="text-sm text-slate-500 font-medium mb-1">
                                                <i class="fa-regular fa-calendar mr-1"></i>
                                                {{ \Carbon\Carbon::parse($att->trainingReport->training_date)->format('d F Y') }}
                                            </p>
                                            <h4 class="font-bold text-slate-800 text-lg">
                                                {{ $att->trainingReport->schedule->poolLocation->name ?? 'Lokasi Dihapus' }}
                                            </h4>
                                            <p class="text-sm text-slate-600 mt-1">
                                                <i class="fa-solid fa-person-swimming text-blue-500 mr-1"></i>
                                                Pelatih: {{ $att->trainingReport->coach->name ?? '-' }}
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            @if($att->status == 'Hadir')
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                    <i class="fa-solid fa-check mr-1"></i> Hadir
                                                </span>
                                                <div class="mt-2 text-xs font-bold text-slate-500 bg-slate-100 px-2 py-1 rounded">
                                                    Pertemuan ke-{{ $att->meeting_number }}
                                                </div>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800 border border-red-200">
                                                    <i class="fa-solid fa-xmark mr-1"></i> Absen
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <h5 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Evaluasi & Catatan</h5>
                                        @if($att->evaluation)
                                            <div class="bg-slate-50 rounded-lg p-3 text-sm text-slate-700 whitespace-pre-wrap border border-slate-100 leading-relaxed">{{ $att->evaluation }}</div>
                                        @else
                                            <p class="text-sm text-slate-400 italic">Tidak ada catatan evaluasi untuk pertemuan ini.</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
