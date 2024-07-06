<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    use HasFactory;

    protected $fillable = [
        'icon',
        'title',
        'title_en',
        'from',
        'to',
        'available',
        'price',
        'price_dollar',
    ];


    public function Carts(){
        return $this->hasMany(Cart::class);
    }
}
