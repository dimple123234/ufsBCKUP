<?php

// database/migrations/xxxx_xx_xx_create_customers_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id('customer_id'); // This creates an unsignedBigInteger by default
            $table->string('name');
            $table->string('phone');
            $table->string('address');
            $table->string('payment');
            $table->string('card');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};

