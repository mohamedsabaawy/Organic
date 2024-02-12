<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FavoriteResource extends JsonResource
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
                'price'=>$this->offer->price,
                'is_offer'=>1,
                'created_at'=>date("Y-m-d",strtotime($this->created_at)),
                'updated_at'=>date("Y-m-d",strtotime($this->updated_at)),
            ];
        }
        return [
            'id'=>$this->id,
            'item_id'=>$this->item_id,
            'item_name'=>app()->getLocale()=='ar' ? $this->item->name : $this->item->name_en,
            'icon'=>asset('photo/'.$this->item->icon),
            'price'=>$this->item->price,
            'discount'=>$this->item->discount,
            'is_offer'=>0,
            'created_at'=>date("Y-m-d",strtotime($this->created_at)),
            'updated_at'=>date("Y-m-d",strtotime($this->updated_at)),
        ];
        //return parent::toArray($request);
    }
}
