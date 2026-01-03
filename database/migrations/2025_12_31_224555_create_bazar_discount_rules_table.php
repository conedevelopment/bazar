<?php

declare(strict_types=1);

use Cone\Bazar\Enums\DiscountRuleType;
use Cone\Bazar\Enums\DiscountValueType;
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
        Schema::create('bazar_discount_rules', static function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->boolean('active')->default(true);
            $table->string('type')->default(DiscountRuleType::CART->value);
            $table->string('value_type')->default(DiscountRuleValueType::TOTAL->value);
            $table->boolean('stackable')->default(false);
            $table->json('rules')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bazar_discount_rules');
    }
};
