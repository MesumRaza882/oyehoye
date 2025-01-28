<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Order_item;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use Domain;
use App\Traits\{GlobalScopesTrait,ProductAttributesTrait};


class Product extends Model
{
    /**
     * soldItem == qunatity in stock
     */
    use HasFactory,ProductAttributesTrait,GlobalScopesTrait,SoftDeletes;

    const QTY_COLUMN_NAME = 'soldItem';
    var $select_fileds_imreseller = ['id', 'name', 'soldItem as qty', 'price', 'discount', 'is_white_list', 'reviews', 'thumbnail', 'video',];
    // var $select_fileds_imreseller = ['id','name', 
    // DB::raw('soldItem + markeetItem as qty'),
    //     'price',
    //     'discount',
    //     'is_white_list',
    //     'reviews',
    //     'thumbnail',
    //     'video'
    // ];

    protected $fillable = [
        'name', 'price', 'purchase',  'profit',
        'discount', 'article', 'category_id',  'soldItem',
        'soldAdm',  'exceed_limit', 'increase_perMin','reseller_price',
        'reviews',  'is_white_list', 'variety', 'thumbnail',
        'video', 'soldstatus', 'video_link_embed', 'show_point', 'stop_fake_after_quantity','specific_reseller_profit',
        'is_dc_free', 'is_locked', 'hide_to_new_arrival', 'for_notification', 'markeetItem', 'markeetPickup','product_upload_for'
    ];

    // product_upload_for (1 for all,  2 for super admin , 3 for resellers, 4 for managers, 5 for superadmin+managers, 6 for partbers)
    protected $casts = [
        'is_dc_free' => 'string',
    ];

    protected $dates = ['deleted_at'];

    // Apply the global scope in the booted method
    protected static function booted()
    {
        static::addGlobalScope('unfreezed', function (Builder $builder) {
            $builder->whereNull('is_freez_item');
        });
    }

    public function orderItemsPending()
    {
        return $this->hasMany(Order_item::class, 'prod_id', 'id')
            ->where('order_status', 'PENDING')
            ->whereHas('order', function ($query) {
                $query->where('is_blocked_customer_order', 0);
            });
    }
    public function orderItemsCancelHold()
    {
        return $this->hasMany(Order_item::class, 'prod_id', 'id')
            ->where('order_status', 'CANCELHOLD');
    }

    public function orderItemsDispatchedDelivered()
    {
        return $this->hasMany(Order_item::class, 'prod_id', 'id')->whereIn('order_status', ['DISPATCHED', 'DELIVERED'])
            ->whereHas('order', function ($query) {
                $query->where('is_blocked_customer_order', 0);
            });;
    }

    // relation ships
    public function carts()
    {
        return $this->hasMany(Cart::class);
    }
    public function prodReviews()
    {
        return $this->hasMany(ProductReview::class, 'prod_id', 'id');
    }
    public function SingproductRev()
    {
        return $this->hasMany(SinglePro_Review::class, 'prod_id', 'id');
    }
    public function productitems()
    {
        return $this->hasMany(Order_item::class, 'prod_id', 'id');
    }
    public function itemcategory()
    {
        return $this->belongsTo(category::class, 'category_id', 'id')->withTrashed();
    }

		public function reseller_setting()
    {
			return $this->hasOne(ResellerSetting::class, 'prod_id', 'id');
    }

    // get products based on specific profit
    public function resellerSpecificProduct()
    {
        return $this->hasMany(ResellerSetting::class, 'prod_id', 'id')->where('is_specific_profit',1);
    }

    // get products based on status of product like draft or published for reseller for many
    public function resellerUploadProduct()
    {
        return $this->hasMany(ResellerSetting::class, 'prod_id', 'id')->whereNotNull('product_upload_status');
    }

    // single product get
    public function resellerUploadProductObject()
    {
        return $this->belongsTo(ResellerSetting::class, 'id', 'prod_id')->whereNotNull('product_upload_status');
    }
    

    // scope functions
    public function scopeDetail($q, $price_colum_cast = 'price')
    {
        return $q->select([
            'id', 'name',
            // 'price',
            'discount', 'article', 'is_dc_free', 'category_id',
            'soldAdm', 'exceed_limit', 'video', 'thumbnail', 'increase_perMin', 'updated_at',
            DB::raw('CAST((soldItem + markeetItem) AS CHAR) as soldItem'),
            DB::raw("CAST($price_colum_cast AS CHAR) as price"),
            // 'soldItem',
            // 'markeetItem',
            // DB::raw('CASE WHEN increase_perMin = 0 THEN 0 ELSE CAST((soldItem + markeetItem) AS CHAR) END as soldItem'),            'stop_fake_after_quantity',
            // 'autoSaleItemNow',
        ]);
    }

    public function scopefilterStatus($q, $status)
    {
        if ($status == 1) {
            return $q->where('soldstatus', 0)->where('soldItem', '>', 0)->whereNull('is_freez_item');
        }
        if ($status == 2) {
            return $q->where('soldstatus', 1)->orwhere('soldItem', '<=', 0);
        }
        if ($status == 'published' || $status == 'draft') {
            return $q->where('product_status', $status);
        }
        if ($status == 'freez') {
            return $q->where('is_freez_item', 1);
        }
        return $q;
    }

    public function scopefilterStatusApp($q, $status_app)
    {
        if ($status_app == 2) {
            return $q->where('is_locked', 1);
        }
        if ($status_app == 3) {
            return $q->where('hide_to_new_arrival', 1);
        }
        if ($status_app == 4) {
            return $q->where('is_white_list', 1);
        }
        return $q;
    }

    public function scopefilterByHours($q, $hours)
    {
        if ($hours) {
            return $q->whereBetween('updated_at', [Carbon::now()->subHours($hours), Carbon::now()]);
        }
        return $q;
    }

  public function scopeWebQuery($query, $request)
  {
    return $query
		->with([
      'reseller_setting' => function($q) use ($request){
        $admin_id = Domain::admin('id');
        $admin_id = $admin_id ? $admin_id : 1;
        return $q->where('admin_id', $admin_id)->adminLoginScreen($request);
      }
    ])
    ->adminLoginScreenHas($request)
    // ->latest('pinned_at')
    ->orderBy('pinned_at', 'desc')
    ->where('product_status', 'published')
    ->where('is_locked', 0)
    ->orderBy('id', 'desc')
    ->where('soldstatus', 0)
    ->where('soldItem', '>', 0)
    // ->orderBy('id', 'desc')
    ->get()
    ->map(function ($i) {
      if($i->reseller_setting != null && $i->reseller_setting->price != null && $i->reseller_setting->price > 0){
        $i->price = $i->reseller_setting->price;
        $i->reseller_product_profit = $i->reseller_setting->reseller_product_profit; 
        return $i;
      }else{
        $i->reseller_product_profit = 0;
      }
    })
    ;
  }

	public function scopeAdminLoginScreenHas($q, $request)
	{
		if(!$request->has('showUpdate')){
			// $q->has('reseller_setting');
			return $q->whereHas('reseller_setting', function ($q) {
				$admin_id = Domain::admin('id');
				$admin_id = $admin_id ? $admin_id : 1;
				$q->where('admin_id', $admin_id)
                ->where('product_upload_status', 'published')
                // ->where('reseller_product_profit', '>', '0')
                ; 
				// $q->where('admin_id', $admin_id);
			});
		}
		return $q;
	}

    protected $appends = ['thumbnailWithLink', 'autoSaleItemNow','productSaleReason'];

}
