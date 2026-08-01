<x-guest-layout>
    <!-- Form Heading -->
    <div class="text-center mb-8">
        <h2 class="text-xl font-bold tracking-widest text-white uppercase" style="letter-spacing: 2px;">
            Daftar Anggota Baru
        </h2>
        <p class="text-xs text-slate-400 mt-2">Lengkapi informasi di bawah ini untuk bergabung dengan AFFA Swimming</p>
        <div class="w-16 h-[2px] bg-gradient-to-r from-yellow-500 to-yellow-300 mx-auto mt-4 rounded-full"></div>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-8">
        @csrf

        <!-- INFORMASI AKUN -->
        <div class="bg-slate-900/50 p-6 rounded-2xl border border-white/5">
            <h3 class="text-sm font-bold text-yellow-400 uppercase tracking-wider mb-4 border-b border-white/10 pb-2"><i class="fa-solid fa-user-lock mr-2"></i> 1. Informasi Akun</h3>
            
            <div class="space-y-4">
                <!-- Email -->
                <div>
                    <x-input-label for="email" :value="__('Alamat Email')" class="mb-2 block text-xs font-semibold uppercase tracking-wider text-slate-300" />
                    <div class="input-group">
                        <i class="fa-solid fa-envelope"></i>
                        <x-text-input id="email" class="block w-full" type="email" name="email" :value="old('email')" required autofocus placeholder="Masukkan alamat email Anda" />
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-xs text-red-400" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Password -->
                    <div>
                        <x-input-label for="password" :value="__('Kata Sandi')" class="mb-2 block text-xs font-semibold uppercase tracking-wider text-slate-300" />
                        <div class="input-group">
                            <i class="fa-solid fa-lock"></i>
                            <x-text-input id="password" class="block w-full" type="password" name="password" required autocomplete="new-password" placeholder="••••••••" />
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2 text-xs text-red-400" />
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <x-input-label for="password_confirmation" :value="__('Konfirmasi Sandi')" class="mb-2 block text-xs font-semibold uppercase tracking-wider text-slate-300" />
                        <div class="input-group">
                            <i class="fa-solid fa-lock"></i>
                            <x-text-input id="password_confirmation" class="block w-full" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••" />
                        </div>
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-xs text-red-400" />
                    </div>
                </div>
            </div>
        </div>

        <!-- BIODATA MURID -->
        <div class="bg-slate-900/50 p-6 rounded-2xl border border-white/5">
            <h3 class="text-sm font-bold text-yellow-400 uppercase tracking-wider mb-4 border-b border-white/10 pb-2"><i class="fa-solid fa-address-card mr-2"></i> 2. Biodata Murid</h3>
            
            <div class="space-y-4">
                <!-- Nama Lengkap -->
                <div>
                    <x-input-label for="name" :value="__('Nama Lengkap Anak')" class="mb-2 block text-xs font-semibold uppercase tracking-wider text-slate-300" />
                    <div class="input-group">
                        <i class="fa-regular fa-id-badge"></i>
                        <x-text-input id="name" class="block w-full" type="text" name="name" :value="old('name')" required placeholder="Masukkan nama lengkap murid" />
                    </div>
                    <x-input-error :messages="$errors->get('name')" class="mt-2 text-xs text-red-400" />
                </div>

                <!-- Gender / Jenis Kelamin -->
                <div>
                    <x-input-label for="gender" :value="__('Jenis Kelamin')" class="mb-2 block text-xs font-semibold uppercase tracking-wider text-slate-300" />
                    <div class="input-group">
                        <i class="fa-solid fa-venus-mars"></i>
                        <select id="gender" name="gender" class="block w-full" required>
                            <option value="" disabled selected>Pilih Jenis Kelamin</option>
                            <option value="Laki-laki" {{ old('gender') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="Perempuan" {{ old('gender') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                    <x-input-error :messages="$errors->get('gender')" class="mt-2 text-xs text-red-400" />
                </div>

                <!-- Tempat, Tanggal Lahir -->
                <div>
                    <x-input-label for="birth_place_date" :value="__('Tempat, Tanggal Lahir')" class="mb-2 block text-xs font-semibold uppercase tracking-wider text-slate-300" />
                    <div class="input-group">
                        <i class="fa-regular fa-calendar"></i>
                        <x-text-input id="birth_place_date" class="block w-full" type="text" name="birth_place_date" :value="old('birth_place_date')" required placeholder="Contoh: Bandung, 17 Agustus 2015" />
                    </div>
                    <x-input-error :messages="$errors->get('birth_place_date')" class="mt-2 text-xs text-red-400" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Usia -->
                    <div>
                        <x-input-label for="age" :value="__('Usia (Tahun)')" class="mb-2 block text-xs font-semibold uppercase tracking-wider text-slate-300" />
                        <div class="input-group">
                            <i class="fa-solid fa-child"></i>
                            <x-text-input id="age" class="block w-full" type="number" min="1" max="99" name="age" :value="old('age')" required placeholder="Contoh: 8" />
                        </div>
                        <x-input-error :messages="$errors->get('age')" class="mt-2 text-xs text-red-400" />
                    </div>

                    <!-- Sekolah -->
                    <div>
                        <x-input-label for="school" :value="__('Asal Sekolah')" class="mb-2 block text-xs font-semibold uppercase tracking-wider text-slate-300" />
                        <div class="input-group">
                            <i class="fa-solid fa-school"></i>
                            <x-text-input id="school" class="block w-full" type="text" name="school" :value="old('school')" required placeholder="Contoh: SDN 1 Bandung" />
                        </div>
                        <x-input-error :messages="$errors->get('school')" class="mt-2 text-xs text-red-400" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="pt-2 flex flex-col sm:flex-row items-center justify-between gap-4">
            <a class="text-sm text-slate-400 hover:text-yellow-300 transition-colors" href="{{ route('login') }}">
                Sudah punya akun? Masuk
            </a>

            <x-primary-button class="w-full sm:w-auto px-8 py-3 bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-400 hover:to-yellow-500 text-slate-900 border-none shadow-[0_0_15px_rgba(234,179,8,0.3)] hover:shadow-[0_0_25px_rgba(234,179,8,0.5)] transition-all duration-300">
                {{ __('Daftar Sekarang') }} <i class="fa-solid fa-paper-plane ms-2"></i>
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
