<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('game_user_questions', function (Blueprint $table) {
            $table->id();
            $table->uuid('game_id');
            $table->uuid('user_id');
            $table->string('player_name');
            $table->string('question_text');
            $table->string('answer')->nullable();
            $table->string('question_type');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_user_question');
    }
};
