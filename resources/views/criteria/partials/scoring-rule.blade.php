@if ($decisionSession->status === 'draft')
  @php
    $isEdit = $rule !== null;
    $oldSemantics = $rule?->getParameter('scale_semantics') ?? [];
    $oldUtilities = $rule?->getParameter('scale_utilities') ?? [];
    $range = $rule?->getParameter('scale_range') ?? ['min' => 1, 'max' => 5];
  @endphp

  <div x-data="scoringRule({
      isEdit: {{ $isEdit ? 'true' : 'false' }},
      inputType: '{{ $rule->input_type ?? '' }}',
      preferenceType: '{{ $rule->preference_type ?? 'linear' }}',
      min: {{ $range['min'] }},
      max: {{ $range['max'] }},
      semantics: @js($oldSemantics),
      utilities: @js($oldUtilities)
  })" class="w-full">

    <form x-show="open" x-transition method="POST"
      action="{{ $isEdit ? route('criteria.scoring.update', [$c->id, $rule->id]) : route('criteria.scoring.store', $c->id) }}"
      class="space-y-6 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm md:p-8">

      @csrf
      @if ($isEdit)
        @method('PUT')
      @endif

      {{-- HEADER --}}
      <div class="flex flex-col justify-between gap-3 border-b border-slate-100 pb-5 sm:flex-row sm:items-center">
        <div>
          <h4 class="text-xs font-black uppercase tracking-[0.2em] text-slate-400">
            Aturan Penilaian
          </h4>
          <p class="text-lg font-bold tracking-tight text-slate-800">{{ $c->name }}</p>
        </div>
        @if ($isEdit)
          <div class="flex">
            <span
              class="inline-flex items-center gap-1.5 rounded-full border border-indigo-100 bg-indigo-50 px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-indigo-600">
              <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-indigo-500"></span>
              Mode Update
            </span>
          </div>
        @endif
      </div>

      {{-- CONFIG GRID --}}
      <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <div class="space-y-2">
          <label class="ml-1 text-[10px] font-black uppercase tracking-widest text-slate-500">Mekanisme Input</label>
          <select x-model="inputType" name="input_type"
            class="w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3.5 font-bold text-slate-700 transition-all focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10">
            <option value="">Pilih Mekanisme</option>
            <option value="scale">Skala (Pilihan/Likert)</option>
            <option value="numeric">Input Angka Langsung</option>
          </select>
        </div>

        <div class="space-y-2">
          <label class="ml-1 text-[10px] font-black uppercase tracking-widest text-slate-500">Fungsi Utilitas</label>
          <select x-model="preferenceType" name="preference_type"
            class="w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3.5 font-bold text-slate-700 transition-all focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10">
            <option value="linear">Linear (Stabil)</option>
            <option value="concave">Concave (Menurun)</option>
            <option value="convex">Convex (Meningkat)</option>
          </select>
        </div>
      </div>

      {{-- RANGE CONFIG (Tampil hanya jika tipe Skala) --}}
      <div x-show="inputType === 'scale'" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform -translate-y-2"
        class="grid grid-cols-1 gap-4 rounded-2xl border border-dashed border-slate-300 bg-slate-50/50 p-5 sm:grid-cols-2">
        <div class="space-y-1.5">
          <label class="ml-1 text-xs font-bold text-slate-600">Nilai Minimum</label>
          <input type="number" x-model.number="min" name="scale_min"
            class="w-full rounded-lg border-slate-200 px-4 py-2.5 font-bold transition-all focus:ring-2 focus:ring-indigo-500">
        </div>
        <div class="space-y-1.5">
          <label class="ml-1 text-xs font-bold text-slate-600">Nilai Maksimum</label>
          <input type="number" x-model.number="max" name="scale_max"
            class="w-full rounded-lg border-slate-200 px-4 py-2.5 font-bold transition-all focus:ring-2 focus:ring-indigo-500">
        </div>
      </div>

      {{-- DYNAMIC UTILITIES LIST --}}
      <div class="space-y-4" x-show="inputType === 'scale'" x-transition>
        <div class="ml-1 flex items-center gap-2">
          <label class="text-[10px] font-black uppercase tracking-widest text-slate-500">Definisi Skala
            Penilaian</label>
          <div class="h-px flex-1 bg-slate-100"></div>
        </div>

        <div class="grid grid-cols-1 gap-3">
          <template x-for="i in range()" :key="i">
            <div
              class="group flex flex-col items-stretch gap-4 rounded-xl border border-slate-200 p-3 shadow-sm transition-all hover:border-indigo-200 hover:bg-indigo-50/30 sm:flex-row sm:items-center">

              {{-- Indikator Angka --}}
              <div
                class="flex h-10 w-10 flex-none items-center justify-center rounded-lg bg-slate-800 text-sm font-black text-white shadow-sm transition-colors group-hover:bg-indigo-600"
                x-text="i"></div>

              {{-- Label Semantik --}}
              <div class="min-w-0 flex-1">
                <input type="text" :name="`semantics[${i}]`" x-model="semantics[i]"
                  placeholder="Contoh: Sangat Baik, Murah, Tinggi..."
                  class="w-full border-none bg-transparent py-1 text-sm font-bold text-slate-800 placeholder:font-medium placeholder:text-slate-300 focus:ring-0 sm:text-base">
              </div>

              {{-- Nilai Utility --}}
              <div
                class="flex items-center gap-3 rounded-lg border border-slate-100 bg-white px-3 py-1.5 sm:border-transparent">
                <span class="text-[10px] font-bold uppercase tracking-tighter text-slate-400">Utility:</span>
                <input type="number" step="0.01" min="0" max="1" :name="`utilities[${i}]`"
                  :value="utilityValue(i)" @input="utilities[i] = $event.target.value"
                  class="w-20 rounded-md border-slate-200 bg-slate-50 py-1 text-center text-xs font-black text-indigo-600 focus:border-indigo-500 focus:ring-0">
              </div>

            </div>
          </template>
        </div>
      </div>

      {{-- ACTION BUTTONS --}}
      <div class="flex flex-col items-center justify-end gap-3 border-t border-slate-100 pt-8 sm:flex-row">
        <p class="mb-2 text-[10px] font-medium italic text-slate-400 sm:mb-0 sm:mr-auto">Pastikan rentang nilai dan
          utilitas sudah sesuai sebelum menyimpan.</p>

        <button type="submit"
          class="w-full rounded-xl bg-indigo-600 px-10 py-4 text-xs font-black uppercase tracking-widest text-white shadow-lg shadow-indigo-200 transition-all hover:-translate-y-0.5 hover:bg-indigo-700 active:scale-95 sm:w-auto">
          {{ $isEdit ? 'Simpan Perubahan' : 'Tetapkan Aturan' }}
        </button>
      </div>

    </form>
  </div>
@endif
