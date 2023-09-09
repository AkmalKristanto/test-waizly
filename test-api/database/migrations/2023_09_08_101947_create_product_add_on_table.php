<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductAddOnTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_add_on', function (Blueprint $table) {
            $table->integer('id_product_add_on', true);
            $table->integer('id_product')->index('id_product');
            $table->string('name_product_add_on', 50)->nullable();
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
        Schema::dropIfExists('product_add_on');
    }
}
