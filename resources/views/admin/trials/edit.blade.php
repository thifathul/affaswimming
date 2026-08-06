<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-slate-800 tracking-tight">
            {{ __('Edit Data Trial') }}
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

                <form action="{{ route('admin.trials.update', $trial->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-6">
                        <!-- Nama Murid -->
                        <div>
                            <label for="name" class="block text-sm font-semibold text-slate-700 mb-1">Nama Murid <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name', $trial->name) }}" required class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500" placeholder="Masukkan nama murid">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Umur -->
                            <div>
                                <label for="age" class="block text-sm font-semibold text-slate-700 mb-1">Umur <span class="text-red-500">*</span></label>
                                <input type="number" name="age" id="age" value="{{ old('age', $trial->age) }}" required min="1" class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <!-- Gender -->
                            <div>
                                <label for="gender" class="block text-sm font-semibold text-slate-700 mb-1">Jenis Kelamin <span class="text-red-500">*</span></label>
                                <select name="gender" id="gender" required class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                                    <option value="L" {{ old('gender', $trial->gender) === 'L' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="P" {{ old('gender', $trial->gender) === 'P' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Sekolah -->
                            <div>
                                <label for="school" class="block text-sm font-semibold text-slate-700 mb-1">Asal Sekolah</label>
                                <input type="text" name="school" id="school" value="{{ old('school', $trial->school) }}" class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <!-- Kontak -->
                            <div>
                                <label for="contact_number" class="block text-sm font-semibold text-slate-700 mb-1">Nomor Kontak (WA)</label>
                                <input type="text" name="contact_number" id="contact_number" value="{{ old('contact_number', $trial->contact_number) }}" class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>

                        <hr class="border-slate-100">

                        <!-- Lokasi Kolam -->
                        <div>
                            <label for="pool_location_id" class="block text-sm font-semibold text-slate-700 mb-1">Lokasi Kolam <span class="text-red-500">*</span></label>
                            <select name="pool_location_id" id="pool_location_id" required class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                                @foreach($poolLocations as $pool)
                                    <option value="{{ $pool->id }}" {{ old('pool_location_id', $trial->pool_location_id) == $pool->id ? 'selected' : '' }}>{{ $pool->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Tanggal Jadwal -->
                            <div>
                                <label for="schedule_date" class="block text-sm font-semibold text-slate-700 mb-1">Tanggal Trial <span class="text-red-500">*</span></label>
                                <input type="date" name="schedule_date" id="schedule_date" value="{{ old('schedule_date', $trial->schedule_date->format('Y-m-d')) }}" required class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <!-- Jam Jadwal -->
                            <div>
                                <label for="schedule_time" class="block text-sm font-semibold text-slate-700 mb-1">Jam Trial <span class="text-red-500">*</span></label>
                                <input type="time" name="schedule_time" id="schedule_time" value="{{ old('schedule_time', \Carbon\Carbon::parse($trial->schedule_time)->format('H:i')) }}" required class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>

                        <!-- Pelatih -->
                        <div>
                            <label for="coach_id" class="block text-sm font-semibold text-slate-700 mb-1">Pelatih <span class="text-red-500">*</span></label>
                            <select name="coach_id" id="coach_id" required class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                                @foreach($coaches as $coach)
                                    <option value="{{ $coach->id }}" {{ old('coach_id', $trial->coach_id) == $coach->id ? 'selected' : '' }}>{{ $coach->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <hr class="border-slate-100">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nominal Pembayaran -->
                            <div>
                                <label for="payment_amount" class="block text-sm font-semibold text-slate-700 mb-1">Nominal Pembayaran (Rp) <span class="text-red-500">*</span></label>
                                <input type="text" name="payment_amount" id="payment_amount" value="{{ old('payment_amount', $trial->transaction ? number_format($trial->transaction->amount, 0, '', '.') : '0') }}" required class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500 format-rupiah" placeholder="Contoh: 150.000">
                                <p class="text-xs text-slate-500 mt-1">Isi 0 jika trial gratis.</p>
                            </div>

                            <!-- Metode Pembayaran -->
                            <div>
                                <label for="payment_method" class="block text-sm font-semibold text-slate-700 mb-1">Metode Pembayaran <span class="text-red-500">*</span></label>
                                @php
                                    $currentMethod = $trial->transaction ? $trial->transaction->payment_method : '';
                                @endphp
                                <select name="payment_method" id="payment_method" required class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                                    <option value="" disabled {{ old('payment_method', $currentMethod) ? '' : 'selected' }}>Pilih Metode</option>
                                    <option value="Tunai/Cash" {{ old('payment_method', $currentMethod) === 'Tunai/Cash' ? 'selected' : '' }}>Tunai / Cash</option>
                                    <option value="Transfer BCA" {{ old('payment_method', $currentMethod) === 'Transfer BCA' ? 'selected' : '' }}>Transfer BCA</option>
                                    <option value="Transfer Mandiri" {{ old('payment_method', $currentMethod) === 'Transfer Mandiri' ? 'selected' : '' }}>Transfer Mandiri</option>
                                    <option value="E-Wallet (OVO/Dana/GoPay)" {{ old('payment_method', $currentMethod) === 'E-Wallet (OVO/Dana/GoPay)' ? 'selected' : '' }}>E-Wallet (OVO/Dana/GoPay)</option>
                                    <option value="Gratis/Free Trial" {{ old('payment_method', $currentMethod) === 'Gratis/Free Trial' ? 'selected' : '' }}>Gratis / Free Trial</option>
                                </select>
                            </div>
                        </div>

                        <div class="pt-4 flex justify-end gap-3">
                            <a href="{{ route('admin.trials.index') }}" class="px-5 py-2.5 bg-slate-100 text-slate-700 rounded-xl font-medium hover:bg-slate-200 transition">Batal</a>
                            <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white rounded-xl font-semibold hover:bg-blue-700 transition shadow-sm">
                                <i class="fa-solid fa-save mr-2"></i> Perbarui
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const rupiahInputs = document.querySelectorAll('.format-rupiah');
            
            rupiahInputs.forEach(function(input) {
                input.addEventListener('keyup', function(e) {
                    this.value = formatRupiah(this.value);
                });
            });

            function formatRupiah(angka, prefix) {
                let number_string = angka.replace(/[^,\d]/g, '').toString(),
                    split = number_string.split(','),
                    sisa = split[0].length % 3,
                    rupiah = split[0].substr(0, sisa),
                    ribuan = split[0].substr(sisa).match(/\d{3}/gi);

                if (ribuan) {
                    let separator = sisa ? '.' : '';
                    rupiah += separator + ribuan.join('.');
                }

                rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
                return prefix == undefined ? rupiah : (rupiah ? 'Rp ' + rupiah : '');
            }
        });
    </script>
</x-app-layout>
