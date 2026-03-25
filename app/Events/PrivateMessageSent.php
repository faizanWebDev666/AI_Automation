<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PrivateMessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $id;
    public int $sender_id;
    public string $sender_name;
    public int $receiver_id;
    public string $message;
    public string $type;
    public ?string $file_url;
    public string $timestamp;

    public function __construct(Message $msg, string $senderName)
    {
        $this->id = $msg->id;
        $this->sender_id = $msg->sender_id;
        $this->sender_name = $senderName;
        $this->receiver_id = $msg->receiver_id;
        $this->message = $msg->message;
        $this->type = $msg->type ?? 'text';
        $this->file_url = $msg->file_path ? asset('storage/' . $msg->file_path) : null;
        $this->timestamp = $msg->created_at->toDateTimeString();
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('chat.' . $this->receiver_id);
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }
}
