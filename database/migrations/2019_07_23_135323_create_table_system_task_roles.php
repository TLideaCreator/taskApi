<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableSystemTaskRoles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('system_task_roles', function (Blueprint $table) {
            $table->uuid('id');
            $table->string('temp_id');
            $table->string('name')->comment('角色名称');
            $table->string('logo')->comment('角色logo');
            $table->unsignedTinyInteger('project_mgr')->comment('项目管理');
            $table->unsignedTinyInteger('sprint_mgr')->comment('冲刺管理');
            $table->unsignedTinyInteger('task_mgr')->comment('任务管理');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('system_task_roles');
    }
}
