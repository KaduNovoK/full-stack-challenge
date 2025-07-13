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
        Schema::create('tracks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('artists'); // lista em string (pode usar json se preferir)
            $table->string('duration'); // formato mm:ss
            $table->string('thumb_url');
            $table->string('preview_url')->nullable();
            $table->string('spotify_url');
            $table->date('release_date')->nullable();
            $table->boolean('is_available_in_br')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tracks');
    }
};
