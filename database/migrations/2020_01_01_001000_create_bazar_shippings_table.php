<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bazar_shippings', static function (Blueprint $table): void {
            $table->id();
            $table->morphs('shippable');
            $table->string('driver');
            $table->float('cost')->unsigned()->default(0);
            $table->float('tax')->unsigned()->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bazar_shippings');
    }
};
