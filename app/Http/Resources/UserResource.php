<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
//        $avatar_path = '/public/'.$this->id.'/avatar/'.$post->feature_image;
        $auth_id = $this->id;

        $avatar_path =
            $this->avatar ? asset('storage/'.$auth_id.'/avatar/'.$this->avatar)
            : '/storage/default_avatar/avatar.png';

        Log::debug($avatar_path);
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'email' => $this->email,
            'avatar' => asset($this->avatar),
//            'profile' => [
//                'avatar' => $avatar_path,
//            ],
        ];
    }
}
