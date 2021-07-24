<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dsoft_settings', function (Blueprint $table) {
            $table->increments('id');

            $table->string('title')->nullable();
            $table->string('setting_key')->nullable();
            $table->string('setting_value')->nullable();
            $table->string('group_key')->nullable();
            $table->string('setting_type')->nullable();
            $table->integer('site_id')->nullable()->default(0);
            $table->tinyInteger('is_active')->default(1);
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
        Schema::dropIfExists('dsoft_settings');
    }
}
