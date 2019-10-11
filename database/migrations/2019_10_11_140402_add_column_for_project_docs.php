<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnForProjectDocs extends Migration
{
    /**
     * Run the migrations.
     *å
     * @return void
     */
    public function up()
    {
        Schema::table('project_docs', function (Blueprint $table) {
            $table->string('doc_name', 1024)->after('file_name');
            $table->unsignedSmallInteger('is_home')->after('file_name');
            $table->smallInteger('status')->after('creator_id');
            $table->unsignedTinyInteger('version')->after('creator_id');
            $table->uuid('update_id')->after('creator_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('project_docs', function (Blueprint $table) {
            $table->dropColumn([
                'parent_id',
                'doc_name',
                'is_home',
                'update_id',
                'status',
                'version'
            ]);
        });
    }
}
