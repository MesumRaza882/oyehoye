<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\GlobalScopesTrait;

class ResellerAmountHistory extends Model
{
    use HasFactory, GlobalScopesTrait;

    protected $fillable = [
        'date',
        'admin_id',
        'balance',
        'status',
        'order_id',
        'note'
    ];
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id', 'id')->withTrashed();
    }


    public function scopefilterByDateRange($query, $startdate, $enddate)
    {
        if ($startdate && $enddate) {
            // Filter between both dates
            $query->whereBetween('date', [$startdate, $enddate]);
        } elseif ($startdate || $enddate) {
            // If only one of the dates is provided, filter by one day
            $date = $startdate ?? $enddate;
            $query->whereDate('date', '=', $date);
        }
        return $query;
    }

    public function scopeFilterByAdmin($query)
    {
        $user = auth()->user();
        // if there is not manager/super admin then only get own histries
        if ($user->role != 1) {
            return $query->where('admin_id', $user->id);
        }

        return $query;
    }
}
