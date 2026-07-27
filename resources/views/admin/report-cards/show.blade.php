<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-bold text-2xl text-slate-800 tracking-tight">
                    <i class="fa-solid fa-clipboard-list text-blue-600 mr-2"></i> Detail Raport: {{ $student->user->name ?? 'Murid' }}
                </h2>
                <p class="text-sm text-slate-500 mt-1"> Kelas: {{ $student->swimClasses->pluck('name')->join(', ') ?: '-' }}</p>
            </div>
            <div>
                <a href="{{ route('admin.report-cards.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-300 rounded-lg text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                    <i class="fa-solid fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-6 flex items-center bg-white p-4 rounded-xl border border-blue-100 shadow-sm max-w-sm">
                <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-xl mr-4">
                    <i class="fa-solid fa-person-swimming"></i>
                </div>
                <div>
                    <p class="text-sm text-slate-500 font-medium">Total Latihan Diikuti</p>
                    <p class="text-2xl font-bold text-slate-800">{{ $totalTrainings }} <span class="text-base font-medium text-slate-500">Pertemuan</span></p>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100 p-6">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-slate-600">
                        <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-200">
                            <tr>
                                <th scope="col" class="px-6 py-4 font-semibold">Tanggal</th>
                                <th scope="col" class="px-6 py-4 font-semibold">Pelatih</th>
                                <th scope="col" class="px-6 py-4 font-semibold">Kelas</th>
                                <th scope="col" class="px-6 py-4 font-semibold">Catatan Evaluasi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($evaluations as $eval)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-medium text-slate-800">{{ \Carbon\Carbon::parse($eval->meeting_date)->translatedFormat('d F Y') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xs border border-blue-100">
                                                {{ strtoupper(substr($eval->coach->name ?? '?', 0, 2)) }}
                                            </div>
                                            <div class="font-semibold text-slate-800">{{ $eval->coach->name ?? 'Pelatih' }}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center justify-center bg-slate-100 text-slate-600 text-[10px] font-bold px-2 py-1 rounded">
                                            {{ $eval->swimClass->name ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="text-slate-700 text-sm whitespace-pre-line">{{ $eval->notes }}</p>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-slate-500">
                                        <div class="flex flex-col items-center justify-center text-slate-400">
                                            <i class="fa-regular fa-clipboard text-4xl mb-3"></i>
                                            <p class="text-base font-medium">Belum ada rapor evaluasi.</p>
                                            <p class="text-sm mt-1">Pelatih akan memberikan penilaian setelah sesi latihan berlangsung.</p>
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
