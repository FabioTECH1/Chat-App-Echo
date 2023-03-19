<?php

namespace App\Http\Controllers;

use App\Events\MessageRead;
use App\Events\MessageReceived;
use App\Events\MessageSent;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function message($id, Request $request)
    {
        $request->validate([
            'message' => 'required'
        ]);
        // check if convo exists
        $convo =  Conversation::Where(function ($query) use ($id) {
            $query->Where("user_1", $id)
                ->where("user_2", auth()->user()->id);
        })
            ->orWhere(function ($query) use ($id) {
                $query->Where("user_1", auth()->user()->id)
                    ->Where("user_2", $id);
            })
            ->latest()->firstorCreate([
                'user_1' => auth()->user()->id,
                'user_2' => $id
            ]);
        $convo->update([
            'updated_at' => now()
        ]);

        //send message 
        $message = $convo->message()->create([
            'message' => $request->message,
            'incoming_id' => $id,
            'outgoing_id' => auth()->user()->id
        ]);
        $time_sent = date('g:i a', strtotime($message->created_at));
        event(new MessageSent($message, $time_sent));
        return (["status" => 201, 'timestamp' => $time_sent, $message]);
    }


    public function markMessageRead(Request $request)
    {
        $request->validate([
            'convo_id' => ['required'],
            'id' => ['required']
        ]);
        Message::where('conversation_id', $request->convo_id)
            ->whereIn('status', [0, 1])
            ->update(['status' => 2]);

        event(new MessageRead(auth()->id(), $request->id));
    }

    public function markMessageReceived(Request $request)
    {
        $request->validate([
            'convo_id' => ['required'],
            'id' => ['required']
        ]);
        Message::where('conversation_id', $request->convo_id)->where('status', 0)
            ->update(['status' => 1]);
        event(new MessageReceived(auth()->id(), $request->id));
    }
}