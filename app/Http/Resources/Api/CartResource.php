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
        return [
            'client_id'=>$this->client_id,
            'item_id'=>$this->item_id,
            'item_name'=>$this->item->name,
            'count'=>$this->count,
            'unit_price'=>$this->item->price,
            'price'=>$this->item->price*$this->count,
        ];
        //return parent::toArray($request);
    }
}
