<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if ($this->offer_id){
            return [
                'id'=>$this->id,
                'item_id'=>$this->offer_id,
                'item_name'=>app()->getLocale()=='ar' ? $this->offer->title : $this->offer->title_en,
                'icon'=>asset('photo/'.$this->offer->icon),
                'count'=>$this->count,
                'is_offer'=>$this->is_offer,
                'unit_price'=>$this->offer->price,
                'price'=>$this->offer->price*$this->count,
                'created_at'=>date("Y-m-d",strtotime($this->created_at)),
                'updated_at'=>date("Y-m-d",strtotime($this->updated_at)),
            ];
        }
        return [
            'id'=>$this->id,
            'item_id'=>$this->item_id,
            'item_name'=>app()->getLocale()=='ar' ? $this->item->name : $this->item->name_en,
            'icon'=>asset('photo/'.$this->item->icon),
            'count'=>$this->count,
            'is_offer'=>$this->is_offer,
            'unit_price'=>$this->item->price,
            'price'=>$this->item->price*$this->count,
            'created_at'=>date("Y-m-d",strtotime($this->created_at)),
            'updated_at'=>date("Y-m-d",strtotime($this->updated_at)),
        ];
        //return parent::toArray($request);
    }
}
