<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-slate-800 tracking-tight">
                {{ __('Master Paket & Lokasi Kolam') }}
            </h2>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.pool-locations.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold shadow-sm transition-colors flex items-center gap-2">
                    <i class="fa-solid fa-plus"></i> Tambah Paket & Lokasi
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-r-lg text-emerald-700 font-medium text-sm flex items-center">
                    <i class="fa-solid fa-circle-check mr-3 text-lg"></i>
                    {{ session('success') }}
                </div>
            @endif

            {{-- Filter Bar --}}
            <div class="mb-4">
                <form action="{{ route('admin.pool-locations.index') }}" method="GET">
                    <div class="flex flex-wrap items-center gap-3">
                        <input
                            type="text"
                            name="search"
                            id="search"
                            value="{{ request('search') }}"
                            placeholder="Cari nama paket / lokasi..."
                            class="rounded-lg border border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2 min-w-[220px]"
                        >

                        <select name="location" id="location" class="rounded-lg border border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2 min-w-[160px]">
                            <option value="">Semua Lokasi</option>
                            @foreach($allLocations as $loc)
                                <option value="{{ $loc }}" {{ request('location') === $loc ? 'selected' : '' }}>{{ $loc }}</option>
                            @endforeach
                        </select>

                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-sm font-semibold shadow-sm transition-colors flex items-center gap-2">
                            <i class="fa-solid fa-filter"></i> Filter
                        </button>

                        @if(request()->hasAny(['search', 'location']))
                            <a href="{{ route('admin.pool-locations.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100">
                <div class="p-0 overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Nama Paket</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Pertemuan</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Nama Lokasi</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Fee Pelatih</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Potongan Kas</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Harga Tiket (Private / Semi)</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-200">
                            @forelse($locations as $location)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-medium text-slate-900">{{ $location->package_name ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-slate-900">{{ $location->meeting_count ? $location->meeting_count . ' Kali' : '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-medium text-slate-900">{{ $location->name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-slate-900">Rp{{ number_format($location->coach_fee, 0, ',', '.') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-slate-900">{{ $location->cash_percentage }}%</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-slate-900">
                                            @if($location->private_ticket_price)
                                                Private: Rp{{ number_format($location->private_ticket_price, 0, ',', '.') }}<br>
                                            @endif
                                            @if($location->semi_private_ticket_price)
                                                Semi: Rp{{ number_format($location->semi_private_ticket_price, 0, ',', '.') }}
                                            @endif
                                            @if(!$location->private_ticket_price && !$location->semi_private_ticket_price)
                                                <span class="text-slate-400 italic">Gratis/Termasuk</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('admin.pool-locations.edit', $location) }}" class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                                        <form action="{{ route('admin.pool-locations.destroy', $location) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus lokasi ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-rose-600 hover:text-rose-900">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-8 text-center text-slate-500">
                                        Belum ada data lokasi kolam.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($locations->hasPages())
                    <div class="px-6 py-4 border-t border-slate-100">
                        {{ $locations->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

