<?php

namespace App\Http\Controllers;

use App\Events\MessageRead;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index($id)
    {
        Message::where('incoming_id', auth()->id())->update([
            'status' => 2
        ]);
        event(new MessageRead(auth()->id(), $id));
        // event(new MessageRead(auth()->id()));
        $convo =  Conversation::Where(function ($query) use ($id) {
            $query->where("user_1", $id)
                ->where("user_2", auth()->user()->id);
        })
            ->orWhere(function ($query) use ($id) {
                $query->Where("user_1", auth()->user()->id)
                    ->Where("user_2", $id);
            })
            ->first();
        $messages = [];
        if ($convo) {
            $messages = $convo->message;
        }
        // $last_seen = Carbon::parse($user->last_seen)->diffForHumans();
        $user = User::where('id', $id)->first();
        return view('chats', compact('user', 'messages'));
    }



    public function search(Request $request)
    {
        //search query
        $users =  User::Where(function ($query) use ($request) {
            $query->where('fname', 'LIKE', '%' . $request->search . '%')
                ->orWhere('lname', 'LIKE', '%' . $request->search . '%');
        })
            ->where(function ($query) {
                $query->Where("id", '!=', auth()->user()->id);
            })
            ->get();

        $output = '';
        if ($users->count() > 0) {
            foreach ($users as $user) {
                $url = asset('uploads/' . $user->profile_pic);
                $href = route('convo', $user->id);
                // if ($user->id != auth()->user()->id) {
                //get last message
                $messages =  Conversation::Where(function ($query) use ($user) {
                    $query->where("incoming_id", $user->id)
                        ->where("outgoing_id", auth()->user()->id);
                })
                    ->orWhere(function ($query) use ($user) {
                        $query->Where("incoming_id", auth()->user()->id)
                            ->Where("outgoing_id", $user->id);
                    })
                    ->latest()->first();
                if ($messages) {
                    $message = $messages->message;
                    if (strlen($message) > 28) {
                        $message = substr($message, 0, 26) . '....';
                    }
                    if (auth()->user()->id == $messages->outgoing_id) {
                        $message = 'You: ' . $message;
                    }
                } else {
                    $message = 'No message available';
                }

                // //get status of sent message
                $msg_count = '';
                $status = '';
                $message_status = Conversation::where('outgoing_id', auth()->user()->id)->latest()->first();
                if ($message_status) {
                    if ($message_status->outgoing_id == $messages->outgoing_id) {
                        if ($message_status->status == 0) {
                            $status = '<span class="iconify" data-icon="bi:check"></span>';
                        } elseif ($message_status->status == 1) {
                            $status = '<span class="iconify" data-icon="bi:check-all"></span>';
                        } else {
                            $status = '<span class="iconify" data-icon="bi:check-all" style="color: blue;"></span>';
                        }
                    } else {
                        $msg =  Conversation::where('incoming_id', auth()->user()->id)->where('outgoing_id', $user->id)->where('status', 1)->get();
                        if ($msg->count() > 0) {
                            $msg_count =  '<span class="badge bg-primary badge-number ms-end" style="font-size:11px;margin-left:50px;">' . $msg->count() . ' </span>';
                        }
                    }
                }


                $user->status == 'Offline' ? $offline = ' offline' : $offline = '';
                $output .= '
                <a href="' . $href . '">
                    <div class="content">
                    <img src="' . $url . '">
                    <div class="details">
                        <span>' . $user->fname . " " . $user->lname . '</span>
                        <p>' . $message . $status . $msg_count . '</p>
                    </div>
                    </div>
                    <div class="status-dot' . $offline . '"><i class="fas fa-circle"></i></div>
                </a>';
            }
        } else {
            $output .= 'No user found related to your search term';
        }
        return $output;
    }
}