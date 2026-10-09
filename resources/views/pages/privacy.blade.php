@extends('layouts.app')

@section('title', 'Privacy Policy | BOUESTI Sports')
@section('meta_description', 'BOUESTI Sports privacy policy template to be reviewed before production.')

@section('content')
    <x-site.page-hero
        title="Privacy Policy"
        eyebrow="Legal"
        description="A placeholder privacy page to update before production deployment."
        image="{{ asset('frontend/assets/images/facilities/football-pitch-sky.jpg') }}"
    />

    {{-- INFORMATION YOU SUBMIT --}}
    <section class="section">
        <div class="container legal">
            <div class="eyebrow">Template Policy</div>
            <h2>Information you submit</h2>
            <p>When the backend is connected, describe what personal information is collected through trial, contact and supporter forms and why it is needed.</p>
            <h2>How information is used</h2>
            <p>Add the university-approved explanation of storage, access, retention and communication practices.</p>
            <h2>Cookies and analytics</h2>
            <p>List any analytics, embedded media or cookies actually used by the production website.</p>
            <h2>Contact</h2>
            <p>Add the appropriate official university or club privacy contact before launch.</p>
        </div>
    </section>
@endsection
