<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable =[
      'title',
      'title_en',
      'photo',
      'content',
      'content_en',
      'author',
      'author_en',
      'photo',
    ];
}
