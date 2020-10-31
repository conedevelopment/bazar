<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('items', static function (Blueprint $table) {
            $table->uuid('id');
            $table->foreignId('product_id')->nullable()->constrained()->onDelete('set null');
            $table->morphs('itemable');
            $table->unsignedDecimal('price');
            $table->unsignedDecimal('tax')->default(0);
            $table->unsignedInteger('quantity');
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
        Schema::dropIfExists('items');
    }
}
