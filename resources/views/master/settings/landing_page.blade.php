<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-slate-800 tracking-tight">
            {{ __('Pengaturan Halaman Utama (Landing Page)') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-slate-50/50 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
            <div class="mb-6 bg-emerald-50 text-emerald-700 p-4 rounded-xl border border-emerald-100 flex items-center gap-3">
                <i class="fa-solid fa-circle-check text-emerald-500 text-xl"></i>
                <p class="font-medium text-sm">{{ session('success') }}</p>
            </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100 p-8">
                <div class="flex items-center gap-3 mb-6 border-b border-slate-100 pb-4">
                    <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center">
                        <i class="fa-solid fa-laptop-code"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-800">Ubah Teks Sambutan</h3>
                        <p class="text-sm text-slate-500">Sesuaikan teks yang akan tampil di halaman utama website.</p>
                    </div>
                </div>

                <form action="{{ route('master.settings.landing.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-6">
                        <label class="block text-sm font-bold text-slate-700 mb-2">
                            Judul Utama (Headline) <span class="text-red-500">*</span>
                        </label>
                        <p class="text-xs text-slate-500 mb-2">Gunakan format biasa, sistem otomatis mendesain dengan tema emas (gold) pada bagian tertentu.</p>
                        <input type="text" name="landing_title" value="{{ old('landing_title', $landingTitle) }}" required
                               class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-sm">
                        @error('landing_title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-8">
                        <label class="block text-sm font-bold text-slate-700 mb-2">
                            Deskripsi / Sub-judul <span class="text-red-500">*</span>
                        </label>
                        <textarea name="landing_subtitle" rows="4" required
                                  class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-sm">{{ old('landing_subtitle', $landingSubtitle) }}</textarea>
                        @error('landing_subtitle') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-5 mb-8">
                        <h4 class="font-bold text-slate-700 text-sm mb-2"><i class="fa-solid fa-circle-info text-blue-500 mr-2"></i>Informasi Indikator Statistik</h4>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Angka indikator pada halaman utama (Siswa Aktif, Total Alumni, Pelatih Profesional) tidak perlu diisi manual. Sistem akan secara otomatis menghitung metrik tersebut berdasarkan data aktual yang ada di database aplikasi Anda secara *real-time*.
                        </p>
                    </div>

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('master.dashboard') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-semibold text-sm hover:bg-slate-50 transition">
                            Batal
                        </a>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-600 text-white font-semibold text-sm hover:bg-blue-700 shadow-sm transition">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
