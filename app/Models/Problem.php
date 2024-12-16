<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Problem extends Model
{
    use HasFactory;
  
    protected $fillable = [
        'user_id',
        'whatsapp',
        'user_name',
        'comment',
    ];

    public function user()
{
    return $this->belongsTo(User::class, 'user_id', 'id');
}
}


