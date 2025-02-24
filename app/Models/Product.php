<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'image',
        'seller_name'
    ];

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_name', 'name');
    }
} 