@props(['player', 'squad' => false])

{{-- PLAYER CARD --}}
<article @class(['player-card', 'squad-card' => $squad]) @if ($player->team?->primary_color) style="--team: {{ $player->team->primary_color }}" @endif>
    @if ($player->photo_url)
        <img src="{{ $player->photo_url }}" alt="{{ $player->full_name }}" loading="lazy" decoding="async">
    @else
        <div class="player-fallback" aria-hidden="true"><span>{{ mb_substr($player->first_name, 0, 1) }}{{ mb_substr($player->last_name, 0, 1) }}</span></div>
    @endif
    <span class="player-no">{{ $player->shirt }}</span>
    <div class="player-info">
        <span class="player-country">{{ $player->team?->name }}</span>
        <h3>{{ $player->full_name }}</h3>
        <span class="player-pos">{{ $player->position_label }}@if ($player->is_captain) • Captain @endif</span>
    </div>
    <a class="player-go" href="{{ route('players.show', $player) }}" aria-label="View {{ $player->full_name }}’s profile"><i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
</article>
