<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'city_name',
        'city_id',
        'courier_phone',
        'country',
        'phone',
        'whatsapp',
        'address',
        'address',
        'password',
        'status',
        // 'remember_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'deleted_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

     //city
     public function city()
     {
         return $this->hasOne(City::class,'id','city_id');
     }
     public function bussiness_detail()
     {
         return $this->hasOne(UserBusinessDetail::class,'user_id','id');
     }
      //order
     public function order()
     {
         return $this->hasMany(Order::class);
     }
     //admin
     public function admin()
     {
         return $this->belongsTo(Admin::class);
     }

    // scope functions
    public function scopeWhereIf($q, $col, $operator, $val)
    {
        if($val){
            $q->Where($col, $operator, $val);
        }
        return $q;
    }
    // scope functions
    public function scopeOrWhereIf($q, $col, $operator, $val)
    {
        if($val){
            $q->orWhere($col, $operator, $val);
        }
        return $q;
    }
    public function scopefilterByDate($q, $startdate, $enddate)
    {
        if($startdate && $enddate){
            $incEndDate =  date('Y', strtotime($enddate)).'-'.date('m', strtotime($enddate)).'-'.date('d', strtotime($enddate))+1;
            $q->whereBetween('created_at', [$startdate, $incEndDate]);
        }
        else if($startdate)
        {
            $q->whereDate('created_at', $startdate);
        }
        return $q;
    }

    public function scopeWhereFilterReseller($q, $is_resller)
    {
        if($is_resller == -1){
            return $q;
        }

        if($is_resller == 1){
            return $q->where('is_reseller', 1);
        }

        return $q->where('is_reseller', 0);

    }

}
