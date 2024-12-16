<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;
  
    public function user()
    {
        return $this->belongsTo(User::class,'id','city_id');
    }
     // scope functions
     public function scopeWhereIf($q, $col, $operator, $val)
     {
         if($val){
             $q->Where($col, $operator, $val);
         }
         return $q;
     }
}
