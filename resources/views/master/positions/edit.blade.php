<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-slate-800 tracking-tight">
                {{ __('Perbarui Kategori Jabatan') }}
            </h2>
            <div class="flex items-center gap-4">
                <a href="{{ route('master.positions.index') }}" class="text-sm text-slate-500 hover:text-slate-800 transition-colors font-medium flex items-center gap-1.5">
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
                    <h3 class="text-lg font-bold text-slate-800"><i class="fa-solid fa-briefcase mr-2 text-blue-500"></i> Edit Jabatan</h3>
                    <p class="text-sm text-slate-500 mt-1">Ubah nama jabatan, rentang gaji, serta status dari jabatan terpilih.</p>
                </div>

                <form method="POST" action="{{ route('master.positions.update', $position->id) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Position Name -->
                    <div>
                        <label for="name" class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Jabatan</label>
                        <div class="input-group">
                            <i class="fa-solid fa-id-badge"></i>
                            <x-text-input id="name" class="block w-full" type="text" name="name" :value="old('name', $position->name)" required autofocus placeholder="Contoh: Pelatih Kepala" />
                        </div>
                        <x-input-error :messages="$errors->get('name')" class="mt-2 text-xs text-red-500" />
                    </div>

                    <!-- Base Salary -->
                    <div>
                        <label for="base_salary" class="block text-sm font-semibold text-slate-700 mb-1.5">Gaji Pokok / Honor (Rp)</label>
                        <div class="input-group">
                            <i class="fa-solid fa-money-bill-wave"></i>
                            <x-text-input id="base_salary" class="block w-full" type="number" name="base_salary" :value="old('base_salary', intval($position->base_salary))" placeholder="Contoh: 3000000" min="0" />
                        </div>
                        <p class="text-xs text-slate-400 mt-1.5">Masukkan angka saja tanpa titik/koma (Opsional).</p>
                        <x-input-error :messages="$errors->get('base_salary')" class="mt-2 text-xs text-red-500" />
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-semibold text-slate-700 mb-1.5">Deskripsi / Tugas Jabatan</label>
                        <div class="input-group">
                            <i class="fa-solid fa-align-left" style="top: 1.5rem;"></i>
                            <textarea id="description" name="description" rows="3" placeholder="Jelaskan peran dan tanggung jawab untuk jabatan ini (Opsional)...">{{ old('description', $position->description) }}</textarea>
                        </div>
                        <x-input-error :messages="$errors->get('description')" class="mt-2 text-xs text-red-500" />
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-semibold text-slate-700 mb-1.5">Status Pengaktifan</label>
                        <div class="input-group">
                            <i class="fa-solid fa-toggle-on"></i>
                            <select id="status" name="status">
                                <option value="aktif" {{ old('status', $position->status) === 'aktif' ? 'selected' : '' }}>Aktif (Dapat Digunakan)</option>
                                <option value="nonaktif" {{ old('status', $position->status) === 'nonaktif' ? 'selected' : '' }}>Nonaktif (Tidak Dapat Digunakan)</option>
                            </select>
                        </div>
                        <x-input-error :messages="$errors->get('status')" class="mt-2 text-xs text-red-500" />
                    </div>

                    <!-- Action Trigger Buttons -->
                    <div class="flex items-center justify-end gap-4 pt-6 border-t border-slate-100 mt-8">
                        <a href="{{ route('master.positions.index') }}" class="text-slate-500 hover:text-slate-800 font-semibold px-4 py-2 transition-colors">
                            Batal
                        </a>
                        <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-bold text-sm rounded-xl transition-all duration-200 shadow-md shadow-slate-800/20 hover:shadow-lg hover:shadow-slate-800/30 active:scale-95">
                            <i class="fa-solid fa-rotate mr-1"></i> Perbarui Jabatan
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
