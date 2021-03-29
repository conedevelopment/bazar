<?php

use Bazar\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ExtendUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        $column = User::proxy()->getDeletedAtColumn();

        Schema::table('users', static function (Blueprint $table) use ($column): void {
            if (! Schema::hasColumn('users', $column)) {
                $table->softDeletes($column);
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        //
    }
}
