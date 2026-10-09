@extends('layouts.app')

@section('title', 'FAQ | BOUESTI Sports')
@section('meta_description', 'BOUESTI Sports frequently asked questions template for students and supporters.')

@section('content')
    <x-site.page-hero
        title="Frequently Asked Questions"
        eyebrow="Help Centre"
        description="Clear answers for players, students, supporters and visitors."
        image="{{ asset('frontend/assets/images/facilities/sports-courts-row.jpg') }}"
    />

    {{-- SECTION --}}
    <section class="section">
        <div class="container">
            <div class="faq-list">
                {{-- FAQ ITEM --}}
                <div class="faq-item">
                    <button class="faq-q">Who can join BOUESTI Sports?<i class="fa-solid fa-plus" aria-hidden="true"></i></button>
                    <div class="faq-a">
                        <p>Use the official eligibility rules here. The current template is ready for student-specific requirements.</p>
                    </div>
                </div>
                {{-- FAQ ITEM --}}
                <div class="faq-item">
                    <button class="faq-q">How do football trials work?<i class="fa-solid fa-plus" aria-hidden="true"></i></button>
                    <div class="faq-a">
                        <p>Publish confirmed trial dates, registration steps, assessment stages and selection information once approved.</p>
                    </div>
                </div>
                {{-- FAQ ITEM --}}
                <div class="faq-item">
                    <button class="faq-q">Can first-year students apply?<i class="fa-solid fa-plus" aria-hidden="true"></i></button>
                    <div class="faq-a">
                        <p>Replace this answer with the club’s verified eligibility policy.</p>
                    </div>
                </div>
                {{-- FAQ ITEM --}}
                <div class="faq-item">
                    <button class="faq-q">Where does the team train?<i class="fa-solid fa-plus" aria-hidden="true"></i></button>
                    <div class="faq-a">
                        <p>Use the approved BOUESTI training venue and schedule when confirmed.</p>
                    </div>
                </div>
                {{-- FAQ ITEM --}}
                <div class="faq-item">
                    <button class="faq-q">How can I attend matches?<i class="fa-solid fa-plus" aria-hidden="true"></i></button>
                    <div class="faq-a">
                        <p>Match-day pages can display venue, kick-off time, entry guidance and visitor instructions.</p>
                    </div>
                </div>
                {{-- FAQ ITEM --}}
                <div class="faq-item">
                    <button class="faq-q">How can alumni support the club?<i class="fa-solid fa-plus" aria-hidden="true"></i></button>
                    <div class="faq-a">
                        <p>The supporters and partners pages can be used for approved alumni and sponsorship opportunities.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
