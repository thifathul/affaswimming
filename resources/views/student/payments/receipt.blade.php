<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kuitansi Pembayaran - {{ $transaction->student?->name ?? $transaction->manual_student_name ?? 'Murid' }}</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
        <div class="mb-8 flex flex-col sm:flex-row justify-between items-stretch sm:items-center gap-3 no-print">
            <button onclick="window.close()" class="text-slate-500 hover:text-slate-800 font-medium text-sm py-2">
                &larr; Tutup Jendela
            </button>
            <div class="flex flex-wrap items-center gap-2">
                <button type="button" onclick="copyReceiptLink()" id="btnCopyLink" class="px-4 py-2.5 bg-emerald-600 text-white rounded-xl text-sm font-semibold hover:bg-emerald-700 shadow-sm transition flex items-center justify-center gap-2">
                    <i class="fa-regular fa-copy"></i>
                    <span id="btnCopyText">Salin Link</span>
                </button>
                <button type="button" onclick="shareToWhatsApp()" class="px-4 py-2.5 bg-green-500 text-white rounded-xl text-sm font-semibold hover:bg-green-600 shadow-sm transition flex items-center justify-center gap-2">
                    <i class="fa-brands fa-whatsapp text-base"></i>
                    <span>Kirim ke WA</span>
                </button>
                <button onclick="exportToImage()" class="px-4 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-semibold hover:bg-blue-700 shadow-sm transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-image"></i>
                    <span>Simpan Gambar</span>
                </button>
                <button onclick="window.print()" class="px-4 py-2.5 bg-slate-800 text-white rounded-xl text-sm font-semibold hover:bg-slate-700 shadow-sm transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-print"></i>
                    <span>Print PDF</span>
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
                    <p class="font-bold text-slate-800">{{ $transaction->student?->name ?? $transaction->manual_student_name ?? '-' }}</p>
                    <p class="text-sm text-slate-600 mt-1">{{ $transaction->student?->school ?? 'Sekolah Tidak Diketahui' }} (Usia: {{ $transaction->student?->age ?? '-' }} Tahun)</p>
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
                        <td class="py-4 pr-4">
                            <p class="font-semibold text-slate-800 text-base">Paket {{ $transaction->package_type }} Pertemuan @if($transaction->class_type) ({{ ucfirst($transaction->class_type) }}) @endif</p>
                            @if($transaction->practice_start_date)
                                <p class="text-sm text-slate-500 mt-1">Masa aktif: {{ \Carbon\Carbon::parse($transaction->practice_start_date)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($transaction->practice_start_date)->addMonths(2)->format('d M Y') }}</p>
                            @endif
                            @if(!empty($transaction->notes))
                                <div class="mt-3 p-3.5 bg-slate-50 rounded-xl border border-slate-200/70 text-xs text-slate-700">
                                    <span class="font-bold text-slate-800 block mb-1">Deskripsi Keterangan:</span>
                                    <p class="whitespace-pre-line leading-relaxed">{{ $transaction->notes }}</p>
                                </div>
                            @endif
                        </td>
                        <td class="py-4 text-right align-top font-bold text-slate-800 text-base whitespace-nowrap">
                            Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                        </td>
                    </tr>
                    @if($transaction->credit > 0)
                        <tr class="border-b border-slate-100">
                            <td class="py-3 text-sm text-slate-500 font-medium">Sisa Tagihan (Belum Lunas)</td>
                            <td class="py-3 text-right text-sm font-bold text-rose-600">
                                Rp {{ number_format($transaction->credit, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endif
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
                    <p class="font-bold text-slate-800 border-t border-slate-300 pt-2 px-6 inline-block">{{ $transaction->student?->name ?? $transaction->manual_student_name ?? '-' }}</p>
                </div>
            </div>

            <div class="mt-12 pt-6 border-t border-slate-100 text-center">
                @if($transaction->credit > 0)
                    <span class="inline-block px-3 py-1 bg-amber-100 text-amber-800 text-xs font-bold rounded-full border border-amber-200">
                        BELUM LUNAS (Sisa: Rp {{ number_format($transaction->credit, 0, ',', '.') }})
                    </span>
                @else
                    <span class="inline-block px-3 py-1 bg-emerald-100 text-emerald-700 text-xs font-bold rounded-full border border-emerald-200">
                        LUNAS
                    </span>
                @endif
                <p class="text-xs text-slate-400 mt-3">Dokumen ini adalah bukti pembayaran yang sah. Harap simpan dengan baik.</p>
            </div>
        </div>
    </div>

    <!-- html2canvas library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script>
        const publicReceiptUrl = '{{ route('receipt.public', $transaction->id) }}';

        function copyReceiptLink() {
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(publicReceiptUrl).then(() => {
                    handleCopiedState();
                }).catch(() => {
                    fallbackCopy(publicReceiptUrl);
                });
            } else {
                fallbackCopy(publicReceiptUrl);
            }
        }

        function fallbackCopy(text) {
            const tempInput = document.createElement('textarea');
            tempInput.value = text;
            tempInput.style.position = 'fixed';
            tempInput.style.left = '-9999px';
            document.body.appendChild(tempInput);
            tempInput.focus();
            tempInput.select();
            try {
                document.execCommand('copy');
                handleCopiedState();
            } catch (err) {
                prompt('Salin link kuitansi di bawah ini:', text);
            }
            document.body.removeChild(tempInput);
        }

        function handleCopiedState() {
            const btnText = document.getElementById('btnCopyText');
            const originalText = btnText.textContent;
            btnText.textContent = 'Link Tersalin!';
            setTimeout(() => {
                btnText.textContent = originalText;
            }, 2500);
        }

        function shareToWhatsApp() {
            const studentName = '{{ addslashes($transaction->student?->name ?? $transaction->manual_student_name ?? "Siswa") }}';
            const packageName = '{{ addslashes($transaction->poolLocation->package_name ?? "Paket Renang") }}';
            const amount = 'Rp {{ number_format($transaction->amount, 0, ",", ".") }}';
            const method = '{{ addslashes($transaction->payment_method ?? "Transfer") }}';
            const status = '{{ $transaction->credit > 0 ? "BELUM LUNAS" : "LUNAS" }}';
            
            const message = `Halo Kak ${studentName},\n\nBerikut kuitansi pembayaran resmi AFFA Swimming Academy:\n` +
                `• No. TRX: TRX-{{ str_pad($transaction->id, 5, "0", STR_PAD_LEFT) }}\n` +
                `• Paket: ${packageName}\n` +
                `• Total: ${amount}\n` +
                `• Metode: ${method}\n` +
                `• Status: ${status}\n\n` +
                `Buka / Unduh Kuitansi Resmi:\n${publicReceiptUrl}\n\n` +
                `Terima kasih!`;
            
            const waUrl = 'https://api.whatsapp.com/send?text=' + encodeURIComponent(message);
            window.open(waUrl, '_blank');
        }

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
