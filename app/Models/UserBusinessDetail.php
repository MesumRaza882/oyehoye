<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserBusinessDetail extends Model
{
  use HasFactory;
  protected $fillable = [
    'user_id',
    'store_name',
    'store_address',
    'bank_name',
    'account_title',
    'account_number',
  ];
}
