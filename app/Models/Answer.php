<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Answer extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_user_id',
        'question_id',
        'answer',
    ];

    /**
     * @return BelongsTo<GameUser, $this>
     */
    public function gameUser(): BelongsTo
    {
        return $this->belongsTo(GameUser::class);
    }

    /**
     * @return BelongsTo<Question, $this>
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}
