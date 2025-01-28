<?php

namespace App\Http\Controllers\Api\V2\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductReview;
use Carbon\Carbon;
use DB;
use Validator;

class ReviewController extends Controller
{
    
  public function reviews(Request $req)
  {
    $user = auth('sanctum')->user();
    if($user){
      $user_id = $user->id;
    }else{
      $user_id = 0;
    }

    $reviews = ProductReview::where('status', 1)
              ->orWhere('user_id', $user_id)
              ->orderBy('updated_at','desc')
              ->paginate(50);

    return $this->success_paginate($reviews, '', 1);
  }

  public function add_review(Request $request)
  {

    $validator = Validator::make($request->all(), [
      // 'review' => 'required|in:1,2,3,4,5',
      'desc' => 'required|string',
      'image' => 'required|image',
    ]);

    // check validation
    if (!$validator->passes()) {
      return $this->error($validator->errors()->all(), $validator->errors()->first(), 422);
    }

    $user = auth('sanctum')->user();
    if($user){
      $user_id = $user->id;
      $user_name = $user->name;
      // if($user_name == null){
      //   $user_name = 'no name';
      // }
    }else{
      $user_id = null;
      $user_name = 'Anonymous';
    }

    // if($req->hasFile('advance_payment_proof')){
      $file = $request->file('image');
      $ext = $file->getClientOriginalExtension();
      $filename = time().'.'.$ext;
      $file->move('reviews/',$filename);
      $attachment = url('reviews', $filename);
    // }else{
    //   $advance_payment_proof = null;
    // }

    $review = new ProductReview;
    // $review->review = $request->review;
    $review->desc = $request->desc;
    $review->attachment = $attachment;
    $review->user_id = $user_id;
    $review->cus_name = $user_name;
    $review->status = 0;
    if($review->save()){

      return $this->success([
        'id' => $review->id,
        'review' => $review->review,
        'desc' => $review->desc,
        'attachment' => $attachment,
        'user_id' => $user_id,
        'cus_name' => $user_name,
        'created_at' => $review->created_at,
      ], 'Great! 😊 Review added successfully', 1);

    }else{

      return $this->error('', 'Sorry! 😔 There is an error please try later', 0,422);

    }
  }
  
}
