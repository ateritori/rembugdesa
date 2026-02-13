<div
  class="{{ $border }} flex flex-col items-center justify-between gap-8 rounded-2xl border-2 bg-white p-8 shadow-lg lg:flex-row lg:p-10">

  {{-- KIRI --}}
  <div class="flex-1 text-center lg:text-left">
    <span
      class="{{ $badgeBg }} {{ $badgeText }} rounded-full px-4 py-1.5 text-[10px] font-black uppercase tracking-widest">
      {{ $phase }}
    </span>

    <h3 class="mt-4 text-2xl font-black uppercase tracking-tight text-slate-800 lg:text-3xl">
      {{ $title }}
    </h3>

    <p class="mt-3 max-w-2xl text-sm font-medium leading-relaxed text-slate-500 lg:text-base">
      {{ $description }}
    </p>

    {{-- Jika ada partial status tambahan (warning/info) --}}
    @if (isset($left_path))
      <div class="mt-4">
        @include($left_path)
      </div>
    @endif
  </div>

  {{-- KANAN --}}
  @if (isset($right_path))
    <div class="flex-shrink-0">
      {{-- DISINI KUNCINYA: OPER canActivate KE TOMBOL --}}
      @include($right_path, ['canActivate' => $canActivate ?? false])
    </div>
  @endif
</div>
