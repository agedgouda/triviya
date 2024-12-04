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
        'last_name',
        'date_time',
        'mode_id',
        'location'
    ];

    public function players()
    {
        return $this->belongsToMany(User::class, 'game_user')
                    ->withPivot('id','status', 'is_host')
                    ->wherePivot('is_host',false);
    }

    public function host()
    {
        return $this->belongsToMany(User::class, 'game_user')
                    ->wherePivot('is_host', true);
    }


    public function getHostAttribute()
    {
        return $this->host()->first();
    }

    public function mode()
    {
        return $this->belongsTo(Mode::class);
    }

    public function invitees()
    {
        return $this->hasMany(Invite::class);
    }

    public function questions()
    {
        return $this->belongsToMany(Question::class);
    }

}
