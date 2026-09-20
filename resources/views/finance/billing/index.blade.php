<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Billing Murid') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ showModal: false, editData: { id: '', name: '', active_until: '', add_meetings: 0, current_meetings: 0 } }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 bg-emerald-100 border border-emerald-400 text-emerald-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    
                    <div class="flex flex-col sm:flex-row justify-between items-center mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4 sm:mb-0">Data Sisa Billing Murid Aktif</h3>
                        <form action="{{ route('finance.billing.index') }}" method="GET" class="w-full sm:w-1/3">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg aria-hidden="true" class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </div>
                                <input type="search" name="search" value="{{ request('search') }}" style="padding-left: 2.5rem;" class="block w-full p-2 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500" placeholder="Cari nama murid...">
                            </div>
                        </form>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Nama Murid</th>
                                    <th scope="col" class="px-6 py-3">Gender</th>
                                    <th scope="col" class="px-6 py-3">Jatuh Tempo (Aktif Sampai)</th>
                                    <th scope="col" class="px-6 py-3">Sisa Pertemuan</th>
                                    <th scope="col" class="px-6 py-3 text-center">Status Billing</th>
                                    <th scope="col" class="px-6 py-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($students as $student)
                                    <tr class="bg-white border-b hover:bg-gray-50">
                                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                            {{ $student->name ?? ($student->user->name ?? '-') }}
                                        </td>
                                        <td class="px-6 py-4">
                                            @if(strtolower($student->gender) == 'l')
                                                Laki-laki
                                            @elseif(strtolower($student->gender) == 'p')
                                                Perempuan
                                            @else
                                                {{ $student->gender ?? '-' }}
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($student->package_active_until)
                                                {{ \Carbon\Carbon::parse($student->package_active_until)->translatedFormat('d F Y') }}
                                            @else
                                                <span class="text-gray-400 italic">Belum ada paket</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 font-semibold text-center">
                                            <span class="w-6 h-6 inline-flex items-center justify-center text-xs font-bold {{ $student->remaining_meetings > 0 ? 'text-emerald-700 bg-emerald-100 border border-emerald-200' : 'text-red-700 bg-red-100 border border-red-200' }} rounded-full">
                                                {{ $student->remaining_meetings ?? 0 }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @if($student->remaining_meetings > 0)
                                                <span class="px-2 py-1 bg-emerald-100 text-emerald-700 text-xs rounded-full border border-emerald-200">
                                                    Aktif
                                                </span>
                                            @else
                                                <span class="px-2 py-1 bg-red-100 text-red-700 text-xs rounded-full border border-red-200">
                                                    Habis / Unpaid
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <button type="button" @click="editData = { id: '{{ $student->id }}', name: '{{ addslashes($student->name ?? ($student->user->name ?? '-')) }}', active_until: '{{ $student->package_active_until ? \Carbon\Carbon::parse($student->package_active_until)->format('Y-m-d') : '' }}', add_meetings: 0, current_meetings: {{ $student->remaining_meetings ?? 0 }} }; showModal = true" class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 px-3 py-1 rounded-md text-xs font-semibold transition">
                                                Edit Billing
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                            Tidak ada data murid aktif.
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

            <!-- Alpine Modal -->
            <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    
                    <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showModal = false" aria-hidden="true"></div>

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                        
                        <form :action="`/finance/billing/${editData.id}`" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                <div class="sm:flex sm:items-start">
                                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                            Edit Billing Manual
                                        </h3>
                                        <div class="mt-4 space-y-4">
                                            
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Nama Murid</label>
                                                <input type="text" x-model="editData.name" readonly class="mt-1 block w-full bg-gray-100 border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none sm:text-sm text-gray-500 cursor-not-allowed">
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Tanggal Aktif Sampai</label>
                                                <input type="date" name="package_active_until" x-model="editData.active_until" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                            </div>
                                            
                                            <div class="grid grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700">Sisa Saat Ini</label>
                                                    <input type="number" x-model="editData.current_meetings" readonly class="mt-1 block w-full bg-gray-100 border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none sm:text-sm text-gray-500 cursor-not-allowed">
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700">Tambah Pertemuan <span class="text-red-500">*</span></label>
                                                    <input type="number" name="add_meetings" x-model="editData.add_meetings" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Contoh: 4">
                                                </div>
                                            </div>

                                            <div class="p-3 bg-blue-50 rounded-md border border-blue-100 mt-2">
                                                <p class="text-xs text-blue-700">
                                                    <strong>Info:</strong> Nilai yang Anda masukkan pada "Tambah Pertemuan" akan ditambahkan ke sisa saat ini. 
                                                    Total akhirnya nanti akan menjadi <strong x-text="Number(editData.current_meetings) + Number(editData.add_meetings)"></strong>.
                                                </p>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-200">
                                <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                                    Simpan Perubahan
                                </button>
                                <button type="button" @click="showModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                    Batal
                                </button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
