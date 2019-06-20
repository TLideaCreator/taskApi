<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumForTasks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->uuid('sprint_id')->nullable()->after('id')->comment('冲刺id');
            $table->uuid('project_id')->nullable()->after('id')->comment('项目id');
            $table->float('points')->default(0.0)->after('desc')->comment('任务点数');
            $table->unsignedTinyInteger('type')->default(0)->after('desc')->comment('任务类型');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn(['sprint_id', 'project_id', 'points']);
        });
    }
}
