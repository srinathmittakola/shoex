<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'brand',
        'type',
        'description',
        'shoe_for',
        'mrp',
        'actual_price',
        'stock',
        'image_paths',
    ];
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
    public function wishedByCustomers()
    {
        return $this->belongsToMany(Customer::class, 'wishlists');
    }
    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

}
