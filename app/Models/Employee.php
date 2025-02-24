<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $table = 'employees';
    protected $primaryKey = 'employee_id';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone_number',
        'notes'
    ];

    // Relationship with orders
    public function orders()
    {
        return $this->hasMany(Order::class, 'employee_id', 'employee_id');
    }
}
