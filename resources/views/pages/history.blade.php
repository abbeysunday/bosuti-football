@extends('layouts.app')

@section('title', 'History | BOUESTI Sports')
@section('meta_description', 'BOUESTI Sports history timeline template, ready for verified university football milestones.')

@section('content')
    <x-site.page-hero
        title="Our History"
        eyebrow="Club Story"
        description="A timeline structure ready for verified BOUESTI football milestones, achievements and future growth."
        image="{{ asset('frontend/assets/images/facilities/football-pitch-sky.jpg') }}"
    />

    {{-- FROM CAMPUS TO COMPETITION --}}
    <section class="section">
        <div class="container">
            <div class="section-head">
                <div>
                    <div class="eyebrow">Timeline</div>
                    <h2 class="section-title">From Campus to <span class="gold">Competition</span></h2>
                </div>
            </div>
            <div class="timeline">
                {{-- TIMELINE ITEM --}}
                <div class="timeline-item">
                    <small>Foundation</small>
                    <h3>The football programme takes shape</h3>
                    <p>Use this section for the verified founding story and the people who helped establish organised football at BOUESTI.</p>
                </div>
                {{-- TIMELINE ITEM --}}
                <div class="timeline-item">
                    <small>Early Development</small>
                    <h3>Training, structure and identity</h3>
                    <p>Add accurate milestones about training culture, squad formation and early university fixtures.</p>
                </div>
                {{-- TIMELINE ITEM --}}
                <div class="timeline-item">
                    <small>University Competitions</small>
                    <h3>Representing BOUESTI</h3>
                    <p>Document verified inter-university appearances, tournaments and memorable match-day moments.</p>
                </div>
                {{-- TIMELINE ITEM --}}
                <div class="timeline-item">
                    <small>Growth</small>
                    <h3>A stronger student football community</h3>
                    <p>Show how the programme expanded through players, coaches, volunteers, supporters and alumni.</p>
                </div>
                {{-- TIMELINE ITEM --}}
                <div class="timeline-item">
                    <small>Future Vision</small>
                    <h3>More than football</h3>
                    <p>Use the final stage for the club's official objectives in talent development, facilities, competitions and community.</p>
                </div>
            </div>
        </div>
    </section>
@endsection
