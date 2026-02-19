<x-guest-layout>
  <div
    class="animate-in fade-in zoom-in relative flex min-h-[90vh] flex-col justify-center px-4 py-8 duration-1000 selection:bg-[#7f1d1d] selection:text-white">

    {{-- Background Ambience --}}
    <div class="absolute -top-20 left-1/4 -z-10 h-64 w-64 rounded-full bg-[#7f1d1d]/5 blur-[100px]"></div>

    {{-- Branding Section --}}
    <div class="relative mb-8 text-center md:mb-12">
      <div class="mb-4 flex justify-center">
        <div class="h-1.5 w-10 rounded-full bg-[#7f1d1d] shadow-[0_4px_12px_rgba(127,29,29,0.3)]"></div>
      </div>
      <h1 class="text-4xl font-[900] leading-none tracking-tighter text-[#2d1a12] sm:text-5xl">
        Cek<span class="italic text-[#7f1d1d]">Email</span>
      </h1>
      <p class="mt-4 text-[10px] font-black uppercase tracking-[0.5em] text-[#78716c] md:text-[11px]">
        Verifikasi Identitas Anda
      </p>
    </div>

    {{-- Main Card --}}
    <div class="mx-auto w-full max-w-[480px]">
      <div
        class="relative overflow-hidden rounded-[2rem] border border-[#f5f5f4] bg-white p-8 text-center shadow-[0_30px_70px_-15px_rgba(45,26,18,0.12)] sm:p-10 md:rounded-[3rem] md:p-14">

        <div class="absolute left-0 top-0 h-1 w-full bg-gradient-to-r from-transparent via-[#7f1d1d]/40 to-transparent">
        </div>

        {{-- Instructions --}}
        <div class="mb-8 text-[11px] font-bold uppercase leading-relaxed tracking-widest text-[#78716c]">
          {{ __('Terima kasih telah mendaftar! Sebelum memulai, mohon verifikasi email Anda dengan mengklik tautan yang baru saja kami kirimkan. Jika tidak menerima email, kami akan mengirimkan ulang.') }}
        </div>

        {{-- Success Status --}}
        @if (session('status') == 'verification-link-sent')
          <div
            class="mb-8 rounded-xl border border-emerald-100 bg-emerald-50 py-3 text-[10px] font-black uppercase tracking-widest text-emerald-600">
            {{ __('Tautan verifikasi baru telah dikirimkan ke alamat email Anda.') }}
          </div>
        @endif

        <div class="relative z-10 space-y-4">
          <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit"
              class="w-full rounded-xl bg-[#2d1a12] py-5 text-[11px] font-black uppercase tracking-[0.4em] text-white transition-all duration-500 ease-in-out hover:-translate-y-1 hover:bg-[#7f1d1d] hover:shadow-xl hover:shadow-[#7f1d1d]/30 active:scale-[0.98]">
              Kirim Ulang Email
            </button>
          </form>

          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
              class="text-[10px] font-black uppercase tracking-[0.2em] text-[#a8a29e] transition-colors duration-300 hover:text-[#7f1d1d]">
              {{ __('Keluar / Log Out') }}
            </button>
          </form>
        </div>
      </div>
    </div>

    {{-- Footer --}}
    <div class="mt-12 text-center text-[#d6d3d1]">
      <p class="text-[9px] font-black uppercase tracking-[0.4em] md:text-[10px]">AHP Digital Core • 2026</p>
    </div>
  </div>
</x-guest-layout>
