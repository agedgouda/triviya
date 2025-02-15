<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameUserQuestions extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'game_id',
        'player_name',
        'question_text',
        'answer',
    ];
}
