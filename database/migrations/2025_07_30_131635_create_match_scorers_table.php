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
        Schema::create('match_scorers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('match_result_id');
            $table->unsignedBigInteger('player_id');
            $table->unsignedBigInteger('scored_at_minute');
            $table->tinyInteger('is_penalty')->default(0); // 0 for normal goal, 1 for penalty goal
            $table->tinyInteger('is_own_goal')->default(0); // 0 for normal goal, 1 for own goal
            $table->foreign('match_result_id')->references('id')->on('match_results')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('match_scorers');
    }
};
