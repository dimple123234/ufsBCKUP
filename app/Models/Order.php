<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';
    protected $primaryKey = 'OrderID';
    
    protected $fillable = [
        'CustomerID',
        'employee_id',
        'OrderDate'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'CustomerID', 'customer_id');
    }
    
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }
    
    protected static function boot()
    {
        parent::boot();
        
        static::deleting(function($order) {
            $order->orderDetails()->delete();
        });
    }
    
    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'OrderID', 'OrderID');
    }
}