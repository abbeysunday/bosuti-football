@props(['fixture'])

{{-- FIXTURE CARD --}}
<article class="fixture-card2">
    <div class="fixture-date">
        <b>{{ $fixture->match_date->format('D d M') }}</b>
        <span>
            {{ $fixture->kickoff_label ?? 'Time TBC' }}
            @if ($fixture->competition) · {{ $fixture->competition->display_name }}@endif
            @if ($fixture->venue)<br class="hide-sm">{{ $fixture->venue }}@endif
        </span>
    </div>
    <div class="fixture-teams">
        <div class="side">
            <x-site.team-crest :team="$fixture->homeTeam" small />
            <span>{{ $fixture->homeTeam?->name }}</span>
        </div>
        <div class="score">
            {{ $fixture->scoreline }}
            @if ($fixture->status !== 'scheduled' && $fixture->status !== 'completed')
                <small class="status-pill {{ $fixture->status }}">{{ $fixture->status_label }}</small>
            @elseif ($fixture->isCompleted())
                <small>FT</small>
            @endif
        </div>
        <div class="side">
            <x-site.team-crest :team="$fixture->awayTeam" small />
            <span>{{ $fixture->awayTeam?->name }}</span>
        </div>
    </div>
    <div class="fixture-action"><a class="btn dark sm" href="{{ route('matches.show', $fixture) }}" aria-label="Match centre: {{ $fixture->title }}">Match Centre</a></div>
</article>
