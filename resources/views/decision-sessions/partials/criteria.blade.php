    <div class="bg-card p-6 rounded shadow">

        {{-- Form tambah --}}
        <form method="POST" action="{{ route('criteria.store', $decisionSession->id) }}"
            class="flex flex-col md:flex-row gap-3 mb-6 {{ $decisionSession->status !== 'draft' ? 'opacity-50 pointer-events-none' : '' }}">
            @csrf

            <input type="text" name="name" placeholder="Nama kriteria"
                class="flex-1 border border-app rounded px-3 py-2 bg-transparent" required>

            <select name="type" class="border border-app rounded px-3 py-2 bg-transparent" required>
                <option value="">Jenis</option>
                <option value="benefit">Benefit</option>
                <option value="cost">Cost</option>
            </select>

            <button type="submit" class="px-4 py-2 rounded bg-primary text-white text-sm"
                {{ $decisionSession->status !== 'draft' ? 'disabled' : '' }}>
                Tambah
            </button>
        </form>

        {{-- List --}}
        <div class="space-y-2">
            @forelse ($criteria as $c)
                <div x-data="{ open: false }">
                    <div class="flex justify-between items-center border border-app rounded px-4 py-2">

                        <div
                            class="flex items-center gap-3 {{ $decisionSession->status !== 'draft' ? 'opacity-50 pointer-events-none' : '' }}">
                            <span class="font-medium">
                                {{ $c->order }}. {{ $c->name }}
                            </span>

                            <span class="text-xs px-2 py-0.5 rounded border border-app">
                                {{ strtoupper($c->type) }}
                            </span>

                            @if (!$c->is_active)
                                <span class="text-xs px-2 py-0.5 rounded bg-gray-200 text-gray-700">
                                    NOT USED
                                </span>
                            @endif
                        </div>

                        <div
                            class="flex items-center gap-3 {{ $decisionSession->status !== 'draft' ? 'opacity-50 pointer-events-none' : '' }}">

                            <button type="button" {{ $decisionSession->status !== 'draft' ? 'disabled' : '' }}
                                @click="open = !open" class="text-app hover:text-primary" title="Edit">
                                ✏️
                            </button>

                            {{-- Toggle --}}
                            <form method="POST" action="{{ route('criteria.toggle', $c->id) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="text-app hover:text-primary">
                                    —
                                </button>
                            </form>

                            {{-- Delete --}}
                            <form method="POST" action="{{ route('criteria.destroy', $c->id) }}"
                                onsubmit="return confirm('Hapus kriteria ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800">
                                    ×
                                </button>
                            </form>

                        </div>
                    </div>
                    <form x-show="open" x-transition method="POST" action="{{ route('criteria.update', $c->id) }}"
                        class="mt-2 flex flex-col md:flex-row gap-2 border border-app rounded px-4 py-3"
                        @click.outside="open = false">
                        @csrf
                        @method('PUT')

                        <input type="text" name="name" value="{{ $c->name }}"
                            class="flex-1 border border-app rounded px-2 py-1" required>

                        <select name="type" class="border border-app rounded px-2 py-1" required>
                            <option value="benefit" @selected($c->type === 'benefit')>Benefit</option>
                            <option value="cost" @selected($c->type === 'cost')>Cost</option>
                        </select>

                        <button type="submit" class="px-4 py-1 rounded bg-primary text-white text-sm">
                            Simpan
                        </button>
                    </form>
                </div>
            @empty
                <p class="text-sm opacity-70">
                    Belum ada kriteria.
                </p>
            @endforelse
        </div>

    </div>
