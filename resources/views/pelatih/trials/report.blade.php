<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-slate-800 tracking-tight">
            {{ __('Laporan Trial') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-slate-50/50 min-h-screen">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100 p-8">
                
                @if ($errors->any())
                    <div class="mb-6 bg-red-50 text-red-700 p-4 rounded-xl text-sm border border-red-200">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="mb-6 p-5 bg-blue-50 rounded-xl border border-blue-100">
                    <h3 class="font-bold text-blue-900 mb-2">Informasi Trial</h3>
                    <div class="grid grid-cols-2 gap-4 text-sm text-blue-800">
                        <div>
                            <span class="block text-blue-600/70 font-medium text-xs mb-0.5">Nama Murid</span>
                            {{ $trial->name }} ({{ $trial->age }} Thn)
                        </div>
                        <div>
                            <span class="block text-blue-600/70 font-medium text-xs mb-0.5">Jadwal</span>
                            {{ $trial->schedule_date->format('d M Y') }} - {{ \Carbon\Carbon::parse($trial->schedule_time)->format('H:i') }}
                        </div>
                        <div class="col-span-2">
                            <span class="block text-blue-600/70 font-medium text-xs mb-0.5">Lokasi</span>
                            {{ $trial->poolLocation->name ?? '-' }}
                        </div>
                    </div>
                </div>

                <form action="{{ route('pelatih.trials.update', $trial->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-6">
                        <!-- Kehadiran -->
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Status Kehadiran <span class="text-red-500">*</span></label>
                            <div class="flex gap-4">
                                <label class="flex items-center gap-2 cursor-pointer p-3 border rounded-xl hover:bg-slate-50 transition-colors {{ old('status', $trial->status) === 'hadir' ? 'border-emerald-500 bg-emerald-50/30' : 'border-slate-200' }}">
                                    <input type="radio" name="status" value="hadir" class="text-emerald-600 focus:ring-emerald-500" {{ old('status', $trial->status) === 'hadir' ? 'checked' : '' }} required>
                                    <span class="text-sm font-medium text-slate-700">Hadir</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer p-3 border rounded-xl hover:bg-slate-50 transition-colors {{ old('status', $trial->status) === 'absen' ? 'border-rose-500 bg-rose-50/30' : 'border-slate-200' }}">
                                    <input type="radio" name="status" value="absen" class="text-rose-600 focus:ring-rose-500" {{ old('status', $trial->status) === 'absen' ? 'checked' : '' }} required>
                                    <span class="text-sm font-medium text-slate-700">Tidak Hadir</span>
                                </label>
                            </div>
                        </div>

                        <!-- Catatan -->
                        <div>
                            <label for="report_note" class="block text-sm font-semibold text-slate-700 mb-1">Catatan / Evaluasi Latihan</label>
                            <p class="text-xs text-slate-500 mb-2">Berikan penilaian singkat mengenai kemampuan anak, kenyamanan di air, dsb.</p>
                            <textarea name="report_note" id="report_note" rows="5" class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500" placeholder="Anak sudah berani masuk air, perlu dilatih pernapasan...">{{ old('report_note', $trial->report_note) }}</textarea>
                        </div>

                        <div class="pt-4 flex justify-end gap-3">
                            <a href="{{ route('pelatih.trials.index') }}" class="px-5 py-2.5 bg-slate-100 text-slate-700 rounded-xl font-medium hover:bg-slate-200 transition">Batal</a>
                            <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white rounded-xl font-semibold hover:bg-blue-700 transition shadow-sm">
                                <i class="fa-solid fa-save mr-2"></i> Simpan Laporan
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
