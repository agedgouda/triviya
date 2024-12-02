<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_user_id',  // Links to the GameUser model
        'question_id',   // Links to the Question model
        'answer',        // The actual answer (string or date)
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
