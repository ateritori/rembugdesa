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
    <div class="absolute -top-20 left-1/4 -z-10 h-64 w-64 rounded-full bg-[#7f1d1d]/5 blur-[100px]"></div>

    {{-- Branding Section --}}
    <div class="relative mb-8 text-center md:mb-12">
      <div class="mb-4 flex justify-center">
        <div class="h-1.5 w-10 rounded-full bg-[#7f1d1d] shadow-[0_4px_12px_rgba(127,29,29,0.3)]"></div>
      </div>
      <h1 class="text-4xl font-[900] leading-none tracking-tighter text-[#2d1a12] sm:text-5xl">
        Gabung<span class="italic text-[#7f1d1d]">Sistem</span>
      </h1>
      <p class="mt-4 text-[10px] font-black uppercase tracking-[0.5em] text-[#78716c] md:text-[11px]">
        Pendaftaran Akses Baru
      </p>
    </div>

    {{-- Main Register Card --}}
    <div class="mx-auto w-full max-w-[500px]">
      <div
        class="relative overflow-hidden rounded-[2rem] border border-[#f5f5f4] bg-white p-8 shadow-[0_30px_70px_-15px_rgba(45,26,18,0.12)] sm:p-10 md:rounded-[3rem] md:p-14">

        <div class="absolute left-0 top-0 h-1 w-full bg-gradient-to-r from-transparent via-[#7f1d1d]/40 to-transparent">
        </div>

        <form method="POST" action="{{ route('register') }}" class="relative z-10 space-y-5 md:space-y-6">
          @csrf

          {{-- Name --}}
          <div class="group space-y-2">
            <label for="name"
              class="ml-1 text-[10px] font-black uppercase tracking-widest text-[#2d1a12] transition-colors duration-300 group-focus-within:text-[#7f1d1d] md:text-[11px]">
              Nama Lengkap
            </label>
            <input id="name" type="text" name="name" :value="old('name')" required autofocus
              class="w-full rounded-xl border-2 border-[#f5f5f4] bg-[#fafaf9] px-5 py-4 text-sm font-bold text-[#2d1a12] outline-none transition-all duration-300 placeholder:text-[#d6d3d1] focus:border-[#7f1d1d] focus:bg-white focus:text-[#2d1a12]"
              placeholder="Contoh: Budi Santoso">
            <x-input-error :messages="$errors->get('name')" class="mt-1 px-1 text-[10px] font-bold italic text-[#b91c1c]" />
          </div>

          {{-- Email --}}
          <div class="group space-y-2">
            <label for="email"
              class="ml-1 text-[10px] font-black uppercase tracking-widest text-[#2d1a12] transition-colors duration-300 group-focus-within:text-[#7f1d1d] md:text-[11px]">
              Identitas Email
            </label>
            <input id="email" type="email" name="email" :value="old('email')" required
              class="w-full rounded-xl border-2 border-[#f5f5f4] bg-[#fafaf9] px-5 py-4 text-sm font-bold text-[#2d1a12] outline-none transition-all duration-300 placeholder:text-[#d6d3d1] focus:border-[#7f1d1d] focus:bg-white focus:text-[#2d1a12]"
              placeholder="name@rembugdesa.id">
            <x-input-error :messages="$errors->get('email')" class="mt-1 px-1 text-[10px] font-bold italic text-[#b91c1c]" />
          </div>

          {{-- Password Grid --}}
          <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
            <div class="group space-y-2">
              <label for="password"
                class="ml-1 text-[10px] font-black uppercase tracking-widest text-[#2d1a12] transition-colors duration-300 group-focus-within:text-[#7f1d1d]">
                Kunci Baru
              </label>
              <input id="password" type="password" name="password" required
                class="w-full rounded-xl border-2 border-[#f5f5f4] bg-[#fafaf9] px-5 py-4 text-sm font-bold text-[#2d1a12] outline-none transition-all duration-300 placeholder:text-[#d6d3d1] focus:border-[#7f1d1d] focus:bg-white focus:text-[#2d1a12]"
                placeholder="••••••••">
            </div>
            <div class="group space-y-2">
              <label for="password_confirmation"
                class="ml-1 text-[10px] font-black uppercase tracking-widest text-[#2d1a12] transition-colors duration-300 group-focus-within:text-[#7f1d1d]">
                Konfirmasi
              </label>
              <input id="password_confirmation" type="password" name="password_confirmation" required
                class="w-full rounded-xl border-2 border-[#f5f5f4] bg-[#fafaf9] px-5 py-4 text-sm font-bold text-[#2d1a12] outline-none transition-all duration-300 placeholder:text-[#d6d3d1] focus:border-[#7f1d1d] focus:bg-white focus:text-[#2d1a12]"
                placeholder="••••••••">
            </div>
          </div>
          <x-input-error :messages="$errors->get('password')" class="px-1 text-[10px] font-bold italic text-[#b91c1c]" />
          <x-input-error :messages="$errors->get('password_confirmation')" class="px-1 text-[10px] font-bold italic text-[#b91c1c]" />

          {{-- Actions --}}
          <div class="flex flex-col items-center justify-between gap-4 pt-4 sm:flex-row">
            <a class="order-2 text-[10px] font-black uppercase tracking-wider text-[#a8a29e] transition-colors duration-300 hover:text-[#7f1d1d] sm:order-1"
              href="{{ route('login') }}">
              Sudah Terdaftar?
            </a>

            <button type="submit"
              class="order-1 w-full rounded-xl bg-[#2d1a12] px-10 py-5 text-[11px] font-black uppercase tracking-[0.4em] text-white transition-all duration-500 ease-in-out hover:-translate-y-1 hover:bg-[#7f1d1d] hover:shadow-xl hover:shadow-[#7f1d1d]/30 active:scale-[0.98] sm:order-2 sm:w-auto">
              Buat Akun
            </button>
          </div>
        </form>
      </div>
    </div>

    {{-- Footer --}}
    <div class="mt-12 text-center text-[#d6d3d1]">
      <p class="text-[9px] font-black uppercase tracking-[0.4em] md:text-[10px]">AHP Digital Core • 2026</p>
    </div>
  </div>
</x-guest-layout>
