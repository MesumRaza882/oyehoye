<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LockedkFolderPassword extends Model
{
    use HasFactory;
    protected $fillable = [
        'reset_orders_date',
    ];
}
