<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-slate-800 tracking-tight">
            {{ __('Status Pengajuan Reschedule & Inval') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100 p-6">
                
                <div class="overflow-x-auto">
                    <table class="w-full min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Tipe</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Jadwal Asli</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Detail Usulan Baru</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Status & Catatan Admin</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-200">
                            @forelse($requests as $request)
                                <tr class="hover:bg-slate-50 transition-colors">
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
                                        <div class="text-[10px] text-slate-400 mt-2 font-medium">Diajukan: {{ $request->created_at->format('d M Y') }}</div>
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-slate-800">{{ $request->schedule->day }}</div>
                                        <div class="text-sm text-slate-600">{{ \Carbon\Carbon::parse($request->schedule->start_time)->format('H:i') }}</div>
                                        <div class="text-xs text-slate-500 mt-1"><i class="fa-solid fa-location-dot mr-1"></i>{{ $request->schedule->poolLocation->name ?? '-' }}</div>
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <div class="font-medium text-slate-800">{{ \Carbon\Carbon::parse($request->proposed_date)->translatedFormat('d F Y') }}</div>
                                        <div class="text-slate-600">Jam: {{ \Carbon\Carbon::parse($request->proposed_start_time)->format('H:i') }}</div>
                                        
                                        @if($request->proposed_pool_location_id)
                                            <div class="text-xs mt-1 text-rose-600 font-medium"><i class="fa-solid fa-map-location-dot mr-1"></i>Pindah: {{ $request->proposedPoolLocation->name ?? '-' }}</div>
                                        @endif
                                        
                                        @if($request->type === 'inval')
                                            <div class="text-xs mt-1 text-blue-600 font-medium"><i class="fa-solid fa-user-plus mr-1"></i>Pengganti: {{ $request->substituteCoach->name ?? '-' }}</div>
                                        @endif
                                    </td>
                                    
                                    <td class="px-6 py-4">
                                        @if($request->status === 'pending')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-700 mb-2">
                                                <i class="fa-regular fa-clock mr-1.5"></i> Menunggu
                                            </span>
                                            <p class="text-xs text-slate-500 italic">Pengajuan Anda sedang ditinjau oleh Admin.</p>
                                        @elseif($request->status === 'approved')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700 mb-2">
                                                <i class="fa-solid fa-check mr-1.5"></i> Disetujui
                                            </span>
                                            @if($request->admin_note)
                                                <div class="bg-slate-50 border border-slate-200 rounded p-2 text-xs text-slate-600 mt-1">
                                                    <strong>Catatan Admin:</strong> {{ $request->admin_note }}
                                                </div>
                                            @endif
                                        @else
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700 mb-2">
                                                <i class="fa-solid fa-xmark mr-1.5"></i> Ditolak
                                            </span>
                                            @if($request->admin_note)
                                                <div class="bg-rose-50 border border-rose-100 rounded p-2 text-xs text-rose-700 mt-1">
                                                    <strong>Alasan Ditolak:</strong> {{ $request->admin_note }}
                                                </div>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center text-slate-400">
                                            <i class="fa-solid fa-inbox text-4xl mb-3"></i>
                                            <p class="text-base font-medium text-slate-500">Belum ada riwayat pengajuan</p>
                                            <p class="text-sm mt-1 text-slate-400">Anda belum pernah mengajukan Reschedule atau Inval.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
