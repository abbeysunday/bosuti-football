@extends('layouts.app')

@section('title', 'Gallery | BOUESTI Sports')
@section('meta_description', 'Photos from BOUESTI internal football matches, training sessions and supporters.')

@section('content')
    <x-site.page-hero
        title="Gallery"
        eyebrow="Media"
        description="Match-day moments, training sessions and supporters from football at BOUESTI."
        image="{{ asset('frontend/assets/images/facilities/football-pitch-hills.jpg') }}"
    />

    {{-- PHOTOS --}}
    <section class="section">
        <div class="container">
            <x-site.filter-links label="Filter photos" param="category" :current="$currentCategory" all="All" :options="$categories" />

            @if ($items->isEmpty())
                <x-site.empty-state icon="fa-images" title="No photos yet" description="{{ $currentCategory ? 'There are no photos in this category yet.' : 'Photos from matches and training will be shared here.' }}">
                    @if ($currentCategory)<a class="btn dark sm" href="{{ route('gallery') }}">Show all photos</a>@endif
                </x-site.empty-state>
            @else
                <div class="gallery-grid">
                    @foreach ($items as $item)
                        {{-- GALLERY ITEM --}}
                        <button class="gallery-item" type="button">
                            <img src="{{ $item->image_url }}" alt="{{ $item->alt_text }}" loading="lazy" decoding="async">
                            @if ($item->fixture)
                                <span class="gallery-caption">{{ $item->fixture->title }}</span>
                            @elseif ($item->title)
                                <span class="gallery-caption">{{ $item->title }}</span>
                            @endif
                        </button>
                    @endforeach
                </div>
                {{ $items->links('vendor.pagination.bouesti') }}
            @endif
        </div>
    </section>
@endsection
