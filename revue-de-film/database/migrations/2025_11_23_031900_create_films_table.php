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
        Schema::create('films', function (Blueprint $table) {
            $table->id();
            $table->string('title', 50);
            $table->string('release_year', 4);
            $table->unsignedSmallInteger('length');
            $table->text('description');
            $table->string('rating', 5);
            $table->foreignId('language_id')->constrained('languages');
            $table->string('special_features', 200)->nullable();
            $table->string('image', 40)->nullable();
            // https://stackoverflow.com/questions/29886497/how-to-only-use-created-at-in-laravel
            $table->timestamps('created_at')->nullable(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('films');
    }
};
