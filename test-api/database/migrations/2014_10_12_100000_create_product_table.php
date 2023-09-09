<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product', function (Blueprint $table) {
            $table->integer('id_product', true);
            $table->integer('id_user')->index('id_user');
            $table->integer('id_category')->index('id_category');
            $table->string('name_product', 50)->nullable();
            $table->integer('price');
            $table->string('url_logo', 50)->nullable();
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
        Schema::dropIfExists('password_resets');
    }
}
