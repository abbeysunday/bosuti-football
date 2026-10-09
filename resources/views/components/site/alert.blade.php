@props([
    'type' => 'info',
    'title' => null,
    'dismissible' => false,
])

@php
    $icon = [
        'success' => 'fa-circle-check',
        'error' => 'fa-circle-exclamation',
        'warning' => 'fa-triangle-exclamation',
        'info' => 'fa-circle-info',
    ][$type] ?? 'fa-circle-info';
@endphp

<div {{ $attributes->class(['alert', $type]) }} role="{{ $type === 'error' ? 'alert' : 'status' }}">
    <i class="fa-solid {{ $icon }}" aria-hidden="true"></i>
    <div class="alert-body">
        @if ($title)
            <strong class="alert-title">{{ $title }}</strong>
        @endif
        {{ $slot }}
    </div>
    @if ($dismissible)
        <button class="alert-close" type="button" aria-label="Dismiss message" data-dismiss-alert><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
    @endif
</div>
