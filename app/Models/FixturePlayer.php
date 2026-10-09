<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** A player named in a team's match-day squad (lineup entry). */
class FixturePlayer extends Model
{
    protected $fillable = [
        'fixture_id', 'player_id', 'team_id', 'is_starting', 'position',
        'shirt_number', 'is_captain', 'minutes_played',
    ];

    protected function casts(): array
    {
        return [
            'is_starting' => 'boolean',
            'is_captain' => 'boolean',
            'shirt_number' => 'integer',
            'minutes_played' => 'integer',
        ];
    }

    public function fixture(): BelongsTo
    {
        return $this->belongsTo(Fixture::class);
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class)->withTrashed();
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class)->withTrashed();
    }
}
