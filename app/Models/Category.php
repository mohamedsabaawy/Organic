<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory;
    protected $fillable = ['id','name','name_en','photo'];

    public function items(){
        return $this->hasMany(Item::class);
    }
}
