{{-- ================= ALTERNATIF ================= --}}

<div class="bg-card p-6 rounded shadow">

    {{-- Header --}}
    <div class="mb-6">
        <h2 class="text-sm font-semibold">Alternatif</h2>
        <p class="text-xs opacity-70">
            Daftar alternatif yang akan dinilai pada sesi ini
        </p>
    </div>

    {{-- Form tambah alternatif --}}
    <form method="POST" action="{{ route('alternatives.store', $decisionSession->id) }}"
        class="flex flex-col md:flex-row gap-3 mb-6
                 {{ $decisionSession->status !== 'draft' ? 'opacity-50 pointer-events-none' : '' }}">
        @csrf

        <input type="text" name="name" placeholder="Nama alternatif"
            class="flex-1 border border-app rounded px-3 py-2 bg-transparent" required>

        <button type="submit" class="px-4 py-2 rounded bg-primary text-white text-sm"
            {{ $decisionSession->status !== 'draft' ? 'disabled' : '' }}>
            Tambah
        </button>
    </form>

    {{-- List alternatif --}}
    <div class="space-y-2">
        @forelse ($alternatives as $a)
            <div x-data="{ open: false }">

                <div class="flex justify-between items-center border border-app rounded px-4 py-2">

                    <div class="flex items-center gap-3">
                        <span class="font-mono text-sm opacity-70">
                            {{ $a->code }}
                        </span>

                        <span class="font-medium">
                            {{ $a->name }}
                        </span>

                        @if (!$a->is_active)
                            <span class="text-xs px-2 py-0.5 rounded bg-gray-200 text-gray-700">
                                NOT USED
                            </span>
                        @endif
                    </div>

                    {{-- Actions --}}
                    <div
                        class="flex items-center gap-3
                        {{ $decisionSession->status !== 'draft' ? 'opacity-50 pointer-events-none' : '' }}">

                        {{-- Edit --}}
                        <button type="button" @click="open = !open" class="text-app hover:text-primary" title="Edit">
                            ✏️
                        </button>

                        {{-- Toggle active --}}
                        <form method="POST" action="{{ route('alternatives.toggle', $a->id) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="text-app hover:text-primary" title="Aktif / Nonaktif">
                                ⛔
                            </button>
                        </form>

                        {{-- Delete --}}
                        <form method="POST" action="{{ route('alternatives.destroy', $a->id) }}"
                            onsubmit="return confirm('Hapus alternatif ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800" title="Hapus">
                                🗑️
                            </button>
                        </form>

                    </div>
                </div>

                {{-- Inline edit --}}
                <form x-show="open" x-transition @click.outside="open = false" method="POST"
                    action="{{ route('alternatives.update', $a->id) }}"
                    class="mt-2 flex flex-col md:flex-row gap-2 border border-app rounded px-4 py-3
                           {{ $decisionSession->status !== 'draft' ? 'hidden' : '' }}">
                    @csrf
                    @method('PUT')

                    <input type="text" name="name" value="{{ $a->name }}"
                        class="flex-1 border border-app rounded px-3 py-2 bg-transparent" required>

                    <button type="submit" class="px-4 py-2 rounded bg-primary text-white text-sm">
                        Simpan
                    </button>
                </form>

            </div>
        @empty
            <p class="text-sm opacity-70">
                Belum ada alternatif.
            </p>
        @endforelse
    </div>

</div>
