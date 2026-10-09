@props([
    'title',
    'description' => null,
])

<div {{ $attributes->class(['grid justify-items-center gap-2 rounded-2xl border border-dashed border-line bg-pitch-900/60 px-6 py-12 text-center']) }}>
    <span class="mb-2 grid h-14 w-14 place-items-center rounded-full bg-brand/15 text-gold-light">
        @isset($icon)
            {{ $icon }}
        @else
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0l-3-3m3 3l3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/></svg>
        @endisset
    </span>
    <h3 class="card-title">{{ $title }}</h3>
    @if ($description)
        <p class="max-w-md text-sm text-ink-muted">{{ $description }}</p>
    @endif
    @isset($action)
        <div class="mt-3">{{ $action }}</div>
    @endisset
</div>
