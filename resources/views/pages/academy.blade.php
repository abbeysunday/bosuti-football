@extends('layouts.app')

@section('title', 'Player Development | BOUESTI Sports')
@section('meta_description', 'BOUESTI Sports player development and university football training template.')

@section('content')
    <x-site.page-hero
        title="Player Development"
        eyebrow="Develop the Player"
        description="Training, fitness, tactical learning and student growth within the BOUESTI football environment."
        image="{{ asset('frontend/assets/images/facilities/football-pitch-view.jpg') }}"
    />

    {{-- TRAIN. LEARN. COMPETE. --}}
    <section class="section">
        <div class="container">
            <div class="section-head">
                <div>
                    <div class="eyebrow">Development Pathway</div>
                    <h2 class="section-title">Train. Learn. <span class="gold">Compete.</span></h2>
                </div>
            </div>
            <div class="feature-grid">
                <div class="feature-card">
                    <i class="fa-solid fa-dumbbell" aria-hidden="true"></i>
                    <h3>Physical Development</h3>
                    <p>Structured fitness, recovery and conditioning content can be added by the technical team.</p>
                </div>
                <div class="feature-card">
                    <i class="fa-solid fa-chess-board" aria-hidden="true"></i>
                    <h3>Tactical Learning</h3>
                    <p>Use this area for team principles, positional education and match preparation.</p>
                </div>
                <div class="feature-card">
                    <i class="fa-solid fa-graduation-cap" aria-hidden="true"></i>
                    <h3>Student First</h3>
                    <p>Football development should sit alongside academic responsibility and personal growth.</p>
                </div>
                <div class="feature-card">
                    <i class="fa-solid fa-futbol" aria-hidden="true"></i>
                    <h3>Technical Sessions</h3>
                    <p>Ball mastery, passing, receiving, finishing and position-specific development.</p>
                </div>
                <div class="feature-card">
                    <i class="fa-solid fa-people-group" aria-hidden="true"></i>
                    <h3>Team Culture</h3>
                    <p>Leadership, communication, discipline and respect across the student squad.</p>
                </div>
                <div class="feature-card">
                    <i class="fa-solid fa-arrow-trend-up" aria-hidden="true"></i>
                    <h3>Opportunity</h3>
                    <p>A clear pathway from open trials to squad selection and competitive university football.</p>
                </div>
            </div>
        </div>
    </section>
@endsection
