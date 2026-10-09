@props(['title' => 'Admin'])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="theme-color" content="#040907">
        <meta name="robots" content="noindex, nofollow">

        <title>{{ $title }} | BOUESTI Sports Admin</title>
        <link rel="icon" type="image/png" href="{{ asset('frontend/assets/images/bouesti-fc-crest.png') }}">

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@700;800;900&family=Inter:wght@400;500;600;700;800&display=swap">

        {{-- Page assets (e.g. the news editor) load first so the admin theme can override them. --}}
        @stack('head')
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body
        class="bg-pitch-950"
        x-data="{ sidebar: false }"
        x-effect="document.body.classList.toggle('overflow-hidden', sidebar)"
        @keydown.escape.window="sidebar = false"
    >
        <a href="#content" class="sr-only z-[60] rounded-full bg-gold-light px-4 py-3 font-bold text-pitch-950 focus:not-sr-only focus:fixed focus:start-4 focus:top-3">Skip to content</a>

        @include('admin.partials.sidebar')

        <div class="lg:ps-64">
            <header class="sticky top-0 z-30 border-b border-subtle bg-pitch-950/90 backdrop-blur-lg">
                <div class="flex h-16 items-center gap-3 px-4 sm:px-6 lg:px-8">
                    <button type="button" class="icon-btn lg:hidden" @click="sidebar = true" aria-label="Open navigation" aria-controls="app-sidebar" :aria-expanded="sidebar">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" /></svg>
                    </button>
                    <p class="min-w-0 flex-1 truncate font-display text-xl font-extrabold uppercase leading-none text-white">{{ $title }}</p>
                    <a href="{{ route('home') }}" class="btn btn-ghost hidden sm:inline-flex" target="_blank" rel="noopener">View site</a>
                    @include('layouts.partials.account-menu')
                </div>
            </header>

            <main id="content" class="mx-auto w-full max-w-7xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
                @foreach (['success', 'error', 'warning', 'info'] as $type)
                    @if (session($type))
                        <x-alert :type="$type" dismissible class="mb-6">{{ session($type) }}</x-alert>
                    @endif
                @endforeach

                @if ($errors->any())
                    <x-alert type="error" class="mb-6" title="Please fix the highlighted fields.">
                        {{ $errors->count() === 1 ? $errors->first() : $errors->count() . ' fields need attention.' }}
                    </x-alert>
                @endif

                {{ $slot }}
            </main>
        </div>

        {{-- Shared confirmation for destructive actions: any <form data-confirm="..."> opens it. --}}
        <dialog id="confirm-dialog" class="w-[min(28rem,calc(100vw-2rem))] rounded-2xl border border-line bg-pitch-900 p-0 text-ink shadow-pop backdrop:bg-black/70 backdrop:backdrop-blur-sm" aria-labelledby="confirm-title">
            <form method="dialog" class="p-6">
                <div class="flex items-start gap-4">
                    <span class="grid h-11 w-11 shrink-0 place-items-center rounded-full bg-danger/15 text-danger">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>
                    </span>
                    <div>
                        <h2 id="confirm-title" class="card-title text-xl">Are you sure?</h2>
                        <p class="mt-1 text-sm text-ink-2" data-confirm-message></p>
                    </div>
                </div>
                <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                    <button value="cancel" class="btn btn-outline" autofocus>Cancel</button>
                    <button value="confirm" class="btn btn-danger" data-confirm-button>Delete</button>
                </div>
            </form>
        </dialog>
    </body>
</html>
