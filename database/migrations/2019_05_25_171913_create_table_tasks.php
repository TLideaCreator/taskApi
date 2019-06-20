<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableTasks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->uuid('id')->default(null)->comment('id');
            $table->string('title', 128)->default(null)->comment('标题');
            $table->string('desc', 1024)->default(null)->comment('描述');
            $table->uuid('exe_id')->default(null)->comment('执行者id');
            $table->uuid('report_id')->default(null)->comment('监管者id');
            $table->tinyInteger('priority')->default(2)->comment('优先级');
            $table->string('status')->default(null)->comment('状态id');
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
        Schema::dropIfExists('tasks');
    }
}
