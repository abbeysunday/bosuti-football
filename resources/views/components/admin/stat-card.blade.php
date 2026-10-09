@props(['label', 'value', 'href' => null, 'icon'])

<a href="{{ $href ?? '#' }}" class="card group flex items-center gap-4 p-4 transition duration-200 hover:border-line-strong hover:bg-pitch-800 sm:p-5">
    <span class="grid h-12 w-12 shrink-0 place-items-center rounded-xl bg-brand/15 text-gold-light">
        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}"/></svg>
    </span>
    <span class="min-w-0">
        <span class="block font-display text-3xl font-extrabold leading-none text-white">{{ number_format($value) }}</span>
        <span class="mt-1 block truncate text-xs font-semibold uppercase tracking-wide text-ink-muted">{{ $label }}</span>
    </span>
</a>
