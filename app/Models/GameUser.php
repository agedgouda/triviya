<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameUser extends Model
{
    use HasFactory;

    protected $table = 'game_user';

    protected $fillable = [
        'user_id',
        'game_id',
        'is_host',
        'status',
    ];

    // A GameUser can have many answers
    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    // Optionally, a GameUser belongs to a specific user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Optionally, a GameUser belongs to a specific game
    public function game()
    {
        return $this->belongsTo(Game::class);
    }
}
