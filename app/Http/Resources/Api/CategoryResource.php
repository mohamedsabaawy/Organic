<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
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
            'photo'=>$this->photo,
            'name'=>$this->name,
            'name_en'=>$this->name_en,
            'items'=>ItemResource::collection($this->items)
        ];
        //return parent::toArray($request);
    }
}
