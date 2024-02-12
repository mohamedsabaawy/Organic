<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = ['price','payment_type','status','payment_code','amount','address_id'];

    public function items(){
        return $this->belongsToMany(Item::class)->withPivot('count','price');
    }
    public function offers(){
        return $this->belongsToMany(Offer::class,'invoice_item')->withPivot('count','price');
    }

    public function client (){
        return $this->belongsTo(Client::class);
    }

    public function address (){
        return $this->belongsTo(Address::class);
    }

    public function invoiceStatuses (){
        return $this->hasMany(InvoiceStatus::class);
    }

}
