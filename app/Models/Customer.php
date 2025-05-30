<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
class Customer extends Authenticatable
{
    use HasFactory;
    protected $fillable = ['name', 'email', 'password', 'address', 'phone', 'city', 'state', 'postal_code'];
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function wishedProducts()
    {
        return $this->belongsToMany(Product::class, 'wishlists');
    }

}
