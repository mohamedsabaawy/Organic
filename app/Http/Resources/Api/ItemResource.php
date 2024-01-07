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
            'name'=>$this->name,
            'name_en'=>$this->name_en,
            'details'=>$this->details,
            'details_en'=>$this->details_en,
            'manual'=>$this->manual,
            'manual_en'=>$this->manual_en,
            'price'=>$this->price,
            'discount'=>$this->discount,
            'percent'=>($this->discount/$this->price)*100 . "%",
            'icon'=>$this->icon,
            'available'=>$this->available,
            'production_date'=>$this->production_date,
            'created_at'=>date("Y-m-d",strtotime($this->created_at)),
            'updated_at'=>date("Y-m-d",strtotime($this->updated_at)),
            'test'=>asset('photo/img.png','public'),
        ];
//        return parent::toArray($request);
    }
}
