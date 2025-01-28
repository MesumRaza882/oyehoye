<?php

namespace App\Http\Controllers\Api\V2\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Carbon\Carbon;
use Exception;
use DB;
use Validator;
use Laravel\Sanctum\PersonalAccessToken;
use App\Models\LockedkFolderPassword;

class ProductController extends Controller
{

    public function products(Request $req)
    {
        // \Log::info($req->all());
        $products = Product::Detail()
            ->where('product_status', 'published')
            ->whereIf('category_id', '=', $req->category_id)
            ->orderBy('updated_at', 'desc')
            ->orderBy('id', 'desc')
            // ->latest('pinned_at')
            // ->orderBy('pinned_at', 'desc')
            ->groupBy('id')
            ->where(function ($q) use ($req) {
                $q->where('show_point', $req->show_point ? $req->show_point : 1)
                    ->orWhere('show_point', 3);
            })
            ->where('is_locked', 0)
            ->paginate(50);

        return $this->success_paginate($products, 'Product Records with Reviews', 1);
    }

    public function search_products(Request $req)
    {
        $products = Product::Detail()
        ->where('product_status', 'published')
        // ->where('name', 'like', '%' . $req->name . '%')
        ->where('is_locked', 0)
        ->WhereIf('article', '=', $req->name)
            ->orderBy('updated_at', 'desc')
            ->orderBy('id', 'desc')
            ->groupBy('id')
            ->where(function ($q) use ($req) {
                $q->where('show_point', 1)
                    ->orWhere('show_point', 3);
            })
            // ->where('show_point', $req->show_point ? $req->show_point : 1)
            // ->orWhere('show_point',3)
            ->paginate(50);

        return $this->success_paginate($products, 'Product Records with Reviews', 1);
    }

    public function reseller_products(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'password' => 'required',
        ]);

        if (!$validator->passes()) {
            return $this->error($validator->errors()->first(), 'Password Validation', 0, 422);
        }
        if ($req->password == LockedkFolderPassword::find(1)->text_password) {
            $products = Product::Detail('reseller_price')
             // order by pinned
             ->orderBy('pinned_at', 'desc')
            ->where('product_status', 'published')
            ->whereIf('category_id', '=', $req->category_id)
            // ->orderBy('updated_at', 'desc')
            ->orderBy('id', 'desc')
            // ->latest()
            ->groupBy('id')
            ->where(function ($q) use ($req) {
                $q->where('show_point', $req->show_point ? $req->show_point : 1)
                    ->orWhere('show_point', 3);
            })
            ->where('is_locked', 0)
            ->paginate(50);
            return $this->success_paginate($products, '', 1);
        }
        return $this->error([], 'Locked Password Invalid', 0);
    }

    //New Arrivals 
    public function new_arrivals()
    {
        $arrivals = Product::select([
            'id', 'name', 'price', 'discount', 'article', 'is_dc_free', 'category_id',
            'soldAdm', 'exceed_limit', 'video', 'thumbnail', 'increase_perMin', 'updated_at',
            DB::raw('CAST((soldItem + markeetItem) AS CHAR) as soldItem'),
            // DB::raw('CASE WHEN increase_perMin = 0 THEN 0 ELSE CAST((soldItem + markeetItem) AS CHAR) END as soldItem'),
            'stop_fake_after_quantity',
            'markeetItem',
        ])
        ->orderBy('pinned_at', 'desc')
        ->where('product_status', 'published')
        ->orderBy('id', 'desc')
        // ->latest('pinned_at')
        ->groupBy('id')
        ->where('is_locked', 0)
        ->paginate(50);
        return $this->success_paginate($arrivals, 'New Arrivals Records', 2);
    }

    // Active Stock
    public function active_stock()
    {
        $active_stock = Product::select([
                'id', 'name', 'price', 'discount', 'article', 'is_dc_free', 'category_id',
                'soldAdm', 'exceed_limit', 'video', 'thumbnail', 'increase_perMin', 'updated_at',
                DB::raw('CAST((soldItem + markeetItem) AS CHAR) as soldItem'),
                // DB::raw('CASE WHEN increase_perMin = 0 THEN 0 ELSE CAST((soldItem + markeetItem) AS CHAR) END as soldItem'),                'stop_fake_after_quantity',
                // 'soldItem',
                // 'markeetItem',
            ])/*->inRandomOrder()*/
            ->orderBy('pinned_at', 'desc')
            ->where('soldstatus', 0)
            ->where('soldItem', '>', 0)
            ->where('product_status', 'published')
            ->groupBy('id')
            ->where('is_locked', 0)
            ->paginate(50);
        return $this->success_paginate($active_stock, 'Active Stock Records', 2);
    }

    // Sold out Stock
    public function sold_out()
    {
        $sold_out = Product::select([
                'id', 'name', 'price', 'discount', 'article', 'is_dc_free', 'category_id',
                'soldAdm', 'exceed_limit', 'video', 'thumbnail', 'increase_perMin', 'updated_at',
                DB::raw('CAST((soldItem + markeetItem) AS CHAR) as soldItem'),
                // DB::raw('CASE WHEN increase_perMin = 0 THEN 0 ELSE CAST((soldItem + markeetItem) AS CHAR) END as soldItem'),                'stop_fake_after_quantity',
                // 'soldItem',
                // 'markeetItem',
            ])->inRandomOrder()
            ->orderBy('pinned_at', 'desc')
            ->where('soldstatus', 1)
            ->orwhere('soldItem', '=<', 0)
            ->groupBy('id')
            ->where('is_locked', 0)
            ->paginate(50);
        return $this->success_paginate($sold_out, 'Sold Out Stock Records', 2);
    }

    public function play_video()
    {
        return view('api2.play-video');
    }

    public function play_video_2(Request $request)
    {
        $video_path = str_replace('https://oyehoyebridalhouses.com', '',$request->video);
        $stream = new \App\Helpers\VideoStream(public_path($video_path));
        $stream->start();
    }
}
