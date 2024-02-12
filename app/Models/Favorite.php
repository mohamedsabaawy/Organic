<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use HasFactory;
    protected $fillable =['client_ic','item_id','offer_id'];

    public function item(){
        return $this->belongsTo(Item::class);
    }

    public function offer(){
        return $this->belongsTo(Offer::class);
    }

}
