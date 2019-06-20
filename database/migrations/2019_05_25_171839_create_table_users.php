<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->default(null)->comment('唯一id');
            $table->string('phone')->default(null)->comment('登录手机');
            $table->string('email', 256)->default(null)->comment('登录邮箱');
            $table->string('password',256)->default(null)->comment('密码');
            $table->string('nickname')->default(null)->comment('用户名字');
            $table->string('avatar')->default(null)->comment('头像');
            $table->unsignedTinyInteger('is_admin')->default(0)->comment('是否是admin');
            $table->string('token')->default(null)->comment('登录令牌');
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
        Schema::dropIfExists('users');
    }
}
