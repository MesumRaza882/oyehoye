<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Support\Str;


class UserController extends Controller
{
    //add user
       public function register(Request $req){

        \Log::info($req->all());
        $request->merge(['new_phone' => str_replace(' ', '', $req->phone)]);
        $request->merge(['new_whatsapp' => str_replace(' ', '', $req->whatsapp)]);
        $validator = Validator::make($req->all(), [
            'country'=>'required',
            'city_name'=>'required|string',
            'user_name'=>'required',
            // 'phone'=>'required|digits:11|unique:users,phone',
            'new_phone'=>'required|digits:11|unique:users,phone',
            'password'=>'required|min:5|max:20',
            'address'=>'required',
            // 'whatsapp'=>'required|digits:11',
            'new_whatsapp'=>'required|digits:11',
            'remember_token'=>'required',
        ]);

        if (!$validator->passes()) 
         {
            return response()->json([
            'statusCode'=>500,
            'message'=> $validator->errors()->first(),
            'error'=>$validator->errors()->toArray()]);
        }
        else
        {
                $user = new User();
                // $user->phone = $req->phone;
                // $user->whatsapp = $req->whatsapp;
                $user->phone = $req->new_phone;
                $user->whatsapp = $req->new_whatsapp;
                $user->name = $req->user_name;
                $user->country = $req->country;
                $user->city_name = $req->city_name;
                $user->password = \Hash::make($req->password);
                $user->address = $req->address;
                $user->remember_token = $req->remember_token;
                $saveArrival = $user->save();
                if($saveArrival){
                    return response()->json([
                        'statusCode'=>200,
                        'message'=>'Registered Successfully',
                        'data'=>$user,
                    ]);
                }
                return response()->json([
                    'statusCode'=>404,
                    'message'=>'Not Save',
                ]);
         }
    }

    //login
    public function login(Request $req){
            
        $request->merge(['new_phone' => str_replace(' ', '', $req->phone)]);
        $validator = Validator::make($req->all(), [
            'new_phone'=>'required|exists:users,phone',
            // 'phone'=>'required|exists:users,phone',
            'password'=>'required|min:5|max:20',
            'remember_token'=>'required',
        ]);

        if (!$validator->passes()) 
         {
            return response()->json([
            'statusCode'=>422,
            'message'=>$validator->errors()->first(),
            'error'=>$validator->errors()->toArray()]);
        }
        else
        {
            // check delete user
            $checkUser = User::where('phone',$req->new_phone)->first();
                // ->makeHidden(['created_at','updated_at' ]);
            if($checkUser)
            {
                $creds= $req->only('new_phone','password');
                if(Auth::guard('web')->attempt($creds))
                {
                    $user = User::where('phone',$req->new_phone)->first()
                        ->makeHidden(['created_at','updated_at','deleted_at']);
                    $user->remember_token = $req->remember_token;
                    $saveArrival = $user->save();
                    if($saveArrival)
                    {
                        return response()->json([
                        'status'=>'success',
                        'statusCode'=>'200',
                        'message'=>'User Verified',
                        'data'=>$user,
                        ]);
                    }
                    return response()->json([
                        'status'=>'fail',
                        'statusCode'=>'500',
                        'message'=>'User token did not save',
                        'data'=>$user,
                        ]);
                }
                 return response()->json([
                    'status'=>'fail',
                    'statusCode'=>'404',
                    'message'=>'Password Not matched',
                    ]);
            }
            
            return response()->json([
                'status'=>'error',
                'statusCode'=>404,
                'message'=>'Use Record deleted By admin',]);
        }
            
    }
    
     //profile
     public function profile(Request $req){

        $validator = Validator::make($req->all(), [
            'id'=>'required|exists:users,id',
        ]);

        if (!$validator->passes()) 
         {
            return response()->json([
            'statusCode'=>500,
            'message'=>$validator->errors()->first(),
            'error'=>$validator->errors()->toArray()]);
        }
        else
        {
            $user = User::find($req->id);
            return response()->json([
                'statusCode'=>200,
                'message'=>'succes',
                'data'=>$user,
            ]);
        }


    }
    
    //update profile
    public function updateprofile(Request $req)
    {
        $request->merge(['new_phone' => str_replace(' ', '', $req->phone)]);
        $request->merge(['new_whatsapp' => str_replace(' ', '', $req->whatsapp)]);
        $validator = Validator::make($req->all(), [
            'city_name'=>'required|string',
            'user_name'=>'required|string',
            // 'whatsapp'=>'required|digits:11',
            'new_whatsapp'=>'required|digits:11',
            'id'=>'required|exists:users,id',
            'address'=>'required',
            // 'phone'=>'required|digits:11',
            'new_phone'=>'required|digits:11',
        ]);

        if (!$validator->passes()) 
         {
            return response()->json([
            'statusCode'=>500,
            'message'=>$validator->errors()->first(),
            'error'=>$validator->errors()->toArray()]);
        }
        else
        {
            // password
            if($req->password)
            {
                $passlen = Str::length($req->password);
                if($passlen > 20 || $passlen < 5)
                {
                        return response()->json([
                        'check_num'=>102,
                        'status'=>'Password Must be 5-20 Character']);
                }

            }
            $user = User::find($req->id) ->makeHidden(['deleted_at' ]);;
            $user->address = $req->address;
            $user->city_name = $req->city_name;
            // $user->phone = $req->phone;
            // $user->whatsapp = $req->whatsapp;
            $user->phone = $req->new_phone;
            $user->whatsapp = $req->new_whatsapp;
            $user->name = $req->user_name;
             //password
            if($req->password)
            {
                $user->password =\Hash::make($req->password);
            }
            $saveArrival = $user->save();
                if($saveArrival){

                    return response()->json([
                        'statusCode'=>200,
                        'message'=>'Profile Update',
                        'data'=>$user,
                    ]);
                }
                return response()->json([
                    'statusCode'=>404,
                    'message'=>'Not Save',
                ]);

        }
      
        
    }
    
    // forget_phonenum
    public function forget_phonenum(Request $req)
    {
        $request->merge(['new_phone' => str_replace(' ', '', $req->phone)]);
         $validator = Validator::make($req->all(), [
            // 'phone'=>'required|exists:users,phone',
            'new_phone'=>'required|exists:users,phone',
        ]);

        if (!$validator->passes()) 
         {
            return response()->json([
            'statusCode'=>500,
            'message'=>$validator->errors()->first(),
            'error'=>$validator->errors()->toArray()]);
        }
        else
        {
            $user = User::where('phone',$req->new_phone)->first();
            return response()->json([
                'statusCode'=>200,
                'message'=>'succes',
                'data'=>$user,
            ]);
        }

    }
    
    // resetpassword
    public function resetpassword(Request $req)
    {
         $validator = Validator::make($req->all(), [
            'id'=>'required|exists:users,id',
            'password'=>'required|min:5|max:20',
        ]);

        if (!$validator->passes()) 
         {
            return response()->json([
            'statusCode'=>500,
            'message'=>$validator->errors()->first(),
            'error'=>$validator->errors()->toArray()]);
        }
        else
        {
            $user = user::findorFail($req->id);
            $user->password =\Hash::make($req->password);
            $saveArrival = $user->save();
            
                if($saveArrival){
                    return response()->json([
                        'statusCode'=>200,
                        'message'=>'Password Updated successfully!',
                        'data'=>$user,
                    ]);
                }
                return response()->json([
                    'statusCode'=>404,
                    'message'=>'Not Save',
                ]);

        }
      
    }
    
}
