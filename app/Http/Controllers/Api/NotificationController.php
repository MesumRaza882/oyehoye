<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\User;
use App\Models\Product;
use App\Models\SinglePro_Review;
use Illuminate\Support\Facades\File;
use Validator;

class NotificationController extends Controller
{
    //Arrival Notification
    public function arrivalNot(Request $req){
        
        $user = User::where('id',$req->user_id)->first();
        if($user){
            $count = 0;
            $products = Notification::all();
            foreach($products as $product){
                $ids = explode(',',$product->user_id);
                
                foreach($ids as $id){
                    
                    if($id == $user->id){
                        $count += 1;
                    }
                }
            }
                return response()->json([
                'statusCode'=>200,
                'message'=>'succes',
                'data'=>$count,
            ]);
        }
        return response()->json([
                'statusCode'=>404,
                'message'=>'Not found',

            ]);
    }
    
    
    // Remove Arruval Notification
    public function delarrivalNot(Request $req){
        
     $user = User::where('id',$req->user_id)->first();
     if($user){
         
        $products = Notification::all();
        foreach($products as $product){
            $ids = explode(',',$product->user_id);
            // get id position
            $position = array_search($req->user_id, $ids);
            // unset id by check position
            foreach($ids as $id){
                if($id == $req->user_id){
                     $position = array_search($req->user_id, $ids);
                     unset($ids[$position]);
                }
            }
            // save remaining ids 
            $product->user_id = implode(',', $ids);
            $product->save();
        }
        return response()->json([
        'statusCode'=>200,
        'message'=>'Remove Notification Successfully',
        ]);
     }
     return response()->json([
        'statusCode'=>404,
        'message'=>'Not found',
        ]);
     
    }
    
   
    
    //viewsoldpro
    public function viewsoldpro(){
        //  $luxuries = Product::with('prodReviews')->where('soldstatus',1)->orWhere('soldItem','<=',0)->get();
        // $luxuries = Product::where('soldstatus',1)->orWhere('soldItem','<=',0)->get();
        $luxuries = Product::where('soldstatus',1)->orderBy('updated_at','desc')->get();
        if(count($luxuries)>0){
         $viewArr=[];
         foreach($luxuries as $luxuury){
            $luxuury->video=asset('video/'.$luxuury->video);
            array_push($viewArr,$luxuury);
            }
            
             return response()->json([
                 'statusCode'=>200,
                 'message'=>'succes',
                 'data'=>$viewArr,
             ]);
         }
         return response()->json([
             'statusCode'=>404,
             'message'=>'Not found',
         ]);
    }
    

    
}
