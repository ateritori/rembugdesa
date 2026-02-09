<x-guest-layout>
    <div class="relative min-h-[90vh] flex flex-col justify-center animate-in fade-in zoom-in duration-1000 px-4 py-8">

        {{-- Background Ambience --}}
        <div class="absolute -top-20 left-1/4 w-64 h-64 bg-[#4c0519]/5 rounded-full blur-[100px] -z-10"></div>
        <div class="absolute bottom-0 right-1/4 w-64 h-64 bg-[#7f1d1d]/5 rounded-full blur-[100px] -z-10"></div>

        {{-- Branding Section --}}
        <div class="mb-8 md:mb-12 text-center relative">
            <div class="flex justify-center mb-4">
                <div class="h-1.5 w-10 bg-[#7f1d1d] rounded-full shadow-[0_0_20px_rgba(127,29,29,0.3)]"></div>
            </div>

            <h1 class="text-4xl sm:text-5xl md:text-6xl font-[900] tracking-tighter text-[#2d1a12] leading-none">
                Rembug<span class="text-[#7f1d1d] italic">Desa</span>
            </h1>
            <div class="flex items-center justify-center gap-3 mt-4">
                <p class="text-[10px] md:text-[11px] font-black text-[#78716c] uppercase tracking-[0.5em] text-center">
                    Decision Support System
                </p>
            </div>
        </div>

        {{-- Main Login Card --}}
        <div class="w-full max-w-[440px] mx-auto">
            <div
                class="bg-white rounded-[2rem] md:rounded-[3rem] p-8 sm:p-10 md:p-14 shadow-[0_30px_70px_-15px_rgba(45,26,18,0.12)] border border-[#f5f5f4] relative overflow-hidden">

                <div
                    class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-[#7f1d1d]/40 to-transparent">
                </div>

                <x-auth-session-status
                    class="mb-6 text-center text-[10px] font-black uppercase tracking-widest text-[#7f1d1d]"
                    :status="session('status')" />

                <form method="POST" action="{{ route('login') }}" class="space-y-6 md:space-y-7 relative z-10">
                    @csrf

                    {{-- Input Email --}}
                    <div class="space-y-2 group">
                        <label for="email"
                            class="text-[10px] md:text-[11px] font-black uppercase tracking-widest text-[#2d1a12] ml-1 group-focus-within:text-[#7f1d1d] transition-colors duration-300">
                            Identitas Akses
                        </label>
                        <input id="email" type="email" name="email" :value="old('email')" required autofocus
                            class="w-full px-5 py-4 bg-[#fafaf9] border-2 border-[#f5f5f4] rounded-xl text-sm font-bold text-[#2d1a12] placeholder:text-[#d6d3d1] focus:bg-white focus:border-[#7f1d1d] transition-all duration-300 outline-none"
                            placeholder="admin@rembugdesa.id">
                        <x-input-error :messages="$errors->get('email')"
                            class="text-[10px] font-bold text-[#b91c1c] italic mt-1 px-1" />
                    </div>

                    {{-- Input Password --}}
                    <div class="space-y-2 group">
                        <label for="password"
                            class="text-[10px] md:text-[11px] font-black uppercase tracking-widest text-[#2d1a12] ml-1 group-focus-within:text-[#7f1d1d] transition-colors duration-300">
                            Kunci Keamanan
                        </label>
                        <input id="password" type="password" name="password" required
                            class="w-full px-5 py-4 bg-[#fafaf9] border-2 border-[#f5f5f4] rounded-xl text-sm font-bold text-[#2d1a12] placeholder:text-[#d6d3d1] focus:bg-white focus:border-[#7f1d1d] transition-all duration-300 outline-none"
                            placeholder="••••••••">
                        <x-input-error :messages="$errors->get('password')"
                            class="text-[10px] font-bold text-[#b91c1c] italic mt-1 px-1" />
                    </div>

                    {{-- Options --}}
                    <div class="flex flex-row items-center justify-between px-1 gap-2">
                        <label class="flex items-center cursor-pointer group/label">
                            <input id="remember_me" type="checkbox" name="remember"
                                class="w-4 h-4 rounded border-[#d6d3d1] text-[#7f1d1d] focus:ring-0 transition-all cursor-pointer">
                            <span
                                class="ms-2 text-[10px] md:text-[11px] font-black uppercase text-[#a8a29e] group-hover/label:text-[#7f1d1d] transition-colors duration-300">Ingat
                                Sesi</span>
                        </label>
                        @if (Route::has('password.request'))
                            <a class="text-[10px] font-black uppercase text-[#7f1d1d]/80 hover:text-[#7f1d1d] transition-colors duration-300"
                                href="{{ route('password.request') }}">Lupa?</a>
                        @endif
                    </div>

                    {{-- Submit Button --}}
                    <div class="pt-2">
                        <button type="submit"
                            class="w-full bg-[#2d1a12] text-white py-5 rounded-xl font-black text-[11px] uppercase tracking-[0.4em] transition-all duration-500 ease-in-out hover:bg-[#7f1d1d] hover:-translate-y-1 hover:shadow-xl hover:shadow-[#7f1d1d]/30 active:scale-[0.98]">
                            Masuk Ke Sistem
                        </button>
                    </div>

                    {{-- Register Navigation: Baru & Clean --}}
                    <div class="pt-4 text-center">
                        <p class="text-[10px] font-black text-[#d6d3d1] uppercase tracking-widest">
                            Belum memiliki akses?
                            <a href="{{ route('register') }}"
                                class="ml-1 text-[#7f1d1d] hover:text-[#2d1a12] transition-colors duration-300 underline underline-offset-4 decoration-[#7f1d1d]/30">
                                Daftar Sekarang
                            </a>
                        </p>
                    </div>
                </form>
            </div>
        </div>

        {{-- Footer --}}
        <div class="mt-12 text-center text-[#d6d3d1]">
            <p class="text-[9px] md:text-[10px] font-black uppercase tracking-[0.4em]">AHP Digital Core • 2026</p>
        </div>
    </div>
</x-guest-layout>
