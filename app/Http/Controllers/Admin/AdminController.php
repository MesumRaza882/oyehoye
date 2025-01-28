<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\{Admin, Product, Order, category};
use Illuminate\Support\Facades\Hash;
use Validator;
use Illuminate\Support\Facades\Cookie;
use Cache;
use App\Models\ProductReview;


class AdminController extends Controller
{

    //index
    public function index()
    {
        $data = [];

        // orders data
        $data['pendingOrders'] = Order::where('status', 'PENDING')->where('is_blocked_customer_order', '!=', 1)->count();
        $data['dispatchedOrders'] = Order::where('status', 'DISPATCHED')->where('is_blocked_customer_order', '!=', 1)->count();
        $data['cancelOrders'] = Order::where('status', 'CANCEL')->where('is_blocked_customer_order', '!=', 1)->count();
        $data['overallOrdersCount'] = Order::count();
        $data['pendingProfit'] = Order::where('status', 'pending')
            ->sum('grandProfit');
        $data['dispatchedProfit'] = Order::where('status', 'DISPATCHED')
            ->sum('grandProfit');

        // categories
        $data['allCategories'] = category::count();
        $data['activeCategories'] = category::where('status', 0)->count();
        $data['InactiveCategories'] = category::where('status', 1)->count();

        // products
        $data['allProducts'] = Product::count();
        $data['activeProducts'] = Product::where('soldstatus', 0)->orwhere('soldItem', '!=', 0)->count();
        $data['InactiveProducts'] = Product::where('soldstatus', 1)->orwhere('soldItem', '=', 0)->count();

        return view('admin.index', compact('data'));
    }

    //Check 
    public function check(Request $req)
    {
        $req->validate([
            'email' => 'required|exists:admins,email',
            'password' => 'required|min:5|max:20',
        ]);

        // Check if admin exists
        $admin = Admin::where('email', $req->email)->first();
        $remember = $req->has('remember') ? true : false;
        if (!$admin) {
            return redirect()->route('admin.login')->with('error', 'Invalid Credientials...');
        }

        // Check if the admin is inactive
        if ($admin->status === 2) {
            return redirect()->route('admin.login')->with('error', 'Your status is In-active...');
        }

        $creds = $req->only('email', 'password');
        if (Auth::guard('admin')->attempt($creds)) {

            // Delete previous cookies
            Cookie::queue(Cookie::forget('cookieEmail'));
            Cookie::queue(Cookie::forget('cookiePassword'));

            if ($remember) {
                Cookie::queue('cookieEmail', $req->email, 90000);
                Cookie::queue('cookiePassword', $req->password, 90000);
            }

            Cache::forget('pending_reviews_count');
            Cache::remember('pending_reviews_count', 60, function () {
                return ProductReview::where('status', 0)->count();
            });

            Cache::forget('shipper_advice_count');
            Cache::remember('shipper_advice_count', 60, function () {
                return Order::where('status', 'Re-Attempted')->count();
            });



            // ... Rest of your login logic ...
            return redirect()->route('admin.home');
        }

        return redirect()->route('admin.login')->with('error', 'Invalid Credientials...');
    }

    //logout
    public function logout(Request $req)
    {
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login');
    }

    //adminUpdate profile
    public function updateAdminProfile(Request $req)
    {
        $admin = Admin::where('id', auth()->user()->id)->first();
        //password
        if ($req->password) {
            $admin->password = Hash::make($req->password);
        }

        //name
        $admin->name = $req->name;
        $admin->trax_api_key = $req->trax_api_key;
        $admin->trax_pickup_address_id = $req->trax_pickup_address_id;
        $admin->postEx_apiToken = $req->postEx_apiToken;
        $admin->postEx_pickupAddressCode = $req->postEx_pickupAddressCode;
        $admin->postEx_apiToken_nowshera = $req->postEx_apiToken_nowshera;
        $admin->postEx_pickupAddressCode_nowshera = $req->postEx_pickupAddressCode_nowshera;
        $admin->mnp_username = $req->mnp_username;
        $admin->mnp_password = $req->mnp_password;
        $admin->locationID = $req->locationID;
        $save =  $admin->save();
        if ($save) {
            return response()->json([
                'check_num' => 100,
                'status' => 'Profile Update Successfully!'
            ]);
        }
    }
}
