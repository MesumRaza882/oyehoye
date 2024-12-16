<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class ProductReview extends Model
{
    use HasFactory;
    protected $fillable = [
        'prod_id',
        'review',
        'desc',
        'attachment',
        'user_id',
        'cus_name',
        'status',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class,'prod_id','id');
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }
}
