<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-slate-800 tracking-tight">
            {{ __('Daftar Murid Unpaid') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100 p-6">
                
                <!-- Header Section -->
                <div class="mb-6">
                    <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <i class="fa-solid fa-triangle-exclamation text-red-500"></i>
                        Tagihan Kosong / Minus
                    </h3>
                    <p class="text-sm text-slate-500 mt-1 max-w-3xl">Daftar murid di bawah ini adalah murid aktif yang kehabisan paket namun masih mengikuti latihan, atau sudah waktunya membeli paket baru.</p>
                </div>

                <!-- Toolbar Section -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4 p-4 bg-slate-50/50 rounded-xl border border-slate-100">
                    <div class="text-sm font-medium text-slate-500">
                        Total: <span class="text-slate-800 font-bold bg-white px-2 py-1 rounded-md border border-slate-200 shadow-sm ml-1">{{ $students->total() }}</span> Murid
                    </div>
                    
                    <form method="GET" action="{{ route('finance.unpaid.index') }}" class="w-full sm:w-auto flex flex-col sm:flex-row gap-2">
                        <div class="relative w-full sm:w-64">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fa-solid fa-magnifying-glass text-slate-400"></i>
                            </div>
                            <input type="text" name="search" value="{{ request('search') }}" class="block w-full pl-10 pr-3 py-2 border border-slate-200 rounded-lg text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all bg-white shadow-sm" placeholder="Cari nama murid...">
                        </div>
                        <div class="flex gap-2 w-full sm:w-auto">
                            <button type="submit" class="flex-1 sm:flex-none px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-lg text-sm font-semibold transition-colors shadow-sm flex items-center justify-center gap-2">
                                <i class="fa-solid fa-filter text-xs"></i> Filter
                            </button>
                            @if(request('search'))
                                <a href="{{ route('finance.unpaid.index') }}" class="flex-1 sm:flex-none px-4 py-2 bg-white hover:bg-slate-50 text-slate-600 border border-slate-200 rounded-lg text-sm font-semibold transition-colors flex items-center justify-center gap-2 shadow-sm">
                                    <i class="fa-solid fa-rotate-right text-xs"></i> Reset
                                </a>
                            @endif
                        </div>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200 bg-slate-50/50">
                                <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Nama Murid</th>
                                <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Sisa Pertemuan</th>
                                <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($students as $student)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 font-bold">
                                                {{ substr($student->user->name ?? 'M', 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="font-bold text-slate-800">{{ $student->user->name }}</div>
                                                <div class="text-xs text-slate-500">Terakhir Latihan: {{ $student->attendances->first() ? \Carbon\Carbon::parse($student->attendances->first()->created_at)->diffForHumans() : 'Belum ada' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-sm font-bold bg-red-100 text-red-700 border border-red-200">
                                            {{ $student->remaining_meetings }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded text-xs font-bold bg-rose-100 text-rose-700 uppercase tracking-wider">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Unpaid
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="https://wa.me/{{ preg_replace('/^0/', '62', $student->user->phone ?? '') }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg text-sm font-semibold transition-colors shadow-sm">
                                            <i class="fa-brands fa-whatsapp"></i> Hubungi WA
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-slate-500">
                                        <div class="w-16 h-16 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center mx-auto mb-3 text-2xl">
                                            <i class="fa-regular fa-face-smile"></i>
                                        </div>
                                        <p class="font-medium text-slate-600">Luar biasa! Tidak ada murid yang menunggak.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $students->links() }}
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
