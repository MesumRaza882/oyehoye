<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResellerSetting extends Model
{
	use HasFactory;
	protected $fillable = [
			'prod_id','admin_id','is_specific_profit','profit','product_upload_status','price','reseller_product_profit','for_app_reseller'
	];

	// product_upload_status (means admin can draft or publish their products by default set by super admin)
	// profit set default if is_specific_profit != 1
	// for_app_reseller (means this product also for reseller app)

	// scope functions
	public function scopeWhereIf($q, $col, $operator, $val)
	{
		if($val){
			$q->Where($col, $operator, $val);
		}
		return $q;
	}

	public function scopeAdminLoginScreen($q, $request)
	{
		// any change here can need to change scope function in products scopeAdminLoginScreenHas
		if(!$request->has('showUpdate')){
			$q->where('product_upload_status', 'published');
		}
		return $q;
	}

}
