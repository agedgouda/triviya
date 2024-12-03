<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_user_id',
        'question_id',
        'answer',
    ];

    public function gameUser()
    {
        return $this->belongsTo(GameUser::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
