<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\{Order, OrderHistory, Admin, WaoInventoryRecord};
use Carbon\Carbon;
use App\Http\Controllers\ThirdPartyApis\{Trax, Mnp, PostEx};
use App\Helpers\Notification;


class OrderStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check order status of courier';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // \Log::info('mesum start order cron 20 days');
        $defaultAdminDetails = Admin::where('id', 1)->first();
        $inventoryRecord = WaoInventoryRecord::first();
        $last_10days_orders = Order::whereNotIn('status', ['PENDING'])
            ->whereDate('created_at', '>=', Carbon::now()->subDays(10))
            ->where('courier_tracking_id', '!=','')
            ->whereIn('tracking_order_type',['postEx','mnp','trax'])
            ->with('waoSellerDetail:id,postEx_apiToken,postEx_apiToken_nowshera,trax_api_key,mnp_username,locationID,mnp_password')
            ->with('waoAdminDetail:id,postEx_apiToken,postEx_apiToken_nowshera,trax_api_key,mnp_username,locationID,mnp_password')
            ->withCount('orderitems')
            ->latest()
            ->get(['id', 'courier_tracking_id', 'admin_id', 'is_cancel', 'wao_seller_id', 'user_id', 'remarks', 'status', 'courier_date', 'created_at', 'tracking_order_type']);

        if ($last_10days_orders) {
            foreach ($last_10days_orders as $order) {
                // trax track order
                if ($order->tracking_order_type === 'trax') {
                    $api_key = $order->waoSellerDetail && $order->waoSellerDetail->trax_api_key
                        ? $order->waoSellerDetail->trax_api_key
                        : ($defaultAdminDetails && $defaultAdminDetails->trax_api_key
                            ? $defaultAdminDetails->trax_api_key
                            : null);
                    $results = Trax::track_order_by_cn($api_key, $order->courier_tracking_id);
                    if ($results["st"] == 1) {
                        if ($order->status != $results['system_status'] && $order->is_cancel != 1) {
                            $order->remarks = $results['msg'];
                            $order->status = $results['system_status'];
                            $order->courier_date = $results['delivery_status_time'];
                            $order->save();
                        }
                        // check order history to create or update
                        $orderHistory = OrderHistory::where('order_id', $order->id)
                            ->where('history', $results['delivery_status'])->first();

                        if (!$orderHistory) {
                            // if status is Picked By PostEx then deduct inventory
                            OrderHistory::create([
                                'order_id' => $order->id,
                                'history' => $results['delivery_status'],
                                'time' => $results['delivery_status_time'],
                            ]);
                            // inc sale inventory
                            if ($results['delivery_status'] === 'Shipment - Rider Picked') {
                                if ($inventoryRecord) {
                                    $inventoryRecord->increment('sale_inventory', $order->orderitems_count);
                                }
                            }

                            // set return inventory notify
                            if ($results['delivery_status'] === 'Return - Delivered to Shipper') {
                                $order->is_returned_order = 1;
                                $order->save();
                            }
                        }
                    }
                }

                // postEx track order
                if ($order->tracking_order_type === 'postEx') {

                    $api_key = null;
                    if ($order->waoSellerDetail) {
                        $api_key = $order->postex_api_type === 'multan' ? $order->waoSellerDetail->postEx_apiToken : $order->waoSellerDetail->postEx_apiToken_nowshera;
                    } elseif ($order->waoAdminDetail) {
                        $api_key = $order->postex_api_type === 'multan' ? $order->waoAdminDetail->postEx_apiToken : $order->waoAdminDetail->postEx_apiToken_nowshera;
                    }
                    $results = PostEx::track_order_by_cn($api_key, $order->courier_tracking_id);
                    if ($results["st"] == 1) {
                        if ($order->status != $results['system_status'] && $order->is_cancel != 1) {
                            // \Log::info('order status update');
                            $order->remarks = $results['msg'];
                            $order->status = $results['system_status'];
                            $order->courier_date = $results['delivery_status_time'];
                            $order->save();
                            
                            if (strtolower($order->status) == 'attempted') {
                                Notification::orderInProblemAlert($order);
                            }
                        }
                        // check order history to create or update
                        $orderHistory = OrderHistory::where('order_id', $order->id)
                            ->where('history', $results['delivery_status'])->first();

                        if (!$orderHistory) {
                            // if status is Picked By PostEx then deduct inventory
                            OrderHistory::create([
                                'order_id' => $order->id,
                                'history' => $results['delivery_status'],
                                'time' => $results['delivery_status_time'] ? $results['delivery_status_time'] : Carbon::now()->toTimeString(),
                            ]);
                            // inc sale inventory
                            if ($results['delivery_status'] === 'Picked By PostEx' || $results['delivery_status'] === 'Shipment - Rider Picked') {
                                if ($inventoryRecord) {
                                    $inventoryRecord->increment('sale_inventory', $order->orderitems_count);
                                }
                            }
                            // set return inventory notify
                            if ($results['delivery_status'] === 'Returned') {
                                $order->is_returned_order = 1;
                                $order->save();
                            }
                        }
                    }
                }

                // mnp track order
                if ($order->tracking_order_type === 'mnp') {
                    $username = $order->waoSellerDetail && $order->waoSellerDetail->mnp_username
                        ? $order->waoSellerDetail->mnp_username
                        : ($defaultAdminDetails && $defaultAdminDetails->mnp_username
                            ? $defaultAdminDetails->mnp_username
                            : null);

                    $password = $order->waoSellerDetail && $order->waoSellerDetail->mnp_password
                        ? $order->waoSellerDetail->mnp_password
                        : ($defaultAdminDetails && $defaultAdminDetails->mnp_password
                            ? $defaultAdminDetails->mnp_password
                            : null);

                    $location_id = $order->waoSellerDetail && $order->waoSellerDetail->locationID
                        ? substr($order->waoSellerDetail->locationID, 0, 5)
                        : ($defaultAdminDetails && $defaultAdminDetails->locationID
                            ? substr($defaultAdminDetails->locationID, 0, 5)
                            : null);
                    $cn_no = $order->courier_tracking_id;
                    $results = Mnp::track_order_by_cn($username, $password, $location_id, $cn_no);

                    if ($results["st"] == 1) {
                        if ($order->status != $results['system_status'] && $order->is_cancel != 1) {
                            $order->remarks = $results['msg'];
                            $order->status = $results['system_status'];
                            $order->courier_date = $results['delivery_status_time'];
                            $order->save();
                        }
                        // check order history to create or update
                        $orderHistory = OrderHistory::where('order_id', $order->id)
                            ->where('history', $results['delivery_status'])->first();

                        if (!$orderHistory) {
                            // if status is Picked By PostEx then deduct inventory
                            OrderHistory::create([
                                'order_id' => $order->id,
                                'history' => $results['delivery_status'],
                                'time' => $results['delivery_status_time'],
                            ]);
                            if ($results['delivery_status'] === 'Picked By PostEx' || $results['delivery_status'] === 'Shipment - Rider Picked') {
                                if ($inventoryRecord) {
                                    $inventoryRecord->increment('sale_inventory', $order->orderitems_count);
                                }
                            }
                        }
                    }
                }
            }
        }

        $this->info('Courier Order status updated successfully.');
    }
}
