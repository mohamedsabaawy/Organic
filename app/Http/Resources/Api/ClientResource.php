<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'role' => $this->role,
            'is_dollar' => request()->ipinfo->country == "EG" ?0:1,
            'created_at'=>date("Y-m-d",strtotime($this->created_at)),
            'updated_at'=>date("Y-m-d",strtotime($this->updated_at)),
            'addresses'=>AddressResource::collection($this->addresses),
        ];
    }
}
