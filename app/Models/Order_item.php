<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order_item extends Model
{
    use HasFactory;
  
    protected $fillable = [
        'order_id',
        'prod_id',
        'qty',
        'is_dc_free',
        'price',
        'purchase',
        'profit',
        'reseller_profit',
        'discount',
        'order_status',
    ];

    protected $casts = [
        'purchase' => 'string',
        'profit' => 'string',
        'is_dc_free' => 'string',
    ];


    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    
    public function product()
    {
        return $this->belongsTo(Product::class,'prod_id','id')->withTrashed();
    }
}
