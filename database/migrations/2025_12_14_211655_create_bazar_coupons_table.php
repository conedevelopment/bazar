<?php

declare(strict_types=1);

use Cone\Bazar\Enums\DiscountType;
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
        Schema::create('bazar_coupons', static function (Blueprint $table): void {
            $table->id();
            $table->string('code')->unique();
            $table->float('value', 10, 2)->unsigned();
            $table->string('type')->default(DiscountType::FIX->value);
            $table->json('rules')->nullable();
            $table->boolean('active')->default(true);
            $table->boolean('stackable')->default(false);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bazar_coupons');
    }
};
