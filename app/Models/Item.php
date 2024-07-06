<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use HasFactory;

    //, SoftDeletes;

    protected $fillable = [
        'name',
        'name_en',
        'details',
        'details_en',
        'icon',
        'available',
        'price',
        'price_dollar',
        'discount',
        'discount_dollar',
        'category_id',
        'manual',
        'manual_en',
        'production_date',
        'special',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function photos()
    {
        return $this->hasMany(Photo::class);
    }

    public function favorites()
    {
        return $this->belongsToMany(Client::class, 'favorites');
    }

    public function invoices()
    {
        return $this->belongsToMany(Invoice::class);
    }

//    public function getPriceAttribute()
//    {
//        if (request()->ipinfo->country == "EG")
//            return $this->price;
//        return $this->price_dollar;
//    }
//
//    public function getDiscountAttribute()
//    {
//        if (request()->ipinfo->country == "EG")
//            return $this->discount;
//        return $this->discount_dollar;
//    }
}
