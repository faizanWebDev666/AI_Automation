<?php

namespace App\Http\Controllers;

use App\Events\PrivateMessageSent;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    /**
     * Show the chat page.
     */
    public function index()
    {
        return view('chat', [
            'authUser' => Auth::user(),
        ]);
    }

    /**
     * Return all users except current, with last message preview and unread count.
     */
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

            return [
                'id'           => $user->id,
                'name'         => $user->name,
                'email'        => $user->email,
                'last_message' => $lastMessage?->message,
                'last_time'    => $lastMessage?->created_at?->toDateTimeString(),
                'unread'       => $unread,
            ];
        });

        // Sort by last message time (most recent first), users with no messages at the end
        $sorted = $contacts->sortByDesc(function ($c) {
            return $c['last_time'] ?? '0000-00-00';
        })->values();

        return response()->json($sorted);
    }

    /**
     * Return messages between auth user and a specific user.
     */
    public function messages(User $user)
    {
        $me = Auth::id();

        // Mark incoming messages as read
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
                'is_read'     => $m->is_read,
                'timestamp'   => $m->created_at->toDateTimeString(),
            ];
        }));
    }

    /**
     * Send a message to another user.
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
        ]);

        broadcast(new PrivateMessageSent($msg, Auth::user()->name))->toOthers();

        return response()->json([
            'id'          => $msg->id,
            'sender_id'   => $msg->sender_id,
            'receiver_id' => $msg->receiver_id,
            'message'     => $msg->message,
            'timestamp'   => $msg->created_at->toDateTimeString(),
        ]);
    }
}
