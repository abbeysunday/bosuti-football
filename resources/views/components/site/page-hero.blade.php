@props([
    'title',
    'eyebrow' => null,
    'description' => null,
    'image' => null,
    'breadcrumb' => null,
])

<section {{ $attributes->class(['page-hero']) }} @if ($image) style="background-image:url('{{ $image }}')" @endif>
    <div class="container">
        @if ($eyebrow)
            <div class="eyebrow">{{ $eyebrow }}</div>
        @endif
        <h1>{{ $title }}</h1>
        @if ($description)
            <p>{{ $description }}</p>
        @endif
        <nav class="crumb" aria-label="Breadcrumb">
            <a href="{{ route('home') }}">Home</a><i class="fa-solid fa-chevron-right" aria-hidden="true"></i><span aria-current="page">{{ $breadcrumb ?? $title }}</span>
        </nav>
        {{ $slot }}
    </div>
</section>
