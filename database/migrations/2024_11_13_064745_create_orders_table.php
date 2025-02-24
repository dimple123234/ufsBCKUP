<?php

// database/migrations/xxxx_xx_xx_create_orders_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id('OrderID');
            $table->foreignId('CustomerID')->constrained('customers', 'customer_id');
            $table->foreignId('employee_id')->nullable()->constrained('employees', 'employee_id');
            $table->dateTime('OrderDate');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

