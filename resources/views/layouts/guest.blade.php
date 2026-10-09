<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="theme-color" content="#040907">

        <title>{{ isset($title) ? $title . ' | ' : '' }}BOUESTI Sports</title>
        <link rel="icon" type="image/png" href="{{ asset('frontend/assets/images/bouesti-fc-crest.png') }}">

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@700;800;900&family=Inter:wght@400;500;600;700;800&display=swap">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-pitch-950">
        <div class="grid min-h-screen lg:grid-cols-[1.05fr_1fr]">
            {{-- Brand panel (desktop) --}}
            <aside class="relative hidden overflow-hidden border-e border-line lg:block" aria-hidden="true">
                <img src="{{ asset('frontend/assets/images/facilities/football-stadium.jpg') }}" alt="" class="absolute inset-0 h-full w-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-r from-pitch-950/95 via-pitch-950/70 to-pitch-950/30"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-pitch-950 via-transparent"></div>
                <div class="relative flex h-full flex-col justify-end p-12 xl:p-16">
                    <p class="eyebrow">Official sports portal</p>
                    <p class="mt-4 font-display text-7xl font-black uppercase leading-[.85] tracking-tight text-white">
                        BOUESTI<br><span class="text-gold-light">Sports</span>
                    </p>
                    <p class="mt-4 max-w-sm text-ink-2">Pride of Ikere. Passion for the Game.</p>
                </div>
            </aside>

            {{-- Form column --}}
            <main class="flex flex-col px-4 py-8 sm:px-6 sm:py-12">
                <a href="{{ route('home') }}" class="mx-auto flex items-center gap-3 rounded-lg lg:mx-0">
                    <x-application-logo class="h-14 w-14" />
                    <span>
                        <span class="block font-display text-lg font-extrabold uppercase leading-none text-white">BOUESTI SPORTS</span>
                        <span class="text-[0.6875rem] font-bold uppercase tracking-[0.15em] text-gold-light">Back to website</span>
                    </span>
                </a>

                <div class="mx-auto my-auto w-full max-w-md py-10">
                    <div class="card p-6 sm:p-8">
                        {{ $slot }}
                    </div>
                </div>

                <p class="text-center text-xs text-ink-faint">&copy; {{ date('Y') }} BOUESTI Sports</p>
            </main>
        </div>
    </body>
</html>
