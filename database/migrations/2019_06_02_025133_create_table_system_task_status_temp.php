<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableSystemTaskStatusTemp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('system_task_temps', function (Blueprint $table) {
            $table->uuid('id')->default('')->comment('唯一id');
            $table->string('name',256)->default('')->comment('模板名称');
            $table->string('desc', 2048)->default('')->comment('模板描述');
            $table->string('img',1024)->default('')->comment('模板图片');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('system_task_temps');
    }
}
