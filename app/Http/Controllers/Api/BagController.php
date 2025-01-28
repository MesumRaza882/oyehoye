<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Facades\File;
use App\Models\{CategoryReview,category};
use Validator;
use Carbon\Carbon;
use Illuminate\Support\Arr;

class BagController extends Controller
{
 
    
    // allcategories
    function allcategories(Request $req)
    {
        // \Log::info('Test Categories Log');

        $user = User::where('id',$req->user_id)->first();
        // check user availble and not deleted by admin
        if($user){
            $categories = category::has('product')->where('status',0)->get();
            $count = 0;
                foreach($categories as $cat){
                    $cat->image=asset('video/category/'.$cat->image);
                    
                    // count user notification based on category product
                    $notification = Notification::where('category',$cat->id)->get();
                    if(count($notification))
                    {
                        foreach($notification as $notif){
                            $ids = explode(',',$notif->user_id);
                            // check user id not see notification then add count user's ntification
                            
                            $position = array_search($req->user_id, $ids);
                            if(!$position){
                                 $count += 1;
                            }
                            
                        }
                    } 
                    $cat['count_user_notif'] = $count;
                    $count = 0;
                }
                // return $categories;
                if(count($categories)> 0){
                    // \Log::info($categories);
                    return response()->json([
                        'statusCode'=>200,
                        'message'=>'succes',
                        'data'=>$categories,
                    ]);
                }
                return response()->json([
                    'statusCode'=>404,
                    'message'=>'Not found any Active Category',
                ]);
        }
        // if deleted user then not display categories
        return response()->json([
            'statusCode'=>404, 'message'=>'User Not Found',]);
    }
    
    // itemBased Category
    public function category_items(Request $req)
    {
         $validator = Validator::make($req->all(), [
            'category_id'=>'required|exists:categories,id',
            'user_id'=>'required|exists:users,id',
        ]);
         
        if (!$validator->passes()) 
         {
            return response()->json([
            'statusCode'=>500,
            'message'=>'Valiadtion Error',
            'error'=>$validator->errors()->toArray()]);
        }else{
            // $bags = Product::where('category_id',$req->category_id)->where('soldstatus',0)->Where('soldItem','>',0)->orderByRaw("id DESC")->get();
            $bags = Product::where('category_id',$req->category_id)
            ->orderBy('updated_at','desc')
            ->orderBy('id','desc')
            ->take(20)
            ->get();
            // $notification = Notification::where('category',$req->category_id)->get();
            // $viewBags=[];
            // if(count($notification))
            // {
            //     // add notification'see from specific User
            //     foreach($notification as $notif){
            //        $ids = explode(',',$notif->user_id);
            //         foreach($ids as $id){
            //             $position = array_search($req->user_id, $ids);
            //         }
            //             if(!$position){
            //                  array_push($ids, $req->user_id);
            //             }
            //         // save notif id
            //         $notif->user_id = implode(',', $ids);
            //         $notif->save();
            //     }
                
            // }
            
            // increase Minutes
            // foreach($bags as $pro){
                    
            //         // if minutes not increased then Apply limit fake quantity
            //         if($pro->soldAdm < $pro->stop_fake_after_quantity)
            //         {
            //             $minutes = $pro->updated_at->diffInMinutes(Carbon::now());
            //             $increaseMnt= $pro->increase_perMin;
            //             $divide = intdiv($minutes,$increaseMnt);
            //             $pro->soldAdm = ($pro->soldAdm)+$divide;

            //             if($divide > $pro->stop_fake_after_quantity)
            //             {
            //                 $pro->soldAdm = $pro->stop_fake_after_quantity;
            //             }
            //             $pro->save();
            //         }
                    
            //     }
            // assets video image link
            //  foreach($bags as $bag){
            //     $bag->video=asset('video/'.$bag->video);
            //     $bag->thumbnail=asset('video/thumbnail/'.$bag->thumbnail);
            //     array_push($viewBags,$bag);
            // }
            
            
            if($bags){
                // $bag_review = CategoryReview::take(10)->latest()->get();
                $bag_review = [];
                return response()->json([
                    'statusCode'=>200,
                    'message'=>'succes',
                    'All_category_Reviews'=>$bag_review,
                    'data'=>$bags,
                ]);
            }
            return response()->json([
                'statusCode'=>404,
                'message'=>'Not found Product',
            ]);
            
        }
    }
}
