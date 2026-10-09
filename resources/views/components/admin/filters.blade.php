@props(['action', 'search' => true, 'placeholder' => 'Search…'])

@php $active = collect(request()->except('page'))->filter(fn ($v) => filled($v))->isNotEmpty(); @endphp

<form method="GET" action="{{ $action }}" class="card mb-5 flex flex-col gap-3 p-3 sm:flex-row sm:flex-wrap sm:items-center" role="search">
    @if ($search)
        <label class="relative min-w-0 flex-1 sm:min-w-[14rem]">
            <span class="sr-only">Search</span>
            <svg class="pointer-events-none absolute start-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-ink-faint" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd"/></svg>
            <input type="search" name="q" value="{{ request('q') }}" placeholder="{{ $placeholder }}" class="form-input min-h-[44px] ps-10">
        </label>
    @endif
    {{ $slot }}
    <div class="flex gap-2">
        <button type="submit" class="btn btn-secondary flex-1 sm:flex-none">Filter</button>
        @if ($active)
            <a href="{{ $action }}" class="btn btn-ghost flex-1 sm:flex-none">Clear</a>
        @endif
    </div>
</form>
