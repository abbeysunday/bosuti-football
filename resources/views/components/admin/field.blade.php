@props([
    'name',
    'label',
    'type' => 'text',
    'value' => null,
    'options' => [],
    'placeholder' => null,
    'help' => null,
    'required' => false,
    'preview' => null,     // current image URL for file inputs
    'removable' => false,  // show "remove current image" for optional images
    'empty' => null,       // first option label for selects
    'rows' => 4,
])

@php
    $id = $attributes->get('id', 'f-' . str_replace(['[', ']', '.'], '-', $name));
    $key = rtrim(str_replace(['[', ']'], ['.', ''], $name), '.');
    $error = $errors->first($key) ?: $errors->first($key . '.*');
    $current = old($key, $value);
    $describedBy = trim(($help ? "$id-help " : '') . ($error ? "$id-error" : ''));
    $control = $attributes->except('id')->merge(['id' => $id, 'name' => $name]);
    if ($describedBy) $control = $control->merge(['aria-describedby' => $describedBy]);
    if ($error) $control = $control->merge(['aria-invalid' => 'true']);
@endphp

<div {{ $attributes->only('class')->class(['min-w-0']) }}>
    @if ($type === 'checkbox')
        <label for="{{ $id }}" class="flex min-h-[44px] cursor-pointer items-start gap-3 rounded-xl border border-white/10 bg-[#050c08] px-4 py-3 transition hover:border-white/20">
            <input type="hidden" name="{{ $name }}" value="0">
            <input {{ $control->except('class')->merge(['class' => 'form-checkbox mt-0.5']) }} type="checkbox" value="1" @checked((bool) $current)>
            <span>
                <span class="block text-sm font-semibold text-white">{{ $label }}</span>
                @if ($help)<span id="{{ $id }}-help" class="mt-0.5 block text-xs text-ink-muted">{{ $help }}</span>@endif
            </span>
        </label>
    @else
        <label for="{{ $id }}" class="form-label">{{ $label }}@if ($required)<span class="ms-0.5 text-gold-light" aria-hidden="true">*</span>@endif</label>

        @if ($type === 'select')
            <select {{ $control->except('class')->merge(['class' => 'form-input']) }} @required($required)>
                @if ($empty !== null)
                    <option value="">{{ $empty }}</option>
                @endif
                @foreach ($options as $optionValue => $optionLabel)
                    <option value="{{ $optionValue }}" @selected((string) $current === (string) $optionValue)>{{ $optionLabel }}</option>
                @endforeach
            </select>
        @elseif ($type === 'textarea')
            <textarea {{ $control->except('class')->merge(['class' => 'form-input min-h-[7rem] resize-y']) }} rows="{{ $rows }}" placeholder="{{ $placeholder }}" @required($required)>{{ $current }}</textarea>
        @elseif ($type === 'file')
            <div class="flex items-center gap-4">
                @if ($preview)
                    <img src="{{ $preview }}" alt="Current image" class="h-16 w-16 shrink-0 rounded-lg border border-line bg-pitch-900 object-cover">
                @endif
                <input {{ $control->except('class')->merge(['class' => 'block w-full min-w-0 text-sm text-ink-2 file:me-3 file:min-h-[40px] file:cursor-pointer file:rounded-full file:border file:border-line file:bg-pitch-800 file:px-4 file:text-xs file:font-bold file:uppercase file:tracking-wide file:text-white hover:file:border-line-strong']) }} type="file" @required($required)>
            </div>
            @if ($preview && $removable)
                <label class="mt-2 inline-flex min-h-[36px] cursor-pointer items-center gap-2 text-sm text-ink-2">
                    <input type="checkbox" name="remove_{{ $name }}" value="1" class="form-checkbox"> Remove current image
                </label>
            @endif
        @else
            <input {{ $control->except('class')->merge(['class' => 'form-input']) }} type="{{ $type }}" value="{{ $current }}" placeholder="{{ $placeholder }}" @required($required)>
        @endif

        @if ($help)
            <p id="{{ $id }}-help" class="mt-1.5 text-xs text-ink-muted">{{ $help }}</p>
        @endif
    @endif

    @if ($error)
        <p id="{{ $id }}-error" class="form-error" role="alert">
            <svg class="mt-px h-3.5 w-3.5 shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/></svg>
            <span>{{ $error }}</span>
        </p>
    @endif
</div>
