{{-- Assign Decision Maker --}}
<div class="bg-card p-6 rounded shadow" x-data="{ search: '' }">

    {{-- Header --}}
    <div class="mb-4">
        <h2 class="text-sm font-semibold">Decision Maker</h2>
        <p class="text-xs opacity-70">
            Pilih DM yang terlibat dalam sesi ini
        </p>
    </div>

    <div class="mb-4">
        <input type="text" placeholder="Cari decision maker..." x-model="search"
            class="w-full px-3 py-2 text-sm border border-app rounded bg-transparent">
    </div>

    <form method="POST" action="{{ route('decision-sessions.assign-dm', $decisionSession->id) }}"
        class="{{ $decisionSession->status !== 'draft' ? 'opacity-50 pointer-events-none' : '' }}">
        @csrf

        {{-- List DM --}}
        <div class="space-y-2 max-h-64 overflow-y-auto">
            @foreach ($dms as $index => $dm)
                @php
                    $checked = in_array($dm->id, $assignedDmIds);
                    $code = 'D' . ($index + 1);
                @endphp

                <label x-show="'{{ strtolower($dm->name) }}'.includes(search.toLowerCase())"
                    class="flex items-center justify-between gap-3 px-3 py-2 border border-app rounded">
                    <div class="flex items-center gap-3">
                        <input type="checkbox" name="dm_ids[]" value="{{ $dm->id }}"
                            {{ $checked ? 'checked' : '' }} {{ $decisionSession->status !== 'draft' ? 'disabled' : '' }}
                            class="rounded border-app">

                        <span class="font-mono text-xs opacity-70">
                            {{ $code }}
                        </span>

                        <span class="text-sm">
                            {{ $dm->name }}
                        </span>
                    </div>
                </label>
            @endforeach
        </div>

        {{-- Footer --}}
        <div class="mt-4 flex items-center justify-between">
            <p class="text-sm opacity-70">
                Terpilih: {{ count($assignedDmIds) }} DM
            </p>

            @if ($decisionSession->status === 'draft')
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded text-sm">
                    Simpan Assignment
                </button>
            @endif
        </div>
    </form>

    @if ($decisionSession->status !== 'draft')
        <p class="mt-3 text-xs text-red-600">
            Sesi sudah aktif. Assignment DM tidak dapat diubah.
        </p>
    @endif

</div>
