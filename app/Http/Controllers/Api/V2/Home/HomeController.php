<?php

namespace App\Http\Controllers\Api\V2\Home;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\category as Category;
use App\Models\{Product, Order, User, MessageNotification};
use App\Models\message; // as Message;

use Validator;
use Illuminate\Support\Str;
use Laravel\Sanctum\PersonalAccessToken;
use DB;
use Cache;
use Carbon\Carbon;
use App\Models\Problem;
use App\Models\Setting;
use App\Models\UserToken;
use App\Models\LockedkFolderPassword;
use App\Models\Charge;
use App\Models\Admin;
use App\Helpers\Helper;


class HomeController extends Controller
{
    public function home(Request $req)
    {
        $user = auth('sanctum')->user();
        $user_id = $user ? $user->id : null;
        // \Log::info($req->all());
        $messages = [];
        if ($user_id) {
            // $messages = MessageNotification::with('message:id,message,days,created_at')
            // ->whereHas('message', function($query){
            //     $query->where('created_at', '>=', Carbon::today()->subDays((int)'days' + 1));
            // })
            // ->where('user_id',$req->user_id)
            // ->whereNull('read_at')
            // ->get();
            // $messages = message::
            //                     // select(['id', 'message'])
            //                     // ->
            //                     with('message_notification')
            //                     ->whereHas('message_notification', function($query) use ($req){
            //                         // $query->select('message_id')->where('user_id', $req->user_id);
            //                     })
            //                     ->get();

            // $messages = message::select(['messages.id','messages.message','message_notifications.read_at'])
            // ->leftJoin('message_notifications', 'messages.id', '=', 'message_notifications.message_id')
            // ->where(function($q) use($user_id){
            //     $q->where('message_notifications.user_id', null)
            //       ->orWhere('message_notifications.user_id', $user_id);
            // })
            // // ->where('message_notifications.read_at', null)
            // ->where('messages.created_at', '>=', Carbon::today()->subDays((int)'messages.days' + 1))
            // ->get();
            $message_notifications = MessageNotification::where('user_id', $user_id)->get()->pluck('message_id');
            $messages = message::select(['messages.id', 'messages.message'])
                // ->join('message_notifications', 'messages.id', '=', 'message_notifications.message_id')
                ->where(function ($q) use ($user_id) {
                    $q->where('messages.user_id', NULL)
                        ->orWhere('messages.user_id', $user_id);
                })
                ->whereNotIn('messages.id', $message_notifications)
                ->where('messages.created_at', '>=', Carbon::today()->subDays((int)'messages.days' + 1))
                ->get();
        } elseif ($req->device_id) {
            $message_notifications = MessageNotification::where('device_id', $req->device_id)->get()->pluck('message_id');
            $messages = message::select(['messages.id', 'messages.message'])
                ->join('message_notifications', 'messages.id', '=', 'message_notifications.message_id')
                ->where('message_notifications.device_id', $req->device_id)
                ->whereNotIn('messages.id', $message_notifications)
                ->where('messages.created_at', '>=', Carbon::today()->subDays((int)'messages.days' + 1))
                ->get();
        }

        // $categories = Category::/*has('product')->where('status',0)->*/where('for_wao', 1)->get(['id','name','image']);
        $categories = Category::where('status', 0)->orderByRaw('ISNULL(order_number), order_number ASC')
            ->get(['id', 'name', 'image', 'order_number']);

        $admin_id = $req->admin_id ? $req->admin_id : 1;
        // \Log::info($req->all());
        if ($req->device_token && $req->device_id) {
            UserToken::updateOrCreate([
                'device_token' => $req->device_token,
                'device_id' => $req->device_id,
                'admin_id' => $admin_id,
            ], [
                'device_token' => $req->device_token,
                'device_id' => $req->device_id,
                'user_id' => $user_id,
                'admin_id' => $admin_id,
            ]);

            $token = $req->device_token;
            Helper::subscribeToFcm([$token], 'add', 'products');
        }

        $settings = Cache::rememberForever('zahidaz_settings_' . $admin_id, function () use ($admin_id) {
            return DB::table('settings')->select('attribute', 'value')->where('admin_id', $admin_id)->get();
        });

        $admin = Admin::select('name', 'email', 'website', 'whatsapp_number', 'logo', 'color_1', 'color_2', 'color_3', 'color_4', 'color_5')->where('id', $req->admin_id)->first();
        // return $this->success($admin, '');

        // $admin = Admin::select('name', 'email', 'website', 'whatsapp_number', 'logo', 'color_1', 'color_2', 'color_3', 'color_4', 'color_5')->where('id', $req->admin_id)->first();
        return $this->success([
            'messages' => $messages,
            'categories' => $categories,
            'settings' => $settings,
            'admin' => $admin,
        ], 'Categories Record');
    }

    public function admin(Request $req)
    {
        $admin = Admin::select('name', 'email', 'website', 'whatsapp_number', 'logo', 'color_1', 'color_2', 'color_3', 'color_4', 'color_5')->where('id', $req->admin_id)->first();
        return $this->success($admin, '');
    }

    public function categories(Request $req)
    {
        $categories = Category::where('status', 0)->orderByRaw('ISNULL(order_number), order_number ASC')
            ->get(['id', 'name', 'image', 'order_number']);
        return $this->success($categories, 'Categories Record');
    }


    // public function products(Request $req)
    // {
    //     $products = Product::Detail()->where('category_id',$req->category_id)
    //     ->orderBy('pinned_at','desc')->orderBy('id','desc')
    //     ->paginate(20);
    //     return $this->success($products,'Product Records with Reviews',2);
    // }


    // add complaints
    public function addProblem(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'whatsapp' => 'required|digits:11',
            // 'user_id'=>'required|integer',
            'phone' => 'required', //|digits:11
            'comment' => 'required',
            // 'image'=>'required|image',
        ]);

        if (!$validator->passes()) {
            return $this->error($validator->errors()->toArray(), 'Please fix the errors', 0, 422);
        }

        $problem = new Problem();
        $problem->whatsapp = $req->whatsapp;
        $problem->user_name = $req->phone;
        $problem->comment = $req->comment;

        if (auth()->guard('sanctum')->check()) {
            $problem->user_id = auth()->guard('sanctum')->user()->id;
        } else {
            $problem->user_id = 0;
        }

        // image
        if ($req->hasFile('image')) {
            $file = $req->file('image');
            $ext = $file->getClientOriginalExtension();
            $filename = time() . '.' . $ext;
            $file->move('complaint/', $filename);
            $problem->image = $filename;
        }
        $saveArrival = $problem->save();

        if ($saveArrival) {
            return $this->success([], 'We will respond to you within 24 working hours', 1);
        }

        return $this->error([], 'Please try again, there is an issues', 0, 404);
    }

    // lockFolder Products
    public function locked_folder_items(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'password' => 'required',
        ]);

        if (!$validator->passes()) {
            return $this->error($validator->errors()->first(), 'Password Validation', 0, 422);
        }
        if ($req->password == LockedkFolderPassword::find(1)->text_password) {
            $products = Product::
                // order by pinned 
                orderBy('pinned_at', 'desc')
                ->latest()
                ->where('is_locked', 1)
                ->paginate(25);
            return $this->success_paginate($products, 'Locked folder Products', 1);
        }
        return $this->error([], 'Locked Password Invalid', 0);
    }

    // read_message
    public function read_home_message(Request $req)
    {
        $user = auth('sanctum')->user();
        if ($user) {
            // $user_id = $user->id;

            // // get all messages of user
            // $notifications = MessageNotification::whereNull('read_at')->where('user_id',$user_id)->get();

            // // update all message_notifications read_at
            // MessageNotification::whereNull('read_at')
            //                     ->where('user_id',$user_id)
            //                     ->update([
            //                         'read_at' => now()->toDateString()
            //                     ]);

            // // pluck id to update all messages read_at
            // $messages = $notifications->pluck('message_id');
            // // update all messages read_at
            // Message::whereIn('id', $messages)
            //     ->update([
            //         'read_at'=> DB::raw('read_at+1'), 
            //     ]);

            $messages = $req->id;
            $arr = [];
            // \Log::info($messages);
            if (is_array($messages)) {
                foreach ($messages as $index => $message) {
                    // $arr[$index]['user_id'] = $user->id;
                    // $arr[$index]['device_id'] = $req->device_id;
                    // $arr[$index]['message_id'] = $message;
                    // $arr[$index]['read_at'] = now()->toDateString();
                    // \Log::info($arr[$index]);
                    $message_notification = MessageNotification::where('user_id', $user->id)->where('message_id', $message)->first();
                    if (!$message_notification) {
                        $message_notification = new MessageNotification();
                        $message_notification->user_id = $user->id;
                        $message_notification->message_id = $message;
                    }
                    $message_notification->device_id = $req->device_id;
                    $message_notification->read_at = now()->toDateString();
                    $message_notification->save();

                    // MessageNotification::updateOrCreate(
                    //     ['user_id' => $user->id, 'message_id' => $message],
                    //     ['user_id' => $user->id, 'message_id' => $message, 'device_id' => $req->device_id, 'read_at' => now()->toDateString()],
                    // );

                }

                // update all messages read_at
                Message::whereIn('id', $messages)
                    ->update([
                        'read_at' => DB::raw('read_at+1'),
                    ]);
            }
        } else {
            // pluck id to update all messages read_at
            $messages = $req->id;
            $arr = [];
            if (is_array($messages)) {
                foreach ($messages as $index => $message) {
                    $arr[$index]['device_id'] = $req->device_id;
                    $arr[$index]['message_id'] = $message;
                    $arr[$index]['read_at'] = now()->toDateString();
                }
                // update all message_notifications read_at
                MessageNotification::insert($arr);

                // update all messages read_at
                Message::whereIn('id', $messages)
                    ->update([
                        'read_at' => DB::raw('read_at+1'),
                    ]);
            }
        }


        return $this->success('', 'Message Read successfully', 1);
    }

    public function trackOrders(Request $request)
    {
        // $user = User::where('whatsapp', $request->input)->orwhere('phone', $request->input)->latest()->first();
        // $orders = [];
        // if ($user) {
        $orders =  Order::select(['id', 'created_at', 'adjustment_note', 'cancel_note', 'user_id', 'name', 'phone', 'city_id', 'address', 'status', 'charges', 'grandTotal', 'amount', 'description', 'courier_tracking_id', 'wao_seller_id', 'time', 'date'])
            ->with([
                'history' => function ($query) {
                    $query->latest();
                },
                'orderitems:id,order_id,prod_id,qty,price',
                'orderitems.product:id,name,price,article,thumbnail',
                'citydetail',
            ])
            ->whereHas('userDetail', function ($q) use ($request) {
                $q->where('whatsapp', $request->input);
            })
            // ->whereHas('userdetail', function ($q) use ($request) {
            //     $q->where('id', $request->input);
            // })
            ->withSum('orderitems', 'qty')
            // ->whereNotNull('wao_seller_id')
            ->latest()->take(10)
            ->get();
        // }
        return $this->success($orders, 'Track Orders', 1);
    }

    public function charges()
    {
        $charges = Charge::select('suit', 'charges')->get();
        return $this->success($charges, '', 1);
    }
}
