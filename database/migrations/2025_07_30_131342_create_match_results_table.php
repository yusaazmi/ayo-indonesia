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
        Schema::create('match_results', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('match_schedule_id');
            $table->unsignedBigInteger('home_team_score')->default(0);
            $table->unsignedBigInteger('away_team_score')->default(0);
            $table->unsignedBigInteger('winning_team_id')->default(0); // 0 for draw, otherwise the ID of the winning team
            $table->foreign('match_schedule_id')->references('id')->on('match_schedules')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('match_results');
    }
};
