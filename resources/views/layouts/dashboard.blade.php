<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="theme-color" content="#040907">

        <title>{{ isset($title) ? $title . ' | ' : '' }}BOUESTI Sports Portal</title>
        <link rel="icon" type="image/png" href="{{ asset('frontend/assets/images/bouesti-fc-crest.png') }}">

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@700;800;900&family=Inter:wght@400;500;600;700;800&display=swap">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body
        class="bg-pitch-950"
        x-data="{ sidebar: false }"
        x-effect="document.body.classList.toggle('overflow-hidden', sidebar)"
        @keydown.escape.window="sidebar = false"
    >
        <a href="#content" class="sr-only z-[60] rounded-full bg-gold-light px-4 py-3 font-bold text-pitch-950 focus:not-sr-only focus:fixed focus:start-4 focus:top-3">Skip to content</a>

        @include('layouts.navigation')

        <div class="lg:ps-64">
            {{-- Top bar --}}
            <header class="sticky top-0 z-30 border-b border-subtle bg-pitch-950/90 backdrop-blur-lg">
                <div class="flex h-16 items-center gap-3 px-4 sm:px-6 lg:px-8">
                    <button type="button" class="icon-btn lg:hidden" @click="sidebar = true" aria-label="Open navigation" aria-controls="app-sidebar" :aria-expanded="sidebar">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" /></svg>
                    </button>

                    <div class="min-w-0 flex-1">
                        @isset($header)
                            {{ $header }}
                        @endisset
                    </div>

                    @include('layouts.partials.account-menu')
                </div>
            </header>

            <main id="content" class="mx-auto w-full max-w-6xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
                @foreach (['success', 'error', 'warning', 'info'] as $type)
                    @if (session($type))
                        <x-alert :type="$type" dismissible class="mb-6">{{ session($type) }}</x-alert>
                    @endif
                @endforeach

                {{ $slot }}
            </main>
        </div>
    </body>
</html>
