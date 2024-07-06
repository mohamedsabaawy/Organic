<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SpecialItem extends Model
{
    use HasFactory;//,SoftDeletes;

    protected $fillable = ['item_id','photo'];
}
