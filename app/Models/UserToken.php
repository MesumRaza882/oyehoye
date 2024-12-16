<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class UserToken extends Model
{
    use HasFactory;
    protected $fillable = [
        'device_token',
        'device_id',
        'user_id',
        'admin_id',
    ];

}
