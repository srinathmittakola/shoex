<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = ['codename', 'code', 'discount', 'start_date', 'end_date'];
}
