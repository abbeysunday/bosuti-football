@extends('layouts.app')

@section('title', 'Terms & Conditions | BOUESTI Sports')
@section('meta_description', 'BOUESTI Sports website terms template to be reviewed before production.')

@section('content')
    <x-site.page-hero
        title="Terms & Conditions"
        eyebrow="Legal"
        description="A placeholder terms page for the final production website."
        image="{{ asset('frontend/assets/images/facilities/football-pitch-sky.jpg') }}"
    />

    {{-- WEBSITE USE --}}
    <section class="section">
        <div class="container legal">
            <div class="eyebrow">Template Terms</div>
            <h2>Website use</h2>
            <p>This static page is a starting point only. Replace it with university-approved terms that reflect the live website and its actual services.</p>
            <h2>Content accuracy</h2>
            <p>Official fixtures, staff, squad records and announcements should be verified before publication.</p>
            <h2>User submissions</h2>
            <p>When forms become functional, add appropriate rules for trial applications, contact messages, media and supporter submissions.</p>
            <h2>Updates</h2>
            <p>State how changes to the production terms will be communicated and who users should contact with questions.</p>
        </div>
    </section>
@endsection
