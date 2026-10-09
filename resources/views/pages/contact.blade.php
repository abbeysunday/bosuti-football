@extends('layouts.app')

@section('title', 'Contact | BOUESTI Sports')
@section('meta_description', 'Contact BOUESTI Sports in Ikere-Ekiti through this responsive frontend contact template.')

@section('content')
    <x-site.page-hero
        title="Contact"
        eyebrow="Get in Touch"
        description="A responsive contact page ready for official club details and backend message handling."
        image="{{ asset('frontend/assets/images/facilities/sports-courts-palms.jpg') }}"
    />

    {{-- TALK TO THE CLUB --}}
    <section class="section">
        <div class="container">
            <div class="contact-grid">
                <div class="contact-stack">
                    <div class="info-card">
                        <i class="fa-solid fa-location-dot" aria-hidden="true"></i>
                        <h3>Location</h3>
                        <p>BOUESTI, Ikere-Ekiti, Ekiti State, Nigeria.</p>
                    </div>
                    <div class="info-card">
                        <i class="fa-solid fa-envelope" aria-hidden="true"></i>
                        <h3>Email</h3>
                        <p>Official club email to be added.</p>
                    </div>
                    <div class="info-card">
                        <i class="fa-solid fa-phone" aria-hidden="true"></i>
                        <h3>Phone</h3>
                        <p>Official club phone number to be added.</p>
                    </div>
                    <div class="map-placeholder">
                        <div>
                            <div class="eyebrow">BOUESTI</div>
                            <h3 class="stat-figure mt-2">Ikere-Ekiti</h3>
                            <p class="section-copy">Replace with an approved map embed when deploying.</p>
                        </div>
                    </div>
                </div>
                <form class="form-card panel" action="{{ route('contact.store') }}" method="POST" novalidate>
                    @csrf
                    <div class="eyebrow">Send a Message</div>
                    <h2 class="section-title sm">Talk to the <span class="gold">Club</span></h2>
                    <div class="form-feedback" aria-live="polite">
                        @if (session('contact_sent'))
                            <x-site.alert type="success" title="Message sent">{{ session('contact_sent') }}</x-site.alert>
                        @elseif ($errors->any())
                            <x-site.alert type="error" title="Please check the form">Some fields need your attention.</x-site.alert>
                        @endif
                    </div>
                    {{-- Honeypot: hidden from people, filled in by spam bots. --}}
                    <div class="sr-only" aria-hidden="true"><label for="website">Website</label><input id="website" type="text" name="website" tabindex="-1" autocomplete="off"></div>
                    <div class="form-grid">
                        <x-site.field name="name" label="Name" placeholder="Your name" autocomplete="name" required />
                        <x-site.field name="email" label="Email" type="email" placeholder="you@example.com" autocomplete="email" required />
                        <x-site.field name="phone" label="Phone" type="tel" placeholder="Phone number" autocomplete="tel" />
                        <x-site.field name="subject" label="Subject" placeholder="Message subject" required />
                        <x-site.field name="message" label="Message" type="textarea" placeholder="Write your message" full required />
                    </div>
                    <div class="form-actions">
                        <button class="btn gold" type="submit" data-loading-text="Sending…">Send Message <i class="fa-solid fa-paper-plane" aria-hidden="true"></i></button>
                        <p class="form-note"><span class="gold">*</span> Required fields</p>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
