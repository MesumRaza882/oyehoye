<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Notification;
use App\Models\Cart;
use App\Models\category;
use App\Models\Order_item;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use App\Models\SinglePro_Review;
use App\Models\ProductReview;
use App\Models\User;
use App\Events\ItemAdded;
use App\Helpers\Helper;

class LuxuryController extends Controller
{
    public function addLuxury()
    {
        $categories =  category::all();
        return view('admin.luxury.addLuxury',compact('categories'));
    }

    function saveLuxury(Request $req)
    {
      
        $req->validate([
            'name'=>'required|string|unique:products,name',
            'article'=>'required|string',
            'price'=>'required|integer',
            'soldItem'=>'required|integer',
            'purchase'=>'required|integer',
            'profit'=>'required|integer',
            'video'=>'required',
            'inc_perMin'=>'required|integer',
            'solded'=>'required|integer',
            'itemcategory'=>'required|exists:categories,id',
        ]);
        
        $saveLux = new Product();
        $saveLux->name = $req->name;
        $saveLux->article = $req->article;
        $saveLux->category_id = $req->itemcategory;
        $saveLux->price = $req->price;
        $saveLux->soldItem = $req->soldItem;
        $saveLux->reviews = $req->reviews == TRUE ? $req->reviews :'';
        $saveLux->color = $req->color == TRUE ? $req->color :'';
        $saveLux->variety = $req->variety == TRUE ? $req->variety :'';
        $saveLux->discount = $req->discount == TRUE ? $req->discount : 0;
        $saveLux->profit = $req->profit;
        $saveLux->purchase = $req->purchase;
        $saveLux->increase_perMin = $req->inc_perMin;
        $saveLux->soldAdm = $req->solded;
        $saveLux->exceed_limit = $req->exceed_limit == TRUE ? $req->exceed_limit : Null;
        
        if($req->file('video')){
            $file= $req->file('video');
             $filename = date('YmdHi').str_replace(' ', '_', $file->getClientOriginalName());
            // $filename= date('YmdHi').$file->getClientOriginalName();
            $file->move(public_path('video/'), $filename);
            $saveLux['video']= $filename;
        }
        // if Video Thumbnail Uploaded By admin
        if($req->hasFile('image'))
            {
                $file = $req->file('image');
                $ext = $file->getClientOriginalExtension();
                $filename = time().'.'.$ext;
                $file->move('video/thumbnail/',$filename);
                $saveLux->thumbnail = $filename;
            }
        // events Working
        // $name = $req->name;
        // event(new ItemAdded($name));
        // $saveLux->video_link_embed = $req->video_link_embed;
        $saveArrival =  $saveLux->save();

        if($saveArrival){
            
            // fsm Ntification send..........
             $FcmTokens = User::whereNotNull('remember_token')->pluck('remember_token')->all();
            if(!empty($FcmTokens)){
                $arr = [
                    'Alert' => 'New Item Added! ',
                    'title' => $saveLux->name,
                    'Price' => $saveLux->price
                ];
                $regIdChunk=array_chunk($FcmTokens,900);
                foreach($regIdChunk as $FcmToken){
                    $output = Helper::sendWebNotification($arr, $FcmToken);
                }
            }
        // end...........
            $arr = array();
            $not = new Notification();
            $not->prod_id = $saveLux->id;
            $not->category = $req->itemcategory;
            $not->user_id = implode(',', $arr); ;
            $savenot = $not->save();
            return redirect()->route('addLuxury')->with('success','New Item Added Successfully...');
        }
        return redirect()->route('addLuxury')->with('fail','Something Error!');
    }

    function viewLuxury(Request $req)
    {
        $keyword = $req->search;
        $search_pro = Product::where('name', 'LIKE', "%$keyword%")->get();
        $soldProducts = Product::where('soldstatus',1)->orWhere('soldItem','<=',0)->with('itemcategory')->get();
        $newArrival = Product::latest()->where('soldstatus',0)->Where('soldItem','>',0)->with('itemcategory')->get();
        
        if($keyword)
        {
            $newArrival = Product::where('name', 'LIKE', "%$keyword%")->with('itemcategory')->get();
            return view('admin.luxury.viewLuxury',compact('newArrival','search_pro','soldProducts','keyword'));
        }

        else

        {
            $newArrival =Product::latest()->where('soldstatus',0)->Where('soldItem','>',0)->with('itemcategory')->get();
            return view('admin.luxury.viewLuxury',compact('search_pro','newArrival','soldProducts','keyword'));
        }

    }
    
    // delete product
    function delLuxury($id)
    {
        $dellux = Product::find($id);
        if ($dellux->video) {
            
            $path = ('video/'.$dellux->video);
            if(File::exists($path))
            {
                File::delete($path);
            }

        }
        // if Video Thumbnail Uploaded By admin
        // if($req->hasFile('image'))
        //     {
        //         $path = ('video/thumbnail/'.$saveLux->thumbnail);
        //         if(File::exists($path))
        //         {
        //               File::delete($path);
        //         }
        //     }
        // cart items
        $cart = Cart::where('prod_id',$id)->get();
        $notif = Notification::where('prod_id',$id)->get();
        $cartItems = Order_item::where('prod_id',$id)->get();
        $review = ProductReview::where('prod_id',$id)->get();
        $sreview = SinglePro_Review::where('prod_id',$id)->get();
        
        if($cartItems){ $cartItems->each->delete(); }
        if($notif){ $notif->each->delete(); }
        if($cart){ $cart->each->delete(); }
        if($review){ $review->each->delete(); }
        if($sreview){ $sreview->each->delete(); }
        $dellux->delete();
        return redirect()->route('viewLuxury');
    }
    
    // view single product
    function editLuxury($id)
    {
        $editLux = Product::with('prodReviews','SingproductRev')->where('id',$id)->first();
        $categories = category::where('id','!=',$editLux->category_id)->get();
        
        // change minute edit if not solded
        if($editLux->soldstatus == 0 && $editLux->soldItem > 0)
        {
            $minutes = $editLux->updated_at->diffInMinutes(Carbon::now());
            $divide = intdiv($minutes,$editLux->increase_perMin);
            $editLux->soldAdm = ($editLux->soldAdm)+$divide;
            $editLux->save();
        }
        
            return view('admin.luxury.editLuxury',compact('editLux','categories'));
    }

    // update product
    function updateLuxury(Request $req,$id)
    {
        // return $req->all();
         $req->validate([
            'name'=>'required|string',
            'article'=>'required|string',
            'price'=>'required|integer',
            'soldItem'=>'required|integer',
            'purchase'=>'required|integer',
            'profit'=>'required|integer',
            'video'=>'mimes:mp4',
            'inc_perMin'=>'required|integer',
            'solded'=>'required|integer',
            'itemcategory'=>'required|exists:categories,id',
        ]);
        
        // return $req->all()
        $saveLux = Product::find($id);
        $saveLux->name = $req->name;
        $saveLux->article = $req->article;
        $saveLux->price = $req->price;
        $saveLux->category_id = $req->itemcategory;
        $saveLux->soldItem = $req->soldItem;
        $saveLux->reviews = $req->reviews == TRUE ? $req->reviews :'';
        $saveLux->color = $req->color == TRUE ? $req->color :'';
        $saveLux->variety = $req->variety == TRUE ? $req->variety :'';
        $saveLux->discount = $req->discount == TRUE ? $req->discount :0;
        $saveLux->profit = $req->profit;
        $saveLux->purchase = $req->purchase;
        $saveLux->increase_perMin = $req->inc_perMin;
        $saveLux->soldAdm = $req->solded;
        $saveLux->exceed_limit = $req->exceed_limit == TRUE ? $req->exceed_limit : Null;
        
        if ($req->hasFile('video')) {
            
            $path = ('video/'.$saveLux->video);
            if(File::exists($path))
            {
                File::delete($path);
            }

            $file= $req->file('video');
             $filename = date('YmdHi').str_replace(' ', '_', $file->getClientOriginalName());
            // $filename= date('YmdHi').$file->getClientOriginalName();
            $file->move(public_path('video/'), $filename);
            $saveLux['video']= $filename;
        }
         // if Video Thumbnail Uploaded By admin
            if($req->hasFile('image'))
            {
                $path = ('video/thumbnail/'.$saveLux->thumbnail);
                if(File::exists($path))
                {
                      File::delete($path);
                }
                $file = $req->file('image');
                $ext = $file->getClientOriginalExtension();
                $filename = time().'.'.$ext;
                $file->move('video/thumbnail/',$filename);
                $saveLux->thumbnail = $filename;
            }
            
            // $saveLux->video_link_embed = $req->video_link_embed;
            $saveLux->save();
            return redirect()->route('viewLuxury')->with('success','Item Updated Successfully!');
        }
        

    
}
