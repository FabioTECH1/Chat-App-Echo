<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;
    protected $guarded = [
        'id'
    ];

    public function user1()
    {
        return $this->belongsTo(User::class, 'user_1', 'id');
    }
    public function user2()
    {
        return $this->belongsTo(User::class, 'user_2', 'id');
    }

    public function message()
    {
        return $this->hasMany(Message::class);
    }

    public function latest_message()
    {
        return $this->hasOne(Message::class)->latest();
    }

    public function unread_messages()
    {
        return $this->hasMany(Message::class)->where('status', 1)->count();
    }
}