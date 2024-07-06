<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
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
            'photo'=>$this->photo ? asset('photo/'.$this->photo) : null,
//            'title'=>app()->getLocale() == "ar" ? $this->title :$this->title_en,
//            'content'=>app()->getLocale() == "ar" ? $this->content :$this->content_en,
//            'author'=>app()->getLocale() == "ar" ? $this->author :$this->author_en,
            'created_at'=>date("Y-m-d",strtotime($this->created_at)),
            'updated_at'=>date("Y-m-d",strtotime($this->updated_at)),
        ];
    }
}
