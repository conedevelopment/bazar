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
        Schema::create('bazar_discounts', static function (Blueprint $table): void {
            $table->id();
            $table->foreignId('discount_rate_id')->nullable()->constrained('bazar_discount_rates')->nullOnDelete();
            $table->morphs('discountable');
            $table->float('value')->unsigned();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bazar_discounts');
    }
};
