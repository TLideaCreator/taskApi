<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableProjects extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->uuid('id')->default(null);
            $table->string('name')->default(null)->comment('标题');
            $table->string('icon')->default(null)->comment('项目icon');
            $table->string('desc', 2048)->nullable()->comment('项目icon');
            $table->tinyInteger('status')->default(0)->comment('项目状态');
            $table->uuid('creator_id')->default(null)->comment('创建者');
            $table->uuid('cur_sprint_id')->default(null)->comment('当前冲刺id');
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
        Schema::dropIfExists('projects');
    }
}
