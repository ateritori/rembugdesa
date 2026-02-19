<x-guest-layout>
  {{-- CSS Khusus untuk menangani Autofill Browser agar teks tidak putih/hilang --}}
  <style>
    input:-webkit-autofill,
    input:-webkit-autofill:hover,
    input:-webkit-autofill:focus {
      -webkit-text-fill-color: #2d1a12 !important;
      -webkit-box-shadow: 0 0 0px 1000px #fafaf9 inset !important;
      transition: background-color 5000s ease-in-out 0s;
    }
  </style>

  <div
    class="animate-in fade-in zoom-in relative flex min-h-[90vh] flex-col justify-center px-4 py-8 duration-1000 selection:bg-[#7f1d1d] selection:text-white">

    {{-- Background Ambience --}}
    <div class="absolute -top-20 left-1/4 -z-10 h-64 w-64 rounded-full bg-[#4c0519]/5 blur-[100px]"></div>
    <div class="absolute bottom-0 right-1/4 -z-10 h-64 w-64 rounded-full bg-[#7f1d1d]/5 blur-[100px]"></div>

    {{-- Branding Section --}}
    <div class="relative mb-8 text-center md:mb-12">
      <div class="mb-4 flex justify-center">
        <div class="h-1.5 w-10 rounded-full bg-[#7f1d1d] shadow-[0_0_20px_rgba(127,29,29,0.3)]"></div>
      </div>

      <h1 class="text-4xl font-[900] leading-none tracking-tighter text-[#2d1a12] sm:text-5xl md:text-6xl">
        Rembug<span class="italic text-[#7f1d1d]">Desa</span>
      </h1>
      <div class="mt-4 flex items-center justify-center gap-3">
        <p class="text-center text-[10px] font-black uppercase tracking-[0.5em] text-[#78716c] md:text-[11px]">
          Group Decision Support System
        </p>
      </div>
    </div>

    {{-- Main Login Card --}}
    <div class="mx-auto w-full max-w-[440px]">
      <div
        class="relative overflow-hidden rounded-[2rem] border border-[#f5f5f4] bg-white p-8 shadow-[0_30px_70px_-15px_rgba(45,26,18,0.12)] sm:p-10 md:rounded-[3rem] md:p-14">

        <div class="absolute left-0 top-0 h-1 w-full bg-gradient-to-r from-transparent via-[#7f1d1d]/40 to-transparent">
        </div>

        <x-auth-session-status class="mb-6 text-center text-[10px] font-black uppercase tracking-widest text-[#7f1d1d]"
          :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="relative z-10 space-y-6 md:space-y-7">
          @csrf

          {{-- Input Email --}}
          <div class="group space-y-2">
            <label for="email"
              class="ml-1 text-[10px] font-black uppercase tracking-widest text-[#2d1a12] transition-colors duration-300 group-focus-within:text-[#7f1d1d] md:text-[11px]">
              Identitas Akses
            </label>
            <input id="email" type="email" name="email" :value="old('email')" required autofocus
              class="w-full rounded-xl border-2 border-[#f5f5f4] bg-[#fafaf9] px-5 py-4 text-sm font-bold text-[#2d1a12] outline-none transition-all duration-300 placeholder:text-[#d6d3d1] focus:border-[#7f1d1d] focus:bg-white focus:text-[#2d1a12]"
              placeholder="admin@rembugdesa.id">
            <x-input-error :messages="$errors->get('email')" class="mt-1 px-1 text-[10px] font-bold italic text-[#b91c1c]" />
          </div>

          {{-- Input Password --}}
          <div class="group space-y-2">
            <label for="password"
              class="ml-1 text-[10px] font-black uppercase tracking-widest text-[#2d1a12] transition-colors duration-300 group-focus-within:text-[#7f1d1d] md:text-[11px]">
              Kunci Keamanan
            </label>
            <input id="password" type="password" name="password" required
              class="w-full rounded-xl border-2 border-[#f5f5f4] bg-[#fafaf9] px-5 py-4 text-sm font-bold text-[#2d1a12] outline-none transition-all duration-300 placeholder:text-[#d6d3d1] focus:border-[#7f1d1d] focus:bg-white focus:text-[#2d1a12]"
              placeholder="••••••••">
            <x-input-error :messages="$errors->get('password')" class="mt-1 px-1 text-[10px] font-bold italic text-[#b91c1c]" />
          </div>

          {{-- Options --}}
          <div class="flex flex-row items-center justify-between gap-2 px-1">
            <label class="group/label flex cursor-pointer items-center">
              <input id="remember_me" type="checkbox" name="remember"
                class="h-4 w-4 cursor-pointer rounded border-[#d6d3d1] text-[#7f1d1d] transition-all focus:ring-0">
              <span
                class="ms-2 text-[10px] font-black uppercase text-[#a8a29e] transition-colors duration-300 group-hover/label:text-[#7f1d1d] md:text-[11px]">Ingat
                Sesi</span>
            </label>
            @if (Route::has('password.request'))
              <a class="text-[10px] font-black uppercase text-[#7f1d1d]/80 transition-colors duration-300 hover:text-[#7f1d1d]"
                href="{{ route('password.request') }}">Lupa?</a>
            @endif
          </div>

          {{-- Submit Button --}}
          <div class="pt-2">
            <button type="submit"
              class="w-full rounded-xl bg-[#2d1a12] py-5 text-[11px] font-black uppercase tracking-[0.4em] text-white transition-all duration-500 ease-in-out hover:-translate-y-1 hover:bg-[#7f1d1d] hover:shadow-xl hover:shadow-[#7f1d1d]/30 active:scale-[0.98]">
              Masuk Ke Sistem
            </button>
          </div>

          {{-- Register Navigation --}}
          <div class="pt-4 text-center">
            <p class="text-[10px] font-black uppercase tracking-widest text-[#d6d3d1]">
              Belum memiliki akses?
              <a href="{{ route('register') }}"
                class="ml-1 text-[#7f1d1d] underline decoration-[#7f1d1d]/30 underline-offset-4 transition-colors duration-300 hover:text-[#2d1a12]">
                Daftar Sekarang
              </a>
            </p>
          </div>
        </form>
      </div>
    </div>

    {{-- Footer --}}
    <div class="mt-12 text-center text-[#d6d3d1]">
      <p class="text-[9px] font-black uppercase tracking-[0.4em] md:text-[10px]">Rembug Desa • 2026</p>
    </div>
  </div>
</x-guest-layout>
