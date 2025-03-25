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
        'status'
    ];

    public function players()
    {
        return $this->belongsToMany(User::class, 'game_user')
                    ->withPivot('id','status', 'is_host')
                    ->wherePivot('is_host',false);
    }

    public function attendingPlayers()
    {
        return $this->players()->whereIn('game_user.status', ['Questions Answered', 'Questions Sent']);
    }

    public function host()
    {
        return $this->belongsToMany(User::class, 'game_user')
                    ->wherePivot('is_host', true);
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

}
