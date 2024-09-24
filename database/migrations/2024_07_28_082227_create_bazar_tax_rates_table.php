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
        Schema::create('bazar_tax_rates', static function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->integer('value')->unsigned();
            $table->boolean('shipping')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bazar_tax_rates');
    }
};
