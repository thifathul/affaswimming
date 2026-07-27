<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-slate-800 tracking-tight">
                {{ __('Edit Pengguna') }}
            </h2>
            <div class="flex items-center gap-4">
                <a href="{{ route('master.users') }}" class="text-sm text-slate-500 hover:text-slate-800 transition-colors font-medium flex items-center gap-1.5">
                    <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar
                </a>
                <span class="info-badge"><i class="fa-solid fa-crown mr-1"></i> Hak Akses: Master</span>
            </div>
        </div>
    </x-slot>

    <!-- Custom Styling for Form Inputs -->
    <style>
        .input-group {
            position: relative;
        }

        .input-group i {
            position: absolute;
            left: 1.25rem;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            transition: color 0.3s ease;
            z-index: 10;
            font-size: 1.1rem;
        }

        /* Target inputs & select inside group */
        .input-group input, .input-group select {
            background-color: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            color: #334155 !important;
            border-radius: 12px !important;
            padding-left: 3rem !important;
            padding-right: 1.25rem !important;
            height: 3.5rem !important;
            font-size: 0.95rem !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
            width: 100% !important;
        }

        .input-group input:focus, .input-group select:focus {
            border-color: #2563eb !important;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1) !important;
            outline: none !important;
        }

        /* Icon focus effect */
        .input-group:focus-within i {
            color: #2563eb !important;
        }

        /* Select specifically */
        .input-group select {
            cursor: pointer;
            appearance: none;
            -webkit-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2364748b'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E") !important;
            background-repeat: no-repeat !important;
            background-position: right 1.25rem center !important;
            background-size: 1.1rem !important;
        }

        .input-group select option {
            background-color: #ffffff !important;
            color: #334155 !important;
        }
    </style>

    <div class="py-12 bg-slate-50/50 min-h-screen">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl p-8 border border-slate-100">
                
                <div class="mb-8 border-b border-slate-100 pb-5">
                    <h3 class="text-lg font-bold text-slate-800"><i class="fa-solid fa-user-pen mr-2 text-blue-500"></i> Formulir Edit Pengguna</h3>
                    <p class="text-sm text-slate-500 mt-1">Perbarui informasi akun, email, hak akses, atau ubah kata sandi jika diperlukan.</p>
                </div>

                <form method="POST" action="{{ route('master.users.update', $user->id) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Lengkap</label>
                        <div class="input-group">
                            <i class="fa-solid fa-user"></i>
                            <x-text-input id="name" class="block w-full" type="text" name="name" :value="old('name', $user->name)" required autofocus placeholder="Nama Lengkap Anggota" />
                        </div>
                        <x-input-error :messages="$errors->get('name')" class="mt-2 text-xs text-red-500" />
                    </div>

                    <!-- Email Address -->
                    <div>
                        <label for="email" class="block text-sm font-semibold text-slate-700 mb-1.5">Alamat Email</label>
                        <div class="input-group">
                            <i class="fa-solid fa-envelope"></i>
                            <x-text-input id="email" class="block w-full" type="email" name="email" :value="old('email', $user->email)" required placeholder="email@contoh.com" />
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-xs text-red-500" />
                    </div>

                    <!-- Role Dropdown -->
                    <div>
                        <label for="role" class="block text-sm font-semibold text-slate-700 mb-1.5">Hak Akses / Peran</label>
                        <div class="input-group">
                            <i class="fa-solid fa-users-gear"></i>
                            <select id="role" name="role" required>
                                <option value="" disabled>Pilih Hak Akses / Peran</option>
                                <option value="master" {{ old('role', $user->role) === 'master' ? 'selected' : '' }}>Master</option>
                                <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin (Pengelola)</option>
                                <option value="pelatih" {{ old('role', $user->role) === 'pelatih' ? 'selected' : '' }}>Pelatih (Instruktur)</option>
                                <option value="murid" {{ old('role', $user->role) === 'murid' ? 'selected' : '' }}>Murid (Siswa)</option>
                            </select>
                        </div>
                        <x-input-error :messages="$errors->get('role')" class="mt-2 text-xs text-red-500" />
                    </div>

                    <!-- Position Dropdown -->
                    <div>
                        <label for="position_id" class="block text-sm font-semibold text-slate-700 mb-1.5">Jabatan (Opsional)</label>
                        <div class="input-group">
                            <i class="fa-solid fa-briefcase"></i>
                            <select id="position_id" name="position_id">
                                <option value="">Tanpa Jabatan Khusus</option>
                                @foreach($positions as $position)
                                    <option value="{{ $position->id }}" {{ old('position_id', $user->position_id) == $position->id ? 'selected' : '' }}>
                                        {{ $position->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <p class="text-xs text-slate-400 mt-1.5">Pilih kategori jabatan untuk pengguna ini (opsional).</p>
                        <x-input-error :messages="$errors->get('position_id')" class="mt-2 text-xs text-red-500" />
                    </div>

                    <!-- Password -->
                    <div class="mt-8 border-t border-slate-100 pt-6">
                        <h4 class="text-md font-bold text-slate-800 mb-4"><i class="fa-solid fa-key mr-2 text-slate-400"></i> Ubah Kata Sandi (Opsional)</h4>
                        <p class="text-xs text-slate-500 mb-4">Kosongkan kolom kata sandi jika tidak ingin mengubahnya.</p>
                        
                        <div class="space-y-6">
                            <div>
                                <label for="password" class="block text-sm font-semibold text-slate-700 mb-1.5">Kata Sandi Baru</label>
                                <div class="input-group">
                                    <i class="fa-solid fa-lock"></i>
                                    <x-text-input id="password" class="block w-full" type="password" name="password" autocomplete="new-password" placeholder="Minimal 8 karakter" />
                                </div>
                                <x-input-error :messages="$errors->get('password')" class="mt-2 text-xs text-red-500" />
                            </div>

                            <!-- Confirm Password -->
                            <div>
                                <label for="password_confirmation" class="block text-sm font-semibold text-slate-700 mb-1.5">Konfirmasi Kata Sandi Baru</label>
                                <div class="input-group">
                                    <i class="fa-solid fa-circle-check"></i>
                                    <x-text-input id="password_confirmation" class="block w-full" type="password" name="password_confirmation" placeholder="Ulangi kata sandi baru" />
                                </div>
                                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-xs text-red-500" />
                            </div>
                        </div>
                    </div>

                    <!-- Action Trigger Buttons -->
                    <div class="flex items-center justify-end gap-4 pt-6 border-t border-slate-100 mt-8">
                        <a href="{{ route('master.users') }}" class="text-slate-500 hover:text-slate-800 font-semibold px-4 py-2 transition-colors">
                            Batal
                        </a>
                        <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-bold text-sm rounded-xl transition-all duration-200 shadow-md shadow-slate-800/20 hover:shadow-lg hover:shadow-slate-800/30 active:scale-95">
                            <i class="fa-solid fa-floppy-disk mr-1"></i> Perbarui Pengguna
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
