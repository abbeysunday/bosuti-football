<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fixture_players', function (Blueprint $table) {
            // Match lineups: one row per player named in a team's match-day squad.
            $table->id();
            $table->foreignId('fixture_id')->constrained()->cascadeOnDelete();
            $table->foreignId('player_id')->constrained()->cascadeOnDelete();
            $table->foreignId('team_id')->constrained()->restrictOnDelete();
            $table->boolean('is_starting')->default(false);
            $table->string('position', 20)->nullable();
            $table->unsignedTinyInteger('shirt_number')->nullable();
            $table->boolean('is_captain')->default(false);
            $table->unsignedTinyInteger('minutes_played')->nullable();
            $table->timestamps();

            $table->unique(['fixture_id', 'player_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fixture_players');
    }
};
