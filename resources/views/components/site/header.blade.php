@php
    $groups = [
        'sports' => request()->routeIs('basketball'),
        'club' => request()->routeIs('about', 'history', 'management', 'coaching.staff'),
        'team' => request()->routeIs('teams.*', 'squad', 'players.show', 'academy', 'trials'),
        'fixtures' => request()->routeIs('fixtures', 'matches.show', 'league.table'),
        'media' => request()->routeIs('gallery', 'videos'),
        'fans' => request()->routeIs('fan.zone', 'membership', 'sponsors', 'faq', 'match.day'),
    ];
    $current = fn (...$names) => request()->routeIs(...$names) ? 'page' : null;
@endphp

{{-- The homepage nav starts transparent over the hero; inner pages use the solid "inner" variant --}}
<nav @class(['site-nav', 'inner' => ! request()->routeIs('home')]) aria-label="Main">
    <div class="container nav-inner">
        <a class="brand" href="{{ route('home') }}" aria-label="BOUESTI Sports — home">
            <img src="{{ asset('frontend/assets/images/bouesti-fc-crest.png') }}" alt="" width="58" height="58">
            <div>
                <div class="brand-name">Bamidele Olumilua University<br>of Education, Science and Technology</div>
                <span class="brand-club">BOUESTI SPORTS</span>
            </div>
        </a>

        <div class="desktop-nav">
            <a @class(['nav-link', 'active' => request()->routeIs('home')]) href="{{ route('home') }}" aria-current="{{ $current('home') }}">Home</a>

            <div class="drop">
                <button @class(['drop-toggle', 'active' => $groups['sports']]) type="button" aria-expanded="false" aria-controls="menu-sports">Sports <i class="fa-solid fa-chevron-down" aria-hidden="true"></i></button>
                <div class="drop-menu" id="menu-sports">
                    <a href="{{ route('teams.index') }}"><i class="fa-solid fa-futbol" aria-hidden="true"></i>Football</a>
                    <a @class(['active' => request()->routeIs('basketball')]) href="{{ route('basketball') }}" aria-current="{{ $current('basketball') }}"><i class="fa-solid fa-basketball" aria-hidden="true"></i>Basketball</a>
                </div>
            </div>

            <div class="drop">
                <button @class(['drop-toggle', 'active' => $groups['club']]) type="button" aria-expanded="false" aria-controls="menu-club">Club <i class="fa-solid fa-chevron-down" aria-hidden="true"></i></button>
                <div class="drop-menu" id="menu-club">
                    <a @class(['active' => request()->routeIs('about')]) href="{{ route('about') }}" aria-current="{{ $current('about') }}"><i class="fa-solid fa-shield-halved" aria-hidden="true"></i>About Us</a>
                    <a @class(['active' => request()->routeIs('history')]) href="{{ route('history') }}" aria-current="{{ $current('history') }}"><i class="fa-solid fa-landmark" aria-hidden="true"></i>Club History</a>
                    <a @class(['active' => request()->routeIs('management')]) href="{{ route('management') }}" aria-current="{{ $current('management') }}"><i class="fa-solid fa-user-tie" aria-hidden="true"></i>Management</a>
                    <a @class(['active' => request()->routeIs('coaching.staff')]) href="{{ route('coaching.staff') }}" aria-current="{{ $current('coaching.staff') }}"><i class="fa-solid fa-users-gear" aria-hidden="true"></i>Coaching Staff</a>
                </div>
            </div>

            <div class="drop">
                <button @class(['drop-toggle', 'active' => $groups['team']]) type="button" aria-expanded="false" aria-controls="menu-team">Team <i class="fa-solid fa-chevron-down" aria-hidden="true"></i></button>
                <div class="drop-menu" id="menu-team">
                    <a @class(['active' => request()->routeIs('teams.*')]) href="{{ route('teams.index') }}" aria-current="{{ $current('teams.index') }}"><i class="fa-solid fa-shield" aria-hidden="true"></i>Teams</a>
                    <a @class(['active' => request()->routeIs('squad', 'players.show')]) href="{{ route('squad') }}" aria-current="{{ $current('squad') }}"><i class="fa-solid fa-people-group" aria-hidden="true"></i>Squad</a>
                    <a @class(['active' => request()->routeIs('academy')]) href="{{ route('academy') }}" aria-current="{{ $current('academy') }}"><i class="fa-solid fa-graduation-cap" aria-hidden="true"></i>Player Development</a>
                    <a @class(['active' => request()->routeIs('trials')]) href="{{ route('trials') }}" aria-current="{{ $current('trials') }}"><i class="fa-solid fa-user-plus" aria-hidden="true"></i>Football Trials</a>
                </div>
            </div>

            <div class="drop">
                <button @class(['drop-toggle', 'active' => $groups['fixtures']]) type="button" aria-expanded="false" aria-controls="menu-fixtures">Fixtures <i class="fa-solid fa-chevron-down" aria-hidden="true"></i></button>
                <div class="drop-menu" id="menu-fixtures">
                    <a @class(['active' => request()->routeIs('fixtures')]) href="{{ route('fixtures') }}" aria-current="{{ $current('fixtures') }}"><i class="fa-solid fa-calendar-days" aria-hidden="true"></i>Fixtures &amp; Results</a>
                    <a @class(['active' => request()->routeIs('matches.show')]) href="{{ route('match.details') }}" aria-current="{{ $current('matches.show') }}"><i class="fa-solid fa-chart-simple" aria-hidden="true"></i>Match Centre</a>
                    <a @class(['active' => request()->routeIs('league.table')]) href="{{ route('league.table') }}" aria-current="{{ $current('league.table') }}"><i class="fa-solid fa-ranking-star" aria-hidden="true"></i>League Table</a>
                </div>
            </div>

            <a @class(['nav-link', 'active' => request()->routeIs('news', 'news.show')]) href="{{ route('news') }}" aria-current="{{ $current('news') }}">News</a>

            <div class="drop">
                <button @class(['drop-toggle', 'active' => $groups['media']]) type="button" aria-expanded="false" aria-controls="menu-media">Media <i class="fa-solid fa-chevron-down" aria-hidden="true"></i></button>
                <div class="drop-menu" id="menu-media">
                    <a @class(['active' => request()->routeIs('gallery')]) href="{{ route('gallery') }}" aria-current="{{ $current('gallery') }}"><i class="fa-solid fa-images" aria-hidden="true"></i>Gallery</a>
                    <a @class(['active' => request()->routeIs('videos')]) href="{{ route('videos') }}" aria-current="{{ $current('videos') }}"><i class="fa-solid fa-circle-play" aria-hidden="true"></i>Videos</a>
                </div>
            </div>

            <div class="drop">
                <button @class(['drop-toggle', 'active' => $groups['fans']]) type="button" aria-expanded="false" aria-controls="menu-fans">Fans <i class="fa-solid fa-chevron-down" aria-hidden="true"></i></button>
                <div class="drop-menu" id="menu-fans">
                    <a @class(['active' => request()->routeIs('membership')]) href="{{ route('membership') }}" aria-current="{{ $current('membership') }}"><i class="fa-solid fa-id-card" aria-hidden="true"></i>Supporters</a>
                    <a @class(['active' => request()->routeIs('fan.zone')]) href="{{ route('fan.zone') }}" aria-current="{{ $current('fan.zone') }}"><i class="fa-solid fa-flag" aria-hidden="true"></i>Fan Zone</a>
                    <a @class(['active' => request()->routeIs('match.day')]) href="{{ route('match.day') }}" aria-current="{{ $current('match.day') }}"><i class="fa-solid fa-ticket" aria-hidden="true"></i>Match Day</a>
                    <a @class(['active' => request()->routeIs('sponsors')]) href="{{ route('sponsors') }}" aria-current="{{ $current('sponsors') }}"><i class="fa-solid fa-handshake" aria-hidden="true"></i>Partners</a>
                    <a @class(['active' => request()->routeIs('faq')]) href="{{ route('faq') }}" aria-current="{{ $current('faq') }}"><i class="fa-solid fa-circle-question" aria-hidden="true"></i>FAQ</a>
                </div>
            </div>

            <a @class(['nav-link', 'active' => request()->routeIs('contact')]) href="{{ route('contact') }}" aria-current="{{ $current('contact') }}">Contact</a>
        </div>

        <div class="nav-actions">
            <button class="icon-btn search-btn" type="button" aria-label="Search the site" aria-haspopup="dialog" aria-controls="site-search" data-search-open><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i></button>
            <a class="btn gold" href="{{ route('trials') }}">Join the Club <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
            <button class="icon-btn mobile-toggle" type="button" aria-label="Open menu" aria-expanded="false" aria-controls="mobile-menu"><i class="fa-solid fa-bars" aria-hidden="true"></i></button>
        </div>
    </div>
</nav>
