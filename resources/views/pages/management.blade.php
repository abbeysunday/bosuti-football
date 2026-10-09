@extends('layouts.app')

@section('title', 'Management | BOUESTI Sports')
@section('meta_description', 'Management and officials of BOUESTI internal football.')

@section('content')
    <x-site.page-hero
        title="Management"
        eyebrow="Club Leadership"
        description="The people who organise and run football activities at BOUESTI."
        image="{{ asset('frontend/assets/images/facilities/sports-courts-side.jpg') }}"
    />

    {{-- PEOPLE --}}
    <section class="section">
        <div class="container">
            <div class="section-head">
                <div>
                    <div class="eyebrow">People</div>
                    <h2 class="section-title">Meet the <span class="gold">Team</span></h2>
                </div>
            </div>
            @if ($staff->isEmpty())
                <x-site.empty-state icon="fa-user-tie" title="Management profiles coming soon" description="Verified names, roles and photographs will be published here." />
            @else
                <div class="staff-grid">
                    @foreach ($staff as $member)
                        {{-- STAFF CARD --}}
                        <article class="staff-card">
                            @if ($member->photo_url)
                                <img src="{{ $member->photo_url }}" alt="{{ $member->name }}" loading="lazy" decoding="async">
                            @else
                                <div class="staff-fallback" aria-hidden="true"><img src="{{ asset('frontend/assets/images/bouesti-fc-crest.png') }}" alt=""></div>
                            @endif
                            <div class="staff-body">
                                <span class="staff-role">{{ $member->role }}@if ($member->team) • {{ $member->team->name }}@endif</span>
                                <h3>{{ $member->name }}</h3>
                                @if ($member->bio)<p>{{ $member->bio }}</p>@endif
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
@endsection
