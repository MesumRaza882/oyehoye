<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rate;
use App\Models\Charge;
use App\Models\Luckydraw;
use App\Models\RestAddress;
use App\Models\message;
use App\Models\Problem;
use Illuminate\Support\Facades\File;
use Validator;

class RateController extends Controller
{
    
    //view charges
    public function viewCharges(){
            
        $charges = Charge::all();
        return view('admin.Charges.viewCharges',compact('charges'));
    }
    //view Address
    public function viewResAddress(){
            
        $address = RestAddress::all();
        return view('admin.Customer_Rate.addResAddress',compact('address'));
    }
    // add Restrict address
    public function addAddress(Request $req)
    {
         $input = $req->all();
         $address = explode(",", $input['address']);
         
        foreach($address as $key=>$add){
            
            $saveArr = new RestAddress();
            $saveArr->address = $add;
            $saveArr->save();
        }
        return redirect()->route('viewResAddress')->with('success','Address Added Successfully');
        
    }
    
     //delete checked delAddress
    public function delAddress(Request $req)
    {
        $ids = $req->ids;
        $address = RestAddress::whereIn('id',$ids)->delete();
        if($address)
        {
            return response()->json(['status'=>"Addresses  Delete Successfully",
            ]);
        }else
        {
            return response()->json(['status'=>"Addresses  Not deleted",
            ]);
        }
          
    }
    
    
    // add charge
    public function addcharge(Request $req){
         $req->validate([
            'suit'=>'required|numeric|unique:charges,suit',
            'charges'=>'required|numeric',
        ]);
        $delivery_charges = new Charge();
        $delivery_charges->suit = $req->suit;
        $delivery_charges->charges = $req->charges;
        $delivery_charges->save();
        return redirect()->route('viewCharges')->with('message','Charge Added Successfully');
    }
    
    // delete Charge
    public function delcharge(Request $req, $id){
        $charge = Charge::find($id);
        if($charge){
            $charge->delete();
            return redirect()->route('viewCharges')->with('message','Charge Deleted Successfully');
        }
        return redirect()->route('viewCharges')->with('error','Error to delete Charge');
    }
    
   
  
    
    // View LuckyDraw
     public function viewLuckydraw()
    {
        $rates = Luckydraw::get();
        $allrates = Luckydraw::all();
        return view('admin.Customer_Rate.viewLuckydraw',compact('rates','allrates'));   
    }
    
    // del lucky
    public function dellucky(Request $req, $id){
        $charge = Luckydraw::find($id);
        if($charge){
            $charge->delete();
            return redirect()->route('viewLuckydraw')->with('success','Luckydarw Delete Successfully');
        }
        return redirect()->route('viewLuckydraw')->with('fail','Error to delete Luckydraw');
    }
    
      //delete checked Problems
    public function delCheckLucky(Request $req)
    {
        $ids = $req->ids;
        $category = Luckydraw::whereIn('id',$ids)->delete();
        if($category)
        {
            return response()->json(['status'=>"LuckyDraws  Delete Successfully",
            ]);
        }else
        {
            return response()->json(['status'=>"LuckyDraws  Not deleted",
            ]);
        }
         
          
    }
    
    
    
    // View Problems
     public function viewProblems()
    {
        $rates = Problem::get();
        $allrates = Problem::all();
        return view('admin.Customer_Rate.viewProblem',compact('rates','allrates'));
    }

    // del problem
    public function delproblem(Request $req, $id){
        $problem = Problem::find($id);
        if($problem){
            if($problem->image)
            {
                $path = 'complaint/'.$problem->image;
                    if(File::exists($path))
                    {
                          File::delete($path);
                    }
            }
            $problem->delete();
            return redirect()->route('viewProblems')->with('success','Problem Delete Successfully');
        }
        return redirect()->route('viewProblems')->with('fail','Error to delete Problem');
    }
    
    //delete checked Problems
    public function delCheckProblems(Request $req)
    {
        $ids = $req->ids;
        $problems = Problem::whereIn('id',$ids)->get();
        if($problems)
        {
            foreach($problems as $problem)
            {
                 if($problem->image)
                {
                    $path = 'complaint/'.$problem->image;
                        if(File::exists($path))
                        {
                              File::delete($path);
                        }
                }
                $problem->delete();
            }
            
            return response()->json(['status'=>"Problems  Delete Successfully",
            ]);
        }else
        {
            return response()->json(['status'=>"Problems  Not deleted",
            ]);
        }
          
    }

   
    

}
