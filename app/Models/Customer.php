<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'customers';
    protected $primaryKey = 'customer_id';
    
    protected $fillable = [
        'name',
        'phone',
        'address',
        'payment',
        'card'
    ];

    public function orders()
    {
        return $this->hasMany(Order::class, 'CustomerID', 'customer_id');
    }

    protected static function boot()
    {
        parent::boot();
        
        static::deleting(function($customer) {
            $customer->orders->each(function($order) {
                $order->orderDetails()->delete();
                $order->delete();
            });
        });
    }
}