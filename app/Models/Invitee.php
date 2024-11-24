<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invitee extends Model
{
    
    
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function mode()
    {
        return $this->belongsTo(Game::class);
    }
}
