<?php

namespace App\Services;

use Illuminate\Support\Facades\App;

class GameActions
{
    public function __call($method, $parameters)
    {
        // Dynamically resolve the action class from its name
        $actionClass = "App\\Actions\\Games\\" . ucfirst($method);

        if (class_exists($actionClass)) {
            return (new $actionClass())->handle(...$parameters);
        }

        throw new \Exception("Action {$method} does not exist.");
    }
}
