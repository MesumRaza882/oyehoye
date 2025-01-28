<?php
// app/Trait/GlobalScopesTrait.php

namespace App\Traits;

trait GlobalScopesTrait
{
    // where scope functions
    public function scopeWhereIf($q, $col, $operator, $val)
    {
        if ($val) {
            $q->Where($col, $operator, $val);
        }
        return $q;
    }

    // or where scope functions
    public function scopeOrWhereIf($q, $col, $operator, $val)
    {
        if ($val) {
            $q->orWhere($col, $operator, $val);
        }
        return $q;
    }

    public function scopefilterByDate($q, $startdate, $enddate)
    {
        if ($startdate && $enddate) {
            $incEndDate = date('Y-m-d', strtotime($enddate . ' +1 day'));
            $q->whereBetween('created_at', [$startdate, $incEndDate]);
        } elseif ($startdate && !$enddate) {
            $q->whereDate('created_at', $startdate);
        } elseif (!$startdate && $enddate) {
            $q->where('created_at', '<=', $enddate);
        }
        return $q;
    }
}
