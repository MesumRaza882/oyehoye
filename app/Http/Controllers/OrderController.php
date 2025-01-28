<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\{Order, message, Order_item, Charge, OrderHistory, WaoInventoryRecord, Admin, ResellerAmountHistory, ResellerSetting};
use App\Models\{User, City};
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use App\Helpers\Helper;
use App\Helpers\Notification;
use Exception;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Http\Controllers\ThirdPartyApis\{Mnp, PostEx, Trax};

class OrderController extends Controller
{
    public function allorders(Request $req)
    {
        if ($req->has('is_reseller_order')) {
            if ($req->is_reseller_order == 1) {
                $is_reseller_order = 1;
            } else {
                $is_reseller_order = 0;
            }
        } else {
            $is_reseller_order = 0;
        }

        $make_ids_string =  strval($req->filterOrderIds);
        $ids =  explode(",", $make_ids_string);
        $paginate_record = $req->records ? $req->records : 100;
        $status = $req->input('status');
        $returnStatus = $req->input('is_returned_order');
        $orderType = $req->type;

        $orderStatus = in_array($status, ['PENDING', 'DISPATCHED', 'DELIVERED', 'ON-THE-WAY', 'RETURNED', 'CANCEL', 'Team Review your Order']) ? $status : '';
        // $orderHistoryStatus = in_array($status, ['PENDING', 'DISPATCHED', 'DELIVERED', 'ON-THE-WAY', 'RETURNED', 'CANCEL']) ? '' : $status;

        $startdate = $req->fromDate;
        //temporary set for dummyadmin
        $enddate =  $req->toDate;
        if (auth()->user()->id == 58) {
            $enddate = '2024-06-05';
        }
        $records = Order::select('*', DB::raw('(CASE WHEN status = "Unbooked" THEN "DISPATCHED" WHEN status = "booked" THEN "DISPATCHED" ELSE status END) AS status'))
            ->with(['userdetail:id,whatsapp,status', 'citydetail'])
            // track order type filter
            ->when($req->tracking_order_type, function ($query) use ($req) {
                return $query->WhereIf('tracking_order_type', 'Like', "%{$req->tracking_order_type}%");
            })
            ->WhereIf('status', 'Like', "%{$orderStatus}%")
            // ->Where('is_reseller_order', '=', $is_reseller_order)
            ->filterByDate($startdate, $enddate)
            ->filterBlockOrders($req->blocked_orders)
            ->whereHas('userdetail', function ($query) use ($req) {
                $query->WhereIf('whatsapp', 'Like', "%{$req->whatsapp}%");
            })
            ->when($req->is_reseller_order, function ($query) use ($is_reseller_order) {
                return $query->Where('is_reseller_order', '=', $is_reseller_order);
            })
            ->where(function ($q) use ($req) {
                $q->WhereIf('name', 'Like', "%{$req->search_input}%")
                    ->OrWhereIf('id', '=', $req->search_input)
                    ->OrWhereIf('city', 'Like', "%{$req->search_input}%")
                    ->OrWhereIf('phone', 'Like', "%{$req->search_input}%")
                    ->OrWhereIf('address', 'Like', "%{$req->search_input}%");
            })
            // for order history filter
            // ->when($orderHistoryStatus, function ($query) use ($orderHistoryStatus) {
            //     return $query->filterByOrderStatus($orderHistoryStatus);
            // })
            // for return order status
            ->when($returnStatus, function ($query) use ($returnStatus) {
                return $query->WhereIf('is_returned_order', '=', $returnStatus);
            })
            // for order type filter (website + app orders)
            ->when($orderType, function ($query) use ($orderType) {
                if ($orderType == 3) {
                    return $query->where('is_warehouseTeam_order', '=', 1);
                } else {
                    return $query->where('type', '=', $orderType);
                }
            })
            ->whereNotNull('user_id')
            // Get orders app or where wao_seller_id is null(only app side orders) or is_warehouseTeam_order is 1
            ->where(function ($query) {
                $query->whereNull('wao_seller_id')
                    ->orWhere('is_warehouseTeam_order', '=', 1)->where('admin_id', auth()->user()->id);
            })
            // ->where('admin_id', auth()->user()->id)
            ->withcount('orderitems')
            ->with('message')
            ->orderByRaw("id DESC, status DESC");

        if ($req->filterOrderIds) {
            $records->whereIn('id', $ids);
        }

        // filter by specific admin app/web orders
        if ($req->admin_id) {
            $records->WhereIf('admin_id', '=', $req->admin_id);
        }
        // if current user is admin/manager then see all orders
        if (auth()->user()->role != 1) {
            $records->where('admin_id', auth()->user()->id);
        }

        $admins = Admin::wherehas('adminOrders')->where('role', 3)->orWhere('role', 1)->get(['id', 'email']);
        $total_records = $records->count();
        $orders = $records->paginate($paginate_record);
        $count_order_items = Order_item::wherein('order_id', $orders->pluck('id'))->sum('qty');
        // $count_order_items = 0;

        return view('admin.Order.viewOrder', compact('orders', 'admins', 'count_order_items', 'total_records'));
    }

    //single order details page
    public function editOrder($id)
    {
        $currentDate = Carbon::now()->format('d/m/Y');

        $order = Order::select('*', DB::raw('(CASE WHEN status = "Unbooked" THEN "DISPATCHED" WHEN status = "booked" THEN "DISPATCHED" ELSE status END) AS status'))
            ->with([
                'userdetail:id,name,address,whatsapp,status,phone,courier_phone',
                'message',
                'orderitems',
                'notes',
                'orderitems.product:id,name,price,purchase,profit,discount,article,category_id,is_dc_free,thumbnail',
                'citydetail'
            ])
            ->withcount('orderitems')
            ->withSum('orderitems', 'reseller_profit')
            ->where('id', $id)->first();

        $previous_orders = Order::select('*', DB::raw('(CASE WHEN status = "Unbooked" THEN "DISPATCHED" WHEN status = "booked" THEN "DISPATCHED" ELSE status END) AS status'))
            ->where('user_id', $order->user_id)->where('id', '!=', $order->id)
            ->select(['id', 'user_id', 'status', 'grandTotal', 'is_blocked_customer_order', 'date', 'time', 'courier_tracking_id'])
            ->take(10)->orderBy('id', 'desc')
            // ->where('courier_tracking_id', '!=', '')
            ->withcount('orderitems')->get();

        // active row
        if ($already_active = Order::where('id', '!=', $order->id)->where('is_active_row', 1)->first()) {
            $already_active->is_active_row = 0;
            $already_active->save();
        }
        $order->is_active_row = 1;
        $order->save();

        // cities for track
        $traxCities = City::where('trax', '!=', '')->get(['id', 'name', 'trax']);
        $mnpCities = City::where('courier_standard', '!=', '')->get(['id', 'courier_standard']);
        $postExCities = City::where('postex', '!=', '')->get(['id', 'postex']);

        // check if user has current date order dispatch or return
        $currentDayOrderQuery = Order::where(function ($query) use ($order) {
            $query->whereHas('userDetail', function ($q) use ($order) {
                $q->Where('whatsapp', $order->userdetail->whatsapp)
                    ->orWhere('phone', $order->phone);
            })
                ->orWhere('phone', $order->phone);
        })
            ->where('status', '!=', 'PENDING')
            // ->where('courier_tracking_id', '!=', '')
            ->with('waoAdminDetail:id,name,email')
            ->with('waoSellerDetail:id,name,email')
            ->with('userDetail.bussiness_detail')
            ->select(['id', 'name', 'user_id', 'phone', 'admin_id', 'courier_tracking_id', 'wao_seller_id', 'grandTotal', 'status'])
            ->latest();

        if ($currentDayOrderQuery->count() > 0) {
            $currentDayOrderFirst =  $currentDayOrderQuery->first();
            if ($currentDayOrderFirst->status === 'returned' || $currentDayOrderFirst->status === 'Returned') {
                $currentDayOrder = $currentDayOrderFirst;
            } else {
                $currentDayOrder = $currentDayOrderQuery->where('date', $currentDate)->first();
            }
        } else {
            $currentDayOrder = null;
        }

        $perOrderCharge = 25;
        $perProductCharge = 35;
        $purchaseTotal = 0;

        // return $order->orderitems;
        // calculate deducting balance from admin
        foreach ($order->orderitems as $item) {
            // Assuming you have a Product model with an 'article_id' and 'price' column
            $product = Product::where('id', $item->prod_id)->select(['id', 'price', 'profit', 'purchase'])->first();

            if ($product) {

                $specifProfitAdd = 0;
                // Check if there's a specific profit set for the product and the logged-in admin
                $specificResellerProfit = ResellerSetting::where('prod_id', $product->id)
                    ->where('admin_id', auth()->user()->id)
                    ->where('is_specific_profit', 1)
                    ->first();
                $profit = $specificResellerProfit ? $specificResellerProfit->profit : $product->profit;

                /** calculate purchaseTotal
                 * all products purchase amount + new yark product charge per suit(35 per product) +
                 * on order charge(25 rs per order)
                 * if user has specific profit and less than orignal profit
                 *  then deduct from origanl profit and resulting profit also add
                 * */

                if ($specificResellerProfit && $product->profit > $profit) {
                    $specifProfitAdd = ($product->profit - $profit) *  $item->qty;
                }
                $newYarkFlyrExpense = $perProductCharge * $item->qty;
                $productPurchaseAmount = $product->purchase * $item->qty;
                $purchaseTotal += $productPurchaseAmount + $specifProfitAdd + $newYarkFlyrExpense;
            }
        }
        $purchaseTotal += $perOrderCharge;
        return view('admin.Order.singleOrder', compact('order', 'previous_orders', 'traxCities', 'mnpCities', 'postExCities', 'currentDayOrder', 'purchaseTotal', 'perProductCharge', 'perOrderCharge'));
    }

    public function updateOrderProducts(Request $request)
    {
        $orderId = $request->input('order_id');
        $products = $request->input('products');
        $dataValue = $request->input('dataValue');

        $insert_order_items = [];

        foreach ($products as $item) {
            $product = Product::find($item['product_id']);

            if ($product && $item['qty'] > 0) {  // Check if product exists and qty > 0

                // update product is_multan_list to null
                if ($dataValue === 'multanItems') {
                    $product->is_multan_list = null;
                    $product->save();
                }
                // Attempt to find an existing order item for this product in this order
                $orderItem = Order_item::where('order_id', $orderId)
                    ->where('prod_id', $item['product_id'])
                    ->first();

                if ($orderItem) {
                    // Update the quantity of the existing order item
                    $orderItem->qty += $item['qty'];
                    $orderItem->price = $product->price;
                    $orderItem->purchase = $product->purchase;
                    $orderItem->profit = $product->profit;
                    $orderItem->discount = $product->discount;
                    $orderItem->is_dc_free = $product->is_dc_free;
                    $orderItem->save();
                } else {
                    // Prepare new order item data if it doesn't exist
                    $insert_order_items[] = [
                        'order_id' => $orderId,
                        'prod_id' => $product->id,
                        'qty' => $item['qty'],
                        'price' => $product->price,
                        'purchase' => $product->purchase,
                        'profit' => $product->profit,
                        'discount' => $product->discount,
                        'is_dc_free' => $product->is_dc_free,
                    ];
                }
            }
        }

        // Insert all new items in bulk
        if (!empty($insert_order_items)) {
            Order_item::insert($insert_order_items);
        }

        $order = Order::find($orderId);
        // Initialize totals
        $total = 0;
        $discount = 0;
        $profit = 0;

        // Iterate through order items and calculate totals
        foreach ($order->orderitems as $item) {
            $total += $item->price * $item->qty;
            $discount += $item->discount * $item->qty;
            $profit += $item->profit * $item->qty;
        }

        // Calculate charges based on the quantity of non-dc-free products
        $productsQty = $order->orderitems()->where('is_dc_free', 0)->sum('qty');
        $chargesGet = Charge::where('suit', $productsQty)->first();
        $charges = $chargesGet ? $chargesGet->charges : 0;

        // Calculate gtotal and gprofit
        $gtotal = ($total + $charges) - $discount;
        $gprofit = $profit - $discount;

        // Update the order
        $order->amount = $total;
        $order->order_discount = $discount;
        $order->charges = $charges;
        $order->grandTotal = $gtotal;
        $order->grandProfit = $gprofit;
        $order->save();

        return response()->json(['success' => true]);
    }

    //make order slip
    public function makeOrderSlip($id)
    {
        $order = Order::where('id', $id)->withcount('orderitems')->with(['citydetail:id,c_city_name', 'userdetail:id,name,whatsapp'])->firstorfail();

        // active row
        if ($already_active = Order::where('id', '!=', $order->id)->where('is_active_row', 1)->first()) {
            $already_active->is_active_row = 0;
            $already_active->save();
        }
        $order->is_active_row = 1;
        $order->save();

        return view('admin.Order.generalSlip', compact('order'));
    }

    // Order status Update
    public function updateOrder(Request $req, $id)
    {
        $order = Order::find($id);

        // slip
        if ($req->hasFile('slip')) {
            Helper::delete_previous_image($order->slip);
            $order->slip = Helper::upload_image($req->file('slip'), 'Order_slips');
        }

        // meesage to user
        if ($req->message_to_user) {
            $unread_message = message::where('order_id', $order->id)->where('read_at', null)->first();
            if ($unread_message) {
                $unread_message->message = $req->message_to_user;
                $unread_message->save();
            } else {
                $message = message::create([
                    'user_id' => $order->user_id,
                    'order_id' => $order->id,
                    'message' => $req->message_to_user,
                ]);
            }
        }

        $order->amount = $req->amount;
        $order->charges = $req->charges;
        $order->order_discount = $req->order_discount;
        $order->grandProfit = $req->grandProfit;
        $order->grandTotal = $req->grandTotal;
        $order->address = $req->address;
        $order->adjustment_note = $req->adjustment_note;

        // address saved as user's address
        $order->userdetail->update([
            'address' => $req->address,
        ]);


        //Order CANCEL &  added item quantity to item
        if ($req->status == 'CANCEL') {
            $items = Order_item::where('order_id', $order->id)->get();
            Order_item::where('order_id', $order->id)->update([
                'order_status' => $req->status,
            ]);

            foreach ($items as $item) {
                // Product quantity increased
                $product = Product::where('id', $item->prod_id)->first();
                if ($product) {
                    $product->soldItem += $item->qty;
                    $product->soldAdm -= $item->qty;
                    $product->save();
                }
            }
            $order->status = $req->status;

            Notification::orderStatusChange($order, $req->message_to_user);
        }

        // Cancel order and hold the same qunatity (not inc to item)
        if ($req->status == 'CANCELHOLD') {
            $order->status = 'CANCEL';
            Order_item::where('order_id', $order->id)->where('order_status', '!=', 'CANCEL')->update([
                'order_status' => 'CANCELHOLD',
            ]);
        }

        // DISPATCHED (NOT COURIER WORK)
        if ($req->status == 'DISPATCHED') {
            $order->status = 'Team Review your Order';
            Order_item::where('order_id', $order->id)->update([
                'order_status' => 'Team Review your Order',
            ]);
        }

        // BLOCK & CANCEL Order
        if ($req->status == 'BLOCK_CANCEL') {
            $customer = User::find($req->customer_id);
            $customer->status = 1;
            $customer->save();

            // also cancel Order
            $items = Order_item::where('order_id', $order->id)->get();
            Order_item::where('order_id', $order->id)->update([
                'order_status' => 'CANCEL',
            ]);
            foreach ($items as $item) {
                $product = Product::where('id', $item->prod_id)->first();
                if ($product) {
                    $product->soldItem += $item->qty;
                    $product->soldAdm -= $item->qty;
                    $product->save();
                }
            }

            $order->status = 'CANCEL';
            $order->is_blocked_customer_order = 1;
        }

        // BLOCK & Hold Order
        if ($req->status == 'BLOCK_HOLD') {
            $customer = User::find($req->customer_id);
            $customer->status = 1;
            $customer->save();
            $order->status = 'CANCEL';
            $order->is_blocked_customer_order = 1;

            Order_item::where('order_id', $order->id)->where('order_status', '!=', 'CANCEL')->update([
                'order_status' => 'CANCELHOLD',
            ]);
        }

        $order->save();
        // return redirect()->back()->with('message','Order updated successfully');
        session()->put('message', 'Order Updated Successfully');
        session()->put('end_time',  Carbon::now()->addSecond(3));
        echo '<script type="text/javascript">', 'history.go(-2);', '</script>';
    }

    // Delete Order
    public function delOrder($id)
    {
        $order = Order::findorFail($id);
        $purchseTotalOrder = ResellerAmountHistory::where('order_id', $id)->where('status', 'dispatch')->first();
        if ($purchseTotalOrder && $order->is_cancel != 1) {
            try {
                DB::beginTransaction();

                $order->status = 'CANCEL';
                $order->is_cancel = 1;
                $order->save();

                // first check if order has admin_id then wao_seller_id to get admin
                $balanceDeductAdminId = $order->admin_id ? $order->admin_id : $order->wao_seller_id;
                $balanceDeductAdmin = Admin::find($balanceDeductAdminId);
                $balanceDeductAdmin->update([
                    'balance' => $balanceDeductAdmin->balance + $purchseTotalOrder->balance,
                ]);

                // history for deduction
                ResellerAmountHistory::create([
                    'admin_id' => $order->wao_seller_id,
                    'order_id' => $id,
                    'balance' => $purchseTotalOrder->balance,
                    'status' => 'cancel',
                ]);



                DB::commit();
                return redirect()->back()->with('message', 'Order Cancelled Successfully!');
            } catch (Exception $e) {
                DB::rollback();
                return redirect()->back()->with('warning', $e->getMessage());
            }
        }
        return redirect()->back()->with('error', 'Order Not Cancellled!');
    }

    public function delete_order_item(Request $req)
    {

        try {
            DB::beginTransaction();

            $order_item = Order_item::find($req->delete_order_item_id);
            $order = Order::find($order_item->order_id);
            if (!$order->courier_tracking_id) {
                // calculate order data after delete one order item
                $amount = $order_item->price * $order_item->qty;
                $profit = $order_item->profit * $order_item->qty;
                $discount = $order_item->discount * $order_item->qty;
                // charges after assume delete order item
                $quan_pro =  Order_item::where(['order_id' => $order_item->order_id, 'is_dc_free' => 0])
                    ->where('id', '!=', $order_item->id)->sum('qty');
                $charges = Charge::where('suit', ($quan_pro))->first();
                if ($charges) {
                    $charges = $charges->charges;
                } else {
                    $charges = 0;
                }

                $order->amount = $order->amount - $amount;
                $order->charges = $charges;
                $order->grandProfit = ($order->grandProfit - ($profit - $discount));
                $order->order_discount = ($order->order_discount - $discount);
                $order->grandTotal = ($order->amount + $order->charges - $order->order_discount);
                $order->save();

                // add quantity of deleting order-item in product
                $productOfDeleetingOrderItem = $order_item->product;
                $productOfDeleetingOrderItem->soldItem = $productOfDeleetingOrderItem->soldItem += $order_item->qty;
                $productOfDeleetingOrderItem->save();

                // delete order item
                $order_item->delete();
                DB::commit();
                return $this->success('Order Item Deleted Successfully', 'Order Item', 2);
            }
            return $this->success('You can not delete order items , because order status has been chnaged', 'Order Item', 1);
        } catch (Exception $e) {
            DB::rollback();
            return $this->error($e->getMessage(), 'Order Item', 0);
        }
    }

    // trax disptach order
    public function DispatchOrderTrax(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'order_id' => 'required',
            'pickup_address_id' => 'required|integer',
            'consignee_city' => 'required:exists:cities,name',
            'consignee_address' => 'required',
            'consignee_phone_number_1' => 'required|regex:/^((0))(3)([0-9]{9})$/',
            'consignee_name' => 'required',
            'amount' => 'required',
            'item_description' => 'required',
            // 'estimated_weight'=> 'required',
        ]);
        // validation error
        if (!$validator->passes()) {
            return $this->success($validator->errors()->first(), 'Validation Error', 0);
        }
        // validate city trax
        if (!$city_trax_id = City::where('name', $req->consignee_city)->whereNotNull('trax')->first()) {
            return $this->success('This City has not Trax facility', 'Validation Error', 0);
        }

        // Check if reseller has enough balance
        $balanceError = $this->balanceInquiry($req);
        if ($balanceError) {
            return $balanceError;
        }

        try {
            DB::beginTransaction();

            $order = Order::find($req->real_order_id);
            // remove spaces
            $item_description = str_replace(' ', '', $req->item_description);
            // pass detail
            $api_key = auth()->user()->trax_api_key;
            $pickup_address_id = $req->pickup_address_id;

            // get trax track for warehouse team member
            if ($order->is_warehouseTeam_order) {
                $api_key =  $order->waoSellerDetail->trax_api_key;
            }

            $service_type_id = 1;
            $information_display = 1;
            // ** customer detal **
            $consignee_city_id = $city_trax_id->trax;
            $consignee_name = $req->consignee_name;
            $consignee_address = $req->consignee_address;
            $consignee_phone_number_1 = $req->consignee_phone_number_1;
            $consignee_email_address = 'oyehoe@gmail.com';
            $order_id = $req->order_id . '/' . auth()->user()->email;;
            $item_product_type_id = $req->item_product_type_id;
            $item_description = $item_description;
            $item_quantity = $req->item_quantity;
            $item_insurance = 0;
            $pieces_quantity = $req->pieces_quantity;
            $estimated_weight = $req->estimated_weight;
            $shipping_mode_id = $req->shipping_mode_id;
            $amount = $req->amount;
            $payment_mode_id = $req->payment_mode_id;
            $charges_mode_id = 4;


            $response = Trax::create_order(
                $api_key,
                $service_type_id,
                $pickup_address_id,
                $information_display,
                // **customer detail
                $consignee_city_id,
                $consignee_name,
                $consignee_address,
                $consignee_phone_number_1,
                $consignee_email_address,
                $order_id,
                $item_product_type_id,
                $item_description,
                $item_quantity,
                $item_insurance,
                $pieces_quantity,
                $estimated_weight,
                $shipping_mode_id,
                $amount,
                $payment_mode_id,
                $charges_mode_id,
            );

            if ($response['st'] == 0) {
                DB::commit();
                return $this->success($response['msg'], 'Courier Error', 3);
            }

            $order->courier_tracking_id = $response['cn'];
            $order->tracking_order_type = 'trax';
            $order->status = 'DISPATCHED';
            $order->track_created_at = Carbon::now();
            $order->save();

            $order->userdetail->update([
                'address' => $req->consignee_address,
            ]);
            // meesage to user
            if ($req->message_to_user) {
                $unread_message = message::where('order_id', $order->id)->where('read_at', null)->first();
                if ($unread_message) {
                    $unread_message->message = $req->message_to_user;
                    $unread_message->save();
                } else {

                    $message = message::create([
                        'user_id' => $order->user_id,
                        'order_id' => $order->id,
                        'message' => $req->message_to_user,
                    ]);
                }
            }

            ResellerAmountHistory::create([
                'admin_id' => auth()->user()->id,
                'order_id' => $order->id,
                'balance' => $req->purchaseTotal,
                'status' => 'dispatchAppOrder',
            ]);

            $reseller = Admin::find(auth()->user()->id);
            $reseller->update([
                'balance' => $reseller->balance - $req->purchaseTotal,
            ]);

            DB::commit();
            return $this->success('Order Trax Dispatched Successfully', 'Order Trax Dispatched Successfully', 2);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    // Mnp Dispatch order
    public function DispatchOrderMnp(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'order_id' => 'required',
            'mnp_username' => 'required',
            'mnp_password' => 'required',
            'item_quantity' => 'required',
            'consignee_city' => 'required:exists:cities,courier_standard',
            'consignee_address' => 'required',
            'consignee_phone_number_1' => 'required|regex:/^((0))(3)([0-9]{9})$/',
            'consignee_name' => 'required',
            'amount' => 'required',
            'item_description' => 'required',
            'estimated_weight' => 'required',
        ]);
        // validation error
        if (!$validator->passes()) {
            return $this->success($validator->errors()->first(), 'Validation Error', 0);
        }

        // Check if reseller has enough balance
        $balanceError = $this->balanceInquiry($req);
        if ($balanceError) {
            return $balanceError;
        }


        try {
            DB::beginTransaction();

            $order = Order::find($req->real_order_id);
            $item_description = str_replace(' ', '', $req->item_description);

            // ** customer detal **
            $consignee_name = $req->consignee_name;
            $consignee_address = $req->consignee_address;
            $consignee_phone_number_1 = $req->consignee_phone_number_1;
            $destination_city_name = $req->consignee_city;
            $consignee_email_address = 'oyehoe@gmail.com';
            $item_description = $item_description;
            $pieces_quantity = $req->item_quantity;
            $estimated_weight = $req->estimated_weight;
            $amount = $req->amount;
            $services = $req->service;
            $fragile = $req->fragile;
            $remarks = $req->remarks;
            $customer_reference_no = $req->order_id;
            $insurance_value = '0';

            $mnp_username = $req->mnp_username;
            $mnp_password = $req->mnp_password;
            $locationID = $req->locationID;
            $product_details = $item_description;

            $response = Mnp::create_order(
                $mnp_username,
                $mnp_password,
                $locationID,
                $consignee_name,
                $consignee_phone_number_1,
                $consignee_email_address,
                $consignee_address,
                $destination_city_name,
                $estimated_weight,
                $pieces_quantity,
                $amount,
                $customer_reference_no,
                $services,
                $product_details,
                $fragile,
                $remarks,
                $insurance_value
            );


            if ($response['st'] == 0) {
                DB::commit();
                return $this->success($response['msg'], 'Courier Error', 3);
            }

            $order->courier_tracking_id = $response['cn'];
            $order->tracking_order_type = 'mnp';
            $order->status = 'DISPATCHED';
            $order->track_created_at = Carbon::now();
            $order->save();

            $order->userdetail->update([
                'address' => $req->consignee_address,
            ]);

            // meesage to user
            if ($req->message_to_user) {
                $unread_message = message::where('order_id', $order->id)->where('read_at', null)->first();
                if ($unread_message) {
                    $unread_message->message = $req->message_to_user;
                    $unread_message->save();
                } else {

                    $message = message::create([
                        'user_id' => $order->user_id,
                        'order_id' => $order->id,
                        'message' => $req->message_to_user,
                    ]);
                }
            }

            ResellerAmountHistory::create([
                'admin_id' => auth()->user()->id,
                'order_id' => $order->id,
                'balance' => $req->purchaseTotal,
                'status' => 'dispatchAppOrder',
            ]);

            $reseller = Admin::find(auth()->user()->id);
            $reseller->update([
                'balance' => $reseller->balance - $req->purchaseTotal,
            ]);

            DB::commit();
            return $this->success('Order MNP Dispatched Successfully', 'Order MNP Dispatched Successfully', 2);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    // postEx Dispatch order
    public function DispatchOrderPostEx(Request $req)
    {
        // return $req->all();
        $validator = Validator::make($req->all(), [
            'order_id' => 'required',
            'postExOrderType' => 'required',
            'pickupAddressCode' => 'required',
            'invoiceDivision' => 'required',

            'item_quantity' => 'required',
            'consignee_city' => 'required',
            'consignee_address' => 'required',
            'consignee_phone_number_1' => 'required|regex:/^((0))(3)([0-9]{9})$/',
            'consignee_name' => 'required',
            'amount' => 'required',
            'item_description' => 'required',
        ]);
        // validation error
        if (!$validator->passes()) {
            return $this->success($validator->errors()->first(), 'Validation Error', 0);
        }

        if (!$city_post = City::where('name', 'like', "%{$req->consignee_city}%")
            ->orWhere('c_city_name', 'like', "%{$req->consignee_city}%")
            ->orWhere('postex', 'like', "%{$req->consignee_city}%")
            ->first()) {
            return $this->success('This city in not in our database for PostEx', 'Validation Error', 0);
        }


        // Check if reseller has enough balance
        $balanceError = $this->balanceInquiry($req);
        if ($balanceError) {
            return $balanceError;
        }

        $order = Order::find($req->real_order_id);
        //$item_description = str_replace(' ', '', $req->item_description);
        $item_description = preg_replace('/\s+/', '', $req->item_description);
        // return $this->success($item_description, 'Validation Error', 0);

        if ($order->is_reseller_order == 1) {
            // Remove everything between "Bill" and "DC", including "Bill-" and any numbers or characters in between.
            $item_description = preg_replace('/Bill\s*-\s*\d+\s*,?\s*/', '', $req->item_description);
        }


        try {
            DB::beginTransaction();

            $apiToken = $req->pickupAddressCode === 'nowshera' ? auth()->user()->postEx_apiToken_nowshera : auth()->user()->postEx_apiToken;
            // pickup address code for Multan/nowshera
            $pickupAddressCode = $req->pickupAddressCode === 'nowshera' ? auth()->user()->postEx_pickupAddressCode_nowshera : auth()->user()->postEx_pickupAddressCode;

            if ($order->is_warehouseTeam_order) {
                // Check if pickupAddressCode is 'nowshera' and assign corresponding values
                $isNowshera = $req->pickupAddressCode === 'nowshera';

                // Get API token with fallback to waoAdminDetail
                $apiToken = $isNowshera
                    ? ($order->waoSellerDetail->postEx_apiToken_nowshera ?? $order->waoAdminDetail->postEx_apiToken_nowshera)
                    : ($order->waoSellerDetail->postEx_apiToken ?? $order->waoAdminDetail->postEx_apiToken);

                // Get Pickup Address Code with fallback to waoAdminDetail
                $pickupAddressCode = $isNowshera
                    ? ($order->waoSellerDetail->postEx_pickupAddressCode_nowshera ?? $order->waoAdminDetail->postEx_pickupAddressCode_nowshera)
                    : ($order->waoSellerDetail->postEx_pickupAddressCode ?? $order->waoAdminDetail->postEx_pickupAddressCode);
            }


            if ($order->is_reseller_order == 1 && !empty($order->userDetail->bussiness_detail->postex_address_code)) {
                $pickupAddressCode = $order->userDetail->bussiness_detail->postex_address_code;
            }

            $storeAddressCode = null;
            $orderType = $req->postExOrderType;

            // ** customer/order detail **
            $customerName = $req->consignee_name;
            $customerPhone = $req->consignee_phone_number_1;
            $deliveryAddress = $req->consignee_address;
            $transactionNotes = $req->transactionNotes ?: null;
            $cityName = $req->consignee_city;
            $invoiceDivision = $req->invoiceDivision;
            $items = $req->item_quantity;

            $userEmail = $order->waoSellerDetail ? $order->waoSellerDetail->email : $order->waoAdminDetail->email;
            // Get the user's email and remove the domain part
            $emailReplace = str_replace('@gmail.com', '', $userEmail);
            $orderRefNumber = $req->order_id . '/' . $emailReplace;

            $invoicePayment = $req->amount;
            $orderDetail = $item_description;

            if (!$apiToken || !$pickupAddressCode) {
                return $this->success('PostEX Api-Token Or Address-Code mIssing', 'Courier Error', 3);
            }

            $response = PostEx::create_order(
                $apiToken,
                $orderRefNumber,
                $invoicePayment,
                $orderDetail,
                $customerName,
                $customerPhone,
                $deliveryAddress,
                $transactionNotes,
                $cityName,
                $invoiceDivision,
                $items,
                $pickupAddressCode,
                $storeAddressCode,
                $orderType,
            );

            if ($response['st'] == 0) {
                DB::commit();
                return $this->success($response['msg'], 'Courier Error', 3);
            }

            $order->courier_tracking_id = $response['cn'];
            $order->tracking_order_type = 'postEx';
            $order->status = 'DISPATCHED';
            $order->track_created_at = Carbon::now();
            $order->postex_api_type = $req->pickupAddressCode === 'nowshera' ? $req->pickupAddressCode : 'multan';
            $order->save();

            $order->userdetail->update([
                'address' => $req->consignee_address,
            ]);

            // meesage to user
            if ($req->message_to_user) {
                $unread_message = message::where('order_id', $order->id)->where('read_at', null)->first();
                if ($unread_message) {
                    $unread_message->message = $req->message_to_user;
                    $unread_message->save();
                } else {

                    $message = message::create([
                        'user_id' => $order->user_id,
                        'order_id' => $order->id,
                        'message' => $req->message_to_user,
                    ]);
                }
            }

            // not for team member
            // if (!$order->is_warehouseTeam_order) {
            ResellerAmountHistory::create([
                'admin_id' => auth()->user()->id,
                'order_id' => $order->id,
                'balance' => $req->purchaseTotal,
                'status' => 'dispatchAppOrder',
            ]);

            $reseller = Admin::find(auth()->user()->id);
            $reseller->update([
                'balance' => $reseller->balance - $req->purchaseTotal,
            ]);
            // }


            DB::commit();
            return $this->success('Order Post-Ex Dispatched Successfully', 'Order Post-Ex Dispatched Successfully', 2);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    // track single PostEx order
    public function trackSingleOrderApi(Request $request)
    {
        $order  = Order::where('id', $request->orderId)
            ->withCount('orderitems')
            ->with('waoSellerDetail')
            ->with('waoAdminDetail')
            ->first(['id', 'admin_id', 'is_cancel', 'courier_tracking_id', 'wao_seller_id', 'user_id', 'remarks', 'status', 'courier_date', 'created_at', 'tracking_order_type']);

        $data = $this->trackOrderHelper($order, 'singleTrack');
        if ($data['st_status'] === 1) {
            return $this->success($order, 'Order Status Updated Successfully', 2);
        }
        return $this->success([], $data["resultMsg"], 1);
    }

    public function trackOrderHelper($order, $action)
    {
        $inventoryRecord = WaoInventoryRecord::first();
        $defaultAdminDetails = Admin::where('id', 1)->first();
        $st_status = 0;
        // trax track order
        if ($order->tracking_order_type === 'trax') {
            $api_key = $order->waoSellerDetail && $order->waoSellerDetail->trax_api_key
                ? $order->waoSellerDetail->trax_api_key
                : ($defaultAdminDetails && $defaultAdminDetails->trax_api_key
                    ? $defaultAdminDetails->trax_api_key
                    : null);
            $results = Trax::track_order_by_cn($api_key, $order->courier_tracking_id);
            if ($results["st"] == 1) {
                // send notification and history save if status change
                if ($order->status != $results['system_status'] && $order->is_cancel != 1) {

                    $order->status = $results['system_status'];
                    $order->remarks = $results['msg'];
                    $order->courier_date = $results['delivery_status_time'];
                    $order->save();

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
                $order = $order->load(['history' => function ($query) {
                    $query->orderBy('created_at', 'desc');
                }]);
                $st_status = 1;
                // return $this->success($order, 'Order Status Updated Successfully', 2);
            }
        }

        // postEx track order
        if ($order->tracking_order_type === 'postEx') {
            // if order has  waosellerid means(general order)
            $api_key = null;
            if ($order->waoSellerDetail) {
                $api_key = $order->postex_api_type === 'multan' ? $order->waoSellerDetail->postEx_apiToken : $order->waoSellerDetail->postEx_apiToken_nowshera;
            } elseif ($order->waoAdminDetail) {
                $api_key = $order->postex_api_type === 'multan' ? $order->waoAdminDetail->postEx_apiToken : $order->waoAdminDetail->postEx_apiToken_nowshera;
            }

            $results = PostEx::track_order_by_cn($api_key, $order->courier_tracking_id);
            if ($results["st"] == 1) {
                // send notification and history save if status change
                if ($order->status != $results['system_status'] && $order->is_cancel != 1) {

                    $order->status = $results['system_status'];
                    $order->remarks = $results['msg'];
                    $order->courier_date = $results['delivery_status_time'];
                    $order->save();

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
                $order = $order->load(['history' => function ($query) {
                    $query->orderBy('created_at', 'desc');
                }]);
                $st_status = 1;
                // return $this->success($order, 'Order Status Updated Successfully', 2);
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
                // send notification and history save if status change
                if ($order->status != $results['system_status'] && $order->is_cancel != 1) {

                    $order->status = $results['system_status'];
                    $order->remarks = $results['msg'];
                    $order->courier_date = $results['delivery_status_time'];
                    $order->save();

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
                $order = $order->load(['history' => function ($query) {
                    $query->orderBy('created_at', 'desc');
                }]);
                $st_status = 1;
                // return $this->success($order, 'Order Status Updated Successfully', 2);
            }
        }

        $data = [
            'order' => $order,
            'st_status' => $st_status,
            'resultMsg' => $action === 'trackAll' ? '' : $results["msg"],
        ];
        return $data;
    }

    public function trackViewApi()
    {
        $last_1days_orders = Order::whereNotIn('status', ['PENDING'])
            ->whereDate('created_at', '>=', Carbon::now()->subDays(1))
            ->where(function ($query) {
                $query->where('admin_id', auth()->user()->id)
                    ->orWhere('wao_seller_id', auth()->user()->id);
            })
            ->with('waoSellerDetail:id,postEx_apiToken,postEx_apiToken_nowshera,trax_api_key,mnp_username,locationID,mnp_password')
            ->with('waoAdminDetail:id,postEx_apiToken,postEx_apiToken_nowshera,trax_api_key,mnp_username,locationID,mnp_password')
            ->withCount('orderitems')
            ->where('is_cancel', '!=', 1)
            ->select(['id', 'courier_tracking_id', 'postex_api_type', 'is_cancel', 'wao_seller_id', 'admin_id', 'user_id', 'remarks', 'status', 'courier_date', 'created_at', 'tracking_order_type'])
            ->get();

        if ($last_1days_orders) {
            foreach ($last_1days_orders as $order) {
                $this->trackOrderHelper($order, 'trackAll');
            }
        }
        return redirect()->back()->with('message', 'Update Current Order Status');
    }

    public function updatePaymentStatus(Request $req, $id)
    {
        $order = Order::where('id', $id)->first();

        if ($req->advance_payment_status == 2) {
            $order->advance_payment_status = null;
        }
        $order->advance_payment_status = $req->advance_payment_status;
        $order->save();

        Notification::orderPaymentStatusUpdate($order);

        return redirect()->back()->with('message', 'Payment status has been updated');
    }

    public function shipper_advice(Request $req)
    {

        $records = Order::where('status', 'Re-Attempted')
            ->paginate(30);

        return view('admin.Order.shipperAdvice', compact('records'));
    }

    public function shipper_advice_done(Request $request)
    {
        Order::where('id', $request->order_id)->update([
            'status' => 'Advice-Added'
        ]);

        $arr = [];
        $arr['toastr'] = 'success';
        $arr['msg'] = "Shipper advice mark as done";
        $arr['st'] = 1;
        return response()->json($arr, 200);
    }

    public function confirm_order_return(Request $req)
    {

        $inventoryRecord = WaoInventoryRecord::first();
        $order = Order::where('id', $req->confirm_order_return_id)->withCount('orderitems')->first();
        if ($order && $inventoryRecord) {

            $inventoryRecord->increment('return_inventory', $order->orderitems_count);

            $order->is_returned_order = 2;
            $order->save();

            return $this->success('Returned Order Confirmed Successfully', 'Returned Order Confirmed Successfully', 2);
        }
    }

    public function shipper_profit(Request $req)
    {

        $records = Order::where('is_reseller_order', 1)
            ->where('status', 'DELIVERED')
            ->when(!empty($req->status), function ($query) use ($req) {
                return $query->where('is_commission_paid', $req->status);
            })
            ->paginate(30);

        return view('admin.Order.shipperProfit', compact('records'));
    }

    // shipper_profit_done
    public function shipper_profit_done(Request $request, $id)
    {
        Order::where('id', $id)->update([
            'is_commission_paid' => 1,
            'commission_paid_note' => $request->remark
        ]);

        $arr = [];
        $arr['toastr'] = 'success';
        $arr['msg'] = "Shipper profit mark as done";
        $arr['st'] = 1;
        return response()->json($arr, 200);
    }

    public function genrate_slip(Request $request)
    {
        // return $request->all();
        // $withoutCheckOrders = Order::whereIn('id',$orderIds)->whereNull('courier_tracking_id')->get();
        // foreach ($withoutCheckOrders as $key => $value) {
        //     $this->orderpostex_manual($value->id);
        // }
        $orders = Order::whereIn('id', $request->order_id)->whereNotNull('courier_tracking_id')->get();
        return view('admin.Order.slip', compact('orders'));
    }

    public function balanceInquiry($req)
    {
        // if ($req->purchaseTotal > auth()->user()->balance) {
        //     return $this->success('You have insufficient balance to dispatch this order', 'Courier Error', 3);
        // }

        // Check if balance is restricted
        if (auth()->user()->isRestrictBalance) {
            $remainingBalance = auth()->user()->balance - $req->purchaseTotal;
            if ($remainingBalance < auth()->user()->restrictBalance) {
                return $this->success(
                    'You have a restriction to retain your balance. You must retain Rs: ' . auth()->user()->restrictBalance . '. You have Rs: ' . (auth()->user()->restrictBalance - $remainingBalance) . ' more to dispatch.',
                    'Courier Error',
                    3
                );
            }
        }

        // No balance errors, return null
        return null;
    }


    public function uploadscreenShot(Request $request)
    {
        $orderId = $request->input('order_id');

        if ($request->hasFile('payment_screenshot')) {
            $order = Order::find($orderId);
            $order->payment_screenshot = Helper::upload_digital_ocean($request->file('payment_screenshot'), 'thumbnail', 'image');
            $order->save();
            return redirect()->back()->with('message', 'Payment Scrrenshot uploaded successfully.');
        }

        return redirect()->back()->with('error', 'Image upload failed.');
    }

    public function delSelectedOrders(Request $req)
    {
        $ids = $req->ids;
        $items = Order::whereIn('id', $ids)->get();
        if ($items) {
            foreach ($items as $item) {
                $item->orderitems()->delete();
                $item->delete();
            }

            return response()->json([
                'status' => "Orders Delete Successfully",
            ]);
        } else {
            return response()->json([
                'status' => "Orders Not deleted",
            ]);
        }
    }
}
