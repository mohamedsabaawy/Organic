<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=>$this->id,
            'name'=>app()->getLocale()=='ar' ? $this->name : $this->name_en,
//            'name_en'=>$this->name_en,
            'details'=>app()->getLocale()=='ar' ? $this->details :$this->details_en,
//            'details_en'=>$this->details_en,
            'manual'=>app()->getLocale()=='ar' ? $this->manual : $this->manual_en,
//            'manual_en'=>$this->manual_en,
            'price'=>$this->price,
            'discount'=>$this->discount,
            'percent'=>(($this->price - $this->discount)/$this->price)*100 . "%",
            'icon'=>asset('photo/'.$this->icon),
            'available'=>$this->available,
            'production_date'=>$this->production_date,
            'special'=>$this->special,
            'created_at'=>date("Y-m-d",strtotime($this->created_at)),
            'updated_at'=>date("Y-m-d",strtotime($this->updated_at)),
            'category'=>[
                'name'=>app()->getLocale()=='ar' ? $this->category->name : $this->category->name_en,
                'id'=>$this->category->id,
            ],
            'photos'=>PhotoResource::collection($this->photos),
//            'test'=>asset('photo/img.png','public'),
        ];
//        return parent::toArray($request);
    }
}
