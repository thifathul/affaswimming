<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-slate-800 tracking-tight">
            {{ __('Perhitungan Penggajian (Payroll)') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-r-lg text-emerald-700 font-medium text-sm flex items-center">
                    <i class="fa-solid fa-circle-check mr-3 text-lg"></i>
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100 p-6 mb-8">
                <h3 class="text-lg font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">Filter Data Gaji</h3>
                
                <form action="{{ route('finance.payroll.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Tanggal Mulai</label>
                        <input type="date" name="start_date" value="{{ $startDate }}" class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Tanggal Selesai</label>
                        <input type="date" name="end_date" value="{{ $endDate }}" class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Pilih Pelatih</label>
                        <select name="coach_id" class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-sm">
                            <option value="">-- Pilih Pelatih --</option>
                            @foreach($coaches as $coach)
                                <option value="{{ $coach->id }}" {{ $selectedCoachId == $coach->id ? 'selected' : '' }}>
                                    {{ $coach->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <button type="submit" class="w-full px-5 py-2.5 rounded-xl bg-blue-600 text-white font-semibold text-sm hover:bg-blue-700 shadow-sm transition flex items-center justify-center gap-2">
                            <i class="fa-solid fa-calculator"></i> Hitung Gaji
                        </button>
                    </div>
                </form>
            </div>

            @if($selectedCoachId)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100 p-6">
                <div class="flex justify-between items-center mb-6 border-b border-slate-100 pb-4">
                    <div>
                        <h3 class="text-xl font-bold text-slate-800">Rincian Penggajian</h3>
                        <p class="text-sm text-slate-500 mt-1">Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
                    </div>
                    <div class="text-right">
                        @if(isset($coachDebt) && $coachDebt > 0)
                            <div class="mb-3 px-4 py-2 bg-red-50 border border-red-200 rounded-lg text-right inline-block">
                                <p class="text-xs font-bold text-red-600 uppercase tracking-wider mb-1"><i class="fa-solid fa-triangle-exclamation"></i> Total Kasbon/Pinjaman</p>
                                <p class="text-xl font-bold text-red-700">Rp {{ number_format($coachDebt, 0, ',', '.') }}</p>
                            </div>
                        @endif
                        <p class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-1">Total Dibayarkan</p>
                        <div class="flex items-center justify-end gap-4">
                            <p class="text-3xl font-bold text-emerald-600">Rp {{ number_format($totalSalary, 0, ',', '.') }}</p>
                            @if($totalSalary > 0)
                                @php
                                    $selectedCoachName = $coaches->firstWhere('id', $selectedCoachId)?->name;
                                @endphp
                                <button onclick="openPayModal('{{ $selectedCoachName }}', {{ $totalSalary }}, '{{ $startDate }}', '{{ $endDate }}')" class="px-4 py-2 bg-emerald-600 text-white rounded-xl font-bold hover:bg-emerald-700 transition shadow-sm flex items-center gap-2">
                                    <i class="fa-solid fa-money-bill-wave"></i> Bayar Gaji
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                @if(empty($payrollData))
                    <div class="text-center py-10">
                        <i class="fa-regular fa-calendar-xmark text-4xl text-slate-300 mb-3"></i>
                        <p class="text-slate-500 font-medium">Tidak ada data kehadiran latihan untuk pelatih ini pada periode terpilih.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50 border-y border-slate-100">
                                    <th class="py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Lokasi Kolam</th>
                                    <th class="py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Total Pertemuan</th>
                                    <th class="py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Fee per Pertemuan (Rp)</th>
                                    <th class="py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Subtotal (Rp)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($payrollData as $data)
                                    <tr class="hover:bg-slate-50/50 transition-colors">
                                        <td class="py-4 px-4 text-sm font-bold text-slate-700">
                                            {{ $data['pool_name'] }}
                                        </td>
                                        <td class="py-4 px-4 text-sm text-center">
                                            <span class="px-2 py-1 bg-blue-50 text-blue-700 font-bold rounded-lg text-xs">{{ $data['meetings_count'] }}x</span>
                                        </td>
                                        <td class="py-4 px-4 text-sm text-right text-slate-600">
                                            {{ number_format($data['coach_fee'], 0, ',', '.') }}
                                        </td>
                                        <td class="py-4 px-4 text-sm font-bold text-slate-800 text-right">
                                            {{ number_format($data['salary'], 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="bg-slate-50 border-t-2 border-slate-200">
                                    <td class="py-4 px-4 text-sm font-bold text-slate-800 uppercase text-right">Total Keseluruhan</td>
                                    <td class="py-4 px-4 text-sm font-bold text-blue-700 text-center">{{ $totalMeetings }}x</td>
                                    <td class="py-4 px-4 text-sm font-bold text-slate-800 text-right">-</td>
                                    <td class="py-4 px-4 text-base font-bold text-emerald-700 text-right">Rp {{ number_format($totalSalary, 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @endif
            </div>
            @else
            <div class="bg-blue-50/50 border border-blue-100 rounded-xl p-8 text-center">
                <i class="fa-solid fa-hand-holding-dollar text-4xl text-blue-300 mb-4"></i>
                <p class="text-blue-800 font-medium">Silakan filter tanggal dan pilih pelatih untuk menampilkan kalkulasi penggajian.</p>
            </div>
            @endif
        </div>
    </div>
    <!-- Modal Bayar Gaji -->
    @if($selectedCoachId && $totalSalary > 0)
    <div id="payModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-slate-900 bg-opacity-75 transition-opacity backdrop-blur-sm" aria-hidden="true" onclick="closePayModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-100">
                <form action="{{ route('finance.payroll.pay') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-emerald-100 sm:mx-0 sm:h-10 sm:w-10">
                                <i class="fa-solid fa-money-bill-wave text-emerald-600"></i>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-bold text-slate-900" id="modal-title">
                                    Pembayaran Gaji Pelatih
                                </h3>
                                <div class="mt-2 text-sm text-slate-500 mb-4 bg-slate-50 p-3 rounded-lg border border-slate-100">
                                    <p><strong>Pelatih:</strong> <span id="modalCoachName"></span></p>
                                    <p><strong>Periode:</strong> <span id="modalPeriod"></span></p>
                                    <p><strong>Total Gaji Pokok:</strong> Rp <span id="modalTotalSalary"></span></p>
                                    @if(isset($coachDebt) && $coachDebt > 0)
                                        <div class="mt-2 pt-2 border-t border-slate-200">
                                            <p class="font-bold text-emerald-700 text-lg">Total Bersih: Rp <span id="modalNetSalary"></span></p>
                                        </div>
                                    @endif
                                </div>
                                
                                <input type="hidden" name="coach_id" value="{{ $selectedCoachId }}">
                                <input type="hidden" name="start_date" value="{{ $startDate }}">
                                <input type="hidden" name="end_date" value="{{ $endDate }}">
                                <input type="hidden" name="amount" value="{{ $totalSalary }}">
                                
                                @if(isset($coachDebt) && $coachDebt > 0)
                                <div class="mb-4 p-4 rounded-xl border border-red-200 bg-red-50 text-sm">
                                    <h4 class="font-bold text-red-700 mb-1"><i class="fa-solid fa-triangle-exclamation mr-1"></i> Pelatih Memiliki Kasbon!</h4>
                                    <p class="text-red-600 mb-2">Total Hutang/Kasbon: <strong>Rp {{ number_format($coachDebt, 0, ',', '.') }}</strong></p>
                                    
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Nominal Potong Gaji (Rp)</label>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-500 font-bold">Rp</span>
                                        <input type="text" id="deduction_amount" name="deduction_amount" class="w-full bg-white border border-slate-200 rounded-lg pl-10 pr-3 py-2 text-slate-800 focus:border-red-500 focus:ring-red-500/20 format-rupiah" value="{{ number_format(min($totalSalary, $coachDebt), 0, ',', '.') }}" placeholder="0">
                                    </div>
                                    <p class="text-[10px] text-slate-500 mt-1">Kosongkan (isi 0) jika tidak ingin potong gaji bulan ini.</p>
                                </div>
                                @endif

                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-2">Upload Bukti Pembayaran <span class="text-red-500">*</span></label>
                                        <input type="file" name="proof_file" accept="image/*,application/pdf" required class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                                        <p class="text-xs text-slate-500 mt-1">Format: JPG, PNG, atau PDF (Maks 2MB).</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-2">Catatan Tambahan (Opsional)</label>
                                        <textarea name="notes" rows="2" class="w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm text-sm" placeholder="Contoh: Pembayaran melalui BCA"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-slate-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-slate-100">
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-emerald-600 text-base font-medium text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                            Simpan & Bayar
                        </button>
                        <button type="button" onclick="closePayModal()" class="mt-3 w-full inline-flex justify-center rounded-lg border border-slate-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        function openPayModal(coachName, totalSalary, startDate, endDate) {
            document.getElementById('modalCoachName').innerText = coachName;
            document.getElementById('modalPeriod').innerText = startDate + ' s/d ' + endDate;
            document.getElementById('modalTotalSalary').innerText = new Intl.NumberFormat('id-ID').format(totalSalary);
            
            @if(isset($coachDebt) && $coachDebt > 0)
                updateNetSalary();
            @endif

            document.getElementById('payModal').classList.remove('hidden');
        }

        function closePayModal() {
            document.getElementById('payModal').classList.add('hidden');
        }

        @if(isset($coachDebt) && $coachDebt > 0)
        const deductionInput = document.getElementById('deduction_amount');
        const originalSalary = {{ $totalSalary }};
        
        function updateNetSalary() {
            let val = deductionInput.value.replace(/[^,\d]/g, '');
            let deduction = val ? parseInt(val) : 0;
            let net = originalSalary - deduction;
            
            if (net < 0) net = 0; // Prevent negative display just in case
            
            document.getElementById('modalNetSalary').innerText = new Intl.NumberFormat('id-ID').format(net);
        }

        deductionInput.addEventListener('keyup', updateNetSalary);
        @endif
    </script>
    @endif
</x-app-layout>
