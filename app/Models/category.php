<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class category extends Model
{
    use HasFactory;
  
    use SoftDeletes;
     protected $fillable = [
        'name','image','is_active_row'
    ];
    
    protected $casts = [
         'status' => 'integer',
    ];

    public function product()
    {
        return $this->hasMany(Product::class)->withTrashed();
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

}
