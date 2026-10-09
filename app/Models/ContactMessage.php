<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    public const STATUSES = [
        'new' => 'New',
        'read' => 'Read',
        'replied' => 'Replied',
        'archived' => 'Archived',
    ];

    protected $fillable = ['name', 'email', 'phone', 'subject', 'message'];

    /** Visitor contact details stay out of any serialised output. */
    protected $hidden = ['email', 'phone'];

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
}
