<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id('PaymentID');
            $table->unsignedBigInteger('OrderID');
            $table->string('PaymentMethod');
            $table->decimal('Amount', 10, 2);
            $table->timestamps();

            $table->foreign('OrderID')->references('OrderID')->on('orders')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }
}




