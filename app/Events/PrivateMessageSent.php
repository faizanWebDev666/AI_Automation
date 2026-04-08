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
    public ?int $property_id;
    public ?array $property_context;
    public string $message;
    public string $type;
    public ?string $file_url;
    public ?int $reply_to_message_id;
    public ?string $reply_to_message;
    public ?int $forwarded_from_message_id;
    public string $timestamp;

    public function __construct(Message $msg, string $senderName, ?array $propertyContext = null)
    {
        $this->id = $msg->id;
        $this->sender_id = $msg->sender_id;
        $this->sender_name = $senderName;
        $this->receiver_id = $msg->receiver_id;
        $this->property_id = $msg->property_id;
        $this->property_context = $propertyContext;
        $this->message = $msg->message;
        $this->type = $msg->type ?? 'text';
        $this->file_url = $msg->file_path ? asset('storage/' . $msg->file_path) : null;
        $this->timestamp = $msg->created_at->toDateTimeString();
        $this->reply_to_message_id = $msg->reply_to_message_id;
        $this->reply_to_message = $msg->reply_to_message;
        $this->forwarded_from_message_id = $msg->forwarded_from_message_id;
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
