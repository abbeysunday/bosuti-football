@extends('layouts.app')

@section('title', 'Fan Zone | BOUESTI Sports')
@section('meta_description', 'BOUESTI Sports student supporter and community fan zone template.')

@section('content')
    <x-site.page-hero
        title="Fan Zone"
        eyebrow="BOUESTI Community"
        description="A student-focused supporter space for match day, community, media and club engagement."
        image="{{ asset('frontend/assets/images/facilities/football-grandstand.jpg') }}"
    />

    {{-- SECTION --}}
    <section class="section">
        <div class="container">
            <div class="feature-grid">
                <div class="feature-card">
                    <i class="fa-solid fa-volume-high" aria-hidden="true"></i>
                    <h3>Support the Team</h3>
                    <p>Use official chants, supporter information and match-day guidance here.</p>
                </div>
                <div class="feature-card">
                    <i class="fa-solid fa-calendar-day" aria-hidden="true"></i>
                    <h3>Match Day</h3>
                    <p>Share kick-off details, venue guidance, entry instructions and campus information.</p>
                </div>
                <div class="feature-card">
                    <i class="fa-solid fa-users" aria-hidden="true"></i>
                    <h3>Student Community</h3>
                    <p>Highlight supporters, volunteers and football activities across the university.</p>
                </div>
                <div class="feature-card">
                    <i class="fa-solid fa-camera" aria-hidden="true"></i>
                    <h3>Fan Gallery</h3>
                    <p>Connect supporter photos and approved match-day media.</p>
                </div>
                <div class="feature-card">
                    <i class="fa-brands fa-instagram" aria-hidden="true"></i>
                    <h3>Social Media</h3>
                    <p>Add the club's verified social channels when available.</p>
                </div>
                <div class="feature-card">
                    <i class="fa-solid fa-heart" aria-hidden="true"></i>
                    <h3>Club Membership</h3>
                    <p>Create supporter and volunteer pathways without requiring payment in this frontend template.</p>
                </div>
            </div>
        </div>
    </section>
@endsection
