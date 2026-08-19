<?php

namespace App\Actions\Games;

use App\Models\Game;
use App\Models\GameUserQuestions;

class DeleteTestGameAction
{
    public function handle(Game $game): void
    {
        abort_unless($game->is_test, 404);

        $game->players()->detach();
        GameUserQuestions::where('game_id', $game->id)->delete();
        $game->delete();
    }
}
