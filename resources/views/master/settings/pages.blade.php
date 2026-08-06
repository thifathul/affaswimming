<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-slate-800 tracking-tight">
            {{ __('Pengaturan Halaman Statis') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-slate-50/50 min-h-screen">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
            <div class="mb-6 bg-emerald-50 text-emerald-700 p-4 rounded-xl border border-emerald-100 flex items-center gap-3">
                <i class="fa-solid fa-circle-check text-emerald-500 text-xl"></i>
                <p class="font-medium text-sm">{{ session('success') }}</p>
            </div>
            @endif

            <form action="{{ route('master.settings.pages.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- SECTION: TENTANG KAMI -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100 p-8 mb-8">
                    <div class="flex items-center gap-3 mb-6 border-b border-slate-100 pb-4">
                        <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center">
                            <i class="fa-solid fa-address-card"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-slate-800">Halaman "Tentang Kami"</h3>
                            <p class="text-sm text-slate-500">Ubah profil dan sambutan Owner.</p>
                        </div>
                    </div>
                    
                    <div class="mb-6" x-data="{ showPreview: false }">
                        <label class="block text-sm font-bold text-slate-700 mb-2">Foto Owner (Maks. 2MB)</label>
                        @if(isset($settings['about_owner_photo']) && $settings['about_owner_photo'])
                            <div class="mb-3">
                                <button type="button" @click="showPreview = !showPreview" class="px-3 py-1.5 text-xs font-semibold bg-slate-100 text-slate-600 rounded-lg border border-slate-200 hover:bg-slate-200 transition">
                                    <i class="fa-solid fa-eye mr-1"></i> <span x-text="showPreview ? 'Sembunyikan Preview' : 'Lihat Preview Foto Saat Ini'"></span>
                                </button>
                                <div x-show="showPreview" x-transition style="display: none;" class="mt-3">
                                    <img src="{{ asset('storage/' . $settings['about_owner_photo']) }}" alt="Current Photo" class="h-48 object-cover rounded-xl border border-slate-200 shadow-sm">
                                </div>
                            </div>
                        @endif
                        <input type="file" name="about_owner_photo" accept="image/*"
                               class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        @error('about_owner_photo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-bold text-slate-700 mb-2">
                            Teks Sambutan Owner
                        </label>
                        <textarea name="about_owner_message" rows="5"
                                  class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-sm">{{ old('about_owner_message', $settings['about_owner_message'] ?? "Kami berdedikasi untuk memberikan pelatihan renang terbaik untuk seluruh generasi.") }}</textarea>
                        @error('about_owner_message') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- SECTION: KONTAK -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100 p-8 mb-8">
                    <div class="flex items-center gap-3 mb-6 border-b border-slate-100 pb-4">
                        <div class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center">
                            <i class="fa-solid fa-headset"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-slate-800">Halaman "Kontak"</h3>
                            <p class="text-sm text-slate-500">Informasi alamat dan kanal komunikasi.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Email</label>
                            <input type="email" name="contact_email" value="{{ old('contact_email', $settings['contact_email'] ?? '') }}"
                                   class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-sm" placeholder="info@affaswimming.com">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Nomor WhatsApp / Telepon</label>
                            <input type="text" name="contact_phone" value="{{ old('contact_phone', $settings['contact_phone'] ?? '') }}"
                                   class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-sm" placeholder="+62 812 3456 7890">
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-bold text-slate-700 mb-2">Username Instagram / Link Sosial Media</label>
                        <input type="text" name="contact_instagram" value="{{ old('contact_instagram', $settings['contact_instagram'] ?? '') }}"
                               class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-sm" placeholder="@affaswimming">
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-bold text-slate-700 mb-2">Alamat Lengkap</label>
                        <textarea name="contact_address" rows="3"
                                  class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-sm" placeholder="Jl. Kolam Renang No.123...">{{ old('contact_address', $settings['contact_address'] ?? '') }}</textarea>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-bold text-slate-700 mb-2">Kode Embed Google Maps (Iframe)</label>
                        <p class="text-xs text-slate-500 mb-2">Pilih lokasi di Google Maps -> Klik Share -> Embed a Map -> Copy HTML.</p>
                        <textarea name="contact_map_embed" rows="4"
                                  class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-sm font-mono text-xs">{{ old('contact_map_embed', $settings['contact_map_embed'] ?? '') }}</textarea>
                    </div>
                </div>

                <!-- SECTION: PENGATURAN WAKTU JADWAL -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100 p-8 mb-8">
                    <div class="flex items-center gap-3 mb-6 border-b border-slate-100 pb-4">
                        <div class="w-10 h-10 rounded-full bg-purple-50 text-purple-600 flex items-center justify-center">
                            <i class="fa-solid fa-clock"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-slate-800">Pengaturan Kategori Waktu Jadwal</h3>
                            <p class="text-sm text-slate-500">Atur batasan jam untuk kategori Pagi, Siang, dan Sore di halaman Manajemen Jadwal.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Batas Akhir Jam Pagi (Contoh: 11:59)</label>
                            <input type="time" name="schedule_morning_end" value="{{ old('schedule_morning_end', $settings['schedule_morning_end'] ?? '11:59') }}"
                                   class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-sm">
                            <p class="text-xs text-slate-500 mt-1">Jadwal dari jam 00:00 sampai jam ini akan dihitung sebagai Pagi.</p>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Batas Akhir Jam Siang (Contoh: 14:59)</label>
                            <input type="time" name="schedule_afternoon_end" value="{{ old('schedule_afternoon_end', $settings['schedule_afternoon_end'] ?? '14:59') }}"
                                   class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-sm">
                            <p class="text-xs text-slate-500 mt-1">Jadwal setelah batas pagi sampai jam ini akan dihitung sebagai Siang. Sisanya otomatis Sore.</p>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mb-10">
                    <a href="{{ route('master.dashboard') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-semibold text-sm hover:bg-slate-50 transition">
                        Batal
                    </a>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-600 text-white font-semibold text-sm hover:bg-blue-700 shadow-sm transition">
                        Simpan Semua Perubahan
                    </button>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>
