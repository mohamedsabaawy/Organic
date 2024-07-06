<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;
use function included\getDiscount;
use function included\getPrice;

class ItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $price =  getPrice($this);
        $discount = getDiscount($this);
        return [
            'id'=>$this->id,
            'name'=>app()->getLocale()=='ar' ? $this->name : $this->name_en,
            'details'=>app()->getLocale()=='ar' ? $this->details :$this->details_en,
            'manual'=>app()->getLocale()=='ar' ? $this->manual : $this->manual_en,
            'price'=>$price,
            'discount'=>$discount,
            'percent'=>number_format((($price - $discount)/($price > 0 ?$price:1))*100,1) . "%",
            'icon'=>asset('photo/'.$this->icon),
            'available'=>$this->available,
            'production_date'=>$this->production_date,
            'special'=>$this->special,
            'created_at'=>date("Y-m-d",strtotime($this->created_at)),
            'updated_at'=>date("Y-m-d",strtotime($this->updated_at)),
            'is_dollar'=> request()->ipinfo->country == "EG"? 0 : 1,
            'category'=>[
                'name'=>app()->getLocale()=='ar' ? $this->category->name : $this->category->name_en,
                'id'=>$this->category->id,
            ],
            'photos'=>PhotoResource::collection($this->photos),
        ];
//        return parent::toArray($request);
    }
}
