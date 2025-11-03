<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'name',
        'date_time',
        'mode_id',
        'location',
        'status',
        'is_full',
        'short_url',
    ];

    public function players()
    {
        return $this->belongsToMany(User::class, 'game_user')
            ->withPivot('id', 'status', 'is_host');
    }

    public function host()
    {
        return $this->belongsToMany(User::class, 'game_user')
            ->wherePivot('is_host', true)
            ->withPivot('status');
    }

    public function scopeHostedBy($query, $userId)
    {
        return $query->whereHas('host', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        });
    }

    public function scopeAttendedBy($query, $userId)
    {
        return $query->whereHas('players', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        });
    }

    public function isFull(): bool
    {
        return $this->is_full;
    }

    public function hasSpace(): bool
    {
        return $this->players()->count() < config('game.max_players');
    }

    public function isLocked(): bool
    {
        return $this->is_full || ! in_array($this->status, ['new', 'ready']);
    }

    public function getHostAttribute()
    {
        return $this->host()->first();
    }

    public function mode()
    {
        return $this->belongsTo(Mode::class);
    }

    public function questions()
    {
        return $this->belongsToMany(Question::class);
    }

    public function updateStatusIfReady(): void
    {

        if (
            $this->players()->count() >= 4 &&
            $this->players()->where('status', '!=', 'Completed')->doesntExist()
        ) {
            $this->status = 'ready';
            $this->save();
        } elseif ($this->status === 'ready') {
            $this->status = 'new';
            $this->save();
        }
    }

    public function updateFullStatus(): void
    {
        $this->is_full = $this->players()->count() >= config('game.max_players');
        $this->save();
    }
}
