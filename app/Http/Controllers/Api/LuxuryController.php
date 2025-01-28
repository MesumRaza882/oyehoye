<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\File;
use App\Models\User;
use App\Models\Notification;
use App\Models\CategoryReview;
use Validator;
use Carbon\Carbon;

class LuxuryController extends Controller
{
    //
     //
     function viewLuxury()
     {
        //  $luxuries = Product::with('prodReviews','SingproductRev')->where('category','Luxury')->where('soldstatus',0)->Where('soldItem','>',0)->get();
         $luxuries = Product::where('category','Luxury')->where('soldstatus',0)->Where('soldItem','>',0)->get();
         foreach($luxuries as $pro){
                $minutes = $pro->updated_at->diffInMinutes(Carbon::now());
                $divide = intdiv($minutes,$pro->increase_perMin);
                $pro->soldAdm = ($pro->soldAdm)+$divide;
                $pro->save();
            }
         $viewArr=[];
         foreach($luxuries as $luxuury){
            $luxuury->video=asset('video/'.$luxuury->video);
        array_push($viewArr,$luxuury);
        }
        
         if($viewArr){
             $luxury_rev = CategoryReview::get();
             return response()->json([
                 'statusCode'=>200,
                 'message'=>'succes',
                 'luxury_rev'=>$luxury_rev,
                 'data'=>$viewArr,
             ]);
         }
         return response()->json([
             'statusCode'=>404,
             'message'=>'Not found',
         ]);
     }
}
