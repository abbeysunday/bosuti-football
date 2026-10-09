@extends('layouts.app')

@section('title', 'News | BOUESTI Sports')
@section('meta_description', 'Match reports, team news and announcements from BOUESTI internal football.')

@section('content')
    <x-site.page-hero
        title="News"
        eyebrow="Latest Updates"
        description="Match reports, team news and announcements from football at BOUESTI."
        image="{{ asset('frontend/assets/images/facilities/football-pitch-hills.jpg') }}"
    />

    {{-- NEWS --}}
    <section class="section">
        <div class="container">
            <div class="section-head">
                <div>
                    <div class="eyebrow">{{ $currentCategory ?? 'Latest Stories' }}</div>
                    <h2 class="section-title">Club <span class="gold">News</span></h2>
                </div>
            </div>

            @if ($categories->count() > 1)
                <x-site.filter-links label="Filter news by category" param="category" :current="$currentCategory" all="All" :options="$categories->combine($categories)" />
            @endif

            @if ($posts->isEmpty())
                <x-site.empty-state icon="fa-newspaper" title="No news available" description="{{ $currentCategory ? 'There are no ' . $currentCategory . ' articles yet.' : 'Match reports and club announcements will be published here.' }}">
                    @if ($currentCategory)<a class="btn dark sm" href="{{ route('news') }}">All news</a>@endif
                </x-site.empty-state>
            @else
                <div class="news-grid">
                    @foreach ($posts as $post)
                        <x-site.news-card :post="$post" :large="$loop->first && $posts->onFirstPage()" />
                    @endforeach
                </div>
                {{ $posts->links('vendor.pagination.bouesti') }}
            @endif
        </div>
    </section>
@endsection
