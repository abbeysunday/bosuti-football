@props([
    'type' => 'info',
    'title' => null,
    'dismissible' => false,
])

@php
    $map = [
        'success' => ['border-brand-light/35 bg-brand/10', 'text-[#2fd488]', 'M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z'],
        'error' => ['border-danger/40 bg-[#c9362b]/10', 'text-danger', 'M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z'],
        'warning' => ['border-[#f2b84b]/40 bg-[#f2b84b]/10', 'text-[#f2b84b]', 'M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z'],
        'info' => ['border-[#6cb8ff]/35 bg-[#6cb8ff]/10', 'text-[#6cb8ff]', 'M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z'],
    ];
    [$box, $iconColor, $path] = $map[$type] ?? $map['info'];
@endphp

<div
    @if ($dismissible) x-data="{ show: true }" x-show="show" x-transition.opacity.duration.200ms @endif
    {{ $attributes->class(["flex items-start gap-3 rounded-xl border px-4 py-3.5 text-sm leading-relaxed text-ink $box"]) }}
    role="{{ $type === 'error' ? 'alert' : 'status' }}"
>
    <svg class="mt-0.5 h-5 w-5 shrink-0 {{ $iconColor }}" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="{{ $path }}" clip-rule="evenodd"/></svg>
    <div class="min-w-0 flex-1">
        @if ($title)
            <p class="font-bold text-white">{{ $title }}</p>
        @endif
        {{ $slot }}
    </div>
    @if ($dismissible)
        <button type="button" class="-my-1 -me-2 grid h-8 w-8 place-items-center rounded-full text-ink-muted transition hover:bg-white/5 hover:text-white" x-on:click="show = false" aria-label="Dismiss message">
            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z"/></svg>
        </button>
    @endif
</div>
