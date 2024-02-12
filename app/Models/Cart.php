<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cart extends Model
{
    use HasFactory;//SoftDeletes;
    protected $fillable=[
        'client_id',
        'item_id',
        'count',
        'offer_id',
        'is_offer',
    ];


    public function item(){
        return $this->belongsTo(Item::class);
    }

    public function offer(){
        return $this->belongsTo(Offer::class);
    }

}
