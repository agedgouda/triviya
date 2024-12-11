<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class GameActions extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \App\Services\GameActions::class;
    }
}
