<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('bazar_variables', static function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('key');
            $table->json('options')->nullable();
            $table->timestamps();
        });

        Schema::create('bazar_buyable_variable', static function (Blueprint $table): void {
            $table->foreignId('variable_id')->constrained('bazar_variables')->cascadeOnDelete();
            $table->morphs('buyable');
            $table->json('value')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('bazar_buyable_variable');
        Schema::dropIfExists('bazar_variables');
    }
};
