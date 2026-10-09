@props(['post', 'large' => false])

{{-- NEWS CARD --}}
<article @class(['news-card', 'large' => $large])>
    @if ($post->image_url)
        <img src="{{ $post->image_url }}" alt="" loading="lazy" decoding="async">
    @else
        <div class="news-fallback" aria-hidden="true"><img src="{{ asset('frontend/assets/images/bouesti-fc-crest.png') }}" alt=""></div>
    @endif
    <div class="news-body">
        @if ($post->category)<span class="tag">{{ $post->category }}</span>@endif
        <div class="date"><time datetime="{{ $post->published_at->toDateString() }}">{{ $post->published_at->format('d M Y') }}</time> • {{ $post->reading_time }} min read</div>
        <h3><a href="{{ route('news.show', $post) }}">{{ $post->title }}</a></h3>
    </div>
</article>
