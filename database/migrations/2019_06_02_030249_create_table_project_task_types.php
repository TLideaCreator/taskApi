<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableProjectTaskTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_task_types', function (Blueprint $table) {
            $table->uuid('id')->default('')->comment('id');
            $table->uuid('project_id')->default('')->comment('项目id');
            $table->string('name')->default('')->comment('类型名称');
            $table->string('icon')->default('')->comment('类型图标');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('project_task_types');
    }
}
