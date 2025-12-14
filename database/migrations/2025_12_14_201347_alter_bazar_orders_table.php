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
        Schema::table('bazar_orders', static function (Blueprint $table): void {
            $table->dropColumn(['discount']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bazar_orders', static function (Blueprint $table): void {
            $table->float('discount')->unsigned()->default(0)->after('currency');
        });
    }
};
