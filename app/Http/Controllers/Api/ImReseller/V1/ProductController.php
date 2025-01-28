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

class ProductController extends Controller
{
    public function new_arrivals()
    {
      $product_instance = new Product();
      $products = Product::select($product_instance->select_fileds_imreseller)->orderBy('id', 'DESC')->paginate(30);
      return $this->success_paginate($products, '', 1);
    }

    public function out_of_stock()
    {
      $product_instance = new Product();
      $products = Product::select($product_instance->select_fileds_imreseller)->where(Product::QTY_COLUMN_NAME, 0)->orderBy('id', 'DESC')->paginate(30);
      return $this->success_paginate($products, '', 1);
    }

    public function offers()
    {
      $product_instance = new Product();
      $products = Product::select($product_instance->select_fileds_imreseller)->where('discount', '!=', null)->paginate(30);
      return $this->success_paginate($products, '', 1);
    }

}
