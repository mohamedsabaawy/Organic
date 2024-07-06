<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $items = [];
        if (count($this->items) > 0){
            foreach ($this->items as $item){
                $items[]=[
                    'item_id'=>  $item->id,
                    'offer_id'=>  null,
                    'name'=>app()->getLocale()=='ar' ? $item->name : $item->name_en,
                    'icon'=>  asset('photo/'.$item->icon),
                    'count'=>  $item->pivot->count,
                    'price'=>  $item->pivot->price,
                    'is_offer'=>  0,
            ];
            }
        }
        if (count($this->offers) > 0){
            foreach ($this->offers as $offer){
                $items[]=[
                    'item_id'=>  null,
                    'offer_id'=>  $offer->id,
                    'name'=>  null,
                    'icon'=>  asset('photo/'.$offer->icon),
                    'count'=>  $offer->pivot->count,
                    'price'=>  $offer->pivot->price,
                    'is_offer'=>  1,
            ];
            }
        }
        return [
            'id' => $this->id,
            'price' => $this->price,
            'is_dollar' => $this->is_dollar,
            'delivery_price' => $this->delivery_price,
            'total'=>$this->delivery_price+$this->price,
            'payment_type' => $this->payment_type,
            'payment_code' => $this->payment_code,
            'payed_amount' => $this->amount,
            'status' => $this->status,
            'created_at'=>date("Y-m-d",strtotime($this->created_at)),
            'updated_at'=>date("Y-m-d",strtotime($this->updated_at)),
            'items'=>$items,
            'client'=>[
                'id' => $this->client->id,
                'name' => $this->client->name,
                'email' => $this->client->email,
                'phone' => $this->client->phone,
                'role' => $this->client->role,
                'created_at'=>date("Y-m-d",strtotime($this->client->created_at)),
                'updated_at'=>date("Y-m-d",strtotime($this->client->updated_at)),
            ],
            'invoice_status'=>InvoiceStatusResource::collection($this->invoiceStatuses),
            'is_canceled'=>$this->is_canceled,
            'address'=>AddressResource::make($this->address),
        ];
    }
}
