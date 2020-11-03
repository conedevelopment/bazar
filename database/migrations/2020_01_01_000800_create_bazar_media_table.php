<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBazarMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('bazar_media', static function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('file_name');
            $table->string('mime_type');
            $table->unsignedInteger('size');
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->string('disk');
            $table->json('properties')->nullable();
            $table->timestamps();
        });

        Schema::create('bazar_mediables', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('medium_id')->constrained('bazar_media')->cascadeOnDelete();
            $table->morphs('mediable');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('bazar_mediables');
        Schema::dropIfExists('bazar_media');
    }
}
