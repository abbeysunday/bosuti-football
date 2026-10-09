<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="no-js">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#040907">
    <meta name="description" content="@yield('meta_description', 'Official website of BOUESTI Sports: football, basketball and student sport at Bamidele Olumilua University of Education, Science and Technology, Ikere-Ekiti.')">
    <title>@yield('title', 'BOUESTI Sports')</title>
    <script>document.documentElement.classList.replace('no-js', 'js');</script>

    <link rel="icon" type="image/png" href="{{ asset('frontend/assets/images/bouesti-fc-crest.png') }}">

    {{-- Fonts load in parallel with the stylesheet (an @import inside style.css would block it) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:ital,wght@0,500;0,600;0,700;0,800;0,900;1,700&family=Inter:wght@400;500;600;700;800&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/style.css') }}">
    @stack('styles')
</head>
<body>
    <a class="skip-link" href="#main">Skip to content</a>

    <x-site.header />
    <x-site.mobile-menu />

    <main id="main" tabindex="-1">
        @yield('content')
    </main>

    <x-site.footer />

    {{-- Session flash messages --}}
    <div class="toast-region" aria-live="polite" data-toasts>
        @foreach (['success', 'error', 'warning', 'info', 'status'] as $type)
            @if (session($type))
                <x-site.alert :type="$type === 'status' ? 'success' : $type" dismissible data-autodismiss>
                    {{ session($type) }}
                </x-site.alert>
            @endif
        @endforeach
    </div>

    <x-site.search />

    <button class="icon-btn backtop" type="button" aria-label="Back to top"><i class="fa-solid fa-arrow-up" aria-hidden="true"></i></button>

    <div class="lightbox" role="dialog" aria-modal="true" aria-label="Image viewer" aria-hidden="true">
        <button class="icon-btn lightbox-close" type="button" aria-label="Close image viewer"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
        <button class="icon-btn lightbox-nav lightbox-prev" type="button" aria-label="Previous image"><i class="fa-solid fa-chevron-left" aria-hidden="true"></i></button>
        <img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" alt="">
        <button class="icon-btn lightbox-nav lightbox-next" type="button" aria-label="Next image"><i class="fa-solid fa-chevron-right" aria-hidden="true"></i></button>
        <span class="lightbox-count" aria-live="polite"></span>
    </div>

    <script src="{{ asset('frontend/assets/js/main.js') }}" defer></script>
    @stack('scripts')
</body>
</html>
