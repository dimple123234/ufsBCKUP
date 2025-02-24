<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Drop existing foreign key
            $table->dropForeign(['CustomerID']);
            
            // Add new foreign key with cascade delete
            $table->foreign('CustomerID')
                  ->references('customer_id')
                  ->on('customers')
                  ->onDelete('cascade');
        });

        Schema::table('orderdetails', function (Blueprint $table) {
            // Drop existing foreign key
            $table->dropForeign(['OrderID']);
            
            // Add new foreign key with cascade delete
            $table->foreign('OrderID')
                  ->references('OrderID')
                  ->on('orders')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['CustomerID']);
            $table->foreign('CustomerID')
                  ->references('customer_id')
                  ->on('customers');
        });

        Schema::table('orderdetails', function (Blueprint $table) {
            $table->dropForeign(['OrderID']);
            $table->foreign('OrderID')
                  ->references('OrderID')
                  ->on('orders');
        });
    }
};
