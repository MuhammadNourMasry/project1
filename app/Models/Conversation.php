<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'owner_id',
        'apartment_id',
        'last_message_at',
        'status'
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    public function tenant()
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function apartment()
    {
        return $this->belongsTo(Apartment::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class)->orderBy('created_at', 'asc');
    }

    public function latestMessage()
    {
        return $this->hasOne(Message::class)->latest();
    }

    public function unreadMessagesCount($userId)
    {
        return $this->messages()
            ->where('receiver_id', $userId)
            ->whereNull('read_at')
            ->count();
    }
}