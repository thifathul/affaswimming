<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kuitansi Pembayaran - {{ $transaction->student->name }}</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
        }
        @media print {
            @page {
                size: A4;
                margin: 20mm;
            }
            body { 
                background-color: white; 
                padding: 0;
            }
            .no-print { display: none; }
            .print-border { border: 1px solid #e2e8f0; }
            .print-shadow-none { box-shadow: none !important; }
        }
    </style>
</head>
<body class="p-4 sm:p-8">

    <div class="max-w-3xl mx-auto">
        <!-- Controls -->
        <div class="mb-8 flex justify-between items-center no-print">
            <button onclick="window.close()" class="text-slate-500 hover:text-slate-800 font-medium text-sm">
                &larr; Tutup Jendela
            </button>
            <div class="flex gap-3">
                <button onclick="exportToImage()" class="px-5 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-semibold hover:bg-blue-700 shadow-sm transition">
                    <i class="fa-solid fa-image mr-2"></i> Simpan Gambar
                </button>
                <button onclick="window.print()" class="px-5 py-2.5 bg-slate-800 text-white rounded-xl text-sm font-semibold hover:bg-slate-700 shadow-sm transition">
                    <i class="fa-solid fa-print mr-2"></i> Print PDF
                </button>
            </div>
        </div>

        <!-- Receipt Card -->
        <div id="receiptCard" class="bg-white p-8 sm:p-12 rounded-3xl shadow-sm border border-slate-100 print-border print-shadow-none">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center border-b border-slate-100 pb-8 mb-8 gap-4 sm:gap-0">
                <div class="flex items-center gap-5">
                    <img src="{{ asset('affa_logo.jpg') }}" alt="AFFA Logo" class="h-16 w-16 sm:h-20 sm:w-20 object-cover rounded-2xl shadow-sm border border-slate-100">
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-800 tracking-tight">AFFA Swimming</h1>
                        <p class="text-sm font-medium text-slate-500 mt-1">Kuitansi Pembayaran Resmi</p>
                    </div>
                </div>
                <div class="text-left sm:text-right">
                    <p class="text-sm font-bold text-slate-800 bg-slate-50 px-3 py-1.5 rounded-lg inline-block border border-slate-100">No. TRX-{{ str_pad($transaction->id, 5, '0', STR_PAD_LEFT) }}</p>
                    <p class="text-xs text-slate-500 mt-2 font-medium">{{ \Carbon\Carbon::parse($transaction->updated_at)->format('d F Y, H:i') }} WIB</p>
                </div>
            </div>

            <div class="mb-8 grid grid-cols-3 gap-8">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Diterima Dari</p>
                    <p class="font-bold text-slate-800">{{ $transaction->student->name }}</p>
                    <p class="text-sm text-slate-600 mt-1">{{ $transaction->student->school ?? 'Sekolah Tidak Diketahui' }} (Usia: {{ $transaction->student->age ?? '-' }} Tahun)</p>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Paket & Lokasi</p>
                    <p class="font-bold text-slate-800">{{ $transaction->poolLocation->package_name ?? '-' }}</p>
                    <p class="text-sm text-slate-600 mt-1">Lokasi: {{ $transaction->poolLocation->name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Metode Bayar</p>
                    <p class="font-bold text-slate-800">{{ $transaction->payment_method ?? '-' }}</p>
                </div>
            </div>

            <table class="w-full mb-8">
                <thead>
                    <tr class="border-b-2 border-slate-800">
                        <th class="py-3 text-left text-sm font-bold text-slate-800">Keterangan</th>
                        <th class="py-3 text-right text-sm font-bold text-slate-800">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b border-slate-100">
                        <td class="py-4">
                            <p class="font-semibold text-slate-800">Paket {{ $transaction->package_type }} Pertemuan</p>
                            <p class="text-sm text-slate-500 mt-1">Masa aktif: {{ \Carbon\Carbon::parse($transaction->practice_start_date)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($transaction->practice_start_date)->addMonths(2)->format('d M Y') }}</p>
                        </td>
                        <td class="py-4 text-right font-bold text-slate-800">
                            Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                        </td>
                    </tr>
                </tbody>
            </table>

            <div class="flex justify-between items-end mt-8">
                <div class="text-center">
                    <p class="text-xs text-slate-500 mb-2">Penerima</p>
                    <img src="{{ asset('signature.png') }}" alt="Ttd" class="h-16 mx-auto mb-2 object-contain" onerror="this.style.display='none'">
                    <p class="font-bold text-slate-800 border-t border-slate-300 pt-2 px-6 inline-block">Fatimah Nurhafidah<br> AFFA SWIMMING</p>
                </div>
                <div class="text-center">
                    <p class="text-xs text-slate-500 mb-12">Penyetor</p>
                    <p class="font-bold text-slate-800 border-t border-slate-300 pt-2 px-6 inline-block">{{ $transaction->student->name }}</p>
                </div>
            </div>

            <div class="mt-12 pt-6 border-t border-slate-100 text-center">
                <span class="inline-block px-3 py-1 bg-emerald-100 text-emerald-700 text-xs font-bold rounded-full border border-emerald-200">
                    LUNAS
                </span>
                <p class="text-xs text-slate-400 mt-3">Dokumen ini adalah bukti pembayaran yang sah. Harap simpan dengan baik.</p>
            </div>
        </div>
    </div>

    <!-- html2canvas library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script>
        function exportToImage() {
            const receiptCard = document.getElementById('receiptCard');
            const originalBorder = receiptCard.style.border;
            const originalShadow = receiptCard.style.boxShadow;
            
            // Remove border/shadow temporarily for clean image
            receiptCard.style.border = 'none';
            receiptCard.style.boxShadow = 'none';
            
            html2canvas(receiptCard, {
                scale: 2, // 2x resolution
                backgroundColor: '#ffffff',
                useCORS: true // in case of external images
            }).then(canvas => {
                // Restore styles
                receiptCard.style.border = originalBorder;
                receiptCard.style.boxShadow = originalShadow;
                
                // Trigger download
                const link = document.createElement('a');
                link.download = 'Kuitansi_AFFA_{{ str_pad($transaction->id, 5, "0", STR_PAD_LEFT) }}.png';
                link.href = canvas.toDataURL('image/png');
                link.click();
            });
        }
    </script>
</body>
</html>
