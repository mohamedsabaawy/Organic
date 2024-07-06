<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HintResource extends JsonResource
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
//            'content'=>app()->getLocale() == "ar" ? $this->content :$this->content_en,
//            'author'=>app()->getLocale() == "ar" ? $this->author :$this->author_en,
//            'author_job'=>app()->getLocale() == "ar" ? $this->author_job :$this->author_job_en,
            'photo'=>$this->photo ? asset('photo/'.$this->photo) : null,
            'created_at'=>date("Y-m-d",strtotime($this->created_at)),
            'updated_at'=>date("Y-m-d",strtotime($this->updated_at)),
        ];
    }
}
