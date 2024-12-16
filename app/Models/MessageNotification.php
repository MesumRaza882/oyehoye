<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageNotification extends Model
{
    use HasFactory;
  
    public $timestamps = false;
    protected $fillable = [
        'user_id','mesaage_id','read_at',
    ];
    public function message()
    {
    return $this->belongsTo(Message::class);
    }
}
