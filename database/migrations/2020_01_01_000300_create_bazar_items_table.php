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
        Schema::create('bazar_items', static function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->morphs('checkoutable');
            $table->nullableMorphs('buyable');
            $table->string('name');
            $table->float('price')->unsigned();
            $table->float('quantity')->unsigned();
            $table->json('properties')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bazar_items');
    }
};
