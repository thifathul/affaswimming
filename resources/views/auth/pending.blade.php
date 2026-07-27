<x-guest-layout>
    <div class="text-center py-8">
        <div class="w-24 h-24 bg-yellow-500/10 rounded-full flex items-center justify-center mx-auto mb-6">
            <i class="fa-solid fa-hourglass-half text-4xl text-yellow-500"></i>
        </div>
        
        <h2 class="text-2xl font-bold tracking-widest text-white uppercase mb-4" style="letter-spacing: 1px;">
            Pendaftaran Berhasil!
        </h2>
        
        <div class="bg-slate-900/50 p-6 rounded-2xl border border-white/5 mb-8">
            <p class="text-slate-300 leading-relaxed mb-4">
                Terima kasih telah mendaftar di <strong>AFFA Swimming</strong>.
            </p>
            <p class="text-sm text-slate-400 leading-relaxed">
                Akun Anda saat ini sedang dalam status <span class="text-yellow-400 font-semibold">Menunggu Persetujuan (Pending)</span> oleh Admin. Kami akan melakukan verifikasi data Anda secepatnya.
            </p>
            <p class="text-sm text-slate-400 leading-relaxed mt-4">
                Silakan coba <a href="{{ route('login') }}" class="text-yellow-400 hover:text-yellow-300 font-bold underline">Login</a> kembali secara berkala, atau hubungi admin kami jika membutuhkan bantuan cepat.
            </p>
        </div>

        <a href="{{ url('/') }}" class="inline-block px-8 py-3 bg-slate-800 text-white font-semibold rounded-xl hover:bg-slate-700 transition">
            <i class="fa-solid fa-house mr-2"></i> Kembali ke Beranda
        </a>
    </div>
</x-guest-layout>
