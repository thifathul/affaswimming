<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-slate-800 tracking-tight">
                {{ __('Manajemen E-Wallet') }}
            </h2>
            <span class="info-badge"><i class="fa-solid fa-wallet mr-1"></i> E-Wallet</span>
        </div>
    </x-slot>

    <div class="py-12 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 bg-green-50 text-green-700 p-4 rounded-xl flex items-center border border-green-200 shadow-sm">
                    <i class="fa-solid fa-circle-check text-xl mr-3"></i>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-red-50 text-red-700 p-4 rounded-xl flex items-center border border-red-200 shadow-sm">
                    <i class="fa-solid fa-circle-exclamation text-xl mr-3"></i>
                    <span class="font-medium">{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100">
                <div class="p-6">
                    <form method="GET" action="{{ route('admin.wallets.index') }}" class="mb-6 flex gap-4">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau role..." class="flex-1 bg-white border border-slate-200 rounded-xl px-4 py-2 text-sm focus:border-blue-500 focus:ring-blue-500/20">
                        <button type="submit" class="px-4 py-2 bg-slate-800 text-white rounded-xl text-sm font-semibold hover:bg-slate-700 transition-colors shadow-md">
                            <i class="fa-solid fa-search mr-1"></i> Cari
                        </button>
                    </form>

                    <div class="overflow-x-auto rounded-xl border border-slate-100">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50/75 border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                    <th class="p-4 pl-6">Nama Pengguna</th>
                                    <th class="p-4">Role</th>
                                    <th class="p-4 text-right">Saldo / (Pinjaman)</th>
                                    <th class="p-4 pr-6 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-sm">
                                @forelse($users as $user)
                                    @php
                                        $balance = $user->wallet ? $user->wallet->balance : 0;
                                        $isNegative = $balance < 0;
                                    @endphp
                                    <tr class="hover:bg-slate-50/50 transition-colors">
                                        <td class="p-4 pl-6 font-bold text-slate-800">
                                            {{ $user->name }}
                                        </td>
                                        <td class="p-4">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold capitalize {{ $user->role === 'pelatih' ? 'bg-blue-50 text-blue-700 border-blue-100' : 'bg-emerald-50 text-emerald-700 border-emerald-100' }} border shadow-sm">
                                                {{ $user->role }}
                                            </span>
                                        </td>
                                        <td class="p-4 text-right">
                                            @if($isNegative)
                                                <span class="font-bold text-red-600">- Rp {{ number_format(abs($balance), 0, ',', '.') }}</span>
                                                <p class="text-[10px] text-slate-400 mt-1">Pinjaman/Kasbon</p>
                                            @else
                                                <span class="font-bold text-emerald-600">Rp {{ number_format($balance, 0, ',', '.') }}</span>
                                            @endif
                                        </td>
                                        <td class="p-4 pr-6 text-center">
                                            <a href="{{ route('admin.wallets.show', $user->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-lg text-xs font-bold transition-colors">
                                                <i class="fa-solid fa-list-ul"></i> Lihat Detail
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="p-8 text-center text-slate-400">
                                            <div class="flex flex-col items-center justify-center">
                                                <i class="fa-solid fa-folder-open text-4xl mb-3 text-slate-200"></i>
                                                <p class="font-medium">Tidak ada pengguna ditemukan.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-6">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
