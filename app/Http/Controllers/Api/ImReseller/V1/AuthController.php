<?php

namespace App\Http\Controllers\Api\ImReseller\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Str;
use Laravel\Sanctum\PersonalAccessToken;
use Validator;
use DB;
Use Exception;
use Cache;

class AuthController extends Controller
{
    public function send_otp(Request $req)
    {
      $validator = Validator::make($req->all(), [
          'mobile'=>'required',
      ]);

      if(!$validator->passes()) 
      {
        return $this->error($validator->errors()->toArray(), $validator->errors()->first(), 422);
      }

      // send otp here
      $otp = 123456;

      // check if user exits
      $user = User::where('phone', $req->mobile)
                  ->where('whatsapp', $req->mobile)
                  // ->where('for_reseller', 1)
                  ->first();

      // save record in database, if not exits, else update otp
      if(!$user){
        $user = new User();
        $user->name = $req->mobile;
        $user->phone = $req->mobile;
        $user->whatsapp = $req->mobile;
        $user->otp = $otp;
        // $user->for_wao = 0;
        // $user->for_reseller = 1;
        $user->password = \Hash::make(time());
        $user->save();  
      }else{
        $user->otp = $otp;
        $user->save(); 
      }

      return $this->success([],'Otp has been successfully sent', 1);

    }

    public function verify_otp(Request $req)
    {
      $validator = Validator::make($req->all(), [
        'mobile'=>'required',
        'otp'=>'required',
      ]);

      if(!$validator->passes()) 
      {
        return $this->error($validator->errors()->toArray(), $validator->errors()->first(), 422);
      }

      // verify otp here

      $user = User::select('id', 'name', 'phone', 'whatsapp')
                  ->where('phone', $req->mobile)
                  ->where('whatsapp', $req->mobile)
                  // ->where('for_reseller', 1)
                  ->where('otp', $req->otp)
                  ->first();
      if($user){
        $user->is_verified = 1;
        $user->save();
        
        $login_user = Auth::guard('imreseller')->loginUsingId($user->id);
        $token = $login_user->createToken('imreseller')->plainTextToken;
        $user->setAttribute('token',$token);

        return $this->success($user,'Otp has been successfully verified', 1);

      }else{

        return $this->success([],'Invalid otp code', 1);

      }

    }

    public function profile_update(Request $request)
    {
      $user = auth('sanctum')->user();
      if($request->name)
        $user->name = $request->name;

      if($request->gender)
        $user->gender = $request->gender;
  
      if($request->age_group)
        $user->age_group = $request->age_group;
        
      $user->save();
      return $this->success([],'Successfully, updated info', 1);  
      
    }

    public function logout()
    {
      // return auth('sanctum')->user();
      // $request->user()->tokens()->delete();
      $user = auth('sanctum')->user();
      if($user){
        $user->tokens()->delete();
        return $this->success([],'Logout successfully', 1);  
      }else{
        return $this->success([],'Already logout successfully', 1);  
      }

    }
}
