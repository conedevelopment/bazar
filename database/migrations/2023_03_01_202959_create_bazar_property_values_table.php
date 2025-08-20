<?php

declare(strict_types=1);

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
        Schema::create('bazar_property_values', static function (Blueprint $table): void {
            $table->id();
            $table->foreignId('property_id')->constrained('bazar_properties')->cascadeOnDelete();
            $table->string('name');
            $table->string('value')->nullable();
            $table->timestamps();
        });

        Schema::create('bazar_buyable_property_value', static function (Blueprint $table): void {
            $table->foreignId('property_value_id')->constrained('bazar_property_values')->cascadeOnDelete();
            $table->morphs('buyable');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bazar_buyable_property_value');
        Schema::dropIfExists('bazar_property_values');
    }
};
