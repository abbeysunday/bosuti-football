@props(['title', 'description' => null])

<section {{ $attributes->class(['card min-w-0 p-5 sm:p-6']) }}>
    <div class="mb-5 flex flex-wrap items-start justify-between gap-3">
        <div>
            <h2 class="card-title text-xl">{{ $title }}</h2>
            @if ($description)<p class="mt-1 text-sm text-ink-muted">{{ $description }}</p>@endif
        </div>
        @isset($actions)<div class="flex flex-wrap gap-2">{{ $actions }}</div>@endisset
    </div>
    {{ $slot }}
</section>
