<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaoInventoryRecord extends Model
{
    use HasFactory;
    protected $fillable = [
        'total_inventory',
        'sale_inventory',
        'return_inventory',
        'minus_inventory',
    ];
}
