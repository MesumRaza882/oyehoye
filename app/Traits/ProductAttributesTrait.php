<?php

namespace App\Traits;

use App\Models\LockedkFolderPassword;
use Carbon\Carbon;

trait ProductAttributesTrait
{
    // Accessor for thumbnail with link
    public function getThumbnailWithLinkAttribute()
    {
        // return URL::asset('video/thumbnail/').'/'.$this->thumbnail;
        return $this->thumbnail;
    }

    // Accessor for auto sale item now
    public function getAutoSaleItemNowAttribute()
    {
        if ($this->increase_perMin == 0){
            return 0;
        }

        $item = $this->soldItem;
        if ($item == 0) {
            $soldAdm = $this->soldAdm;
            return (int)$soldAdm;
        } else {
            if ($this->updated_at == null) {
                $minutes = 0;
            } else {
                $minutes = $this->updated_at->diffInMinutes(Carbon::now());
            }

            $increaseMnt = $this->increase_perMin;
            if ($increaseMnt == 0)
                $increaseMnt = 1;

            // if($minutes == 0)
            //     $minutes = 1;

            $exact_minutes = intdiv($minutes, $increaseMnt);

            // if minutes after upfate product and solded quantity 
            $new_minutes = (int)$this->soldAdm + (int)$exact_minutes;

            if ($this->stop_fake_after_quantity == 0 || $new_minutes > $this->stop_fake_after_quantity) {
                $q = $this->stop_fake_after_quantity;
                if($q == 0){
                    return 0;
                    // return (int)$this->soldAdm + (int)$exact_minutes;
                }else{
                  return (int)$q;
                }
            } else {
                // return (int)$exact_minutes;
                return (int)$this->soldAdm + (int)$exact_minutes;
            }

            // if ($new_minutes > $this->stop_fake_after_quantity) {
            //     return (int)$this->stop_fake_after_quantity;
            // } else {
            //     return (int)$this->soldAdm + (int)$exact_minutes;
            // }
        }
    }

    // Accessor for product sale reason
    public function getProductSaleReasonAttribute()
    {
        if ($this->soldstatus == 0 && $this->soldItem > 0 && $this->is_freez_item != 1) {
            $setting = LockedkFolderPassword::first();
            return $setting ? $setting->product_sale_reason : null;
        }
        return null;
    }
}
