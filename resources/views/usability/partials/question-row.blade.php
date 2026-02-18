<div class="space-y-3">
    <div class="flex items-start gap-3">
        <div
            class="text-primary bg-primary/10 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-xs font-black">
            {{ $number }}
        </div>

        <p class="adaptive-text-main text-sm font-bold">
            {{ $text }}
        </p>
    </div>

    <div class="grid grid-cols-5 gap-3 pl-11">
        @for ($i = 1; $i <= 5; $i++)
            <label
                class="border-app bg-app hover:border-primary flex cursor-pointer flex-col items-center gap-1 rounded-xl px-3 py-2 text-xs font-black transition">
                <input type="radio" name="answers[{{ $questionId }}]" value="{{ $i }}" required
                    class="sr-only">
                <span>{{ $i }}</span>
            </label>
        @endfor
    </div>
</div>
