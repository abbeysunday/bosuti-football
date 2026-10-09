@extends('layouts.app')

@section('title', $post->title . ' | BOUESTI Sports')
@section('meta_description', Str::limit($post->summary, 155))

@section('content')
    <x-site.page-hero
        :title="$post->title"
        :eyebrow="$post->category ?? 'News'"
        :description="$post->excerpt"
        :image="$post->image_url ?? asset('frontend/assets/images/facilities/football-stadium.jpg')"
        :breadcrumb="Str::limit($post->title, 40)"
        class="article-hero"
    />

    {{-- ARTICLE --}}
    <section class="section">
        <div class="container article-wrap">
            <article class="article-content">
                <div class="eyebrow">{{ $post->category ?? 'News' }} • <time datetime="{{ $post->published_at->toIso8601String() }}">{{ $post->published_at->format('d F Y') }}</time></div>
                {{-- body_html is sanitised against a strict allowlist (App\Support\RichText). --}}
                <div class="article-body">{!! $post->body_html !!}</div>
            </article>
            <aside class="side-card">
                <div class="footer-title">Article Details</div>
                <div class="footer-links">
                    @if ($post->category)<span>Category: {{ $post->category }}</span>@endif
                    <span>Published: {{ $post->published_at->format('d M Y') }}</span>
                    @if ($post->author)<span>By {{ $post->author->name }}</span>@endif
                    <span>{{ $post->reading_time }} min read</span>
                    @if ($post->fixture)
                        <a href="{{ route('matches.show', $post->fixture) }}">Match centre: {{ $post->fixture->title }}</a>
                    @endif
                    <a href="{{ route('news') }}">← Back to News</a>
                </div>
            </aside>
        </div>
    </section>

    @if ($related->isNotEmpty())
        {{-- RELATED --}}
        <section class="section alt">
            <div class="container">
                <div class="section-head">
                    <div>
                        <div class="eyebrow">Keep Reading</div>
                        <h2 class="section-title">More <span class="gold">News</span></h2>
                    </div>
                </div>
                <div class="news-grid">
                    @foreach ($related as $item)
                        <x-site.news-card :post="$item" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
