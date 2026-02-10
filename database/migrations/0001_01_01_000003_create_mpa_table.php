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
        Schema::create('wordle', function (Blueprint $table) {
            $table->id()->auto_increment()->primary();
            $table->string('woord');
            $table->text('description');
            $table->integer('lengte');
        });

        Schema::create('muziek', function (Blueprint $table) {
            $table->id()->auto_increment()->primary();
            $table->string('titel');
            $table->string('artiest');
            $table->string('album');
            $table->integer('jaar');
            $table->string('genre');
            $table->string('song_path');
        });

        Schema::create('scores', function (Blueprint $table) {
            $table->id();
            $table->string('naam');
            $table->float('wordle_score');
            $table->float('muziek_score');
            $table->float('rps_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wordle');
        Schema::dropIfExists('muziek');
        Schema::dropIfExists('scores');
    }
};
