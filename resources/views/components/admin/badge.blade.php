@props(['color' => 'gray'])

@php
    $styles = [
        'green' => 'bg-brand/15 text-[#2fd488] ring-brand-light/30',
        'gold' => 'bg-gold/15 text-gold-light ring-gold/30',
        'red' => 'bg-danger/15 text-[#ff8e84] ring-danger/30',
        'blue' => 'bg-[#6cb8ff]/10 text-[#8fcaff] ring-[#6cb8ff]/30',
        'gray' => 'bg-white/5 text-ink-2 ring-white/10',
    ][$color] ?? 'bg-white/5 text-ink-2 ring-white/10';
@endphp

<span {{ $attributes->class(["inline-flex items-center gap-1 whitespace-nowrap rounded-full px-2.5 py-1 text-[0.6875rem] font-bold uppercase tracking-wide ring-1 ring-inset $styles"]) }}>{{ $slot }}</span>
