<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order', function (Blueprint $table) {
            $table->integer('id_order', true);
            $table->integer('id_user')->index('id_user');
            $table->string('no_transaction', 50)->index('no_transaction');
            $table->string('name_order', 50)->nullable();
            $table->integer('amount');
            $table->integer('tax');
            $table->integer('service');
            $table->integer('total_amount')->comment('total akhir');
            $table->tinyInteger('payment_method');
            $table->tinyInteger('payment_status')->comment('0. Belum Dibayar, 1. Sudah Dibayar');
            $table->tinyInteger('type_order')->comment('1. Dine In, 2. Take Away');
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
        Schema::dropIfExists('order');
    }
}
