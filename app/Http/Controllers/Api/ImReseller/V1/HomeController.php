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
use App\Models\Banner;
use App\Models\Product;

class HomeController extends Controller
{
    public function home(Request $req)
    {

      $categories = Category::select('id', 'name', 'image')
                            ->where('status',1)
                            // ->where('for_reseller', 1)
                            ->get();
      $top_banners = Banner::select('id', 'name', 'image', 'next_action', 'next_action_id')->where('position','top')->orderBy(\DB::raw('-`sort`'), 'desc')->get();
      $bottom_banners = Banner::select('id', 'name', 'image', 'next_action', 'next_action_id')->where('position','bottom')->orderBy(\DB::raw('-`sort`'), 'desc')->get();
      $product_instance = new Product();
      $items_group_one_products = Product::select($product_instance->select_fileds_imreseller)->where('price', '<=', 1600)->limit(10)->get();
      $items_group_two_products = Product::select($product_instance->select_fileds_imreseller)->limit(10)->orderBy('id', 'DESC')->get();
      $items_group_three_products = Product::select($product_instance->select_fileds_imreseller)->where('discount', '!=', null)->limit(10)->get();
      $data = [
        'categories' => $categories,
        'top_banners' => $top_banners,
        'bottom_banners' => $bottom_banners,
        'items_group_one_title' => 'Rs 1600 Tak Jora Collection',
        'items_group_one_products' => $items_group_one_products,
        'items_group_two_title' => 'Latest Fashion',
        'items_group_two_products' => $items_group_two_products,
        'items_group_three_title' => 'Betahasha Discount',
        'items_group_three_products' => $items_group_three_products,
      ];

      return $this->success($data, '', 1);

    }

    public function sell_all_item_group($type)
    {
      $product_instance = new Product();
      $items_group = [];

      if($type == 'one'){
        $items_group = Product::select($product_instance->select_fileds_imreseller)->where('price', '<=', 1600)->paginate(30);
      }elseif($type == 'two'){
        $items_group = Product::select($product_instance->select_fileds_imreseller)->orderBy('id', 'DESC')->paginate(30);
      }elseif($type == 'three'){
        $items_group = Product::select($product_instance->select_fileds_imreseller)->where('discount', '!=', null)->paginate(30);
      }

      return $this->success_paginate($items_group, '', 1);
    }

}
