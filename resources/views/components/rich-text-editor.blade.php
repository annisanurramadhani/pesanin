@vite('resources/css/components/rich-text-editor.css')

@props([
    'name',
    'label' => null,
    'value' => '',
    'placeholder' => 'Tulis di sini...',
    'required' => false
])

<div class="rich-text-editor-wrapper">

    {{-- Label --}}
    @if ($label)
        <label
            for="{{ $name }}"
            class="mb-2 block text-sm font-bold text-slate-700"
        >
            {{ $label }}

            @if ($required)
                <span class="text-rose-500">*</span>
            @endif
        </label>
    @endif

    {{-- Editor Wrapper --}}
    <div
        class="rich-text-editor overflow-hidden rounded-xl border border-slate-200 bg-white transition focus-within:border-amber-500 focus-within:ring-4 focus-within:ring-amber-500/10"
        data-rich-text-editor
        data-name="{{ $name }}"
        data-placeholder="{{ $placeholder }}"
    >

        {{-- Toolbar --}}
        <div class="flex flex-wrap items-center gap-1 border-b border-slate-200 bg-slate-50 p-2">

            {{-- Bold --}}
            <button
                type="button"
                data-command="bold"
                class="editor-button rounded-lg px-3 py-2 text-sm font-bold text-slate-600 transition hover:bg-slate-200"
                title="Bold"
            >
                <i class="fa-solid fa-bold"></i>
            </button>

            {{-- Italic --}}
            <button
                type="button"
                data-command="italic"
                class="editor-button rounded-lg px-3 py-2 text-sm italic text-slate-600 transition hover:bg-slate-200"
                title="Italic"
            >
                <i class="fa-solid fa-italic"></i>
            </button>

            {{-- Underline --}}
            <button
                type="button"
                data-command="underline"
                class="editor-button rounded-lg px-3 py-2 text-sm text-slate-600 transition hover:bg-slate-200"
                title="Underline"
            >
                <i class="fa-solid fa-underline"></i>
            </button>

            {{-- Strike --}}
            <button
                type="button"
                data-command="strike"
                class="editor-button rounded-lg px-3 py-2 text-sm text-slate-600 transition hover:bg-slate-200"
                title="Strikethrough"
            >
                <i class="fa-solid fa-strikethrough"></i>
            </button>

            <div class="mx-1 h-6 w-px bg-slate-200"></div>

            {{-- Heading --}}
            <select
                data-command="heading"
                class="editor-heading rounded-lg border-0 bg-transparent px-3 py-2 text-sm font-semibold text-slate-600 outline-none transition hover:bg-slate-200 focus:ring-0"
                title="Heading"
            >
                <option value="paragraph">Normal</option>
                <option value="1">Heading 1</option>
                <option value="2">Heading 2</option>
                <option value="3">Heading 3</option>
            </select>

            <div class="mx-1 h-6 w-px bg-slate-200"></div>

            {{-- Text Color --}}
            <button
                type="button"
                data-command="textColor"
                class="editor-button rounded-lg px-3 py-2 text-sm text-slate-600 transition hover:bg-slate-200"
                title="Warna Teks"
            >
                <i class="fa-solid fa-font"></i>
            </button>

            {{-- Highlight --}}
            <button
                type="button"
                data-command="highlight"
                class="editor-button rounded-lg px-3 py-2 text-sm text-slate-600 transition hover:bg-slate-200"
                title="Highlight"
            >
                <i class="fa-solid fa-highlighter"></i>
            </button>

            <div class="mx-1 h-6 w-px bg-slate-200"></div>

            {{-- Bullet List --}}
            <button
                type="button"
                data-command="bulletList"
                class="editor-button rounded-lg px-3 py-2 text-sm text-slate-600 transition hover:bg-slate-200"
                title="Bullet List"
            >
                <i class="fa-solid fa-list-ul"></i>
            </button>

            {{-- Ordered List --}}
            <button
                type="button"
                data-command="orderedList"
                class="editor-button rounded-lg px-3 py-2 text-sm text-slate-600 transition hover:bg-slate-200"
                title="Numbered List"
            >
                <i class="fa-solid fa-list-ol"></i>
            </button>

            <div class="mx-1 h-6 w-px bg-slate-200"></div>

            {{-- Undo --}}
            <button
                type="button"
                data-command="undo"
                class="editor-button rounded-lg px-3 py-2 text-sm text-slate-600 transition hover:bg-slate-200"
                title="Undo"
            >
                <i class="fa-solid fa-rotate-left"></i>
            </button>

            {{-- Redo --}}
            <button
                type="button"
                data-command="redo"
                class="editor-button rounded-lg px-3 py-2 text-sm text-slate-600 transition hover:bg-slate-200"
                title="Redo"
            >
                <i class="fa-solid fa-rotate-right"></i>
            </button>

        </div>

        {{-- Text Color Palette --}}
        <div
            data-color-picker
            class="hidden border-b border-slate-200 bg-white p-3"
        >
            <div class="flex flex-wrap items-center gap-2">

                <span class="mr-2 text-xs font-semibold text-slate-500">
                    Warna teks
                </span>

                {{-- No Color --}}
                <button
                    type="button"
                    data-color="unset"
                    class="color-option flex h-7 w-7 items-center justify-center rounded-full border border-slate-300 bg-white text-xs text-slate-500 transition hover:scale-110"
                    title="Warna Default"
                >
                    <i class="fa-solid fa-ban"></i>
                </button>

                {{-- Black --}}
                <button
                    type="button"
                    data-color="#000000"
                    class="color-option h-7 w-7 rounded-full border border-slate-300 bg-black transition hover:scale-110"
                    title="Hitam"
                ></button>

                {{-- Red --}}
                <button
                    type="button"
                    data-color="#ef4444"
                    class="color-option h-7 w-7 rounded-full bg-red-500 transition hover:scale-110"
                    title="Merah"
                ></button>

                {{-- Orange --}}
                <button
                    type="button"
                    data-color="#f97316"
                    class="color-option h-7 w-7 rounded-full bg-orange-500 transition hover:scale-110"
                    title="Orange"
                ></button>

                {{-- Yellow --}}
                <button
                    type="button"
                    data-color="#eab308"
                    class="color-option h-7 w-7 rounded-full bg-yellow-500 transition hover:scale-110"
                    title="Kuning"
                ></button>

                {{-- Green --}}
                <button
                    type="button"
                    data-color="#22c55e"
                    class="color-option h-7 w-7 rounded-full bg-green-500 transition hover:scale-110"
                    title="Hijau"
                ></button>

                {{-- Blue --}}
                <button
                    type="button"
                    data-color="#3b82f6"
                    class="color-option h-7 w-7 rounded-full bg-blue-500 transition hover:scale-110"
                    title="Biru"
                ></button>

                {{-- Violet --}}
                <button
                    type="button"
                    data-color="#8b5cf6"
                    class="color-option h-7 w-7 rounded-full bg-violet-500 transition hover:scale-110"
                    title="Ungu"
                ></button>

                {{-- Pink --}}
                <button
                    type="button"
                    data-color="#ec4899"
                    class="color-option h-7 w-7 rounded-full bg-pink-500 transition hover:scale-110"
                    title="Pink"
                ></button>

            </div>
        </div>

        {{-- Highlight Palette --}}
        <div
            data-highlight-picker
            class="hidden border-b border-slate-200 bg-white p-3"
        >
            <div class="flex flex-wrap items-center gap-2">

                <span class="mr-2 text-xs font-semibold text-slate-500">
                    Highlight
                </span>

                {{-- No Highlight --}}
                <button
                    type="button"
                    data-highlight="unset"
                    class="highlight-option flex h-7 w-7 items-center justify-center rounded-full border border-slate-300 bg-white text-xs text-slate-500 transition hover:scale-110"
                    title="Tanpa Highlight"
                >
                    <i class="fa-solid fa-ban"></i>
                </button>

                {{-- Yellow --}}
                <button
                    type="button"
                    data-highlight="#fef08a"
                    class="highlight-option h-7 w-7 rounded-full border border-yellow-200 bg-yellow-200 transition hover:scale-110"
                    title="Kuning"
                ></button>

                {{-- Red --}}
                <button
                    type="button"
                    data-highlight="#fecaca"
                    class="highlight-option h-7 w-7 rounded-full border border-red-200 bg-red-200 transition hover:scale-110"
                    title="Merah"
                ></button>

                {{-- Orange --}}
                <button
                    type="button"
                    data-highlight="#fed7aa"
                    class="highlight-option h-7 w-7 rounded-full border border-orange-200 bg-orange-200 transition hover:scale-110"
                    title="Orange"
                ></button>

                {{-- Green --}}
                <button
                    type="button"
                    data-highlight="#bbf7d0"
                    class="highlight-option h-7 w-7 rounded-full border border-green-200 bg-green-200 transition hover:scale-110"
                    title="Hijau"
                ></button>

                {{-- Blue --}}
                <button
                    type="button"
                    data-highlight="#bfdbfe"
                    class="highlight-option h-7 w-7 rounded-full border border-blue-200 bg-blue-200 transition hover:scale-110"
                    title="Biru"
                ></button>

                {{-- Violet --}}
                <button
                    type="button"
                    data-highlight="#ddd6fe"
                    class="highlight-option h-7 w-7 rounded-full border border-violet-200 bg-violet-200 transition hover:scale-110"
                    title="Ungu"
                ></button>

                {{-- Pink --}}
                <button
                    type="button"
                    data-highlight="#fbcfe8"
                    class="highlight-option h-7 w-7 rounded-full border border-pink-200 bg-pink-200 transition hover:scale-110"
                    title="Pink"
                ></button>

            </div>
        </div>

        {{-- Editor Content --}}
        <div
            data-editor-content
            class="min-h-[180px] px-4 py-3 text-sm text-slate-800 outline-none"
        ></div>

    </div>

    {{-- Hidden Input --}}
    <input
        type="hidden"
        name="{{ $name }}"
        id="{{ $name }}"
        value="{{ $value }}"
    >

    {{-- Validation Error --}}
    @error($name)
        <p class="mt-1 text-xs font-semibold text-rose-500">
            {{ $message }}
        </p>
    @enderror

</div>