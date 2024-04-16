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
        Schema::table('bazar_categories', static function (Blueprint $table): void {
            $table->after('id', static function (Blueprint $table): void {
                $table->foreignId('parent_id')->nullable()->constrained('bazar_categories')->nullOnDelete();
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bazar_categories', static function (Blueprint $table): void {
            $table->dropColumn('parent_id');
        });
    }
};
