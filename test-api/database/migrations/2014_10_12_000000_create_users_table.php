<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->integer('id_user', true);
            $table->string('token_user', 1000)->nullable();
            $table->string('username');
            $table->string('email')->unique();
            $table->string('phone', 20)->nullable()->index('phone');
            $table->string('password');
            $table->string('url_img', 100)->nullable();
            $table->timestamp('last_login')->nullable();
            $table->timestamp('first_login')->nullable();
            $table->boolean('status_active')->nullable()->default(true)->index('status_active');
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
