<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-slate-800 tracking-tight">
            {{ __('Tambah Lokasi Kolam') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-slate-50/50 min-h-screen">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100 p-8">
                <form action="{{ route('admin.pool-locations.store') }}" method="POST">
                    @csrf
                    
                    <div class="space-y-6">
                        <div>
                            <label for="package_name" class="block text-sm font-medium text-slate-700">Nama Paket</label>
                            <input type="text" name="package_name" id="package_name" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" value="{{ old('package_name') }}">
                            @error('package_name') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-slate-700">Nama Lokasi</label>
                                <input type="text" name="name" id="name" required class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" value="{{ old('name') }}">
                                @error('name') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="meeting_count" class="block text-sm font-medium text-slate-700">Pertemuan</label>
                                <select name="meeting_count" id="meeting_count" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Pilih Jumlah Pertemuan (Opsional)</option>
                                    @for($i = 1; $i <= 8; $i++)
                                        <option value="{{ $i }}" {{ old('meeting_count') == $i ? 'selected' : '' }}>{{ $i }} Kali</option>
                                    @endfor
                                </select>
                                @error('meeting_count') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label for="coach_fee" class="block text-sm font-medium text-slate-700">Fee Pelatih (Rp)</label>
                                <input type="number" name="coach_fee" id="coach_fee" required min="0" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" value="{{ old('coach_fee', 0) }}">
                                @error('coach_fee') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="cash_percentage" class="block text-sm font-medium text-slate-700">Potongan Kas (%)</label>
                                <input type="number" step="0.01" name="cash_percentage" id="cash_percentage" required min="0" max="100" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" value="{{ old('cash_percentage', 0) }}">
                                @error('cash_percentage') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label for="private_ticket_price" class="block text-sm font-medium text-slate-700">Harga Tiket Private (Opsional)</label>
                                <input type="number" name="private_ticket_price" id="private_ticket_price" min="0" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" value="{{ old('private_ticket_price') }}">
                            </div>

                            <div>
                                <label for="semi_private_ticket_price" class="block text-sm font-medium text-slate-700">Harga Tiket Semi Private (Opsional)</label>
                                <input type="number" name="semi_private_ticket_price" id="semi_private_ticket_price" min="0" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" value="{{ old('semi_private_ticket_price') }}">
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end gap-3">
                        <a href="{{ route('admin.pool-locations.index') }}" class="px-4 py-2 border border-slate-300 rounded-lg shadow-sm text-sm font-medium text-slate-700 bg-white hover:bg-slate-50">
                            Batal
                        </a>
                        <button type="submit" class="px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                            Simpan Lokasi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
