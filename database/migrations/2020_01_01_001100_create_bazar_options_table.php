<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBazarOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('bazar_options', static function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->json('values')->nullable();
            $table->timestamps();
        });

        Schema::create('bazar_option_product', static function (Blueprint $table): void {
            $table->id();
            $table->foreignId('option_id')->constrained('bazar_options')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('bazar_products')->cascadeOnDelete();
            $table->boolean('variable')->default(false);
            $table->json('selection')->nullable();
            $table->unique(['option_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('bazar_product_property');
        Schema::dropIfExists('bazar_options');
    }
}
