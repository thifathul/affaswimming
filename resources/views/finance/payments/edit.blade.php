<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('finance.payments.index') }}" class="text-slate-400 hover:text-slate-600 transition">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <h2 class="font-bold text-2xl text-slate-800 tracking-tight">
                {{ __('Edit Pembelian Paket') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12 bg-slate-50/50 min-h-screen">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100 p-8">
                
                @if(session('error'))
                <div class="mb-6 bg-red-50 text-red-700 p-4 rounded-xl border border-red-100 flex items-start gap-3">
                    <i class="fa-solid fa-circle-exclamation mt-0.5"></i>
                    <p class="text-sm font-medium">{{ session('error') }}</p>
                </div>
                @endif

                <form action="{{ route('finance.payments.update', $transaction->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Pilih Murid -->
                    <div class="mb-5">
                        <label class="block text-sm font-bold text-slate-700 mb-2">Pilih Anak (Murid) <span class="text-red-500">*</span></label>
                        <select name="student_id" required class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-sm">
                            <option value="">-- Pilih Anak --</option>
                            @foreach($students as $student)
                                <option value="{{ $student->id }}" {{ old('student_id', $transaction->student_id) == $student->id ? 'selected' : '' }}>{{ $student->name }}</option>
                            @endforeach
                        </select>
                        @error('student_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Kolam / Lokasi -->
                    <div class="mb-5">
                        <label class="block text-sm font-bold text-slate-700 mb-2">Lokasi dan Paket <span class="text-red-500">*</span></label>
                        <select name="pool_location_id" required class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-sm">
                            <option value="">-- Pilih Lokasi dan Paket --</option>
                            @foreach($poolLocations as $pool)
                                <option value="{{ $pool->id }}" {{ old('pool_location_id', $transaction->pool_location_id) == $pool->id ? 'selected' : '' }}>{{ $pool->name }} - {{ $pool->package_name }}</option>
                            @endforeach
                        </select>
                        @error('pool_location_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Tipe Kelas -->
                    <div class="mb-5">
                        <label class="block text-sm font-bold text-slate-700 mb-2">Tipe Kelas <span class="text-red-500">*</span></label>
                        <select name="class_type" required class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-sm">
                            <option value="">-- Tipe Kelas --</option>
                            <option value="private" {{ old('class_type', $transaction->class_type) == 'private' ? 'selected' : '' }}>Private</option>
                            <option value="semi_private" {{ old('class_type', $transaction->class_type) == 'semi_private' ? 'selected' : '' }}>Semi Private</option>
                        </select>
                        @error('class_type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Harga Paket -->
                    <div class="mb-5">
                        <label class="block text-sm font-bold text-slate-700 mb-2">Harga Paket (Rp) <span class="text-red-500">*</span></label>
                        <input type="text" name="amount" id="amount" value="{{ old('amount', number_format($transaction->amount, 0, ',', '.')) }}" required placeholder="Contoh: 350.000" class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-sm">
                        <p class="text-xs text-slate-500 mt-1">Masukkan total harga paket secara utuh.</p>
                        @error('amount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Kredit / Piutang -->
                    <div class="mb-5">
                        <label class="block text-sm font-bold text-slate-700 mb-2">Kredit / Kurang Bayar (Rp) (Opsional)</label>
                        <input type="text" name="credit" id="credit" value="{{ old('credit', $transaction->credit ? number_format($transaction->credit, 0, ',', '.') : '') }}" placeholder="Contoh: 150.000" class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-sm">
                        <p class="text-xs text-slate-500 mt-1">Isi jika murid belum membayar lunas. Kosongkan jika sudah lunas.</p>
                        @error('credit') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Tanggal Mulai -->
                    <div class="mb-5">
                        <label class="block text-sm font-bold text-slate-700 mb-2">Tanggal Mulai Latihan <span class="text-red-500">*</span></label>
                        <input type="date" name="practice_start_date" value="{{ old('practice_start_date', $transaction->practice_start_date) }}" required class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-sm">
                        <p class="text-xs text-slate-500 mt-1">Paket akan aktif selama 1 bulan terhitung sejak tanggal ini.</p>
                        @error('practice_start_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Bukti Transfer -->
                    <div class="mb-5">
                        <label class="block text-sm font-bold text-slate-700 mb-2">Upload Bukti Transfer Baru (Opsional)</label>
                        @if($transaction->proof_of_payment)
                            <div class="mb-3">
                                <span class="text-xs text-slate-500 block mb-1">Bukti Transfer Saat Ini:</span>
                                <a href="{{ Storage::url($transaction->proof_of_payment) }}" target="_blank" class="text-blue-600 hover:underline text-sm flex items-center gap-1">
                                    <i class="fa-solid fa-image"></i> Lihat Bukti Transfer
                                </a>
                            </div>
                        @endif
                        <input type="file" name="proof_of_payment" accept="image/*" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        <p class="text-xs text-slate-500 mt-1">Biarkan kosong jika tidak ingin mengubah bukti transfer.</p>
                        @error('proof_of_payment') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Keterangan -->
                    <div class="mb-8">
                        <label class="block text-sm font-bold text-slate-700 mb-2">Keterangan (Opsional)</label>
                        <textarea name="notes" rows="2" placeholder="Contoh: Pembayaran melalui perwakilan, atau catatan lainnya..." class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-sm">{{ old('notes', $transaction->notes) }}</textarea>
                        @error('notes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                        <a href="{{ route('finance.payments.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-semibold text-sm hover:bg-slate-50 transition">
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

    <script>
        function formatRupiah(input) {
            let value = input.value.replace(/\D/g, '');
            if (value !== '') {
                input.value = parseInt(value, 10).toLocaleString('id-ID');
            } else {
                input.value = '';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const amountInput = document.getElementById('amount');
            if (amountInput) {
                amountInput.addEventListener('input', function() { formatRupiah(this); });
            }
            
            const creditInput = document.getElementById('credit');
            if (creditInput) {
                creditInput.addEventListener('input', function() { formatRupiah(this); });
            }
        });
    </script>
</x-app-layout>
