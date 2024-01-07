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
            'details'=>$this->details,
            'price'=>$this->price,
            'discount'=>$this->discount,
            'percent'=>($this->discount/$this->price)*100 . "%",
            'icon'=>$this->icon,
            'available'=>$this->available,
            'created_at'=>date("Y-m-d",strtotime($this->created_at)),
            'updated_at'=>date("Y-m-d",strtotime($this->updated_at)),
            'test'=>asset('photo/img.png','public'),
        ];
//        return parent::toArray($request);
    }
}
