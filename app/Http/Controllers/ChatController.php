<?php

namespace App\Http\Controllers;

use App\Events\PrivateMessageSent;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index()
    {
        return view('chat', ['authUser' => Auth::user()]);
    }

    public function contacts()
    {
        $me = Auth::id();
        $users = User::where('id', '!=', $me)->orderBy('name')->get();

        $contacts = $users->map(function ($user) use ($me) {
            $lastMessage = Message::where(function ($q) use ($me, $user) {
                $q->where('sender_id', $me)->where('receiver_id', $user->id);
            })->orWhere(function ($q) use ($me, $user) {
                $q->where('sender_id', $user->id)->where('receiver_id', $me);
            })->orderByDesc('created_at')->first();

            $unread = Message::where('sender_id', $user->id)
                ->where('receiver_id', $me)
                ->where('is_read', false)
                ->count();

            $preview = $lastMessage
                ? ($lastMessage->type === 'image' ? '📷 Photo' : $lastMessage->message)
                : null;

            return [
                'id'           => $user->id,
                'name'         => $user->name,
                'email'        => $user->email,
                'last_message' => $preview,
                'last_time'    => $lastMessage?->created_at?->toDateTimeString(),
                'unread'       => $unread,
            ];
        });

        $sorted = $contacts->sortByDesc(fn($c) => $c['last_time'] ?? '0000-00-00')->values();

        return response()->json($sorted);
    }

    public function messages(User $user)
    {
        $me = Auth::id();

        Message::where('sender_id', $user->id)
            ->where('receiver_id', $me)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $messages = Message::where(function ($q) use ($me, $user) {
            $q->where('sender_id', $me)->where('receiver_id', $user->id);
        })->orWhere(function ($q) use ($me, $user) {
            $q->where('sender_id', $user->id)->where('receiver_id', $me);
        })->orderBy('created_at')->get();

        return response()->json($messages->map(function ($m) {
            return [
                'id'          => $m->id,
                'sender_id'   => $m->sender_id,
                'receiver_id' => $m->receiver_id,
                'message'     => $m->message,
                'type'        => $m->type ?? 'text',
                'file_url'    => $m->file_path ? asset('storage/' . $m->file_path) : null,
                'is_read'     => $m->is_read,
                'timestamp'   => $m->created_at->toDateTimeString(),
            ];
        }));
    }

    /**
     * Send a text message.
     */
    public function send(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message'     => 'required|string|max:2000',
        ]);

        $msg = Message::create([
            'sender_id'   => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message'     => $request->message,
            'type'        => 'text',
        ]);

        broadcast(new PrivateMessageSent($msg, Auth::user()->name))->toOthers();

        return response()->json([
            'id'          => $msg->id,
            'sender_id'   => $msg->sender_id,
            'receiver_id' => $msg->receiver_id,
            'message'     => $msg->message,
            'type'        => 'text',
            'file_url'    => null,
            'timestamp'   => $msg->created_at->toDateTimeString(),
        ]);
    }

    /**
     * Send an image message.
     */
    public function sendImage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'image'       => 'required|image|mimes:jpg,jpeg,png,gif,webp|max:5120',
        ]);

        $path = $request->file('image')->store('chat-images', 'public');

        $msg = Message::create([
            'sender_id'   => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message'     => '📷 Photo',
            'type'        => 'image',
            'file_path'   => $path,
        ]);

        broadcast(new PrivateMessageSent($msg, Auth::user()->name))->toOthers();

        return response()->json([
            'id'          => $msg->id,
            'sender_id'   => $msg->sender_id,
            'receiver_id' => $msg->receiver_id,
            'message'     => $msg->message,
            'type'        => 'image',
            'file_url'    => asset('storage/' . $path),
            'timestamp'   => $msg->created_at->toDateTimeString(),
        ]);
    }
}
