<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{User, UserBusinessDetail, City};
use App\Models\Order;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Support\Str;
use Laravel\Sanctum\PersonalAccessToken;
use DB;
use Exception;
use Cache;

class AuthController extends Controller
{
    //login user
    // admin id added
    public function login(Request $req)
    {
        $admin_id = $req->admin_id ? $req->admin_id : 1;

        $validator = Validator::make($req->all(), [
            'whatsapp' => 'required',
            // 'password'=>'required',
            'remember_token' => 'required',
        ]);

        if (!$validator->passes()) {
            return $this->error($validator->errors()->toArray(), $validator->errors()->first(), 422);
        }

        DB::beginTransaction();
        try {

            $user = User::where('whatsapp', $req->whatsapp)->where('admin_id', $admin_id)->latest()->first();
            Auth::login($user);
            // Auth::attempt(['whatsapp' => $req->whatsapp, 'password' => $req->password])
            // if(Auth::login($user))
            // {
            // $user = Auth::guard('web')->user();
            // $login_user = Auth::guard('web')->user();
            $token = $user->createToken('MyApp')->plainTextToken;
            // $user->remember_token = $req->remember_token;
            $user->save();
            $user->setAttribute('token', $token);
            DB::commit();
            return $this->success($user, 'Successfully Login', 1);
            // }elseif($user = User::where('whatsapp', $req->whatsapp)->first()){
            // return $this->success([],'Incorrect detail, please contact admin', 0);
            // }else{
            // DB::commit();
            // return $this->error([],'Invalid login details', 0);
            // }
        } catch (Exception $e) {
            DB::rollback();
            return $this->error($e->getMessage(), $e->getMessage() . 'Login Error ! something went Wrong', 0);
        }
    }

    //add user
    // admin id added
    public function register(Request $req)
    {
        $phone = str_replace(' ', '', $req->phone);
        $req->request->remove('phone');
        $req->merge(['phone' => $phone]);
         
        $whatsapp = str_replace(' ', '', $req->whatsapp);
        $req->request->remove('whatsapp');
        $req->merge(['whatsapp' => $whatsapp]);
        
        $validator = Validator::make($req->all(), [
            'name' => 'required',
            // 'whatsapp'=>'required|unique:users,whatsapp|regex:/^((0))(3)([0-9]{9})$/',
            'whatsapp' => 'required|regex:/^((0))(3)([0-9]{9})$/',
            // 'phone'=>'required|regex:/^((0))(3)([0-9]{9})$/',
            // 'password'=>'required',
            // 'address'=>'required',
            // 'city_id'=>'required',
            'remember_token' => 'required',
        ]);

        if (!$validator->passes()) {
            return $this->error($validator->errors()->toArray(), $validator->errors()->first(), 422);
        }

        DB::beginTransaction();
        try {
            // $req->merge(['password' => \Hash::make($req->password)]);
            // check if user has blocked by admin or not
            $status = 0;
            $is_blocked = User::where(['whatsapp' => $req->whatsapp, 'status' => 1])->latest()->first();
            if ($is_blocked) {
                $status = 1;
            }

            $admin_id = $req->admin_id ? $req->admin_id : 1;
            $user = User::where('whatsapp', $req->whatsapp)->where('admin_id', $admin_id)->where('name', $req->name)->latest()->first();
            if (!$user) {
                // $user = User::create($req->all());
                $user = new User();
                $user->admin_id = $admin_id;
                $user->name = $req->name;
                $user->city_name = $req->city_name;
                $user->city_id = $req->city_id;
                $user->country = $req->country;
                $user->phone = $req->phone;
                // $user->password = \Hash::make($req->password);
                $user->password = \Hash::make($req->phone);
                $user->whatsapp = $req->whatsapp;
                $user->address = $req->address;
                $user->status = $status;
                $user->is_reseller = 0;
                // $user->remember_token = $req->remember_token;
                $user->save();
            } else {
                // return $this->success($user,'Account already exists, login or contact admin', 0);
            }

            // Auth::attempt(['whatsapp' => $req->whatsapp, 'password' => $req->password])
            // if(Auth::login($user))
            // {
            Auth::login($user);
            $login_user = Auth::guard('web')->user();
            $token = $login_user->createToken('MyApp')->plainTextToken;
            $user->setAttribute('token', $token);

            DB::commit();
            return $this->success($user, 'Joined Successfully', 1);
            // }else{
            //     DB::commit();
            //     return $this->error($user,'Something went wrong, contact admin', 0);
            // }

        } catch (Exception $e) {
            DB::rollback();
            return $this->error($e->getMessage(), 'Registeration Error ! something went Wrong', 0);
        }
    }

    // Cache::forget('cities');

    // cities
    public function cities()
    {

        // \Cache::forget('cities');
        $cities = Cache::rememberForever('cities', function () {
            return $cities = City::select('id', 'postex as name')
                                    // ->whereNotNull('c_city_name')
                                    // ->whereNotNull('postex_city_id')
                                    ->orderBy('c_city_name', 'ASC')
                                    ->whereNotNUll('postex')
                                    ->get();
            // $cities =  City::/*whereNotNull('c_city_name')->*/get(['id', 'name', 'c_city_name']);
            // $cities = $cities->transform(function ($city) {
            //     $city->name = $city->c_city_name;
            //     unset($city->c_city_name); // remove the old attribute
            //     return $city;
            // });
        });

        return $this->success($cities, '');
    }

    //profile
    public function profile(Reuqest $req)
    {
        $admin_id = $req->admin_id ? $req->admin_id : 1;
        $user = User::with('city')->where('admin_id', $admin_id)->find(auth('sanctum')->user()->id);
        return $this->success([$user, 'User Profile']);
    }

    //profile/update
    public function profile_update(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'city_id' => 'required',
            'name' => 'required',
            'courier_phone' => 'nullable|regex:/^((0))(3)([0-9]{9})$/',
        ]);

        if ($req->city_id == 'null') {
            return $this->error('Incorrect city id', 'Incorrect city id', 422);
        }

        if (!$validator->passes()) {
            return $this->error($validator->errors()->first(), 'Validation Error ! Enter Correct Details', 422);
        }

        $user = User::where('id', auth('sanctum')->user()->id)
            ->update($req->all());
        return $this->success([], 'User Update his Profile', 1);
    }

    // otp_verify
    public function otp_verify(Request $req)
    {
        $user = User::find(auth('sanctum')->user()->id);
        $user->is_verified = 1;
        $user->save();
        return $this->success([], 'User Verified');
    }

    //profile/update
    public function register_as_reseller(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'store_name' => 'required',
            'store_name' => 'required',
            'bank_name' => 'required',
            'account_title' => 'required',
            'account_number' => 'required',
        ]);

        if (!$validator->passes()) {
            return $this->error($validator->errors()->first(), $validator->errors()->first(), 422);
        }

        $user = auth('sanctum')->user();
        $user_id = $user->id;
        if ($user->is_reseller == 0) {
            User::where('id', $user_id)
                ->update([
                    'is_reseller' => 1
                ]);
        }

        UserBusinessDetail::updateOrCreate([
            'user_id' => $user_id
        ], [
            'user_id' => $user_id,
            'store_name' => $request->store_name,
            'store_address' => $request->store_address,
            'bank_name' => $request->bank_name,
            'account_title' => $request->account_title,
            'account_number' => $request->account_number,
        ]);

        return $this->success([], 'Your seller profile updated', 1);
    }

    public function reseller_profile()
    {
        $user = auth('sanctum')->user();
        $business_detail = UserBusinessDetail::where('user_id', $user->id)->first();

        return $this->success($business_detail, '', 1);
    }

    //profile
    public function user_info(Request $request)
    {
        $user = User::select('name', 'city_id', 'phone', 'address')->with('city:id,name')->where('whatsapp', $request->whatsapp)->first();
        if($user){
            if($user->city == null){
                $order = Order::select('name', 'city_id', 'phone', 'address')->with('city:id,name')->where('phone', $request->whatsapp)->first();
                $user->city = $order->city;
            }
            return $this->success($user, '', 1);
        }

        $user = Order::select('name', 'city_id', 'phone', 'address')->with('city:id,name')->where('phone', $request->whatsapp)->first();
        
        return $this->success($user, '', 1);
    }

    //login
    // public function login(Request $req){

    //     $validator = Validator::make($req->all(), [
    //             'phone'=>'required|exists:users,phone',
    //             'password'=>'required',
    //             'remember_token'=>'required',
    //     ]);

    //     if(!$validator->passes()) 
    //     {
    //         return $this->error($validator->errors()->toArray(),'Validation Error ! Enter Correct Details', 422);
    //     }
    //     // check delete user
    //     $checkUser = User::where('phone',$req->phone)->first();
    //         // ->makeHidden(['created_at','updated_at' ]);
    //     if($checkUser)
    //     {
    //         $creds= $req->only('phone','password');
    //         if(Auth::guard('web')->attempt($creds))
    //         {
    //             $user = User::where('phone',$req->phone)->first()
    //                 ->makeHidden(['created_at','updated_at','deleted_at']);
    //             $user->remember_token = $req->remember_token;
    //             $saveArrival = $user->save();
    //             if($saveArrival)
    //             {
    //                 return response()->json([
    //                 'status'=>'success',
    //                 'statusCode'=>'200',
    //                 'message'=>'User Verified',
    //                 'data'=>$user,
    //                 ]);
    //             }
    //             return response()->json([
    //                 'status'=>'fail',
    //                 'statusCode'=>'500',
    //                 'message'=>'User token did not save',
    //                 'data'=>$user,
    //                 ]);
    //         }
    //             return response()->json([
    //             'status'=>'fail',
    //             'statusCode'=>'404',
    //             'message'=>'Password Not matched',
    //             ]);
    //     }

    //     return response()->json([
    //         'status'=>'error',
    //         'statusCode'=>404,
    //         'message'=>'Use Record deleted By admin',]);


    // }



    //update profile
    // public function updateprofile(Request $req)
    // {

    //     $validator = Validator::make($req->all(), [
    //         'city_name'=>'required|string',
    //         'user_name'=>'required|string',
    //         'whatsapp'=>'required|digits:11',
    //         'id'=>'required|exists:users,id',
    //         'address'=>'required',
    //         'phone'=>'required|digits:11',
    //     ]);

    //     if (!$validator->passes()) 
    //      {
    //         return response()->json([
    //         'statusCode'=>500,
    //         'message'=>'Valiadtion Error',
    //         'error'=>$validator->errors()->toArray()]);
    //     }
    //     else
    //     {
    //         // password
    //         if($req->password)
    //         {
    //             $passlen = Str::length($req->password);
    //             if($passlen > 20 || $passlen < 5)
    //             {
    //                     return response()->json([
    //                     'check_num'=>102,
    //                     'status'=>'Password Must be 5-20 Character']);
    //             }

    //         }
    //         $user = User::find($req->id) ->makeHidden(['deleted_at' ]);;
    //         $user->address = $req->address;
    //         $user->city_name = $req->city_name;
    //         $user->phone = $req->phone;
    //         $user->whatsapp = $req->whatsapp;
    //         $user->name = $req->user_name;
    //          //password
    //         if($req->password)
    //         {
    //             $user->password =\Hash::make($req->password);
    //         }
    //         $saveArrival = $user->save();
    //             if($saveArrival){

    //                 return response()->json([
    //                     'statusCode'=>200,
    //                     'message'=>'Profile Update',
    //                     'data'=>$user,
    //                 ]);
    //             }
    //             return response()->json([
    //                 'statusCode'=>404,
    //                 'message'=>'Not Save',
    //             ]);

    //     }


    // }

    // forget_phonenum
    // public function forget_phonenum(Request $req)
    // {
    //      $validator = Validator::make($req->all(), [
    //         'phone'=>'required|exists:users,phone',
    //     ]);

    //     if (!$validator->passes()) 
    //      {
    //         return response()->json([
    //         'statusCode'=>500,
    //         'message'=>'Valiadtion Error',
    //         'error'=>$validator->errors()->toArray()]);
    //     }
    //     else
    //     {
    //         $user = User::where('phone',$req->phone)->first();
    //         return response()->json([
    //             'statusCode'=>200,
    //             'message'=>'succes',
    //             'data'=>$user,
    //         ]);
    //     }

    // }

    // resetpassword
    // public function resetpassword(Request $req)
    // {
    //      $validator = Validator::make($req->all(), [
    //         'id'=>'required|exists:users,id',
    //         'password'=>'required|min:5|max:20',
    //     ]);

    //     if (!$validator->passes()) 
    //      {
    //         return response()->json([
    //         'statusCode'=>500,
    //         'message'=>'Valiadtion Error',
    //         'error'=>$validator->errors()->toArray()]);
    //     }
    //     else
    //     {
    //         $user = user::findorFail($req->id);
    //         $user->password =\Hash::make($req->password);
    //         $saveArrival = $user->save();

    //             if($saveArrival){
    //                 return response()->json([
    //                     'statusCode'=>200,
    //                     'message'=>'Password Updated successfully!',
    //                     'data'=>$user,
    //                 ]);
    //             }
    //             return response()->json([
    //                 'statusCode'=>404,
    //                 'message'=>'Not Save',
    //             ]);

    //     }

    // }

}
