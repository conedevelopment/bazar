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
        Schema::create('bazar_categories', static function (Blueprint $table): void {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('bazar_categories')->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('bazar_category_product', static function (Blueprint $table): void {
            $table->id();
            $table->foreignId('category_id')->constrained('bazar_categories')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('bazar_products')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bazar_category_product');
        Schema::dropIfExists('bazar_categories');
    }
};
