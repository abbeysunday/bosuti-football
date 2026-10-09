<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fixtures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition_id')->constrained()->restrictOnDelete();
            $table->foreignId('season_id')->constrained()->restrictOnDelete();
            $table->foreignId('home_team_id')->constrained('teams')->restrictOnDelete();
            $table->foreignId('away_team_id')->constrained('teams')->restrictOnDelete();
            $table->date('match_date');
            $table->time('kickoff_time')->nullable();
            $table->string('venue')->nullable();
            $table->string('status', 20)->default('scheduled'); // scheduled | live | completed | postponed | cancelled
            $table->unsignedTinyInteger('home_score')->nullable();
            $table->unsignedTinyInteger('away_score')->nullable();
            $table->unsignedSmallInteger('matchday')->nullable();
            $table->string('referee')->nullable();
            $table->unsignedInteger('attendance')->nullable();
            $table->boolean('featured')->default(false);
            $table->text('report')->nullable();
            $table->timestamps();

            $table->index(['status', 'match_date']);
            $table->index(['competition_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fixtures');
    }
};
