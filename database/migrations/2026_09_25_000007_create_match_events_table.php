<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('match_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fixture_id')->constrained()->cascadeOnDelete();
            $table->foreignId('team_id')->constrained()->restrictOnDelete();
            // goal / penalty: scorer. card: booked player. substitution: player coming OFF.
            $table->foreignId('player_id')->nullable()->constrained()->nullOnDelete();
            // goal / penalty: assisting player. substitution: player coming ON.
            $table->foreignId('related_player_id')->nullable()->constrained('players')->nullOnDelete();
            $table->string('type', 20); // goal | own_goal | assist | yellow_card | red_card | substitution | penalty_scored | penalty_missed
            $table->unsignedTinyInteger('minute');
            $table->unsignedTinyInteger('additional_minute')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();

            $table->index(['player_id', 'type']);
            $table->index(['related_player_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('match_events');
    }
};
