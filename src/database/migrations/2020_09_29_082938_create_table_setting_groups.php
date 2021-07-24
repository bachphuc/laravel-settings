<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableSettingGroups extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dsoft_setting_groups', function (Blueprint $table) {
            $table->increments('id');

            $table->string('title')->nullable();
            $table->string('color')->nullable();
            $table->string('group_key')->nullable();
            $table->integer('site_id')->nullable()->default(0);

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
        Schema::dropIfExists('dsoft_setting_groups');
    }
}
