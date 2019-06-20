<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableRoles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->uuid('id')->default(null)->comment('角色id');
            $table->string('name')->default(null)->comment('角色名');
            $table->unsignedTinyInteger('type')->default(0)->comment('角色类型');
            $table->string('icon')->default(null)->comment('角色图标');
            $table->unsignedTinyInteger('create')->default(0)->comment('创建任务权限');
            $table->unsignedTinyInteger('read')->default(0)->comment('查看任务权限');
            $table->unsignedTinyInteger('update')->default(0)->comment('修改任务权限');
            $table->unsignedTinyInteger('delete')->default(0)->comment('删除任务权限');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('roles');
    }
}
