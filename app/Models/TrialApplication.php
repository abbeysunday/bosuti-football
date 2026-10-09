<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TrialApplication extends Model
{
    public const STATUSES = [
        'pending' => 'Pending',
        'reviewing' => 'Reviewing',
        'accepted' => 'Accepted',
        'rejected' => 'Rejected',
    ];

    protected $fillable = [
        'full_name', 'email', 'phone', 'matric_number', 'department', 'level', 'preferred_position',
        'dominant_foot', 'height', 'previous_team', 'playing_experience', 'message',
    ];

    /** Contact and student details stay out of any serialised output. */
    protected $hidden = ['email', 'phone', 'matric_number'];

    public function scopeStatus(Builder $query, ?string $status): void
    {
        if ($status && array_key_exists($status, self::STATUSES)) {
            $query->where('status', $status);
        }
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst($this->status);
    }

    public function getPositionLabelAttribute(): ?string
    {
        return $this->preferred_position ? (Player::POSITIONS[$this->preferred_position] ?? ucfirst($this->preferred_position)) : null;
    }
}
