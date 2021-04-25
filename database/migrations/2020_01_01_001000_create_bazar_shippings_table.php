<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBazarShippingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('bazar_shippings', static function (Blueprint $table): void {
            $table->id();
            $table->uuidMorphs('shippable');
            $table->string('driver');
            $table->unsignedDecimal('cost')->default(0);
            $table->unsignedDecimal('tax')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('bazar_shippings');
    }
}
