<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use function included\getPrice;

class OfferResource extends JsonResource
{
    /**
     * @var mixed
     */

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=>$this->id,
            'icon'=>asset('photo/'.$this->icon),
            'from'=>$this->from,
            'to'=>$this->to,
            'available'=>$this->available,
            'price'=>getPrice($this,'offer'),
            'is_dollar'=>request()->ipinfo->country == "EG"?0:1,
            'created_at'=>date("Y-m-d",strtotime($this->created_at)),
            'updated_at'=>date("Y-m-d",strtotime($this->updated_at)),
        ];
    }
}
