<?php
namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;


use App\Models\Product;
use App\Models\User;
use App\Models\Notification;
use App\Models\screenshot;
use App\Models\message;
use Carbon\Carbon;
use App\Models\CategoryReview;
use Illuminate\Support\Facades\File;

use ProtoneMedia\LaravelCrossEloquentSearch\Search;

use Validator;

class ArrivalController extends Controller
{
    
    function viewArrival()

    {
        // $luxuries = Product::with('prodReviews','SingproductRev')->latest()->Where('soldstatus',0)->Where('soldItem','>',0)->get();
        // $luxuries = Product::latest()->Where('soldstatus',0)->Where('soldItem','>',0)->get();
        $luxuries = Product::latest()->Where('soldstatus',0)
        ->orderBy('updated_at','desc')
        ->where('is_locked', 0)
        ->take(10)->get();
        // $luxuries = Product::latest()->get();
        // foreach($luxuries as $pro){
        //         $minutes = $pro->updated_at->diffInMinutes(Carbon::now());
        //         $incPm=$pro->increase_perMin;
        //         if($pro->increase_perMin==0){
        //             $incPm=1;
        //         }
        //         $divide = intdiv($minutes,$incPm);
        //         $pro->soldAdm = ($pro->soldAdm)+$divide;
        //         $pro->save();
        //     }

         $viewArr=[];

         foreach($luxuries as $luxuury){
            $luxuury->video=asset('video/'.$luxuury->video);
            $luxuury->thumbnail=asset('video/thumbnail/'.$luxuury->thumbnail);
            array_push($viewArr,$luxuury);
        }


        if($viewArr){
            $arrival_rev = CategoryReview::get();
            return response()->json([
                'statusCode'=>200,
                'message'=>'succes',
                'All_category_Reviews' => $arrival_rev,
                'data'=>$viewArr,

            ]);

        }

        return response()->json([
            'statusCode'=>404,
            'message'=>'Not found',

        ]);

    }





    function search(Request $req)
    {
        $keyword = $req->search;
        $results = Product::where('name', 'LIKE', "%$keyword%")
        ->where('is_locked', 0)
        ->take(10)->get();
        if(count($results)>0)
        {
            return response()->json([
                'statusCode'=>200,
                'message'=>'succes',
                'data'=> $results,
            ]);
        }
        else
        {
            return response()->json([
                'statusCode'=>404,
                'message'=>'Not found',
            ]);
        }
    }



// view message
function message(Request $req){
    $saveArr = message::where('id',1)->first('message');
    //check errors
    if($saveArr){
        return response()->json([
            'statusCode'=>200,
            'message'=>'succes',
            'data'=>$saveArr,
        ]);
    }
    return response()->json([
        'statusCode'=>404,
        'message'=>'Not found',
    ]);
}

// screenshots
public function screenshots()
{
 $product = screenshot::findorFail(1);
 $images = explode(',',$product->image);
    $screenshots=[];

         foreach($images as $img){

            $img = asset('/'.$img);

            array_push($screenshots,$img);

        }
        if($screenshots){
            return response()->json([
                'statusCode'=>200,
                'message'=>'succes',
                'data'=>$screenshots,
            ]);

        }

        return response()->json([

            'statusCode'=>404,

            'message'=>'Not found',

        ]);
}
}

