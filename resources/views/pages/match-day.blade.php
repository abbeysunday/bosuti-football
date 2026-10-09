@extends('layouts.app')

@section('title', 'Match Day | BOUESTI Sports')
@section('meta_description', 'BOUESTI Sports match-day attendance and venue information template.')

@section('content')
    <x-site.page-hero
        title="Match Day"
        eyebrow="Attend the Game"
        description="Venue information, student entry guidance and match-day details in place of a commercial ticket shop."
        image="{{ asset('frontend/assets/images/facilities/football-grandstand.jpg') }}"
    />

    {{-- SECTION --}}
    <section class="section">
        <div class="container">
            <div class="feature-grid">
                <div class="feature-card">
                    <i class="fa-solid fa-location-dot" aria-hidden="true"></i>
                    <h3>Venue</h3>
                    <p>BOUESTI, Ikere-Ekiti. Replace with the exact approved sports-complex details.</p>
                </div>
                <div class="feature-card">
                    <i class="fa-solid fa-id-card" aria-hidden="true"></i>
                    <h3>Student Entry</h3>
                    <p>Add the verified student entry process and any identification requirements here.</p>
                </div>
                <div class="feature-card">
                    <i class="fa-solid fa-clock" aria-hidden="true"></i>
                    <h3>Kick-Off</h3>
                    <p>Connect this card to the next fixture so match time updates automatically.</p>
                </div>
                <div class="feature-card">
                    <i class="fa-solid fa-users" aria-hidden="true"></i>
                    <h3>Visitors</h3>
                    <p>Use official visitor instructions, access points and venue rules when available.</p>
                </div>
                <div class="feature-card">
                    <i class="fa-solid fa-shield" aria-hidden="true"></i>
                    <h3>Guidelines</h3>
                    <p>Add approved match-day conduct and safety information.</p>
                </div>
                <div class="feature-card">
                    <i class="fa-solid fa-bullhorn" aria-hidden="true"></i>
                    <h3>Support</h3>
                    <p>Encourage a positive university atmosphere built around respect and community.</p>
                </div>
            </div>
        </div>
    </section>
@endsection
