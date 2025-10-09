<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update all existing records to 'text'
        DB::table('questions')->update(['question_type' => 'text']);

        // Change the column default to 'text'
        Schema::table('questions', function (Blueprint $table) {
            $table->enum('question_type', ['date', 'text'])
                ->default('text')
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove the default
        Schema::table('questions', function (Blueprint $table) {
            $table->enum('question_type', ['date', 'text'])
                ->default(null)
                ->change();
        });
    }
};
