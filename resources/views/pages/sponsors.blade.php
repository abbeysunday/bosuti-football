@extends('layouts.app')

@section('title', 'Partners | BOUESTI Sports')
@section('meta_description', 'BOUESTI Sports sponsorship and university partner presentation template.')

@section('content')
    <x-site.page-hero
        title="Partners"
        eyebrow="Work With Us"
        description="A professional sponsorship presentation ready for verified university and commercial partners."
        image="{{ asset('frontend/assets/images/facilities/sports-courts-lines.jpg') }}"
    />

    {{-- OUR PARTNERS --}}
    <section class="section">
        <div class="container">
            <div class="section-head">
                <div>
                    <div class="eyebrow">Partnership</div>
                    <h2 class="section-title">Our <span class="gold">Partners</span></h2>
                </div>
            </div>
            <div class="sponsor-grid">
                {{-- SPONSOR CARD --}}
                <div class="sponsor-card">
                    <div class="footer-title">Main Partner</div>
                    <h3 class="stat-figure">LOGO</h3>
                    <p class="section-copy">Official partner placeholder</p>
                </div>
                {{-- SPONSOR CARD --}}
                <div class="sponsor-card">
                    <div class="footer-title">Official Partner</div>
                    <h3 class="stat-figure">LOGO</h3>
                    <p class="section-copy">Official partner placeholder</p>
                </div>
                {{-- SPONSOR CARD --}}
                <div class="sponsor-card">
                    <div class="footer-title">University Partner</div>
                    <h3 class="stat-figure">LOGO</h3>
                    <p class="section-copy">Official partner placeholder</p>
                </div>
            </div>
            <div class="value-card cta-card mt-6">
                <div class="eyebrow center">Become a Partner</div>
                <h2 class="section-title sm">Build with BOUESTI Football</h2>
                <p class="section-copy">Replace this section with the university's approved partnership contact process.</p>
                <a class="btn gold" href="{{ route('contact') }}">Contact the Club</a>
            </div>
        </div>
    </section>
@endsection
