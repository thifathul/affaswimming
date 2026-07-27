<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-slate-800 tracking-tight">
            {{ __('Daftar Murid Habis Paket') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100">
                <div class="p-6">
                    @if($students->isEmpty())
                        <div class="text-center py-10">
                            <i class="fa-regular fa-face-smile text-4xl text-slate-300 mb-3"></i>
                            <p class="text-slate-500 font-medium">Belum ada murid yang paketnya habis.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-slate-50 border-y border-slate-100">
                                        <th class="py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Nama Murid</th>
                                        <th class="py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Kontak Orang Tua</th>
                                        <th class="py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Sisa Pertemuan</th>
                                        <th class="py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Masa Aktif Paket</th>
                                        <th class="py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($students as $student)
                                        <tr class="hover:bg-slate-50/50 transition-colors">
                                            <td class="py-3 px-4">
                                                <p class="text-sm font-bold text-slate-800">{{ $student->name }}</p>
                                            </td>
                                            <td class="py-3 px-4">
                                                <p class="text-sm text-slate-700">{{ $student->school ?? '-' }}</p>
                                                <p class="text-xs text-slate-500">Usia: {{ $student->age ?? '-' }}</p>
                                            </td>
                                            <td class="py-3 px-4 text-center">
                                                <span class="px-2 py-1 {{ $student->remaining_meetings <= 0 ? 'bg-red-100 text-red-700' : 'bg-emerald-100 text-emerald-700' }} text-xs font-bold rounded-full">
                                                    {{ $student->remaining_meetings ?? 0 }}
                                                </span>
                                            </td>
                                            <td class="py-3 px-4 text-center">
                                                @php
                                                    $isActive = $student->package_active_until && \Carbon\Carbon::parse($student->package_active_until)->isFuture();
                                                @endphp
                                                <span class="px-2 py-1 {{ $isActive ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }} text-xs font-bold rounded-full">
                                                    {{ $student->package_active_until ? \Carbon\Carbon::parse($student->package_active_until)->format('d M Y') : '-' }}
                                                </span>
                                            </td>
                                            <td class="py-3 px-4">
                                                <span class="text-xs font-semibold text-red-600">Habis / Expired</span>
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
