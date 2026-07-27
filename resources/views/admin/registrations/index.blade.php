<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-slate-800 tracking-tight">
            {{ __('Approval Registrasi Murid') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
            <div class="mb-6 bg-emerald-50 text-emerald-700 p-4 rounded-xl border border-emerald-100 flex items-start gap-3 shadow-sm">
                <i class="fa-solid fa-circle-check mt-0.5"></i>
                <p class="text-sm font-medium">{{ session('success') }}</p>
            </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100">
                <div class="p-8">
                    <div class="mb-6 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-slate-800">Daftar Pendaftar Baru</h3>
                        <span class="bg-yellow-100 text-yellow-800 text-xs font-bold px-3 py-1 rounded-full border border-yellow-200">
                            {{ $pendingUsers->count() }} Menunggu
                        </span>
                    </div>

                    @if($pendingUsers->isEmpty())
                        <div class="text-center py-12 bg-slate-50 rounded-xl border border-dashed border-slate-200">
                            <i class="fa-solid fa-user-clock text-4xl text-slate-300 mb-4"></i>
                            <p class="text-slate-500 font-medium text-sm">Tidak ada pendaftaran baru saat ini.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left">
                                <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-y border-slate-100">
                                    <tr>
                                        <th scope="col" class="px-6 py-4 font-bold tracking-wider">Info Akun</th>
                                        <th scope="col" class="px-6 py-4 font-bold tracking-wider">Biodata Anak</th>
                                        <th scope="col" class="px-6 py-4 font-bold tracking-wider">Tanggal Daftar</th>
                                        <th scope="col" class="px-6 py-4 text-center font-bold tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($pendingUsers as $user)
                                    <tr class="hover:bg-slate-50/50 transition duration-150">
                                        <td class="px-6 py-4">
                                            <div class="font-bold text-slate-800">{{ $user->name }}</div>
                                            <div class="text-xs text-slate-500 mt-1">
                                                <i class="fa-solid fa-envelope mr-1 text-slate-400"></i> {{ $user->email }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($user->student)
                                                <div class="font-semibold text-slate-700">{{ $user->student->name }}</div>
                                                <div class="text-xs text-slate-500 mt-1">Usia: {{ $user->student->age ?? '-' }} Tahun</div>
                                                <div class="text-xs text-slate-500">Sekolah: {{ $user->student->school ?? '-' }}</div>
                                                <div class="text-xs text-slate-500">TTL: {{ $user->student->birth_place_date ?? '-' }}</div>
                                            @else
                                                <span class="text-red-400 italic text-xs">Data biodata tidak ditemukan</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-slate-500 whitespace-nowrap">
                                            {{ $user->created_at->format('d M Y') }}<br>
                                            <span class="text-xs">{{ $user->created_at->format('H:i') }} WIB</span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <div class="flex justify-center items-center gap-2">
                                                <!-- Approve Form -->
                                                <form action="{{ route('admin.registrations.approve', $user->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menyetujui akun ini?');">
                                                    @csrf
                                                    <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold shadow-sm transition">
                                                        <i class="fa-solid fa-check mr-1"></i> Terima
                                                    </button>
                                                </form>

                                                <!-- Reject Form -->
                                                <form action="{{ route('admin.registrations.reject', $user->id) }}" method="POST" onsubmit="return confirm('PENTING: Menolak pendaftaran akan menghapus data pendaftar ini selamanya. Lanjutkan?');">
                                                    @csrf
                                                    <button type="submit" class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white rounded-lg text-xs font-semibold shadow-sm transition">
                                                        <i class="fa-solid fa-xmark mr-1"></i> Tolak
                                                    </button>
                                                </form>
                                            </div>
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
