<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\{Admin, ResellerSetting, Product, Order, category, LockedkFolderPassword};

class DashboardController extends Controller
{
    //index
    public function index()
    {
        // for super admin
        $data = [];
        $resetDate = LockedkFolderPassword::where('text_password', 'reset_type')->first()->reset_orders_date;
        $currentDate = Carbon::now()->toDateString();
        $formattedResetDate = Carbon::parse($resetDate)->format('Y-m-d');

        // tempoarary set for admin1
        $startdate = null;
        $enddate = auth()->user()->id == 58 ? '2024-06-06' : null;

        if (auth()->user()->role === 1) {
            $data['pendingOrders'] = Order::filterByDate($startdate, $enddate)->where(function ($query) {
                $query->whereNull('wao_seller_id')
                    ->orWhere('is_warehouseTeam_order', '=', 1);
            })->filterByDate($formattedResetDate, $currentDate)->where('status', 'PENDING')->where('is_blocked_customer_order', '!=', 1)->count();
            // orders data
            $data['dispatchedOrders'] = Order::filterByDate($startdate, $enddate)->where(function ($query) {
                $query->whereNull('wao_seller_id')
                    ->orWhere('is_warehouseTeam_order', '=', 1);
            })->filterByDate($formattedResetDate, $currentDate)->wherein('status', ['DISPATCHED'])->where('is_blocked_customer_order', '!=', 1)->count();
            $data['cancelOrders'] = Order::filterByDate($startdate, $enddate)->where(function ($query) {
                $query->whereNull('wao_seller_id')
                    ->orWhere('is_warehouseTeam_order', '=', 1);
            })->filterByDate($formattedResetDate, $currentDate)->where('status', 'CANCEL')->where('is_blocked_customer_order', '!=', 1)->count();
            $data['overallOrdersCount'] = Order::filterByDate($startdate, $enddate)->where(function ($query) {
                $query->whereNull('wao_seller_id')
                    ->orWhere('is_warehouseTeam_order', '=', 1);
            })->filterByDate($formattedResetDate, $currentDate)->where('is_blocked_customer_order', '!=', 1)->count();
            $data['pendingProfit'] = Order::filterByDate($startdate, $enddate)->where(function ($query) {
                $query->whereNull('wao_seller_id')
                    ->orWhere('is_warehouseTeam_order', '=', 1);
            })->filterByDate($formattedResetDate, $currentDate)->where('status', 'pending')
                ->sum('grandProfit');
            $data['dispatchedProfit'] = Order::filterByDate($startdate, $enddate)->where(function ($query) {
                $query->whereNull('wao_seller_id')
                    ->orWhere('is_warehouseTeam_order', '=', 1);
            })->filterByDate($formattedResetDate, $currentDate)->wherein('status', ['DISPATCHED', 'DELIVERED', 'ON-THE-WAY'])
                ->sum('grandProfit');

            // categories
            $data['allCategories'] = category::count();
            $data['activeCategories'] = category::where('status', 0)->count();
            $data['InactiveCategories'] = category::where('status', 1)->count();

            // products
            $data['allProducts'] = Product::whereHas('itemcategory')->count();
            $data['activeProducts'] = Product::where('soldstatus', 0)->where('soldItem', '>', 0)->count();
            $data['InactiveProducts'] = Product::where('soldstatus', 1)->orwhere('soldItem', '<=', 0)->count();
        } else {

            $data['overallOrdersCount'] = Order::where('wao_seller_id', null)->where('admin_id', auth()->user()->id)->where('is_blocked_customer_order', '!=', 1)->count();
            $data['dispatchedOrders'] = Order::where('wao_seller_id', null)->where('admin_id', auth()->user()->id)->wherein('status', ['DISPATCHED', 'Team Review your Order'])->where('is_blocked_customer_order', '!=', 1)->count();
            $data['cancelOrders'] = Order::where('wao_seller_id', null)->where('admin_id', auth()->user()->id)->where('status', 'CANCEL')->where('is_blocked_customer_order', '!=', 1)->count();
            $data['pendingOrders'] = Order::where('wao_seller_id', null)->where('admin_id', auth()->user()->id)->where('status', 'pending')
                ->count();

            $data['allSellerOrders'] = Order::where('wao_seller_id', auth()->user()->id)->count();
            $data['deliveredOrders'] = Order::where('wao_seller_id', auth()->user()->id)->where('status', 'DELIVERED')->count();
            $data['traxOrders'] = Order::where('wao_seller_id', auth()->user()->id)->where('tracking_order_type', 'trax')->count();
            $data['postExOrders'] = Order::where('wao_seller_id', auth()->user()->id)->where('tracking_order_type', 'postEx')->count();
        }

        return view('admin.index', compact('data', 'formattedResetDate'));
    }

    public function toggleStatus(Request $request)
    {
        if ($request->modelName === 'ResellerSetting') {

            $record = ResellerSetting::findOrFail($request->id);
            $record->update(['product_upload_status' => $record->product_upload_status === 'published' ? 'draft' : 'published']);
        }
        return $this->success($record, 'Status Updated Successfuly', 1);
    }
}
