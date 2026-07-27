<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-slate-800 tracking-tight">
                {{ __('Tambah Anggota Tim') }}
            </h2>
            <div class="flex items-center gap-4">
                <a href="{{ route('master.teams.index') }}" class="text-sm text-slate-505 hover:text-slate-800 transition-colors font-medium flex items-center gap-1.5">
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
                    <h3 class="text-lg font-bold text-slate-800"><i class="fa-solid fa-user-plus mr-2 text-blue-500"></i> Formulir Tambah Anggota</h3>
                    <p class="text-sm text-slate-505 mt-1">Lengkapi informasi profil anggota tim yang akan ditampilkan di halaman Tentang Kami.</p>
                </div>
                
                <form action="{{ route('master.teams.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label for="name" class="block text-sm font-semibold text-slate-700 mb-1">Nama Lengkap</label>
                            <input id="name" name="name" type="text" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-slate-800 focus:border-blue-500 focus:ring focus:ring-blue-500/20 transition-all placeholder-slate-400" value="{{ old('name') }}" required autofocus placeholder="Contoh: Budi Santoso" />
                            <x-input-error class="mt-2 text-red-500 text-xs" :messages="$errors->get('name')" />
                        </div>

                        <div>
                            <label for="position" class="block text-sm font-semibold text-slate-700 mb-1">Jabatan / Peran</label>
                            <div class="relative">
                                <select id="position" name="position" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-slate-800 focus:border-blue-500 focus:ring focus:ring-blue-500/20 transition-all appearance-none cursor-pointer" required>
                                    <option value="" disabled selected>Pilih Jabatan</option>
                                    @foreach($positions as $pos)
                                        <option value="{{ $pos->name }}" {{ old('position') == $pos->name ? 'selected' : '' }}>
                                            {{ $pos->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400">
                                    <i class="fa-solid fa-chevron-down text-xs"></i>
                                </div>
                            </div>
                            <x-input-error class="mt-2 text-red-500 text-xs" :messages="$errors->get('position')" />
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-semibold text-slate-700 mb-1">Nomor Telepon</label>
                            <input id="phone" name="phone" type="text" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-slate-800 focus:border-blue-500 focus:ring focus:ring-blue-500/20 transition-all placeholder-slate-400" value="{{ old('phone') }}" placeholder="Contoh: 081234567890" />
                            <x-input-error class="mt-2 text-red-500 text-xs" :messages="$errors->get('phone')" />
                        </div>
                        
                        <div class="md:col-span-2">
                            <label for="address" class="block text-sm font-semibold text-slate-700 mb-1">Alamat (Opsional)</label>
                            <textarea id="address" name="address" rows="3" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-slate-800 focus:border-blue-500 focus:ring focus:ring-blue-500/20 transition-all placeholder-slate-400" placeholder="Tuliskan alamat lengkap di sini...">{{ old('address') }}</textarea>
                            <x-input-error class="mt-2 text-red-500 text-xs" :messages="$errors->get('address')" />
                        </div>

                        <div class="md:col-span-2 border-t border-slate-100 pt-6 mt-2">
                            <h4 class="text-md font-bold text-slate-800 mb-4"><i class="fa-solid fa-lock mr-2 text-slate-400"></i> Akun Login (Opsional)</h4>
                            <p class="text-xs text-slate-500 mb-4">Isi bagian ini jika anggota tim ini memerlukan akses login ke sistem. Kosongkan jika tidak.</p>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="email" class="block text-sm font-semibold text-slate-700 mb-1">Email Login</label>
                                    <input id="email" name="email" type="email" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-slate-800 focus:border-blue-500 focus:ring focus:ring-blue-500/20 transition-all placeholder-slate-400" value="{{ old('email') }}" placeholder="Contoh: budi@affaswimming.com" />
                                    <x-input-error class="mt-2 text-red-500 text-xs" :messages="$errors->get('email')" />
                                </div>
                                
                                <div>
                                    <label for="role" class="block text-sm font-semibold text-slate-700 mb-1">Hak Akses</label>
                                    <div class="relative">
                                        <select id="role" name="role" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-slate-800 focus:border-blue-500 focus:ring focus:ring-blue-500/20 transition-all appearance-none cursor-pointer">
                                            <option value="" selected>Pilih Hak Akses</option>
                                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                            <option value="pelatih" {{ old('role') == 'pelatih' ? 'selected' : '' }}>Pelatih</option>
                                        </select>
                                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400">
                                            <i class="fa-solid fa-chevron-down text-xs"></i>
                                        </div>
                                    </div>
                                    <p class="text-xs text-slate-400 mt-2"><i class="fa-solid fa-circle-info mr-1"></i> Password default: <strong>password</strong></p>
                                    <x-input-error class="mt-2 text-red-500 text-xs" :messages="$errors->get('role')" />
                                </div>
                            </div>
                        </div>

                        <div class="md:col-span-2">
                            <label for="photo" class="block text-sm font-semibold text-slate-700 mb-1">Foto Profil</label>
                            <div class="mt-1 flex items-center justify-center w-full">
                                <label for="photo" class="flex flex-col items-center justify-center w-full h-24 border-2 border-slate-200 border-dashed rounded-xl cursor-pointer bg-slate-50 hover:bg-slate-100 hover:border-blue-400 transition-all">
                                    <div class="flex flex-col items-center justify-center pt-4 pb-4">
                                        <i class="fa-solid fa-cloud-arrow-up text-slate-400 text-xl mb-1"></i>
                                        <p class="text-sm text-slate-500 font-semibold"><span class="text-blue-600 font-bold">Klik untuk unggah</span> atau seret file ke sini</p>
                                        <p class="text-xs text-slate-400 mt-0.5">PNG, JPG atau JPEG (Maks. 2MB)</p>
                                    </div>
                                    <input type="file" id="photo" name="photo" class="hidden" accept="image/*" onchange="document.getElementById('file-name').textContent = this.files[0].name">
                                </label>
                            </div>
                            <p id="file-name" class="text-xs text-blue-600 mt-2 font-semibold italic text-center"></p>
                            <x-input-error class="mt-2 text-red-500 text-xs" :messages="$errors->get('photo')" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-4 pt-6 border-t border-slate-100 mt-8">
                        <a href="{{ route('master.teams.index') }}" class="text-slate-500 hover:text-slate-800 font-semibold px-4 py-2 transition-colors">Batal</a>
                        <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-bold text-sm rounded-xl transition-all duration-200 shadow-md shadow-slate-800/20 hover:shadow-lg hover:shadow-slate-800/30 active:scale-95">
                            <i class="fa-solid fa-floppy-disk text-xs"></i> Simpan Data Anggota
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
