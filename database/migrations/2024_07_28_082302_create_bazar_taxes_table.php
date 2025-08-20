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
        Schema::create('bazar_taxes', static function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tax_rate_id')->nullable()->constrained('bazar_tax_rates')->nullOnDelete();
            $table->uuidMorphs('taxable');
            $table->float('value')->unsigned();
            $table->timestamps();

            $table->unique(['taxable_id', 'taxable_type', 'tax_rate_id']);
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
