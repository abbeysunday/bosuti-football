@extends('layouts.app')

@section('title', 'Videos | BOUESTI Sports')
@section('meta_description', 'Highlights, interviews and behind-the-scenes videos from BOUESTI internal football.')

@section('content')
    <x-site.page-hero
        title="Videos"
        eyebrow="Media Centre"
        description="Match highlights, interviews and behind-the-scenes football at BOUESTI."
        image="{{ asset('frontend/assets/images/facilities/basketball-court-campus.jpg') }}"
    />

    {{-- VIDEOS --}}
    <section class="section">
        <div class="container">
            @if ($videos->isEmpty())
                <x-site.empty-state icon="fa-circle-play" title="No videos yet" description="Match highlights and interviews will be posted here." />
            @else
                <div class="video-grid">
                    @foreach ($videos as $video)
                        {{-- VIDEO CARD --}}
                        <article class="video-card">
                            <div class="video-thumb">
                                @if ($video->thumbnail_url)
                                    <img src="{{ $video->thumbnail_url }}" alt="" loading="lazy" decoding="async">
                                @endif
                                <a class="play" href="{{ $video->video_url }}" target="_blank" rel="noopener" aria-label="Watch {{ $video->title }} (opens in a new tab)"><i class="fa-solid fa-play" aria-hidden="true"></i></a>
                            </div>
                            <div class="video-body">
                                @if ($video->category)<div class="eyebrow">{{ $video->category }}</div>@endif
                                <h3>{{ $video->title }}</h3>
                                @if ($video->description)<p>{{ Str::limit($video->description, 120) }}</p>@endif
                            </div>
                        </article>
                    @endforeach
                </div>
                {{ $videos->links('vendor.pagination.bouesti') }}
            @endif
        </div>
    </section>
@endsection
