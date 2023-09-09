<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_product', function (Blueprint $table) {
            $table->integer('id_order_product', true);
            $table->integer('id_order')->index('id_order');
            $table->integer('id_product')->index('id_product');
            $table->integer('id_product_add_on')->index('id_product_add_on');
            $table->integer('qty');
            $table->string('notes', 50)->nullable();
            $table->integer('amount');
            $table->integer('total_amount')->comment('total akhir');
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
        Schema::dropIfExists('order_product');
    }
}
