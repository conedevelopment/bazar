<?php

declare(strict_types=1);

use Cone\Bazar\Enums\CouponType;
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
            $table->float('discount', 10, 2)->unsigned();
            $table->enum('type', CouponType::cases())->default(CouponType::FIX->value);
            $table->integer('limit')->unsigned()->nullable();
            $table->timestamp('available_at')->nullable();
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
