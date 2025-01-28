<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrackPlatformSetting extends Model
{
    use HasFactory;
    protected $fillable = ['postEx_pickupAddressCode'];
}
