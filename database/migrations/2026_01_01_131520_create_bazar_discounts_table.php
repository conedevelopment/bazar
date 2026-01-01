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
        Schema::create('bazar_discounts', static function (Blueprint $table): void {
            $table->id();
            $table->foreignId('discount_rule_id')->constrained('bazar_discount_rules')->cascadeOnDelete();
            $table->morphs('discountable');
            $table->decimal('value', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bazar_discountable_discount_rule');
    }
};
