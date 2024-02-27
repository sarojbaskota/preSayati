<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageList implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $to;
    public $message; // This will be a JSON string

    public function __construct(string $to, array $messageData)
    {
        $this->to = $to;
        $this->message = json_encode($messageData); // Convert the message array to JSON string
    }

    public function broadcastOn()
    {
        return ['list_chat_'.$this->to];
    }

    public function broadcastAs()
    {
        return 'list_message_'.$this->to;
    }
}
