<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Hint extends Model
{
    use HasFactory;//,SoftDeletes;

    protected $fillable =[
        'content',
        'content_en',
        'author',
        'author_en',
        'author_job',
        'author_job_en',
        'photo',
    ];
}
