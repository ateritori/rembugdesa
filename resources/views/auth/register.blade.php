<x-guest-layout>
    <div class="relative min-h-[90vh] flex flex-col justify-center animate-in fade-in zoom-in duration-1000 px-4 py-8">

        {{-- Background Ambience --}}
        <div class="absolute -top-20 left-1/4 w-64 h-64 bg-[#7f1d1d]/5 rounded-full blur-[100px] -z-10"></div>

        {{-- Branding Section --}}
        <div class="mb-8 md:mb-12 text-center relative">
            <div class="flex justify-center mb-4">
                <div class="h-1.5 w-10 bg-[#7f1d1d] rounded-full shadow-[0_4px_12px_rgba(127,29,29,0.3)]"></div>
            </div>
            <h1 class="text-4xl sm:text-5xl font-[900] tracking-tighter text-[#2d1a12] leading-none">
                Gabung<span class="text-[#7f1d1d] italic">Sistem</span>
            </h1>
            <p class="text-[10px] md:text-[11px] font-black text-[#78716c] uppercase tracking-[0.5em] mt-4">
                Pendaftaran Akses Baru
            </p>
        </div>

        {{-- Main Register Card --}}
        <div class="w-full max-w-[500px] mx-auto">
            <div
                class="bg-white rounded-[2rem] md:rounded-[3rem] p-8 sm:p-10 md:p-14 shadow-[0_30px_70px_-15px_rgba(45,26,18,0.12)] border border-[#f5f5f4] relative overflow-hidden">

                <div
                    class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-[#7f1d1d]/40 to-transparent">
                </div>

                <form method="POST" action="{{ route('register') }}" class="space-y-5 md:space-y-6 relative z-10">
                    @csrf

                    {{-- Name --}}
                    <div class="space-y-2 group">
                        <label for="name"
                            class="text-[10px] md:text-[11px] font-black uppercase tracking-widest text-[#2d1a12] ml-1 group-focus-within:text-[#7f1d1d] transition-colors duration-300">
                            Nama Lengkap
                        </label>
                        <input id="name" type="text" name="name" :value="old('name')" required autofocus
                            class="w-full px-5 py-4 bg-[#fafaf9] border-2 border-[#f5f5f4] rounded-xl text-sm font-bold text-[#2d1a12] placeholder:text-[#d6d3d1] focus:bg-white focus:border-[#7f1d1d] transition-all duration-300 outline-none"
                            placeholder="Contoh: Budi Santoso">
                        <x-input-error :messages="$errors->get('name')"
                            class="text-[10px] font-bold text-[#b91c1c] italic mt-1 px-1" />
                    </div>

                    {{-- Email --}}
                    <div class="space-y-2 group">
                        <label for="email"
                            class="text-[10px] md:text-[11px] font-black uppercase tracking-widest text-[#2d1a12] ml-1 group-focus-within:text-[#7f1d1d] transition-colors duration-300">
                            Identitas Email
                        </label>
                        <input id="email" type="email" name="email" :value="old('email')" required
                            class="w-full px-5 py-4 bg-[#fafaf9] border-2 border-[#f5f5f4] rounded-xl text-sm font-bold text-[#2d1a12] placeholder:text-[#d6d3d1] focus:bg-white focus:border-[#7f1d1d] transition-all duration-300 outline-none"
                            placeholder="name@rembugdesa.id">
                        <x-input-error :messages="$errors->get('email')"
                            class="text-[10px] font-bold text-[#b91c1c] italic mt-1 px-1" />
                    </div>

                    {{-- Password Grid --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="space-y-2 group">
                            <label for="password"
                                class="text-[10px] font-black uppercase tracking-widest text-[#2d1a12] ml-1 group-focus-within:text-[#7f1d1d] transition-colors duration-300">
                                Kunci Baru
                            </label>
                            <input id="password" type="password" name="password" required
                                class="w-full px-5 py-4 bg-[#fafaf9] border-2 border-[#f5f5f4] rounded-xl text-sm font-bold text-[#2d1a12] placeholder:text-slate-300 focus:bg-white focus:border-[#7f1d1d] transition-all duration-300 outline-none"
                                placeholder="••••••••">
                        </div>
                        <div class="space-y-2 group">
                            <label for="password_confirmation"
                                class="text-[10px] font-black uppercase tracking-widest text-[#2d1a12] ml-1 group-focus-within:text-[#7f1d1d] transition-colors duration-300">
                                Konfirmasi
                            </label>
                            <input id="password_confirmation" type="password" name="password_confirmation" required
                                class="w-full px-5 py-4 bg-[#fafaf9] border-2 border-[#f5f5f4] rounded-xl text-sm font-bold text-[#2d1a12] placeholder:text-slate-300 focus:bg-white focus:border-[#7f1d1d] transition-all duration-300 outline-none"
                                placeholder="••••••••">
                        </div>
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="text-[10px] font-bold text-[#b91c1c] italic px-1" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="text-[10px] font-bold text-[#b91c1c] italic px-1" />

                    {{-- Actions --}}
                    <div class="flex flex-col sm:flex-row items-center justify-between pt-4 gap-4">
                        <a class="text-[10px] font-black uppercase text-[#a8a29e] hover:text-[#7f1d1d] transition-colors duration-300 tracking-wider order-2 sm:order-1"
                            href="{{ route('login') }}">
                            Sudah Terdaftar?
                        </a>

                        <button type="submit"
                            class="w-full sm:w-auto px-10 bg-[#2d1a12] text-white py-5 rounded-xl font-black text-[11px] uppercase tracking-[0.4em] transition-all duration-500 ease-in-out hover:bg-[#7f1d1d] hover:-translate-y-1 hover:shadow-xl hover:shadow-[#7f1d1d]/30 active:scale-[0.98] order-1 sm:order-2">
                            Buat Akun
                        </button>
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
