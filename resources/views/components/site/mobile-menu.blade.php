@php
    $current = fn (...$names) => request()->routeIs(...$names) ? 'page' : null;
@endphp

<div class="mobile-overlay" aria-hidden="true"></div>
<aside class="mobile-panel" id="mobile-menu" role="dialog" aria-modal="true" aria-label="Site menu">
    <div class="mobile-head">
        <a class="brand" href="{{ route('home') }}" aria-label="BOUESTI Sports — home">
            <img src="{{ asset('frontend/assets/images/bouesti-fc-crest.png') }}" alt="" width="46" height="46">
            <span class="brand-club">BOUESTI SPORTS</span>
        </a>
        <button class="icon-btn mobile-close" type="button" aria-label="Close menu"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
    </div>

    <nav class="mobile-links" aria-label="Mobile">
        <a @class(['mobile-link', 'active' => request()->routeIs('home')]) href="{{ route('home') }}" aria-current="{{ $current('home') }}"><i class="fa-solid fa-house" aria-hidden="true"></i>Home</a>

        <div @class(['mobile-acc', 'active' => request()->routeIs('basketball')])>
            <button class="mobile-acc-btn" type="button" aria-expanded="false" aria-controls="m-sports"><i class="fa-solid fa-medal" aria-hidden="true"></i>Sports<i class="fa-solid fa-chevron-down chev" aria-hidden="true"></i></button>
            <div class="mobile-sub" id="m-sports">
                <div>
                    <a href="{{ route('teams.index') }}">Football</a>
                    <a @class(['active' => request()->routeIs('basketball')]) href="{{ route('basketball') }}" aria-current="{{ $current('basketball') }}">Basketball</a>
                </div>
            </div>
        </div>

        <div @class(['mobile-acc', 'active' => request()->routeIs('about', 'history', 'management', 'coaching.staff')])>
            <button class="mobile-acc-btn" type="button" aria-expanded="false" aria-controls="m-club"><i class="fa-solid fa-shield-halved" aria-hidden="true"></i>Club<i class="fa-solid fa-chevron-down chev" aria-hidden="true"></i></button>
            <div class="mobile-sub" id="m-club">
                <div>
                    <a @class(['active' => request()->routeIs('about')]) href="{{ route('about') }}" aria-current="{{ $current('about') }}">About Us</a>
                    <a @class(['active' => request()->routeIs('history')]) href="{{ route('history') }}" aria-current="{{ $current('history') }}">Club History</a>
                    <a @class(['active' => request()->routeIs('management')]) href="{{ route('management') }}" aria-current="{{ $current('management') }}">Management</a>
                    <a @class(['active' => request()->routeIs('coaching.staff')]) href="{{ route('coaching.staff') }}" aria-current="{{ $current('coaching.staff') }}">Coaching Staff</a>
                </div>
            </div>
        </div>

        <div @class(['mobile-acc', 'active' => request()->routeIs('teams.*', 'squad', 'players.show', 'academy', 'trials')])>
            <button class="mobile-acc-btn" type="button" aria-expanded="false" aria-controls="m-team"><i class="fa-solid fa-people-group" aria-hidden="true"></i>Team<i class="fa-solid fa-chevron-down chev" aria-hidden="true"></i></button>
            <div class="mobile-sub" id="m-team">
                <div>
                    <a @class(['active' => request()->routeIs('teams.*')]) href="{{ route('teams.index') }}" aria-current="{{ $current('teams.index') }}">Teams</a>
                    <a @class(['active' => request()->routeIs('squad', 'players.show')]) href="{{ route('squad') }}" aria-current="{{ $current('squad') }}">Squad</a>
                    <a @class(['active' => request()->routeIs('academy')]) href="{{ route('academy') }}" aria-current="{{ $current('academy') }}">Player Development</a>
                    <a @class(['active' => request()->routeIs('trials')]) href="{{ route('trials') }}" aria-current="{{ $current('trials') }}">Football Trials</a>
                </div>
            </div>
        </div>

        <div @class(['mobile-acc', 'active' => request()->routeIs('fixtures', 'matches.show', 'league.table')])>
            <button class="mobile-acc-btn" type="button" aria-expanded="false" aria-controls="m-fixtures"><i class="fa-solid fa-calendar-days" aria-hidden="true"></i>Fixtures<i class="fa-solid fa-chevron-down chev" aria-hidden="true"></i></button>
            <div class="mobile-sub" id="m-fixtures">
                <div>
                    <a @class(['active' => request()->routeIs('fixtures')]) href="{{ route('fixtures') }}" aria-current="{{ $current('fixtures') }}">Fixtures &amp; Results</a>
                    <a @class(['active' => request()->routeIs('matches.show')]) href="{{ route('match.details') }}" aria-current="{{ $current('match.details') }}">Match Centre</a>
                    <a @class(['active' => request()->routeIs('league.table')]) href="{{ route('league.table') }}" aria-current="{{ $current('league.table') }}">League Table</a>
                </div>
            </div>
        </div>

        <a @class(['mobile-link', 'active' => request()->routeIs('news', 'news.show')]) href="{{ route('news') }}" aria-current="{{ $current('news') }}"><i class="fa-solid fa-newspaper" aria-hidden="true"></i>News</a>

        <div @class(['mobile-acc', 'active' => request()->routeIs('gallery', 'videos')])>
            <button class="mobile-acc-btn" type="button" aria-expanded="false" aria-controls="m-media"><i class="fa-solid fa-images" aria-hidden="true"></i>Media<i class="fa-solid fa-chevron-down chev" aria-hidden="true"></i></button>
            <div class="mobile-sub" id="m-media">
                <div>
                    <a @class(['active' => request()->routeIs('gallery')]) href="{{ route('gallery') }}" aria-current="{{ $current('gallery') }}">Gallery</a>
                    <a @class(['active' => request()->routeIs('videos')]) href="{{ route('videos') }}" aria-current="{{ $current('videos') }}">Videos</a>
                </div>
            </div>
        </div>

        <div @class(['mobile-acc', 'active' => request()->routeIs('fan.zone', 'membership', 'sponsors', 'faq', 'match.day')])>
            <button class="mobile-acc-btn" type="button" aria-expanded="false" aria-controls="m-fans"><i class="fa-solid fa-flag" aria-hidden="true"></i>Fans<i class="fa-solid fa-chevron-down chev" aria-hidden="true"></i></button>
            <div class="mobile-sub" id="m-fans">
                <div>
                    <a @class(['active' => request()->routeIs('membership')]) href="{{ route('membership') }}" aria-current="{{ $current('membership') }}">Supporters</a>
                    <a @class(['active' => request()->routeIs('fan.zone')]) href="{{ route('fan.zone') }}" aria-current="{{ $current('fan.zone') }}">Fan Zone</a>
                    <a @class(['active' => request()->routeIs('match.day')]) href="{{ route('match.day') }}" aria-current="{{ $current('match.day') }}">Match Day</a>
                    <a @class(['active' => request()->routeIs('sponsors')]) href="{{ route('sponsors') }}" aria-current="{{ $current('sponsors') }}">Partners</a>
                    <a @class(['active' => request()->routeIs('faq')]) href="{{ route('faq') }}" aria-current="{{ $current('faq') }}">FAQ</a>
                </div>
            </div>
        </div>

        <a @class(['mobile-link', 'active' => request()->routeIs('contact')]) href="{{ route('contact') }}" aria-current="{{ $current('contact') }}"><i class="fa-solid fa-envelope" aria-hidden="true"></i>Contact</a>
        <a class="btn gold block mobile-cta" href="{{ route('trials') }}">Join the Club <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
    </nav>

    <div class="socials">
        <a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook-f" aria-hidden="true"></i></a>
        <a href="#" aria-label="X"><i class="fa-brands fa-x-twitter" aria-hidden="true"></i></a>
        <a href="#" aria-label="Instagram"><i class="fa-brands fa-instagram" aria-hidden="true"></i></a>
        <a href="#" aria-label="YouTube"><i class="fa-brands fa-youtube" aria-hidden="true"></i></a>
    </div>
</aside>
