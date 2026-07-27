<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <!-- Form Heading -->
    <div class="text-center mb-8">
        <h2 class="text-xl font-bold tracking-widest text-white uppercase" style="letter-spacing: 2px;">
            Masuk Aplikasi
        </h2>
        <p class="text-xs text-slate-400 mt-2">Masukkan kredensial Anda untuk mengakses portal AFFA Swimming</p>
        <div class="w-16 h-[2px] bg-gradient-to-r from-yellow-500 to-yellow-300 mx-auto mt-4 rounded-full"></div>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Alamat Email')" class="mb-2 block text-xs font-semibold uppercase tracking-wider text-slate-300" />
            <div class="input-group">
                <i class="fa-solid fa-envelope"></i>
                <x-text-input id="email" class="block w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="email" placeholder="contoh@email.com" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-xs text-red-400" />
        </div>

        <!-- Password -->
        <div>
            <div class="flex justify-between items-center mb-2">
                <x-input-label for="password" :value="__('Kata Sandi')" class="block text-xs font-semibold uppercase tracking-wider text-slate-300" />
                @if (Route::has('password.request'))
                    <a class="text-xs text-slate-400 hover:text-yellow-300 transition-colors" href="{{ route('password.request') }}">
                        {{ __('Lupa password?') }}
                    </a>
                @endif
            </div>
            <div class="input-group">
                <i class="fa-solid fa-lock"></i>
                <x-text-input id="password" class="block w-full"
                                type="password"
                                name="password"
                                required autocomplete="current-password" placeholder="••••••••" />
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-xs text-red-400" />
        </div>

        <!-- Submit Button -->
        <div class="pt-2">
            <x-primary-button>
                {{ __('Masuk Sekarang') }} <i class="fa-solid fa-arrow-right-to-bracket ms-2"></i>
            </x-primary-button>
        </div>

        <!-- Register Link -->
        <div class="text-center pt-2 border-t border-white/5">
            <p class="text-xs text-slate-400">
                Belum punya akun? <a href="{{ route('register') }}" class="text-yellow-400 font-bold hover:text-yellow-300 transition-colors">Daftar Sekarang &rarr;</a>
            </p>
        </div>
    </form>
</x-guest-layout>
