@extends('layouts.app')

@section('title', 'Supporters | BOUESTI Sports')
@section('meta_description', 'BOUESTI Sports student, alumni and volunteer supporter template.')

@section('content')
    <x-site.page-hero
        title="Supporters"
        eyebrow="Be Part of It"
        description="A clean supporter membership concept for students, alumni and club volunteers."
        image="{{ asset('frontend/assets/images/facilities/sports-courts-nets.jpg') }}"
    />

    {{-- SECTION --}}
    <section class="section">
        <div class="container">
            <div class="member-grid">
                {{-- MEMBERSHIP CARD --}}
                <div class="member-card">
                    <i class="fa-solid fa-graduation-cap" aria-hidden="true"></i>
                    <h3>Student Supporter</h3>
                    <p>Follow fixtures, join the match-day community and support university football.</p>
                    <a class="btn outline sm" href="{{ route('contact') }}">Register Interest</a>
                </div>
                {{-- MEMBERSHIP CARD --}}
                <div class="member-card">
                    <i class="fa-solid fa-user-graduate" aria-hidden="true"></i>
                    <h3>Alumni Supporter</h3>
                    <p>A future space for alumni engagement, mentoring and club support opportunities.</p>
                    <a class="btn outline sm" href="{{ route('contact') }}">Register Interest</a>
                </div>
                {{-- MEMBERSHIP CARD --}}
                <div class="member-card">
                    <i class="fa-solid fa-hand-holding-heart" aria-hidden="true"></i>
                    <h3>Club Volunteer</h3>
                    <p>Support media, match operations, events and community activities around the team.</p>
                    <a class="btn gold sm" href="{{ route('contact') }}">Volunteer</a>
                </div>
            </div>
        </div>
    </section>
@endsection
