<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use function included\getDiscount;
use function included\getPrice;

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
            $price =  getPrice($this->offer,'offer');
            return [
                'id'=>$this->id,
                'item_id'=>$this->offer_id,
                'item_name'=>app()->getLocale()=='ar' ? $this->offer->title : $this->offer->title_en,
                'icon'=>asset('photo/'.$this->offer->icon),
                'count'=>$this->count,
                'is_offer'=>$this->is_offer,
                'is_dollar' => request()->ipinfo->country == "EG" ?0:1,
                'unit_price'=>$price,
                'price'=>$price*$this->count,
                'created_at'=>date("Y-m-d",strtotime($this->created_at)),
                'updated_at'=>date("Y-m-d",strtotime($this->updated_at)),
            ];
        }
        $price =  getPrice($this->item);
        $discount = getDiscount($this->item);
        return [
            'id'=>$this->id,
            'item_id'=>$this->item_id,
            'item_name'=>app()->getLocale()=='ar' ? $this->item->name : $this->item->name_en,
            'icon'=>asset('photo/'.$this->item->icon),
            'count'=>$this->count,
            'is_offer'=>$this->is_offer,
            'is_dollar' => request()->ipinfo->country == "EG" ?0:1,
            'unit_price'=>$discount > 0 ? $discount:$price ,
            'price'=>($discount > 0 ? $discount:$price)*$this->count,
            'created_at'=>date("Y-m-d",strtotime($this->created_at)),
            'updated_at'=>date("Y-m-d",strtotime($this->updated_at)),
        ];
        //return parent::toArray($request);
    }
}
