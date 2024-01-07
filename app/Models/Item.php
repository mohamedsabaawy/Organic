<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'name_en',
        'details',
        'details_en',
        'icon',
        'available',
        'price',
        'discount',
        'category_id',
        'manual',
        'manual_en',
        'production_date',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
