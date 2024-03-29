<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApi2cartConnectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('api2cart_connections')) {
            return;
        }

        Schema::create('api2cart_connections', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('location_id')->default('0');
            $table->string('type')->default('');
            $table->string('url')->default('');
            $table->char('prefix', 10)->default('');
            $table->string('bridge_api_key')->nullable();
            $table->dateTime('last_synced_modified_at')->default('2020-01-01 00:00:00');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('configuration_api2cart');
    }
}
