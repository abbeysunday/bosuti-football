@extends('layouts.app')

@section('title', 'About | BOUESTI Sports')
@section('meta_description', 'Learn about BOUESTI Sports, home of football and basketball at Bamidele Olumilua University of Education, Science and Technology, Ikere-Ekiti.')

@section('content')
    <x-site.page-hero
        title="About Us"
        eyebrow="The Club"
        description="The home of student sport at BOUESTI, built around development, teamwork and the university community."
        image="{{ asset('frontend/assets/images/facilities/football-pitch-wide.jpg') }}"
        breadcrumb="About Us"
    />

    {{-- SPORT WITH PURPOSE --}}
    <section class="section">
        <div class="container two-col">
            <div class="prose">
                <div class="eyebrow">Who We Are</div>
                <h2>Sport with purpose</h2>
                <p>
                    BOUESTI Sports brings together the university's football and basketball programmes. It gives students who want to compete, improve and represent the institution an organised place to train, play and grow with discipline.
                </p>
                <p>
                    The visual identity combines BOUESTI green and gold with a dark match-day atmosphere, creating a sporting experience that feels modern while remaining rooted in the university.
                </p>
                <div class="hero-actions">
                    <a class="btn gold" href="{{ route('teams.index') }}"><i class="fa-solid fa-futbol" aria-hidden="true"></i> Football</a>
                    <a class="btn outline" href="{{ route('basketball') }}"><i class="fa-solid fa-basketball" aria-hidden="true"></i> Basketball</a>
                </div>
            </div>
            <div class="media-card">
                <img src="{{ asset('frontend/assets/images/facilities/football-grandstand.jpg') }}" srcset="{{ asset('frontend/assets/images/facilities/football-grandstand-sm.jpg') }} 640w, {{ asset('frontend/assets/images/facilities/football-grandstand.jpg') }} 1080w" sizes="(max-width: 640px) 100vw, (max-width: 1100px) 50vw, 34vw" alt="The covered grandstand beside the BOUESTI football pitch" loading="lazy" decoding="async">
            </div>
        </div>
    </section>

    {{-- MISSION, VISION & VALUES --}}
    <section class="section alt">
        <div class="container">
            <div class="section-head">
                <div>
                    <div class="eyebrow">Our Foundation</div>
                    <h2 class="section-title">Mission, Vision & <span class="gold">Values</span></h2>
                </div>
            </div>
            <div class="value-grid">
                <div class="value-card">
                    <i class="fa-solid fa-bullseye" aria-hidden="true"></i>
                    <h3>Mission</h3>
                    <p>Provide an organised sporting environment that supports competition, learning, teamwork and student development.</p>
                </div>
                <div class="value-card">
                    <i class="fa-solid fa-eye" aria-hidden="true"></i>
                    <h3>Vision</h3>
                    <p>Build a respected university sports culture where talent and academic growth can exist side by side.</p>
                </div>
                <div class="value-card">
                    <i class="fa-solid fa-handshake-angle" aria-hidden="true"></i>
                    <h3>Character</h3>
                    <p>Represent BOUESTI through discipline, respect, community and responsibility on and off the field.</p>
                </div>
            </div>
        </div>
    </section>
@endsection
