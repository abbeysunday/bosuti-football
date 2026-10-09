@extends('layouts.app')

@section('title', 'Basketball | BOUESTI Sports')
@section('meta_description', 'Basketball at Bamidele Olumilua University of Education, Science and Technology (BOUESTI), Ikere-Ekiti: courts, training, competitions and how students can get involved.')

@php
    $img = fn (string $name) => asset('frontend/assets/images/facilities/' . $name);
    $courts = [
        ['basketball-court', 'Outdoor basketball court with hoop beside the BOUESTI hostels', 'Main basketball court'],
        ['basketball-court-campus', 'BOUESTI basketball court with campus buildings in the background', 'Court & campus'],
        ['sports-courts', 'Green and red hard courts on the BOUESTI sports grounds', 'Hard courts'],
        ['sports-courts-palms', 'Hard courts lined with palm trees at BOUESTI', 'Courts by the palms'],
        ['sports-courts-side', 'Side view of the BOUESTI hard courts', 'Court side view'],
    ];
@endphp

@section('content')
    <x-site.page-hero
        title="Basketball"
        eyebrow="BOUESTI Sports"
        description="Fast breaks, sharp passing and team spirit. Basketball is part of student sport at BOUESTI, played on the university's outdoor courts in Ikere-Ekiti."
        :image="$img('basketball-court.jpg')"
    />

    {{-- BASKETBALL AT BOUESTI --}}
    <section class="section">
        <div class="container two-col">
            <div class="prose">
                <div class="eyebrow">On the Court</div>
                <h2>Basketball at <span class="gold">BOUESTI</span></h2>
                <p>
                    Alongside football, BOUESTI Sports gives students the chance to train and compete in basketball. Whether you are an experienced player or picking up a ball for the first time, the court is open to students who want to stay active, learn the game and represent their faculty.
                </p>
                <p>
                    The same values guide every BOUESTI team: discipline, respect, teamwork and putting your studies first. Sport is part of student life, not a replacement for it.
                </p>
                <div class="hero-actions">
                    <a class="btn gold" href="{{ route('contact') }}">Get Involved <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                    <a class="btn outline" href="#courts">See the Courts</a>
                </div>
            </div>
            <div class="media-card">
                <img src="{{ $img('basketball-court-campus.jpg') }}" srcset="{{ $img('basketball-court-campus-sm.jpg') }} 640w, {{ $img('basketball-court-campus.jpg') }} 1080w" sizes="(max-width: 640px) 100vw, (max-width: 1100px) 50vw, 34vw" alt="BOUESTI basketball court with campus buildings in the background" loading="lazy" decoding="async">
            </div>
        </div>
    </section>

    {{-- WHAT WE OFFER --}}
    <section class="section alt">
        <div class="container">
            <div class="section-head">
                <div>
                    <div class="eyebrow">The Programme</div>
                    <h2 class="section-title">Train. Play. <span class="gold">Grow.</span></h2>
                </div>
            </div>
            <div class="feature-grid">
                <div class="feature-card">
                    <i class="fa-solid fa-basketball" aria-hidden="true"></i>
                    <h3>Skills Training</h3>
                    <p>Dribbling, shooting, passing and footwork sessions for every level, from beginners to experienced players.</p>
                </div>
                <div class="feature-card">
                    <i class="fa-solid fa-ranking-star" aria-hidden="true"></i>
                    <h3>Inter-Faculty Games</h3>
                    <p>Friendly and competitive games between faculties and departments across the university.</p>
                </div>
                <div class="feature-card">
                    <i class="fa-solid fa-medal" aria-hidden="true"></i>
                    <h3>Representing BOUESTI</h3>
                    <p>The best players can be selected to represent the university in inter-varsity competitions.</p>
                </div>
                <div class="feature-card">
                    <i class="fa-solid fa-heart-pulse" aria-hidden="true"></i>
                    <h3>Fitness & Wellbeing</h3>
                    <p>Conditioning, agility and recovery to keep students healthy through the academic session.</p>
                </div>
                <div class="feature-card">
                    <i class="fa-solid fa-people-group" aria-hidden="true"></i>
                    <h3>Team Culture</h3>
                    <p>Leadership, communication and respect, built through five players working as one.</p>
                </div>
                <div class="feature-card">
                    <i class="fa-solid fa-venus-mars" aria-hidden="true"></i>
                    <h3>Open to All Students</h3>
                    <p>Male and female students from every faculty and level are welcome to come and play.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- OUR COURTS --}}
    <section class="section" id="courts">
        <div class="container">
            <div class="section-head">
                <div>
                    <div class="eyebrow">Facilities</div>
                    <h2 class="section-title">Our <span class="gold">Courts</span></h2>
                </div>
            </div>
            <div class="gallery-grid">
                @foreach ($courts as [$file, $alt, $caption])
                    <button class="gallery-item" type="button">
                        <img src="{{ $img($file . '.jpg') }}" alt="{{ $alt }}" loading="lazy" decoding="async">
                        <span class="gallery-caption">{{ $caption }}</span>
                    </button>
                @endforeach
            </div>
        </div>
    </section>

    {{-- GET INVOLVED --}}
    <section class="section alt">
        <div class="container">
            <div class="value-card cta-card">
                <div class="eyebrow center">Join the Team</div>
                <h2 class="section-title sm">Ready to <span class="gold">ball?</span></h2>
                <p class="section-copy">Send us a message with your name, faculty and level, and the sports office will share training times and team selection dates.</p>
                <a class="btn gold" href="{{ route('contact') }}">Contact Us <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
            </div>
        </div>
    </section>
@endsection
