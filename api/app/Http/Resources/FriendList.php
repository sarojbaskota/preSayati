<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FriendList extends JsonResource
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
            'fullname' =>  ucwords($this->fname.' '.$this->lname),
            'avatar' => $this->avatar,
            'location' => $this->location,
            'online' => ($this->online == 1)?true:false
        ];
        // return parent::toArray($request);
    }
}
