<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatchEvent extends Model
{
    public const TYPES = [
        'goal' => 'Goal',
        'penalty_scored' => 'Penalty scored',
        'own_goal' => 'Own goal',
        'assist' => 'Assist',
        'yellow_card' => 'Yellow card',
        'red_card' => 'Red card',
        'substitution' => 'Substitution',
        'penalty_missed' => 'Penalty missed',
    ];

    /** Event types that count as goals for the scorer. */
    public const SCORING = ['goal', 'penalty_scored'];

    public const ICONS = [
        'goal' => 'fa-futbol',
        'penalty_scored' => 'fa-futbol',
        'own_goal' => 'fa-futbol',
        'assist' => 'fa-shoe-prints',
        'yellow_card' => 'fa-square',
        'red_card' => 'fa-square',
        'substitution' => 'fa-right-left',
        'penalty_missed' => 'fa-circle-xmark',
    ];

    protected $fillable = [
        'fixture_id', 'team_id', 'player_id', 'related_player_id', 'type',
        'minute', 'additional_minute', 'description',
    ];

    protected function casts(): array
    {
        return [
            'minute' => 'integer',
            'additional_minute' => 'integer',
        ];
    }

    public function fixture(): BelongsTo
    {
        return $this->belongsTo(Fixture::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class)->withTrashed();
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class)->withTrashed();
    }

    public function relatedPlayer(): BelongsTo
    {
        return $this->belongsTo(Player::class, 'related_player_id')->withTrashed();
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? ucfirst(str_replace('_', ' ', $this->type));
    }

    public function getIconAttribute(): string
    {
        return self::ICONS[$this->type] ?? 'fa-circle';
    }

    public function getMinuteLabelAttribute(): string
    {
        return $this->minute . ($this->additional_minute ? '+' . $this->additional_minute : '') . "'";
    }

    /** Label for the second player: assist provider or substitute coming on. */
    public function getRelatedLabelAttribute(): ?string
    {
        return match ($this->type) {
            'goal', 'penalty_scored' => 'Assist',
            'substitution' => 'On',
            default => null,
        };
    }
}
