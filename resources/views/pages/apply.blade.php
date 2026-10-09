@extends('layouts.app')

@section('title', 'Football Trials | BOUESTI Sports')
@section('meta_description', 'Join BOUESTI Sports through the responsive football trials registration template.')

@section('content')
    <x-site.page-hero
        title="Football Trials"
        eyebrow="Join the Club"
        description="A responsive registration experience ready to connect to a future Laravel backend."
        image="{{ asset('frontend/assets/images/facilities/football-pitch-field.jpg') }}"
    />

    {{-- YOUR NEXT STEP --}}
    <section class="section">
        <div class="container">
            <div class="form-shell">
                <aside class="form-aside">
                    <div class="eyebrow">Join BOUESTI FC</div>
                    <h2 class="section-title sm">Your next <span class="gold">step</span></h2>
                    <p class="section-copy">
                        Want to play for a BOUESTI team? Register your interest below. Applications are reviewed by the football coordinators, who will contact you with trial dates.
                    </p>
                    <div class="value-card mt-6">
                        <i class="fa-solid fa-circle-info" aria-hidden="true"></i>
                        <h3>Eligibility</h3>
                        <p>Open to registered BOUESTI students. Bring your student ID to trials. Your matric number and contact details are only seen by the football administrators.</p>
                    </div>
                </aside>
                <form class="form-card" action="{{ route('trials.store') }}" method="POST" novalidate>
                    @csrf
                    <div class="form-feedback" aria-live="polite">
                        @if (session('application_submitted'))
                            <x-site.alert type="success" title="Application received">{{ session('application_submitted') }}</x-site.alert>
                        @elseif ($errors->any())
                            <x-site.alert type="error" title="Please check the form">Some fields need your attention.</x-site.alert>
                        @endif
                    </div>
                    {{-- Honeypot: hidden from people, filled in by spam bots. --}}
                    <div class="sr-only" aria-hidden="true"><label for="website">Website</label><input id="website" type="text" name="website" tabindex="-1" autocomplete="off"></div>
                    <div class="form-grid">
                        <x-site.field name="full_name" label="Full Name" placeholder="Your full name" autocomplete="name" required />
                        <x-site.field name="email" label="Email Address" type="email" placeholder="name@example.com" autocomplete="email" required />
                        <x-site.field name="phone" label="Phone Number" type="tel" placeholder="+234" autocomplete="tel" required />
                        <x-site.field name="matric_number" label="Matric Number" placeholder="Student matric number" required />
                        <x-site.field name="department" label="Department" placeholder="Your department" />
                        <x-site.field name="level" label="Level" type="select" placeholder="Select level" :options="\App\Models\Player::LEVELS" required />
                        <x-site.field name="position" label="Preferred Position" type="select" placeholder="Select position" :options="\App\Models\Player::POSITIONS" required />
                        <x-site.field name="dominant_foot" label="Dominant Foot" type="select" placeholder="Select foot" :options="\App\Models\Player::FEET" />
                        <x-site.field name="height" label="Height" placeholder="e.g. 1.82 m" />
                        <x-site.field name="previous_team" label="Previous Team" placeholder="Optional" />
                        <x-site.field name="experience" label="Playing Experience" type="textarea" placeholder="Tell us briefly about your football experience" full />
                    </div>
                    <div class="form-actions">
                        <button class="btn gold" type="submit" data-loading-text="Submitting…">Submit Trial Interest <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></button>
                        <p class="form-note"><span class="gold">*</span> Required fields</p>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
