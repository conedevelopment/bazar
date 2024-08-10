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
        Schema::create('bazar_taxes', static function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tax_rate_id')->nullable()->constrained('bazar_tax_rates')->nullOnDelete();
            $table->foreignId('item_id')->nullable()->constrained('bazar_items')->nullOnDelete();
            $table->float('value')->unsigned();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bazar_taxes');
    }
};
