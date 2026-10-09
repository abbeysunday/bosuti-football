@props(['title', 'description' => null, 'back' => null])

<div class="mb-6 flex flex-col gap-4 sm:mb-8 sm:flex-row sm:items-end sm:justify-between">
    <div class="min-w-0">
        @if ($back)
            <a href="{{ $back }}" class="mb-3 inline-flex min-h-[32px] items-center gap-1.5 rounded text-sm font-semibold text-ink-muted transition hover:text-gold-light">
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd"/></svg>
                Back
            </a>
        @endif
        <h1 class="page-title">{{ $title }}</h1>
        @if ($description)
            <p class="mt-2 max-w-2xl text-sm text-ink-muted">{{ $description }}</p>
        @endif
    </div>
    @isset($actions)
        <div class="flex flex-wrap gap-2 sm:shrink-0">{{ $actions }}</div>
    @endisset
</div>
