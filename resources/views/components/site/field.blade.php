@props([
    'name',
    'label',
    'type' => 'text',
    'options' => [],
    'placeholder' => null,
    'hint' => null,
    'full' => false,
    'required' => false,
])

@php
    $id = $attributes->get('id', 'field-' . $name);
    $describedBy = trim(($hint ? "$id-hint " : '') . "$id-error");
    $control = $attributes->except('id')->merge([
        'id' => $id,
        'name' => $name,
        'aria-describedby' => $describedBy,
    ]);
    $invalid = $errors->has($name);
@endphp

<div @class(['field', 'full' => $full, 'has-error' => $invalid])>
    <label for="{{ $id }}">{{ $label }}@if ($required)<span class="req" aria-hidden="true">*</span>@endif</label>

    @if ($type === 'select')
        <select {{ $control }} @required($required) @if ($invalid) aria-invalid="true" @endif>
            @if ($placeholder)
                <option value="" disabled @selected(old($name) === null)>{{ $placeholder }}</option>
            @endif
            @foreach ($options as $value => $text)
                <option value="{{ is_int($value) ? $text : $value }}" @selected(old($name) === (is_int($value) ? $text : $value))>{{ $text }}</option>
            @endforeach
        </select>
    @elseif ($type === 'textarea')
        <textarea {{ $control }} placeholder="{{ $placeholder }}" @required($required) @if ($invalid) aria-invalid="true" @endif>{{ old($name) }}</textarea>
    @else
        <input {{ $control }} type="{{ $type }}" value="{{ old($name) }}" placeholder="{{ $placeholder }}" @required($required) @if ($invalid) aria-invalid="true" @endif>
    @endif

    @if ($hint)
        <span class="hint" id="{{ $id }}-hint">{{ $hint }}</span>
    @endif
    <p class="field-error" id="{{ $id }}-error">@error($name){{ $message }}@enderror</p>
</div>
