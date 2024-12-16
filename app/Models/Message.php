<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Message extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id', 'order_id', 'read_at','status','message','days','views',
    ];

    public function notifications()
    {
    return $this->hasMany(MessageNotification::class);
    }

    public function message_read_status()
    {
        return $this->belongsTo(MessageNotification::class);
    }
    
}
