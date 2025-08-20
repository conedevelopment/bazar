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
        Schema::create('bazar_buyable_tax_rate', static function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tax_rate_id')->constrained('bazar_tax_rates')->cascadeOnDelete();
            $table->morphs('buyable');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bazar_buyable_tax_rate');
    }
};
