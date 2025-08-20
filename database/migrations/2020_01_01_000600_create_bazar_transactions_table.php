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
        Schema::create('bazar_transactions', static function (Blueprint $table): void {
            $table->id();
            $table->foreignId('order_id')->constrained('bazar_orders')->cascadeOnDelete();
            $table->string('key')->nullable()->unique();
            $table->string('driver');
            $table->string('type');
            $table->float('amount')->unsigned();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bazar_transactions');
    }
};
