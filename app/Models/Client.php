<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Tymon\JWTAuth\Contracts\Providers\JWT;

class Client extends Authenticatable implements JWTSubject
{
    use  HasApiTokens, HasFactory, Notifiable;//,SoftDeletes;

    protected $fillable =[
        'name','email','phone','password','lang','role'
    ];
    protected $hidden=['password'];

    public function getJWTIdentifier()
    {
        return $this->getKey();
        // TODO: Implement getJWTIdentifier() method.
    }

    public function getJWTCustomClaims()
    {
        return [];
        // TODO: Implement getJWTCustomClaims() method.
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function favorites(){
        return $this->belongsToMany(Item::class,'favorites');
    }

    public function offerFavorites(){
        return $this->belongsToMany(Offer::class,'favorites');
    }

    public function invoices(){
        return $this->hasMany(Invoice::class);
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }
}
