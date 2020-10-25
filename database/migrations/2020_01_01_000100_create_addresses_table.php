<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->morphs('addressable');
            $table->string('first_name', 60)->nullable();
            $table->string('last_name', 60)->nullable();
            $table->string('country', 2)->nullable();
            $table->string('state', 30)->nullable();
            $table->string('city', 60)->nullable();
            $table->string('postcode', 30)->nullable();
            $table->string('address', 100)->nullable();
            $table->string('address_secondary', 100)->nullable();
            $table->string('company', 60)->nullable();
            $table->string('phone', 60)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('alias')->nullable();
            $table->boolean('default')->default(false);
            $table->json('custom')->nullable();
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
        Schema::dropIfExists('addresses');
    }
}
