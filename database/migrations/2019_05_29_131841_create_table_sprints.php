<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableSprints extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sprints', function (Blueprint $table) {
            $table->uuid('id')->default('')->comment('冲刺id');
            $table->unsignedInteger('name_index')->default(0)->comment('冲刺');
            $table->uuid('project_id')->default('')->comment('所属项目id');
            $table->unsignedInteger('status')->default(0)->comment('冲刺项目状态');
            $table->unsignedBigInteger('start_time')->nullable()->comment('开始时间');
            $table->unsignedBigInteger('end_time')->nullable()->comment('结束时间');
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
        Schema::dropIfExists('sprints');
    }
}
