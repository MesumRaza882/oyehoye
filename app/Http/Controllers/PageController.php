<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Order, User};
use Illuminate\Support\Facades\DB;


class PageController extends Controller
{

  //view charges
  public function privacy_policy()
  {
    return view('pages.privacy-policy');
  }

  //view charges
  public function delete_policy()
  {
    return view('pages.delete-policy');
  }

  public function orderTrack(Request $request)
  {
     $order = Order::where('courier_tracking_id', $request->cn)->first();
    if ($order) {
      $order = $order->load(['history' => function ($query) {
        $query->orderBy('created_at', 'desc');
      }]);
      return view('track', compact('order'));
    }
    return (abort(404));
  }
  public function trackOrders(Request $request)
  {
    $user = User::where('whatsapp', $request->input)/*->orwhere('phone', $request->input)*/->latest()->first();
    $orders = [];
    if ($user) {
      $orders =  Order::select(['id', 'user_id', 'name', 'phone', 'city_id', 'address', 'status', 'charges', 'grandTotal', 'slip', 'description', 'courier_tracking_id', 'wao_seller_id', 'time', 'date'], DB::raw('(CASE WHEN status = "Unbooked" THEN "DISPATCHED" WHEN status = "booked" THEN "DISPATCHED" ELSE status END) AS status'))
        ->with([
          'history' => function ($query) {
            $query->latest();
          },
          'orderitems:id,order_id,prod_id,qty',
          'orderitems.product:id,name,price,article,thumbnail',
          'citydetail',
        ])
        // ->withcount('orderitems')
        ->withSum('orderitems', 'qty')

        // ->whereNotNull('wao_seller_id')
        ->latest()->take(10)
        ->where('user_id', $user->id)->get();
    }
    return view('admin.Order.generalOrdersTrack', compact('orders'));
  }
}
