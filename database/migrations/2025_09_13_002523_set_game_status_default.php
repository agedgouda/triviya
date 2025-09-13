<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('games', function (Blueprint $table) {
            // Change the default value to 'new'
            $table->string('status')->default('new')->change();
        });
    }

    public function down(): void
    {
        Schema::table('games', function (Blueprint $table) {
            // Remove default value if rolling back
            $table->string('status')->default(null)->change();
        });
    }
};
