<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class SinglePro_Review extends Model
{
    use HasFactory;
    protected $fillable = [
        'prod_id',
        'name',
        'address',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class,'prod_id','id');
    }
}
