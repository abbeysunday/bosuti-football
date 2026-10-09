@extends('layouts.app')

@section('title', 'BOUESTI Sports | Pride of Ikere')
@section('meta_description', 'Football and basketball at Bamidele Olumilua University of Education, Science and Technology (BOUESTI), Ikere-Ekiti: fixtures, results, league table, teams, players, news and sports facilities.')

@section('content')
    {{-- HERO --}}
    <section class="hero">
        <div class="hero-bg"></div>
        <div class="container hero-content">
            <div class="hero-kicker">Welcome to</div>
            <h1>BOUESTI<br><span class="gold">Sports</span></h1>
            <p class="hero-tagline">Pride of Ikere. Passion for the Game.</p>
            <p class="hero-copy">
                Football, basketball and student sport at Bamidele Olumilua University of Education, Science and Technology, played with discipline, excellence and unity.
            </p>
            <div class="hero-actions">
                <a class="btn gold" href="#our-sports"><i class="fa-solid fa-medal" aria-hidden="true"></i> Explore Our Sports</a><a class="btn outline" href="{{ route('fixtures') }}"><i class="fa-regular fa-calendar" aria-hidden="true"></i> View Fixtures</a>
            </div>
            <div class="hero-values">
                <div class="hero-value">
                    <i class="fa-solid fa-trophy" aria-hidden="true"></i>
                    <div><b>Excellence</b><span>On and off the field</span></div>
                </div>
                <div class="hero-value">
                    <i class="fa-solid fa-people-group" aria-hidden="true"></i>
                    <div><b>Unity</b><span>A stronger community</span></div>
                </div>
                <div class="hero-value">
                    <i class="fa-solid fa-chart-simple" aria-hidden="true"></i>
                    <div><b>Development</b><span>Building greater futures</span></div>
                </div>
            </div>
        </div>
        <div class="hero-note">More<br>Than a Game</div>
    </section>

    {{-- OUR SPORTS --}}
    <section class="section" id="our-sports">
        <div class="container">
            <div class="section-head">
                <div>
                    <div class="eyebrow">One University, Many Games</div>
                    <h2 class="section-title">Our <span class="gold">Sports</span></h2>
                </div>
            </div>
            <div class="sport-grid">
                <a class="sport-card" href="{{ route('teams.index') }}">
                    <img src="{{ asset('frontend/assets/images/facilities/football-pitch-hills.jpg') }}" srcset="{{ asset('frontend/assets/images/facilities/football-pitch-hills-sm.jpg') }} 640w, {{ asset('frontend/assets/images/facilities/football-pitch-hills.jpg') }} 1080w" sizes="(max-width: 640px) 100vw, 50vw" alt="The BOUESTI football pitch with the Ikere hills behind it" loading="lazy" decoding="async">
                    <div class="sport-body">
                        <span class="sport-icon"><i class="fa-solid fa-futbol" aria-hidden="true"></i></span>
                        <h3>Football</h3>
                        <p>Teams, squads, fixtures, results and the league table of BOUESTI's football competitions.</p>
                        <span class="sport-go">Explore Football <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></span>
                    </div>
                </a>
                <a class="sport-card" href="{{ route('basketball') }}">
                    <img src="{{ asset('frontend/assets/images/facilities/basketball-court.jpg') }}" srcset="{{ asset('frontend/assets/images/facilities/basketball-court-sm.jpg') }} 640w, {{ asset('frontend/assets/images/facilities/basketball-court.jpg') }} 1080w" sizes="(max-width: 640px) 100vw, 50vw" alt="The outdoor basketball court at BOUESTI" loading="lazy" decoding="async">
                    <div class="sport-body">
                        <span class="sport-icon"><i class="fa-solid fa-basketball" aria-hidden="true"></i></span>
                        <h3>Basketball</h3>
                        <p>Training, inter-faculty games and the courts where BOUESTI's basketball players compete.</p>
                        <span class="sport-go">Explore Basketball <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></span>
                    </div>
                </a>
            </div>
        </div>
    </section>

    {{-- FEATURED PLAYERS --}}
    <section class="section alt">
        <div class="container">
            <div class="section-head">
                <div>
                    <div class="eyebrow">Football Stars</div>
                    <h2 class="section-title">Featured <span class="gold">Players</span></h2>
                </div>
                <a class="btn dark sm" href="{{ route('squad') }}">View Squad <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
            </div>
            @if ($featuredPlayers->isEmpty())
                <x-site.empty-state icon="fa-people-group" title="Squads coming soon" description="Player profiles for every BOUESTI team will appear here once squads are registered." />
            @else
                <div class="player-grid">
                    @foreach ($featuredPlayers as $player)
                        <x-site.player-card :player="$player" />
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- LATEST NEWS --}}
    <section class="section">
        <div class="container">
            <div class="section-head">
                <div>
                    <div class="eyebrow">Sports News</div>
                    <h2 class="section-title">Latest <span class="gold">News</span></h2>
                </div>
                <a class="btn dark sm" href="{{ route('news') }}">View All News <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
            </div>
            @if ($latestNews->isEmpty())
                <x-site.empty-state icon="fa-newspaper" title="No news yet" description="Match reports and announcements from BOUESTI sports will be published here." />
            @else
                <div class="news-grid">
                    @foreach ($latestNews as $post)
                        <x-site.news-card :post="$post" :large="$loop->first" />
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- FIXTURES --}}
    <section class="section alt">
        <div class="container">
            <div class="section-head">
                <div>
                    <div class="eyebrow">{{ $nextFixture?->status === 'live' ? 'Live Now' : 'Next Match' }}</div>
                    <h2 class="section-title">Football <span class="gold">Fixtures</span></h2>
                </div>
                <a class="btn dark sm" href="{{ route('fixtures') }}">All Fixtures <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
            </div>

            @if ($nextFixture)
                {{-- NEXT FIXTURE --}}
                <div class="fixture-shell">
                    <div class="fixture-meta">
                        <div class="eyebrow">{{ $nextFixture->competition?->name }}</div>
                        <h3>{{ $nextFixture->status === 'live' ? 'Live Match' : 'Upcoming Fixture' }}</h3>
                        <p>{{ $nextFixture->match_date->format('l, d F Y') }}{{ $nextFixture->kickoff_label ? ' • ' . $nextFixture->kickoff_label : '' }}</p>
                        @if ($nextFixture->venue)
                            <small class="fixture-venue"><i class="fa-solid fa-location-dot" aria-hidden="true"></i> {{ $nextFixture->venue }}</small>
                        @endif
                    </div>
                    <div class="fixture-match">
                        <div class="team-badge"><x-site.team-crest :team="$nextFixture->homeTeam" /><b>{{ $nextFixture->homeTeam->name }}</b></div>
                        <div class="vs">{{ $nextFixture->status === 'live' ? $nextFixture->scoreline : 'VS' }}<small>{{ $nextFixture->matchday ? 'Matchday ' . $nextFixture->matchday : $nextFixture->competition?->type_label }}</small></div>
                        <div class="team-badge"><x-site.team-crest :team="$nextFixture->awayTeam" /><b>{{ $nextFixture->awayTeam->name }}</b></div>
                    </div>
                    <div class="fixture-side">
                        @if ($nextFixture->status === 'live')
                            <p class="live-note"><span class="status-pill live">Live</span> Follow the match centre for updates.</p>
                        @else
                            {{-- Counts down to kick-off (JS); shows the date if the time is unknown. --}}
                            <div class="countdown" data-countdown="{{ $nextFixture->kickoff_at->toIso8601String() }}" aria-label="Time until kick-off">
                                <div class="count"><b data-unit="days">–</b><span>Days</span></div>
                                <div class="count"><b data-unit="hours">–</b><span>Hours</span></div>
                                <div class="count"><b data-unit="minutes">–</b><span>Mins</span></div>
                                <div class="count"><b data-unit="seconds">–</b><span>Secs</span></div>
                            </div>
                        @endif
                        <a class="btn gold sm" href="{{ route('matches.show', $nextFixture) }}">Match Centre</a>
                    </div>
                </div>
            @else
                <x-site.empty-state icon="fa-calendar-days" title="No upcoming fixtures" description="The next internal BOUESTI match will appear here as soon as it is scheduled.">
                    <a class="btn dark sm" href="{{ route('fixtures', ['tab' => 'results']) }}">See results</a>
                </x-site.empty-state>
            @endif

            @if ($recentResults->isNotEmpty())
                {{-- RECENT RESULTS --}}
                <h3 class="subsection-title">Recent <span class="gold">Results</span></h3>
                <div class="fixture-list">
                    @foreach ($recentResults as $fixture)
                        <x-site.fixture-card :fixture="$fixture" />
                    @endforeach
                </div>
            @endif

            {{-- IMPACT STATS (calculated from the database) --}}
            <div class="impact">
                <div class="impact-item">
                    <i class="fa-solid fa-trophy" aria-hidden="true"></i>
                    <div><b>{{ number_format($overview['matches_played']) }}</b><span>Matches Played</span></div>
                </div>
                <div class="impact-item">
                    <i class="fa-solid fa-shield-halved" aria-hidden="true"></i>
                    <div><b>{{ number_format($overview['teams']) }}</b><span>Teams</span></div>
                </div>
                <div class="impact-item">
                    <i class="fa-solid fa-users" aria-hidden="true"></i>
                    <div><b>{{ number_format($overview['players']) }}</b><span>Players</span></div>
                </div>
                <div class="impact-item">
                    <i class="fa-regular fa-futbol" aria-hidden="true"></i>
                    <div><b>{{ number_format($overview['goals']) }}</b><span>Goals Scored</span></div>
                </div>
                <div class="impact-item">
                    <i class="fa-solid fa-quote-left" aria-hidden="true"></i>
                    <div><b class="impact-word">Character</b><span>Talent • Unity • Growth</span></div>
                </div>
            </div>
        </div>
    </section>

    {{-- OUR GROUNDS (campus facility photos) --}}
    <section class="section">
        <div class="container">
            <div class="section-head">
                <div>
                    <div class="eyebrow">Facilities</div>
                    <h2 class="section-title">Our <span class="gold">Grounds</span></h2>
                </div>
                <a class="btn dark sm" href="{{ route('basketball') }}#courts">See the Courts <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
            </div>
            <div class="gallery-grid">
                @foreach ([
                    ['football-stadium', 'The BOUESTI football pitch and covered grandstand', 'Football pitch & grandstand'],
                    ['basketball-court-campus', 'The BOUESTI basketball court beside the hostels', 'Basketball court'],
                    ['sports-courts', 'Hard courts on the BOUESTI sports grounds', 'Hard courts'],
                    ['football-grandstand', 'The covered grandstand overlooking the football pitch', 'The grandstand'],
                    ['volleyball-court', 'Outdoor court with nets on the BOUESTI campus', 'Outdoor courts'],
                ] as [$file, $alt, $caption])
                    <button class="gallery-item" type="button">
                        <img src="{{ asset('frontend/assets/images/facilities/' . $file . '.jpg') }}" alt="{{ $alt }}" loading="lazy" decoding="async">
                        <span class="gallery-caption">{{ $caption }}</span>
                    </button>
                @endforeach
            </div>
        </div>
    </section>

    {{-- GALLERY --}}
    <section class="section alt">
        <div class="container">
            <div class="section-head">
                <div>
                    <div class="eyebrow">Media</div>
                    <h2 class="section-title">Gallery</h2>
                </div>
                <a class="btn dark sm" href="{{ route('gallery') }}">View Gallery <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
            </div>
            @if ($gallery->isEmpty())
                <x-site.empty-state icon="fa-images" title="Photos coming soon" description="Match-day, training and fan photos will be shared here." />
            @else
                <div class="gallery-strip">
                    @foreach ($gallery as $item)
                        {{-- GALLERY ITEM --}}
                        <a class="gallery-tile" href="{{ route('gallery', array_filter(['category' => $item->category])) }}"><img src="{{ $item->image_url }}" alt="{{ $item->alt_text }}" loading="lazy" decoding="async"></a>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
@endsection
