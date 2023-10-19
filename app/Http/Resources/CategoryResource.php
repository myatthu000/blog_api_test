<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return
//            parent::toArray($request);
            [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'uuid' => $this->uuid,
            'user' => new UserResource($this->user),
            'created_at' => $this->created_at->diffForHumans(),
            'updated_at' => $this->updated_at->format('d-M-Y'),
        ];
    }
}
