<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orderdetails', function (Blueprint $table) {
            $table->id(); // Primary key for order_details
            $table->foreignId('OrderID')->constrained('orders', 'OrderID'); // Match the OrderID in orders table
            $table->foreignId('product_id')->constrained('products', 'id'); // Match the id in products table
            $table->string('product_name'); // Column to store the product name from products table
            $table->integer('quantity');
            $table->decimal('price', 8, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_details');
    }
};
