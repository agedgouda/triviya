<?php

return [
    'max_players' => env('MAX_GAME_PLAYERS', 12),
    'min_players' => env('MIN_GAME_PLAYERS', 3),

    /*
     * Buying games is off until Stripe payment verification is in place.
     */
    'purchasing_enabled' => (bool) env('GAME_PURCHASING_ENABLED', false),
];
