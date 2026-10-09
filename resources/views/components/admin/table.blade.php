@props(['label' => null])

<div class="card overflow-hidden">
    <div class="relative overflow-x-auto overscroll-x-contain" @if ($label) role="region" aria-label="{{ $label }}" tabindex="0" @endif>
        <table class="w-full min-w-[640px] text-left text-sm">
            @isset($head)
                <thead class="border-b border-subtle bg-pitch-800/60 text-[0.6875rem] font-bold uppercase tracking-[0.08em] text-ink-muted">
                    <tr>{{ $head }}</tr>
                </thead>
            @endisset
            <tbody class="divide-y divide-white/5 [&>tr]:transition [&>tr:hover]:bg-white/[0.02]">
                {{ $slot }}
            </tbody>
        </table>
    </div>
    @isset($footer)
        <div class="border-t border-subtle px-4 py-3">{{ $footer }}</div>
    @endisset
</div>
