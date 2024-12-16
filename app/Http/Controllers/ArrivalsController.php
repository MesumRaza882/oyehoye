<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order_item;
use App\Models\Notification;
use App\Models\{User,Problem};
use App\Models\screenshot;
use App\Models\Rate;
use App\Models\category;
use Illuminate\Support\Facades\File;
use Validator;
use Carbon\Carbon;
class ArrivalsController extends Controller
{

    
    //Rtaes
    public function viewReview()
    {
        $problems = Problem::/*with('user')*/latest()->paginate(10);
       
        return view('admin.Customer_Rate.viewRates',compact('problems'));
    }
  
  // public function viewReview()
//     {
//         return $rates = Rate::with('user')->get();
//         $allrates = Rate::all();
//         $overall_rev = 0;
//         if(count($allrates)>0)
//         {
//             $overall_rev = round(($allrates->sum('review'))/$allrates->count(),1);
//         }
//         $screenshots = screenshot::findorFail(1);
//         return view('admin.Customer_Rate.viewRates',compact('rates','overall_rev','screenshots'));
//     }
    
    // addscreenShots
    public function addscreenShots(Request $req)
    {
        $product = screenshot::findorFail(1);
        $imagesArr = array();
        if($files = $req->file('image'))
            {
                // delete old images
                 $images = explode(',',$product->image);
                 foreach($images as $img){
                    if(File::exists($img)){
                        File::delete($img);
                    }
                 }
                //  New Images
                foreach($files as $file)
                {
                    $image_name = md5(rand(1000, 10000));
                    $ext = strtolower($file->getClientOriginalExtension());
                    $image_full_name = $image_name.'.'.$ext;
                    $upload_path = 'screenshots/';
                    $image_url = $upload_path.$image_full_name;
                    $file->move($upload_path,$image_full_name); 
                    $imagesArr[] = $image_url; 

                }
                $product->image = implode(',', $imagesArr); 
            }
            $product->save();
            return redirect()->back()->with('success','Images Updated Successfully!');
    }

}
