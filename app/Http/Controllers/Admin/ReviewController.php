<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\{Admin, ProductReview};
use Cache;

class ReviewController extends Controller
{
  //index
  public function index()
  {
    Cache::forget('pending_reviews_count');
    $reviews = ProductReview::with('user')->orderBy('status', 'asc')->orderBy('id', 'desc')->paginate(10);
    return view('admin.reviews.index', compact('reviews'));
  }

  public function status_update(Request $request, $status)
  {

    // try{
      ProductReview::where('id', $request->id)->update([
        'status' => $status
      ]);

      $arr=[];
      $arr['toastr'] = 'success';
      $arr['msg'] = "Status has been changed";   
      $arr['st'] = 1;
      return response()->json($arr, 200);

    // } catch(\Exception $e){

    //   $arr=[];
    //   $arr['toastr'] = 'error';
    //   $arr['msg'] = $e->getMessage();   
    //   $arr['st'] = 0;
    //   return response()->json($arr, 200);
  
    // }

  }
}
