<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->restrictOnDelete();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('slug')->unique();
            $table->string('photo')->nullable();
            $table->unsignedTinyInteger('jersey_number')->nullable();
            $table->string('position', 20); // goalkeeper | defender | midfielder | forward
            $table->string('department')->nullable();
            $table->string('level', 20)->nullable();
            $table->string('matric_number', 40)->nullable(); // internal/admin only, never shown publicly
            $table->string('state_of_origin')->nullable();
            $table->string('dominant_foot', 10)->nullable(); // left | right | both
            $table->string('height', 20)->nullable();
            $table->text('bio')->nullable();
            $table->boolean('is_captain')->default(false);
            $table->boolean('is_featured')->default(false)->index();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['team_id', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('players');
    }
};
