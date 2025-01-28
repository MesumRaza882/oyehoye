<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\GlobalScopesTrait;

class Admin extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable,
    HasRoles,GlobalScopesTrait,
    SoftDeletes;

    protected $dates = ['deleted_at'];
    // role 1 for Super admin(id is 1 means super admin otherwise as a manager) 
    // role 3 for wao reseller
    // role 4 for team-member of partner controlled

    // is_partner === 1 means reseller but as a partner

    // type 3 means for reseller has app
    // mute_video 1 for mute 2 for not


    protected $fillable = [
        'name','status','is_partner', 'email', 'password','byc_password','trax_allow','mnp_alllow','postEx_allow','mnp_username','mnp_password','locationID','mute_video',
         'role','postEx_pickupAddressCode','postEx_apiToken','trax_pickup_address_id','trax_api_key','product_upload_status','balance',
         'postEx_apiToken_nowshera','postEx_pickupAddressCode_nowshera','color_1','color_1','color_2','color_3','color_4','color_5','logo','website','whatsapp_number',
         'controlled_by_admin','restrictBalance','isRestrictBalance',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function sellerOrders()
    {
        return $this->hasMany(Order::class,'wao_seller_id','id');
    }

    public function adminOrders()
    {
        return $this->hasMany(Order::class,'admin_id','id');
    }

    public function users()
    {
        return $this->hasMany(User::class,'admin_id','id');
    }

    public function resellerAmountHistories()
    {
        return $this->hasMany(ResellerAmountHistory::class);
    }

    // scope functions
    // public function scopeWhereIf($q, $col, $operator, $val)
    // {
    //     if ($val) {
    //         $q->Where($col, $operator, $val);
    //     }
    //     return $q;
    // }

    // // scope functions
    // public function scopeOrWhereIf($q, $col, $operator, $val)
    // {
    //     if ($val) {
    //         $q->orwhere($col, $operator, $val);
    //     }
    //     return $q;
    // }

}
