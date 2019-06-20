<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableSystemTaskPriorities extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('system_task_priorities', function (Blueprint $table) {
            $table->uuid('id')->default('')->comment('id');
            $table->uuid('temp_id')->default('')->comment('项目id');
            $table->string('name')->default('')->comment('优先级名称');
            $table->string('color')->default('')->comment('优先级颜色');
            $table->unsignedTinyInteger('is_default')->default(0)->comment('默认状态');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('system_task_priorities');
    }
}
