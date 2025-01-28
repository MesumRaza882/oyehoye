<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Order, Admin};
use App\Helpers\Helper;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Validator;

class ProfitManageController extends Controller
{
    public function ordersProfit(Request $req)
    {
        $paginate_record = $req->records ? $req->records : 50;
        $status = $req->input('status');

        $orderStatus = in_array($status, ['PENDING', 'DISPATCHED', 'DELIVERED', 'ON-THE-WAY', 'RETURNED', 'CANCEL', 'Team Review your Order']) ? $status : '';

        $startdate = $req->fromDate;
        $enddate =  $req->toDate;
        $records = Order::select('*', DB::raw('(CASE WHEN status = "Unbooked" THEN "DISPATCHED" WHEN status = "booked" THEN "DISPATCHED" ELSE status END) AS status'))
            ->where('is_blocked_customer_order', 0)
            // track order type filter
            ->when($req->tracking_order_type, function ($query) use ($req) {
                return $query->WhereIf('tracking_order_type', 'Like', "%{$req->tracking_order_type}%");
            })
            ->where('reseller_profit', '>', 0)
            ->WhereIf('status', 'Like', "%{$orderStatus}%")
            ->WhereIf('profit_transaction_status', 'Like', "%{$req->profit_transaction_status}%")
            ->filterByDate($startdate, $enddate)
            ->where(function ($q) use ($req) {
                $q->WhereIf('id', '=', $req->search_input);
            })
            ->whereNotNull('user_id')
            // get order app
            ->where('wao_seller_id', null)
            ->withcount('orderitems')
            ->orderByRaw("id DESC, status DESC");

        // filter by specific admin app/web orders
        if ($req->admin_id) {
            $records->WhereIf('admin_id', '=', $req->admin_id);
        }

        $admins = Admin::wherehas('adminOrders')->whereIn('role', [1, 3])->get(['id', 'email']);
        $total_records = $records->count();
        $orders = $records->paginate($paginate_record);
        return view('admin.Order.resellerProfitOrders', compact('orders', 'admins', 'total_records'));
    }


    public function ordersProfitCalc(Request $req)
    {
        $ids = $req->ids;
        $web_reseller_profit = Order::whereIn('id', $ids)->get()->sum('reseller_profit');
        return $this->success($web_reseller_profit, 'profit');
    }

    public function ordersProfitPaid(Request $req)
    {

        try {
            DB::beginTransaction();

            // Get IDs from request
            $ids = json_decode($req->input('allids'));
            
            // Upload profit screenshot if provided
            $profit_screenshot = null;
            if ($req->hasFile('profit_screenshot')) {
                $profit_screenshot = Helper::upload_image($req->file('profit_screenshot'), 'Order_slips');
            }

            // Update orders
            $orders = Order::whereIn('id', $ids)->get();
            foreach ($orders as $item) {
                $item->payment_method = $req->input('payment_method');
                $item->profit_screenshot = $profit_screenshot;
                $item->profit_transaction_status = 'paid';
                $item->save();
            }

            DB::commit();
            return $this->success([], 'Orders Profit Paid Successfully');
        } catch (Exception $e) {
            DB::rollback();
            return $this->success([], 'Failed to pay orders profit', 1);
        }
    }
}
