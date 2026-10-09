@props([
    'icon' => 'fa-magnifying-glass',
    'title',
    'description' => null,
])

<div {{ $attributes->class(['empty-state']) }}>
    <span class="empty-state-icon"><i class="fa-solid {{ $icon }}" aria-hidden="true"></i></span>
    <h3>{{ $title }}</h3>
    @if ($description)
        <p>{{ $description }}</p>
    @endif
    {{ $slot }}
</div>
