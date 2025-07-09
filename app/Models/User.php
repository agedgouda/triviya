<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\HasTeams;
use Laravel\Sanctum\HasApiTokens;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens;
    use HasUuids;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone_number',
        'birthday',
        'password',
        'is_admin',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        return str_ends_with($this->is_admin, 1);
    }

    public function getHasRegisteredAttribute()
    {
        return !empty($this->password);
    }

    public function getNameAttribute()
    {
        return $this->first_name.' '.$this->last_name;
    }

    protected $appends = [
        'profile_photo_url',
        'has_registered',
        'name',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function hostedGames()
    {
        return $this->belongsToMany(Game::class, 'game_user')
                    ->wherePivot('is_host', true);
    }

    public function games()
    {
        return $this->belongsToMany(Game::class, 'game_user')
                    ->withPivot('is_host');
    }

    public function defaultProfilePhotoUrl()
    {
        return 'https://ui-avatars.com/api/?name='.$this->name.'&color=FFFFFF&background=A93390';
    }
}

