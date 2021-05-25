<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBazarItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('bazar_items', static function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->morphs('itemable');
            $table->nullableMorphs('buyable');
            $table->string('name');
            $table->unsignedDecimal('price');
            $table->unsignedDecimal('tax')->default(0);
            $table->unsignedDecimal('quantity');
            $table->json('properties')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('bazar_items');
    }
}
