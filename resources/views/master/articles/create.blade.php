<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-slate-800 tracking-tight">
                {{ __('Tulis Artikel Baru') }}
            </h2>
            <div class="flex items-center gap-4">
                <a href="{{ route('master.articles.index') }}" class="text-sm text-slate-500 hover:text-slate-800 transition-colors font-medium flex items-center gap-1.5">
                    <i class="fa-solid fa-arrow-left"></i> Kembali
                </a>
                <span class="info-badge"><i class="fa-solid fa-crown mr-1"></i> Hak Akses: {{ ucfirst(auth()->user()->role) }}</span>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-slate-50/50 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl p-8 border border-slate-100">
                
                <div class="mb-8 border-b border-slate-100 pb-5">
                    <h3 class="text-lg font-bold text-slate-800"><i class="fa-solid fa-pen-nib mr-2 text-blue-500"></i> Formulir Publikasi Berita</h3>
                    <p class="text-sm text-slate-500 mt-1">Lengkapi informasi di bawah ini untuk memublikasikan informasi baru ke halaman utama website.</p>
                </div>
                
                <form action="{{ route('master.articles.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label for="title" class="block text-sm font-semibold text-slate-700 mb-1">Judul Berita</label>
                            <input id="title" name="title" type="text" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-slate-800 focus:border-blue-500 focus:ring focus:ring-blue-500/20 transition-all placeholder-slate-400" value="{{ old('title') }}" required autofocus placeholder="Contoh: Tim AFFA Meraih Juara 1 Kejurda" />
                            <x-input-error class="mt-2 text-red-500 text-xs" :messages="$errors->get('title')" />
                        </div>

                        <div>
                            <label for="category" class="block text-sm font-semibold text-slate-700 mb-1">Kategori</label>
                            <div class="relative">
                                <select id="category" name="category" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-slate-800 focus:border-blue-500 focus:ring focus:ring-blue-500/20 transition-all appearance-none cursor-pointer">
                                    <option value="pengumuman" {{ old('category') == 'pengumuman' ? 'selected' : '' }}>📢 Pengumuman</option>
                                    <option value="prestasi" {{ old('category') == 'prestasi' ? 'selected' : '' }}>🏆 Prestasi</option>
                                    <option value="tips" {{ old('category') == 'tips' ? 'selected' : '' }}>💡 Tips & Trik</option>
                                </select>
                            </div>
                            <x-input-error class="mt-2 text-red-500 text-xs" :messages="$errors->get('category')" />
                        </div>

                        <div>
                            <label for="image" class="block text-sm font-semibold text-slate-700 mb-1">Gambar Sampul</label>
                            <div class="mt-1 flex items-center justify-center w-full">
                                <label for="image" class="flex flex-col items-center justify-center w-full h-11 border-2 border-slate-200 border-dashed rounded-xl cursor-pointer bg-slate-50 hover:bg-slate-100 hover:border-blue-400 transition-all">
                                    <div class="flex items-center space-x-2">
                                        <i class="fa-solid fa-cloud-arrow-up text-slate-400"></i>
                                        <span class="text-sm text-slate-500 font-semibold">Klik untuk unggah gambar</span>
                                    </div>
                                    <input type="file" id="image" name="image" class="hidden" accept="image/*" onchange="document.getElementById('file-name').textContent = this.files[0].name">
                                </label>
                            </div>
                            <p id="file-name" class="text-xs text-blue-600 mt-1.5 font-semibold italic"></p>
                            <x-input-error class="mt-2 text-red-500 text-xs" :messages="$errors->get('image')" />
                        </div>
                    </div>

                    <div>
                        <label for="excerpt" class="block text-sm font-semibold text-slate-700 mb-1">Ringkasan Singkat (Opsional)</label>
                        <textarea id="excerpt" name="excerpt" rows="2" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-slate-800 focus:border-blue-500 focus:ring focus:ring-blue-500/20 transition-all placeholder-slate-400" placeholder="Tuliskan ringkasan 1-2 kalimat untuk ditampilkan di halaman depan...">{{ old('excerpt') }}</textarea>
                        <p class="text-xs text-slate-400 mt-1.5"><i class="fa-solid fa-circle-info mr-1 text-slate-400"></i> Jika dikosongkan, sistem otomatis mengambil sebagian dari isi konten.</p>
                        <x-input-error class="mt-2 text-red-500 text-xs" :messages="$errors->get('excerpt')" />
                    </div>

                    <div>
                        <label for="content" class="block text-sm font-semibold text-slate-700 mb-1">Isi Berita Lengkap</label>
                        <textarea id="content" name="content" rows="8" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-slate-800 focus:border-blue-500 focus:ring focus:ring-blue-500/20 transition-all placeholder-slate-400" required placeholder="Tuliskan detail berita selengkapnya di sini...">{{ old('content') }}</textarea>
                        <x-input-error class="mt-2 text-red-500 text-xs" :messages="$errors->get('content')" />
                    </div>

                    <div class="bg-slate-50/75 rounded-xl p-5 flex items-center justify-between border border-slate-100">
                        <div>
                            <h4 class="font-bold text-slate-800 text-sm">Visibilitas Publikasi</h4>
                            <p class="text-xs text-slate-500 mt-0.5">Berita yang dipublikasikan akan langsung muncul di halaman utama.</p>
                        </div>
                        <label for="is_published" class="inline-flex items-center cursor-pointer">
                            <input id="is_published" type="checkbox" class="sr-only peer" name="is_published" value="1" {{ old('is_published', true) ? 'checked' : '' }}>
                            <div class="relative w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            <span class="ms-3 text-sm font-bold text-slate-600">Publikasikan</span>
                        </label>
                    </div>

                    <div class="flex items-center justify-end gap-4 pt-6 border-t border-slate-100 mt-8">
                        <a href="{{ route('master.articles.index') }}" class="text-slate-500 hover:text-slate-800 font-semibold px-4 py-2 transition-colors">Batal</a>
                        <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-bold text-sm rounded-xl transition-all duration-200 shadow-md shadow-slate-800/20 hover:shadow-lg hover:shadow-slate-800/30 active:scale-95">
                            <i class="fa-solid fa-paper-plane text-xs"></i> Simpan & Publikasikan
                        </button>
                    </div>
                </form>

            </div>
        </div>
</x-app-layout>
