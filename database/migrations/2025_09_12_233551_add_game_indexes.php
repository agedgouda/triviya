<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('games', function (Blueprint $table) {
            $table->index('created_at', 'idx_games_created_at');
        });

        Schema::table('game_user', function (Blueprint $table) {
            $table->index('game_id', 'idx_game_user_game_id');
            $table->index('user_id', 'idx_game_user_user_id');
            $table->index(['game_id', 'user_id'], 'idx_game_user_game_id_user_id');
            // Optional if status is used
            // $table->index(['game_id', 'user_id', 'status'], 'idx_game_user_game_id_user_id_status');
        });
    }

    public function down(): void
    {
        Schema::table('games', function (Blueprint $table) {
            $table->dropIndex('idx_games_created_at');
        });

        Schema::table('game_user', function (Blueprint $table) {
            $table->dropIndex('idx_game_user_game_id');
            $table->dropIndex('idx_game_user_user_id');
            $table->dropIndex('idx_game_user_game_id_user_id');
            // $table->dropIndex('idx_game_user_game_id_user_id_status');
        });
    }
};
