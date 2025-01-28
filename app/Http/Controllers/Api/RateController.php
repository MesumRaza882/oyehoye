<?php


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Rate;
use App\Models\Problem;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\Luckydraw;
use Illuminate\Support\Facades\File;
use Validator;

class RateController extends Controller
{
    //add rate
    public function addrate(Request $req){
        $validator = Validator::make($req->all(), [
            'review'=>'required|integer|between:1,5',
            'user_id'=>'required',
        ]);

        if (!$validator->passes()) 
         {
            return response()->json([
            'statusCode'=>500,
            'message'=>'Valiadtion Error',
            'error'=>$validator->errors()->toArray()]);
        }
        else
        {
            $review = Rate::where('user_id',$req->user_id)->first();
            if($review){
                $review->review = $req->review;
                if($req->review_text){
                    $review->review_text = $req->review_text;
                }
                // image
                if($req->hasFile('image'))
                {
                    $path = 'complaint/'.$review->image;
                    if(File::exists($path))
                    {
                          File::delete($path);
                    }
                    $file = $req->file('image');
                    $ext = $file->getClientOriginalExtension();
                    $filename = time().'.'.$ext;
                    $file->move('complaint/',$filename);
                    $review->image = $filename;
                }
                $saveArrival = $review->save();
                if($saveArrival){

                    return response()->json([
                        'statusCode'=>200,
                        'message'=>'succes update',
                        'data'=>$review,
                    ]);
                }
                return response()->json([
                    'statusCode'=>404,
                    'message'=>'Not Save',
                ]);

            }
        
                $review = new Rate();
                $review->review = $req->review;
                $review->review_text = $req->input('review_text') == TRUE ? $req->input('review_text'):'';
                // image
                if($req->hasFile('image'))
                {
                    $file = $req->file('image');
                    $ext = $file->getClientOriginalExtension();
                    $filename = time().'.'.$ext;
                    $file->move('complaint/',$filename);
                    $review->image = $filename;
                }
                $review->user_id = $req->user_id;
                $saveArrival = $review->save();
                if($saveArrival){

                    return response()->json([
                        'statusCode'=>200,
                        'message'=>'succes save',
                        'data'=>$review,
                    ]);
                }
                return response()->json([
                    'statusCode'=>404,
                    'message'=>'Not Save',
                ]);
    }
}
    //view Rtaes
    // public function viewrates()
    // {
    //     $viewArr = Rate::all();
    //      if($viewArr->count()>0){
 
    //          return response()->json([
    //              'statusCode'=>200,
    //              'message'=>'succes',
    //              'data'=>$viewArr,
    //          ]);
    //      }
    //      return response()->json([
    //          'statusCode'=>404,
    //          'message'=>'Not found',
    //      ]);
    // }

    //add luckydrwa
    public function addlucky(Request $req){
        $validator = Validator::make($req->all(), [
            'whatsapp'=>'required|digits:11',
            'user_id'=>'required|integer',
            'user_name'=>'required',
            'facebook_name'=>'required',
        ]);

        if (!$validator->passes()) 
         {
            return response()->json([
            'statusCode'=>500,
            'message'=>'Valiadtion Error',
            'error'=>$validator->errors()->toArray()]);
        }
        else
        {
        //     $lucky = Luckydraw::where('user_id',$req->user_id)->first();
        // if($lucky){
        //     $lucky->whatsapp = $req->whatsapp;
        //     $lucky->user_name = $req->user_name;
        //     $lucky->facebook_name = $req->facebook_name;
        //     $saveArrival = $lucky->save();
        //         if($saveArrival){

        //             return response()->json([
        //                 'statusCode'=>200,
        //                 'message'=>'succes update',
        //                 'data'=>$review,
        //             ]);
        //         }
        //         return response()->json([
        //             'statusCode'=>404,
        //             'message'=>'Not Save',
        //         ]);
        // }
        
                $lucky = new Luckydraw();
                $lucky->whatsapp = $req->whatsapp;
                $lucky->user_name = $req->user_name;
                $lucky->facebook_name = $req->facebook_name;
                $lucky->user_id = $req->user_id;
                $saveArrival = $lucky->save();
                if($saveArrival){

                    return response()->json([
                        'statusCode'=>200,
                        'message'=>'succes save',
                        'data'=>$lucky,
                    ]);
                }
                return response()->json([
                    'statusCode'=>404,
                    'message'=>'Not Save',
                ]);
    }
}

    //view lucky
    // public function viewlucky()
    // {
    //     $viewArr = Luckydraw::all();
    //      if($viewArr->count()>0){
    
    //          return response()->json([
    //              'statusCode'=>200,
    //              'message'=>'succes',
    //              'data'=>$viewArr,
    //          ]);
    //      }
    //      return response()->json([
    //          'statusCode'=>404,
    //          'message'=>'Not found',
    //      ]);
    // }
    
    //add problem
    public function addproblem(Request $req){
        $validator = Validator::make($req->all(), [
            'whatsapp'=>'required|digits:11',
            'user_id'=>'required|integer',
            'phone'=>'required',
            'comment'=>'required',
            'image'=>'required|image',
        ],[
            'phone.required' => 'User Name is Required.'
        ]);
    
        if (!$validator->passes()) 
         {
            return response()->json([
            'statusCode'=>500,
            'message'=>'Valiadtion Error',
            'error'=>$validator->errors()->toArray()]);
        }
        else
        {
                $problem = new Problem();
                $problem->whatsapp = $req->whatsapp;
                $problem->user_name = $req->phone;
                $problem->comment = $req->comment;
                $problem->user_id = $req->user_id;
                // image
                if($req->hasFile('image'))
                {
                    $file = $req->file('image');
                    $ext = $file->getClientOriginalExtension();
                    $filename = time().'.'.$ext;
                    $file->move('complaint/',$filename);
                    $problem->image = $filename;
                }
                    $saveArrival = $problem->save();
                if($saveArrival){
    
                    return response()->json([
                        'statusCode'=>200,
                        'message'=>'succes save',
                        'data'=>$problem,
                    ]);
                }
                return response()->json([
                    'statusCode'=>404,
                    'message'=>'Not Save',
                ]);
        }
    }
    
    
    //view Category Review
     public function viewCatReview()
    {
        $viewArr = ProductReview::with('product:id,name,category,price')->get();
         if($viewArr->count()>0){

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
