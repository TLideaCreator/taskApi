<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableProjectTaskStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_task_status', function (Blueprint $table) {
            $table->uuid('id')->default('')->comment('唯一id');
            $table->uuid('project_id')->default('')->comment('项目id');
            $table->string('name')->default('')->comment('状态名称');
            $table->unsignedInteger('indexes')->default(0)->comment('状态索引');
            $table->unsignedTinyInteger('type')->default(0)->comment('状态类型');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('project_task_status');
    }
}
