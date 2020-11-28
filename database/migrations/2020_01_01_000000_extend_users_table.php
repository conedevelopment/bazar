<?php

use Bazar\Proxies\User as UserProxy;
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
        $model = UserProxy::getProxiedInstance();

        Schema::table($model->getTable(), static function (Blueprint $table) use ($model): void {
            if (! Schema::hasColumn($model->getTable(), $model->getDeletedAtColumn())) {
                $table->softDeletes($model->getDeletedAtColumn());
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
