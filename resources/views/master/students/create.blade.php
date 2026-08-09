<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-slate-800 tracking-tight">
                {{ __('Tambah Murid Baru') }}
            </h2>
            <div class="flex items-center gap-4">
                <a href="{{ route('master.students.index') }}" class="text-sm text-slate-500 hover:text-slate-800 transition-colors font-medium flex items-center gap-1.5">
                    <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar
                </a>
                <span class="info-badge"><i class="fa-solid fa-crown mr-1"></i> Hak Akses: {{ ucfirst(auth()->user()->role) }}</span>
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

        /* Target inputs, textarea, select inside group */
        .input-group input, .input-group select, .input-group textarea {
            background-color: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            color: #334155 !important;
            border-radius: 12px !important;
            padding-left: 3rem !important;
            padding-right: 1.25rem !important;
            font-size: 0.95rem !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
            width: 100% !important;
        }
        
        .input-group input, .input-group select {
            height: 3.5rem !important;
        }

        .input-group textarea {
            padding-top: 1rem !important;
        }

        .input-group input:focus, .input-group select:focus, .input-group textarea:focus {
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
                    <h3 class="text-lg font-bold text-slate-800"><i class="fa-solid fa-user-plus mr-2 text-blue-500"></i> Formulir Profil Murid</h3>
                    <p class="text-sm text-slate-500 mt-1">Lengkapi data pribadi siswa beserta kontak orang tua dan tautkan dengan akun login murid yang sesuai.</p>
                </div>

                <form method="POST" action="{{ route('master.students.store') }}" class="space-y-6">
                    @csrf

                    <!-- Student Name -->
                    <div>
                        <label for="name" class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Siswa</label>
                        <div class="input-group">
                            <i class="fa-solid fa-user-graduate"></i>
                            <x-text-input id="name" class="block w-full" type="text" name="name" :value="old('name')" required autofocus placeholder="Nama Lengkap Siswa" />
                        </div>
                        <x-input-error :messages="$errors->get('name')" class="mt-2 text-xs text-red-500" />
                    </div>

                    <!-- Jenis Kelamin -->
                    <div>
                        <label for="gender" class="block text-sm font-semibold text-slate-700 mb-1.5">Jenis Kelamin</label>
                        <div class="input-group">
                            <i class="fa-solid fa-venus-mars"></i>
                            <select id="gender" name="gender" class="block w-full" required>
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="Laki-laki" {{ old('gender') === 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="Perempuan" {{ old('gender') === 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>
                        <x-input-error :messages="$errors->get('gender')" class="mt-2 text-xs text-red-500" />
                    </div>

                    <!-- Tempat, Tanggal Lahir -->
                    <div>
                        <label for="birth_place_date" class="block text-sm font-semibold text-slate-700 mb-1.5">Tempat, Tanggal Lahir</label>
                        <div class="input-group">
                            <i class="fa-solid fa-calendar-day"></i>
                            <x-text-input id="birth_place_date" class="block w-full" type="text" name="birth_place_date" :value="old('birth_place_date')" required placeholder="Contoh: Bandung, 12 Agustus 2010" />
                        </div>
                        <x-input-error :messages="$errors->get('birth_place_date')" class="mt-2 text-xs text-red-500" />
                    </div>

                    <!-- Usia -->
                    <div>
                        <label for="age" class="block text-sm font-semibold text-slate-700 mb-1.5">Usia</label>
                        <div class="input-group">
                            <i class="fa-solid fa-child"></i>
                            <x-text-input id="age" class="block w-full" type="number" min="1" name="age" :value="old('age')" required placeholder="Contoh: 10" />
                        </div>
                        <x-input-error :messages="$errors->get('age')" class="mt-2 text-xs text-red-500" />
                    </div>

                    <!-- Sekolah -->
                    <div>
                        <label for="school" class="block text-sm font-semibold text-slate-700 mb-1.5">Sekolah</label>
                        <div class="input-group">
                            <i class="fa-solid fa-school"></i>
                            <x-text-input id="school" class="block w-full" type="text" name="school" :value="old('school')" required placeholder="Nama Sekolah (Contoh: SDN 1 Bandung)" />
                        </div>
                        <x-input-error :messages="$errors->get('school')" class="mt-2 text-xs text-red-500" />
                    </div>

                    <!-- Plotting Kelas -->
                    <div class="pt-4 border-t border-slate-100 mt-6">
                        <h4 class="text-md font-bold text-slate-800 mb-4"><i class="fa-solid fa-water text-blue-500 mr-2"></i> Plotting Kelas Berenang</h4>
                        
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Pilih Kelas <span class="text-slate-400 font-normal text-xs">(Bisa lebih dari satu)</span></label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 p-4 bg-slate-50 rounded-xl border border-slate-200">
                            @forelse($swimClasses as $swimClass)
                                <label class="flex items-start gap-3 p-2 rounded-lg hover:bg-slate-100 cursor-pointer transition-colors">
                                    <input type="checkbox" name="swim_class_ids[]" value="{{ $swimClass->id }}" {{ (is_array(old('swim_class_ids')) && in_array($swimClass->id, old('swim_class_ids'))) ? 'checked' : '' }} class="mt-0.5 rounded border-slate-300 text-blue-600 shadow-sm focus:ring-blue-500 w-5 h-5">
                                    <div>
                                        <span class="text-sm font-bold text-slate-700 block">{{ $swimClass->name }}</span>
                                        <span class="text-[10px] text-slate-500 block"><i class="fa-regular fa-clock mr-1"></i> {{ $swimClass->schedule ?? 'Belum ada jadwal' }}</span>
                                    </div>
                                </label>
                            @empty
                                <div class="col-span-2 text-sm text-slate-500 italic p-2 text-center">Belum ada kelas aktif yang tersedia.</div>
                            @endforelse
                        </div>
                        <x-input-error class="mt-2 text-red-500 text-xs" :messages="$errors->get('swim_class_ids')" />
                    </div>

                    <!-- Account Creation (Optional) -->
                    <div class="pt-4 border-t border-slate-100 mt-6">
                        <h4 class="text-md font-bold text-slate-800 mb-4"><i class="fa-solid fa-key text-blue-500 mr-2"></i> Akun Login Murid (Opsional)</h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-semibold text-slate-700 mb-1.5">Email Akses Login</label>
                                <div class="input-group">
                                    <i class="fa-solid fa-envelope"></i>
                                    <x-text-input id="email" class="block w-full" type="email" name="email" :value="old('email')" placeholder="nama@email.com" />
                                </div>
                                <p class="text-xs text-slate-400 mt-1.5">Kosongkan jika belum ingin membuatkan akun login.</p>
                                <x-input-error :messages="$errors->get('email')" class="mt-2 text-xs text-red-500" />
                            </div>

                            <!-- Password Default -->
                            <div>
                                <label for="password" class="block text-sm font-semibold text-slate-700 mb-1.5">Password Default</label>
                                <div class="input-group">
                                    <i class="fa-solid fa-lock"></i>
                                    <x-text-input id="password" class="block w-full bg-slate-50 text-slate-500 cursor-not-allowed" type="text" name="password" value="123456" readonly />
                                </div>
                                <p class="text-xs text-slate-400 mt-1.5">Password default adalah 123456. Murid dapat mengubahnya nanti.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Action Trigger Buttons -->
                    <div class="flex items-center justify-end gap-4 pt-6 border-t border-slate-100 mt-8">
                        <a href="{{ route('master.students.index') }}" class="text-slate-500 hover:text-slate-800 font-semibold px-4 py-2 transition-colors">
                            Batal
                        </a>
                        <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-bold text-sm rounded-xl transition-all duration-200 shadow-md shadow-slate-800/20 hover:shadow-lg hover:shadow-slate-800/30 active:scale-95">
                            <i class="fa-solid fa-floppy-disk mr-1"></i> Simpan Murid
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
