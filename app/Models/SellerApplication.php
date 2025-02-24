<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellerApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'address',
        'status',
        'notes',
        'reviewed_at',
        'password'
    ];

    protected $hidden = [
        'password'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
