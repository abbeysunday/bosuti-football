<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trial_applications', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('email');
            $table->string('phone', 30)->nullable();
            $table->string('matric_number', 40)->nullable();
            $table->string('department')->nullable();
            $table->string('level', 20)->nullable();
            $table->string('preferred_position', 20)->nullable();
            $table->string('dominant_foot', 10)->nullable();
            $table->string('height', 20)->nullable();
            $table->string('previous_team')->nullable();
            $table->text('playing_experience')->nullable();
            $table->text('message')->nullable();
            $table->string('status', 20)->default('pending')->index(); // pending | reviewing | accepted | rejected
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trial_applications');
    }
};
