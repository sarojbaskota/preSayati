<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageWithFriend extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'user' => [
                'id' => $this->user->id,
                'avatar' => $this->user->avatar,
                'online' => $this->user->online,
                'full_name' => ucwords($this->user->fname.' '.$this->user->lname),
            ],
            'latest_message' => [
                'id' => $this->latest_message->id,
                'content' => $this->latest_message->content,
                'time' => Carbon::parse($this->latest_message->created_at)->diffForHumans(),
                'from' => $this->latest_message->from,
                'to' => $this->latest_message->to,
            ],
        ];
    }
}
