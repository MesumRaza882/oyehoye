<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\GlobalScopesTrait;

class Order extends Model
{
    use HasFactory,GlobalScopesTrait;

    // type 2 means website 1 for app

    /**
     * Types of statuses
     * Attempted
     * Re-Attempted
     * Advice-Added
     * CANCEL
     * 
     */
    // is_warehouseTeam_order for reseller make order and will dipatch by admin
    // function getCreatedAtAttribute($date)
    // {   
    //     return \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('Y-m-d');
    // }
    // is_returned_order defualt 0 when returned then 1 and confirmed to 2
    protected $fillable = [
        'user_id','wao_seller_id','admin_id',
        'name','tracking_order_type','postex_api_type',
        'phone',
        'address','courier_tracking_id',
        'country','status',
        'city','city_id',
        'amount','description',
        'charges',
        'grandTotal',
        'grandProfit',
        'order_discount','track_created_at',
        'date','time','is_warehouseTeam_order',
        'is_returned_order','adjustment_note','payment_screenshot',
        'is_commission_paid','commission_paid_note','cancel_note',
    ];

    protected $casts = [
        'city_id' => 'string',
        'order_discount' => 'string',
        'is_active_row' => 'string',
        'is_blocked_customer_order' => 'string',
        'courier_tracking_id' => 'string',
    ];

    public function orderitems()
    {
        return $this->hasMany(Order_item::class);
    }
    public function notes()
    {
        return $this->hasMany(Note::class);
    }
    public function message()
    {
        return $this->hasMany(message::class);
    }
    public function history()
    {
        return $this->hasMany(OrderHistory::class);
    }
    public function userdetail()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }
    public function waoSellerDetail()
    {
        return $this->belongsTo(Admin::class,'wao_seller_id','id')->withTrashed();
    }
    public function waoAdminDetail()
    {
        return $this->belongsTo(Admin::class,'admin_id','id')->withTrashed();
    }
    public function citydetail()
    {
        return $this->belongsTo(City::class,'city_id','id');
    }


    public function scopefilterBlockOrders($q, $val)
    {
        if($val == 1)
        {
            $q->where('is_blocked_customer_order', $val);
        }
        else
        {
            $q->where('is_blocked_customer_order', 0);
        }
    }

    public function scopeFilterByOrderStatus($query, $orderStatus)
    {
        if($orderStatus){
            return $query->whereHas('history', function ($subquery) use ($orderStatus) {
                $subquery->where('history', $orderStatus);
            });
        }
        return $query;
    }
}
