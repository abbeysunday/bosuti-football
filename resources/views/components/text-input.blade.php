@props(['disabled' => false])

@php
    // Flag the field for assistive tech (and the red error style) when the default error bag has a message for it.
    $invalid = $attributes->has('name') && $errors->has($attributes->get('name'));
@endphp

<input @disabled($disabled) @if ($invalid) aria-invalid="true" @endif {{ $attributes->merge(['class' => 'form-input']) }}>
