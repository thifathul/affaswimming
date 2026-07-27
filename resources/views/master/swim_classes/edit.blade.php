<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-slate-800 tracking-tight">
                {{ __('Edit Kelas Berenang') }}
            </h2>
            <div class="flex items-center gap-4">
                <a href="{{ route('master.swim-classes.index') }}" class="text-sm text-slate-505 hover:text-slate-800 transition-colors font-medium flex items-center gap-1.5">
                    <i class="fa-solid fa-arrow-left"></i> Kembali
                </a>
                <span class="info-badge"><i class="fa-solid fa-crown mr-1"></i> Hak Akses: {{ ucfirst(auth()->user()->role) }}</span>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-slate-50/50 min-h-screen">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl p-8 border border-slate-100">
                
                <div class="mb-8 border-b border-slate-100 pb-5">
                    <h3 class="text-lg font-bold text-slate-800"><i class="fa-solid fa-pen-to-square mr-2 text-blue-500"></i> Perbarui Data Kelas</h3>
                    <p class="text-sm text-slate-505 mt-1">Ubah informasi kelas, atur jadwal, atau ganti pelatih yang bertugas untuk kelas ini.</p>
                </div>

                <form method="POST" action="{{ route('master.swim-classes.update', $swimClass->id) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="name" class="block text-sm font-semibold text-slate-700 mb-1">Nama Kelas <span class="text-red-500">*</span></label>
                        <input id="name" name="name" type="text" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-slate-800 focus:border-blue-500 focus:ring focus:ring-blue-500/20 transition-all placeholder-slate-400" value="{{ old('name', $swimClass->name) }}" required autofocus />
                        <x-input-error class="mt-2 text-red-500 text-xs" :messages="$errors->get('name')" />
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Pelatih Pengampu <span class="text-slate-400 font-normal text-xs">(Pilih satu atau lebih)</span></label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 p-4 bg-slate-50 rounded-xl border border-slate-200">
                            @forelse($coaches as $coach)
                                @php
                                    $isChecked = (is_array(old('coach_ids')) && in_array($coach->id, old('coach_ids'))) || 
                                                 (!old('_token') && in_array($coach->id, $selectedCoaches));
                                @endphp
                                <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-slate-100 cursor-pointer transition-colors">
                                    <input type="checkbox" name="coach_ids[]" value="{{ $coach->id }}" {{ $isChecked ? 'checked' : '' }} class="rounded border-slate-300 text-blue-600 shadow-sm focus:ring-blue-500 w-5 h-5">
                                    <span class="text-sm font-medium text-slate-700"><i class="fa-solid fa-user-tie text-slate-400 mr-1.5"></i> {{ $coach->name }}</span>
                                </label>
                            @empty
                                <div class="col-span-2 text-sm text-slate-500 italic p-2 text-center">Belum ada data pelatih terdaftar di sistem.</div>
                            @endforelse
                        </div>
                        <x-input-error class="mt-2 text-red-500 text-xs" :messages="$errors->get('coach_ids')" />
                    </div>

                    @php
                        $scheduleHari = old('schedule_hari');
                        $scheduleJam = old('schedule_jam');
                        $parts = [];
                        if (!old('_token') && $swimClass->schedule) {
                            $parts = explode(', ', $swimClass->schedule);
                            if (count($parts) === 2) {
                                $scheduleHari = $parts[0];
                                $scheduleJam = $parts[1];
                            }
                        }
                    @endphp
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Jadwal Kelas (Opsional)</label>
                        <div class="flex gap-4">
                            <select name="schedule_hari" class="w-1/2 bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-slate-800 focus:border-blue-500 focus:ring focus:ring-blue-500/20 transition-all">
                                <option value="">Pilih Hari</option>
                                <option value="Senin" {{ $scheduleHari === 'Senin' ? 'selected' : '' }}>Senin</option>
                                <option value="Selasa" {{ $scheduleHari === 'Selasa' ? 'selected' : '' }}>Selasa</option>
                                <option value="Rabu" {{ $scheduleHari === 'Rabu' ? 'selected' : '' }}>Rabu</option>
                                <option value="Kamis" {{ $scheduleHari === 'Kamis' ? 'selected' : '' }}>Kamis</option>
                                <option value="Jumat" {{ $scheduleHari === 'Jumat' ? 'selected' : '' }}>Jumat</option>
                                <option value="Sabtu" {{ $scheduleHari === 'Sabtu' ? 'selected' : '' }}>Sabtu</option>
                                <option value="Minggu" {{ $scheduleHari === 'Minggu' ? 'selected' : '' }}>Minggu</option>
                            </select>
                            <input type="time" name="schedule_jam" class="w-1/2 bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-slate-800 focus:border-blue-500 focus:ring focus:ring-blue-500/20 transition-all" value="{{ $scheduleJam }}" />
                        </div>
                        @if($swimClass->schedule && count($parts) !== 2)
                            <p class="text-xs text-amber-600 mt-1"><i class="fa-solid fa-circle-info"></i> Jadwal sebelumnya (format lama): {{ $swimClass->schedule }}</p>
                        @endif
                        <x-input-error class="mt-2 text-red-500 text-xs" :messages="$errors->get('schedule')" />
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-semibold text-slate-700 mb-1">Deskripsi Tambahan (Opsional)</label>
                        <textarea id="description" name="description" rows="3" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-slate-800 focus:border-blue-500 focus:ring focus:ring-blue-500/20 transition-all placeholder-slate-400" placeholder="Informasi detail mengenai target dan level kelas ini...">{{ old('description', $swimClass->description) }}</textarea>
                        <x-input-error class="mt-2 text-red-500 text-xs" :messages="$errors->get('description')" />
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-semibold text-slate-700 mb-1">Status Kelas</label>
                        <div class="relative">
                            <select id="status" name="status" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-slate-800 focus:border-blue-500 focus:ring focus:ring-blue-500/20 transition-all appearance-none cursor-pointer" required>
                                <option value="aktif" {{ old('status', $swimClass->status) === 'aktif' ? 'selected' : '' }}>Aktif (Dibuka untuk Murid)</option>
                                <option value="nonaktif" {{ old('status', $swimClass->status) === 'nonaktif' ? 'selected' : '' }}>Nonaktif (Ditutup Sementara)</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400">
                                <i class="fa-solid fa-chevron-down text-xs"></i>
                            </div>
                        </div>
                        <x-input-error class="mt-2 text-red-500 text-xs" :messages="$errors->get('status')" />
                    </div>

                    <div class="flex items-center justify-end gap-4 pt-6 border-t border-slate-100 mt-8">
                        <a href="{{ route('master.swim-classes.index') }}" class="text-slate-505 hover:text-slate-800 font-semibold px-4 py-2 transition-colors">Batal</a>
                        <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-bold text-sm rounded-xl transition-all duration-200 shadow-md shadow-slate-800/20 hover:shadow-lg hover:shadow-slate-800/30 active:scale-95">
                            <i class="fa-solid fa-arrows-rotate text-xs"></i> Perbarui Kelas
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
