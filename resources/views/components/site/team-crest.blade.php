@props(['team', 'small' => false])

@if ($team?->logo_url)
    <img {{ $attributes->class([$small ? 'mini-badge team-logo' : 'team-logo']) }} src="{{ $team->logo_url }}" alt="{{ $team->name }} logo" loading="lazy" decoding="async">
@else
    <span {{ $attributes->class([$small ? 'mini-badge' : 'badge-placeholder']) }} @if ($team?->primary_color) style="--team: {{ $team->primary_color }}" @endif aria-hidden="true">{{ $team?->initials ?? '?' }}</span>
@endif
