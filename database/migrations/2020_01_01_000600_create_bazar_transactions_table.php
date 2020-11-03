<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBazarTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('bazar_transactions', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('bazar_orders')->cascadeOnDelete();
            $table->string('key')->nullable()->unique();
            $table->string('driver')->nullable();
            $table->string('type');
            $table->unsignedDecimal('amount');
            $table->timestamp('completed_at')->nullable();
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
        Schema::dropIfExists('bazar_transactions');
    }
}
