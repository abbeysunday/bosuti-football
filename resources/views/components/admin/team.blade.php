@props(['team', 'size' => 'h-8 w-8', 'reverse' => false])

<span {{ $attributes->class(['inline-flex min-w-0 max-w-full items-center gap-2.5', 'flex-row-reverse text-end' => $reverse]) }}>
    @if ($team?->logo_url)
        <img src="{{ $team->logo_url }}" alt="" class="{{ $size }} shrink-0 rounded-full bg-white object-contain p-0.5">
    @else
        <span class="{{ $size }} grid shrink-0 place-items-center rounded-full text-[0.6875rem] font-extrabold text-white ring-1 ring-white/15" style="background: {{ $team?->primary_color ?: '#0e2117' }}" aria-hidden="true">{{ $team?->initials }}</span>
    @endif
    <span class="truncate font-semibold text-white">{{ $team?->name ?? 'TBC' }}@if ($team?->trashed())<span class="ms-1 text-xs font-normal text-ink-faint">(archived)</span>@endif</span>
</span>
