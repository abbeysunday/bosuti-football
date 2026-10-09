<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('short_name', 12)->nullable();
            $table->string('logo')->nullable();
            $table->string('primary_color', 7)->nullable();
            $table->string('secondary_color', 7)->nullable();
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('founded_year')->nullable();
            $table->string('captain_name')->nullable();
            $table->string('coach_name')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teams');
    }
};
