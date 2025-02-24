<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected $table = 'orderdetails';  // Match your migration table name

    protected $fillable = [
        'OrderID',
        'product_id',
        'product_name',
        'quantity',
        'price',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'OrderID', 'OrderID');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
} 