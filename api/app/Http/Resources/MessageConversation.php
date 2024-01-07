<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageConversation extends JsonResource
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
            'content' => $this->content, 50,
            'time' => Carbon::parse($this->created_at)->diffForHumans(),

            'sender_id' => $this->sender->id,
            'sender_fullname' =>  ucwords($this->sender->fname.' '.$this->sender->lname),
            'sender_avatar' => $this->sender->avatar,
            'sender_online' => $this->sender->online,
            'sender_avatar' => $this->sender->avatar,

            'receiver_id' => $this->receiver->id,
            'receiver_fullname' =>  ucwords($this->receiver->fname.' '.$this->receiver->lname),
            'receiver_avatar' => $this->receiver->avatar,
            'receiver_online' => $this->receiver->online,
            'receiver_avatar' => $this->receiver->avatar,
        ];
    }
}
