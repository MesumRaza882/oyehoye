<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use Illuminate\Support\Facades\File;
use Validator;
use App\Models\SinglePro_Review;
use App\Models\ProductReview;
use App\Models\CategoryReview;
class NotificationController extends Controller
{
    
    
   
        
        
    //delarrivalNot
    public function delarrivalNot(Request $req){
        
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
        return  'remove successfully';
    }
    

    
    //Customer Deails regard  product
        public function product_custmer(Request $req)
       {

         $req->validate([
                    'prod_id'=>'required|exists:products,id',
                    'name.*'=>'required|string',
                    'address.*' => 'required',
                ]);
        foreach($req->name as $key=>$name){
            
            $saveArr = new SinglePro_Review();
            $saveArr->name = $name;
            $saveArr->prod_id = $req->prod_id;
            $saveArr->address = $req->address[$key];
            $saveArrival = $saveArr->save();
        }
            return redirect()->back();

    }
    // Delete record
    public function del_product_custmer(Request $req){
        $data = SinglePro_Review::find($req->id);
        $data->delete();
            return response()->json([
            'check_num'=>100,
            ]);
    }
    
    //Customer Review regard Product
    public function product_custmer_review(Request $req)
       {

         $req->validate([
                    'prod_id'=>'required|exists:products,id',
                    'name.*'=>'required|string',
                    'description.*' => 'required',
                    'review.*'=>'required|integer',
                ]);
        foreach($req->name as $key=>$name){
            
            $saveArr = new ProductReview();
            $saveArr->cus_name = $name;
            $saveArr->prod_id = $req->prod_id;
            $saveArr->desc = $req->description[$key];
            $saveArr->review = $req->review[$key];
            $saveArrival = $saveArr->save();
        }
            return redirect()->back();

    }
    
    // Delete record
    public function del_product_custmer_rev(Request $req){
        $data = ProductReview::find($req->id);
        $data->delete();
            return response()->json([
            'check_num'=>100,
            'status'=>'Deleted Successfully',
            ]);
    }
    
   // viewCatReview
    public function viewCatReview()
    {
        $rev = CategoryReview::all();
        $bag = $rev->where('category','Bag')->count();
        $lux = $rev->where('category','Luxury')->count();
        $arr = $rev->where('category','Arrival')->count();
        return view('admin.Customer_Rate.CatReviews',compact('rev','bag','arr','lux'));
    }
    
    // add category Reviews
    public function addCatReview(Request $req)
    {
        // $req->validate([
        //     'customer_name'=>'required',
        //     'city'=>'required|string',
        //     'article' => 'required',
        // ]);
         $input = $req->all();
         $cities = explode(",", $input['city']);
         $names = explode(",", $req->customer_name);
         $articles = explode(",", $input['article']);
        if ( ( count($names) == count($cities)) && (count($cities) == count($articles)) ) {
            foreach($names as $key=>$name){
                
                $saveArr = new CategoryReview();
                $saveArr->customer_name = $name;
                $saveArr->city = $cities[$key];
                $saveArr->article = $articles[$key];
                $saveArr->category = $req->category == TRUE ? $req->category :'';
                $saveArrival = $saveArr->save();
            }
                return response()->json([
                'check_num'=>100,
                'status'=>"Add Delete Successfully",
                ]);
        }
        return response()->json([
            'check_num'=>200,
            'status'=>"Add Delete Successfully",
        ]);
    }
    
     //delete checked delCheckCatReview
    public function delCheckCatReview(Request $req)
    {
        $ids = $req->ids;
        $category = CategoryReview::whereIn('id',$ids)->delete();
        if($category)
        {
            return response()->json(['status'=>"Reviews  Delete Successfully",
            ]);
        }else
        {
            return response()->json(['status'=>"Reviews  Not deleted",
            ]);
        }
          
    }
    
    
}
