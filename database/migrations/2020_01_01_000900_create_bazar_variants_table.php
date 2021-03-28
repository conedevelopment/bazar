<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBazarVariantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('bazar_variants', static function (Blueprint $table): void {
            $table->id();
            $table->foreignId('product_id')->constrained('bazar_products')->cascadeOnDelete();
            $table->string('alias')->nullable();
            $table->json('variation');
            $table->json('prices')->nullable();
            $table->json('inventory')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['alias', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('bazar_variants');
    }
}
