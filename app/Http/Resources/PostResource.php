<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
//        return parent::toArray($request);
        $auth_id = 3;
//        if ($request->file('feature_image'))

        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'excerpt' => $this->excerpt,
            'slug' => $this->slug,
            'category' => $this->category->title,
            'username' => $this->user->name,
            'created_at' => $this->created_at->diffForHumans(),
            'updated_at' => $this->updated_at->format('d-M-Y'),
            'feature_image' =>  $this->feature_image != null ? asset("storage/".$this->user_id."/feature_image/".$this->feature_image) : $this->feature_image,
//            'category' => new CategoryResource($this->category),
//            'user' => new UserResource($this->user),
        ];
    }
}
