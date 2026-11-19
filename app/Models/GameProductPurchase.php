<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GameProductPurchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'game_product_id',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<GameProduct, $this>
     */
    public function gameProduct(): BelongsTo
    {
        return $this->belongsTo(GameProduct::class);
    }
}
