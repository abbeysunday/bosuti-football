@php
    // Page index for the header search (navigation shortcuts; content itself is browsed on each page).
    $pages = [
        ['home', 'Home', 'Homepage', 'fa-house', 'welcome sports'],
        ['basketball', 'Basketball', 'Sports', 'fa-basketball', 'court hoops training inter-faculty facilities'],
        ['about', 'About Us', 'Club', 'fa-shield-halved', 'mission values identity'],
        ['history', 'Club History', 'Club', 'fa-landmark', 'timeline story'],
        ['management', 'Management', 'Club', 'fa-user-tie', 'leadership officials'],
        ['coaching.staff', 'Coaching Staff', 'Club', 'fa-users-gear', 'coach technical team'],
        ['squad', 'Squad', 'Team', 'fa-people-group', 'players roster goalkeepers defenders midfielders forwards'],
        ['teams.index', 'Football Teams', 'Sports', 'fa-shield', 'football clubs amapro elite young boys csc engines sovereignty amcoms'],
        ['academy', 'Player Development', 'Team', 'fa-graduation-cap', 'academy training'],
        ['trials', 'Football Trials', 'Team', 'fa-user-plus', 'join apply application register'],
        ['fixtures', 'Fixtures & Results', 'Fixtures', 'fa-calendar-days', 'matches schedule scores'],
        ['match.details', 'Match Centre', 'Fixtures', 'fa-chart-simple', 'match report lineup stats live result'],
        ['league.table', 'League Table', 'Fixtures', 'fa-ranking-star', 'standings points'],
        ['news', 'News', 'News', 'fa-newspaper', 'stories updates articles'],
        ['gallery', 'Gallery', 'Media', 'fa-images', 'photos pictures'],
        ['videos', 'Videos', 'Media', 'fa-circle-play', 'highlights'],
        ['membership', 'Supporters', 'Fans', 'fa-id-card', 'membership join fans'],
        ['fan.zone', 'Fan Zone', 'Fans', 'fa-flag', 'supporters community'],
        ['match.day', 'Match Day', 'Fans', 'fa-ticket', 'tickets attendance stadium'],
        ['sponsors', 'Partners', 'Fans', 'fa-handshake', 'sponsors sponsorship'],
        ['faq', 'FAQ', 'Fans', 'fa-circle-question', 'questions help'],
        ['contact', 'Contact', 'Get in touch', 'fa-envelope', 'email phone location address'],
        ['privacy', 'Privacy Policy', 'Legal', 'fa-lock', 'data'],
        ['terms', 'Terms & Conditions', 'Legal', 'fa-file-lines', 'rules'],
    ];
@endphp

<div class="search-dialog" id="site-search" role="dialog" aria-modal="true" aria-labelledby="site-search-label" hidden>
    <div class="search-backdrop" data-search-close></div>
    <div class="search-panel">
        <div class="search-field">
            <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
            <label class="sr-only" for="site-search-input" id="site-search-label">Search the site</label>
            <input id="site-search-input" type="search" placeholder="Search pages — squad, fixtures, trials…" autocomplete="off" enterkeyhint="go" aria-controls="site-search-results">
            <button class="icon-btn" type="button" aria-label="Close search" data-search-close><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
        </div>
        <ul class="search-results" id="site-search-results">
            @foreach ($pages as [$route, $title, $group, $icon, $keywords])
                <li data-search-item="{{ strtolower("$title $group $keywords") }}">
                    <a href="{{ route($route) }}">
                        <i class="fa-solid {{ $icon }}" aria-hidden="true"></i>
                        <div><span>{{ $title }}</span><small>{{ $group }}</small></div>
                    </a>
                </li>
            @endforeach
        </ul>
        <p class="search-empty" hidden data-search-empty>No pages match your search. Try “fixtures” or “trials”.</p>
    </div>
</div>
