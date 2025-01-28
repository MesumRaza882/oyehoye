<?php

namespace App\Http\Controllers\Api\ImReseller\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Str;
use Laravel\Sanctum\PersonalAccessToken;
use Validator;
use DB;
Use Exception;
use Cache;
use App\Models\category as Category;
use App\Models\Order_item as OrderItem;
use App\Models\Product;

class CategoryController extends Controller
{
    public function top_categories()
    {
      $product_ids = OrderItem::select('prod_id')->distinct()->get()->pluck('prod_id');
      $categories_ids = Product::select('category_id')->distinct()->whereIn('id', $product_ids)->get()->pluck('category_id');
      $categories = Category::select('id', 'name', 'image')
                              ->where('status',1)
                              // ->where('for_reseller', 1)
                              ->whereIn('id', $categories_ids)
                              ->whereIn('id', $categories_ids)
                              ->get();
      return $this->success($categories, '', 1);
    }

    public function categories()
    {
      $categories = Category::select('id', 'name', 'image')
                    ->where('status',1)
                    // ->where('for_reseller', 1)
                    ->get();
      return $this->success($categories, '', 1);
    }

}
