<?php

namespace App\Http\Controllers\Admin\WaoInventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\ThirdPartyApis\{Mnp, PostEx, Trax};
use Illuminate\Support\Facades\DB;
use App\Http\Requests\WaoSeller\{SellerOrderRequest, SellerOrderRequestMnp, SellerOrderRequestPostEx, WarehouseTeamRequest};
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use App\Models\{Admin, TrackPlatformSetting, OrderHistory, Order, Order_item, City, Product, Charge, User, ResellerSetting, ResellerAmountHistory};
use Image;

class WaoInventoryOrderController extends Controller
{
    public function index(Request $req)
    {

        $make_ids_string =  strval($req->filterOrderIds);
        $ids =  explode(",", $make_ids_string);
        $paginate_record = $req->records ? $req->records : 50;
        $status = $req->input('status');
        $returnStatus = $req->input('is_returned_order');


        $orderStatus = in_array($status, ['PENDING', 'DISPATCHED', 'DELIVERED', 'ON-THE-WAY', 'RETURNED', 'CANCEL', 'CANCIL', 'Team Review your Order']) ? $status : '';
        // $orderHistoryStatus = in_array($status, ['PENDING', 'DISPATCHED', 'DELIVERED', 'ON-THE-WAY', 'RETURNED', 'CANCEL']) ? '' : $status;

        $startdate = $req->fromDate;
        //temporary set for dummyadmin
        $enddate =  $req->toDate;
        if (auth()->user()->id == 58) {
            $enddate = '2024-06-05';
        }

        $records = Order::with(['userdetail:id,whatsapp,status', 'citydetail'])
            ->WhereIf('status', '=', $orderStatus)
            ->filterByDate($startdate, $enddate)
            ->whereHas('userdetail', function ($query) use ($req) {
                $query->WhereIf('whatsapp', 'Like', "%{$req->whatsapp}%");
            })
            // track order type filter
            ->when($req->tracking_order_type, function ($query) use ($req) {
                return $query->WhereIf('tracking_order_type', 'Like', "%{$req->tracking_order_type}%");
            })
            // filter by seller
            ->when($req->wao_seller_id, function ($query) use ($req) {
                return $query->WhereIf('wao_seller_id', '=', $req->wao_seller_id);
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
            ->whereNotNull('user_id')->where('wao_seller_id', '!=', null)
            ->withcount('orderitems')
            ->orderByRaw("id DESC, status DESC");

        if ($req->filterOrderIds) {
            $records->whereIn('id', $ids);
        }
        // is Seller or warehouse team member then get his own order or if admin then get all orders
        if (in_array(auth()->user()->role, [3, 4])) {
            $records->where('wao_seller_id', auth()->user()->id);
        }
        $total_records = $records->count();
        $orders = $records->paginate($paginate_record);
        $admins = Admin::wherehas('sellerOrders')->where('role', 3)->orWhere('role', 1)->get(['id', 'email']);
        return view('admin.wao_inventory.seller_orders.indexSellerOrder', compact('orders', 'total_records', 'admins'));
    }

    public function create()
    {
        $traxCities = City::where('trax', '!=', '')->get(['id', 'name', 'trax']);
        $mnpCities = City::where('courier_standard', '!=', '')->get(['id', 'courier_standard']);
        $postExCities = City::whereNotNull('postex')->get(['postex']);
        $whiteListProducts = []/*Product::where('is_white_list', 1)->get(['id', 'name', 'article']) */;
        $codes = TrackPlatformSetting::all();
        return view('admin.wao_inventory.seller_orders.createSellerOrder', compact('codes', 'traxCities', 'mnpCities', 'postExCities', 'whiteListProducts'));
    }

    // get user whatsapp and cnumber history
    public function history(Request $request)
    {
        $phoneNumber = $request->input('phoneNumber');
        $whatsappNumber = $request->input('whatsappNumber');
        $currentDate = Carbon::now()->format('d/m/Y');

        $latestOrders = [];

        // whatsapp record based
        if ($whatsappNumber) {
            $latestOrders = Order::whereHas('userDetail', function ($q) use ($whatsappNumber) {
                $q->where('whatsapp', $whatsappNumber);
            })
                // ->where('courier_tracking_id', '!=', '')
                ->with(['citydetail', 'userdetail:id,name,whatsapp,phone,address'])
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get(['id', 'user_id', 'courier_tracking_id', 'status', 'amount', 'charges', 'date', 'city_id']);
        }
        if ($phoneNumber) {
            $latestOrders = Order::where('phone', $phoneNumber)
                // ->where('courier_tracking_id', '!=', '')
                ->with(['citydetail', 'userdetail:id,name,whatsapp,phone,address'])
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get(['id', 'user_id', 'courier_tracking_id', 'status', 'amount', 'charges', 'date', 'city_id']);
        }

        if (count($latestOrders)) {
            $currentDayOrderQuery = Order::where(function ($query) use ($whatsappNumber, $phoneNumber) {
                $query->whereHas('userDetail', function ($q) use ($whatsappNumber, $phoneNumber) {
                    $q->Where('whatsapp', $whatsappNumber)
                        ->orWhere('phone', $phoneNumber);
                })
                    ->orWhere('phone', $phoneNumber);
            })
                ->where('status', '!=', 'PENDING')
                ->with('waoAdminDetail:id,name,email')
                ->with('waoSellerDetail:id,name,email')
                // ->where('courier_tracking_id', '!=', '')
                ->with('userDetail')
                ->select(['id', 'name', 'user_id', 'phone', 'admin_id', 'courier_tracking_id', 'wao_seller_id', 'grandTotal', 'status'])
                ->latest();
            if ($currentDayOrderQuery->count() > 0) {
                $currentDayOrderFirst =  $currentDayOrderQuery->first();
                if ($currentDayOrderFirst->status === 'returned' || $currentDayOrderFirst->status === 'Returned') {
                    $data['currentDayOrder'] = $currentDayOrderFirst;
                } else {
                    $data['currentDayOrder'] = $currentDayOrderQuery->where('date', $currentDate)->first();
                }
            } else {
                $data['currentDayOrder'] = null;
            }

            $data['latestOrders'] = $latestOrders;
            return $this->success($data, 'orders', 2);
        }
        return $this->success([], 'No Order History Found', 1);
    }

    // create Order For Trax
    public function store(SellerOrderRequest $req)
    {
        try {
            DB::beginTransaction();
            // validate city trax
            if (!$city_trax_id = City::where('name', 'like', "%{$req->consignee_city_trax}%")
                ->orWhere('c_city_name', 'like', "%{$req->consignee_city_trax}%")
                ->whereNotNull('trax')->first()) {
                return $this->success('This City has not Trax facility', 'Validation Error', 0);
            }


            // Check if reseller has enough balance
            $balanceError = $this->balanceInquiry($req);
            if ($balanceError) {
                return $balanceError;
            }

            // remove all spaces
            $item_description = preg_replace('/\s+/', '', $req->item_description);

            // pass detail
            $api_key = auth()->user()->trax_api_key;
            $service_type_id = 1;
            $pickup_address_id = $req->pickup_address_id;
            $information_display = 1;
            // ** customer detal **
            $consignee_city_id = $city_trax_id->trax;
            $consignee_name = $req->consignee_name;
            $consignee_address = $req->consignee_address;
            $consignee_phone_number_1 = $req->consignee_phone_number_1;
            $consignee_email_address = 'oyehoe@gmail.com';
            $order_id = $req->order_id;
            $item_product_type_id = $req->item_product_type_id;
            $item_description = $item_description;
            $item_quantity = $req->item_quantity;
            $item_insurance = 0;
            $pieces_quantity = $req->pieces_quantity ? $req->pieces_quantity : null;
            $estimated_weight = $req->estimated_weight;
            $shipping_mode_id = $req->shipping_mode_id;
            $amount = $req->grandTotal;
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

            $data = $this->createOrderAndUserDetail($req, $city_trax_id, $response, 'trax');
            DB::commit();
            return $this->success($data, 'Order Trax Dispatched Successfully', 2);
        } catch (\Exception $e) {
            // Handle the exception
            DB::rollBack(); // Rollback the transaction in case of an exception
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500); // Use an appropriate HTTP status code
        }
    }

    // create order for MNP
    public function storeMnp(SellerOrderRequestMnp $req)
    {
        try {
            DB::beginTransaction();

            // validate city trax
            if (!$city_mnp = City::where('name', $req->consignee_city_mnp)->where('courier_standard', '!=', '')->first()) {
                return $this->success('This City has not MNP facility', 'Validation Error', 0);
            }

            // check if reseller has enough amount balance
            // if (auth()->user()->id != 1) {
            // Check if reseller has enough balance
            $balanceError = $this->balanceInquiry($req);
            if ($balanceError) {
                return $balanceError;
            }
            // }

            $item_description = preg_replace('/\s+/', '', $req->item_description);

            // ** customer detal **
            $consignee_name = $req->consignee_name;
            $consignee_address = $req->consignee_address;
            $consignee_phone_number_1 = $req->consignee_phone_number_1;
            $destination_city_name = $city_mnp->courier_standard;
            $consignee_email_address = 'oyehoe@gmail.com';
            $item_description = $item_description;
            $pieces_quantity = $req->item_quantity;
            $estimated_weight = $req->estimated_weight;
            $amount = $req->grandTotal;
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


            $this->createOrderAndUserDetail($req, $city_mnp, $response, 'mnp');
            DB::commit();
            return $this->success('Order MNP Dispatched Successfully', 'Order MNP Dispatched Successfully', 2);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    // create Order For PostEx
    public function storePostEx(SellerOrderRequestPostEx $req)
    {
        try {
            DB::beginTransaction();

            if (!$city_post = City::where('name', 'like', "%{$req->consignee_city_postEx}%")
                ->orWhere('c_city_name', 'like', "%{$req->consignee_city_postEx}%")
                ->orWhere('postex', 'like', "%{$req->consignee_city_postEx}%")
                ->first()) {
                return $this->success('This city in not in our database', 'Validation Error', 0);
            }


            // Check if reseller has enough balance
            $balanceError = $this->balanceInquiry($req);
            if ($balanceError) {
                return $balanceError;
            }

            $item_description = preg_replace('/\s+/', '', $req->item_description);

            // Check if the user's role reseller
            if (auth()->user()->role == 3 && is_null(auth()->user()->is_partner)) {
                // Remove "Bill-" followed by any number, and the "-" before "DC"
                $item_description = preg_replace('/Bill-\d+-?DC/', 'DC', $item_description);
            }

            $apiToken = $req->pickupAddressCode === 'nowshera' ? auth()->user()->postEx_apiToken_nowshera : auth()->user()->postEx_apiToken;

            if (!$apiToken) {
                return $this->success('Please Contact to admin to set Postex Token ', 'Validation Error', 0);
            }

            // Get the user's email and remove the domain part
            $email = str_replace('@gmail.com', '', auth()->user()->email);
            $orderRefNumber = $req->order_id . '/' . $email;


            $invoicePayment = $req->grandTotal;
            $orderDetail = $item_description;
            $customerName = $req->consignee_name;
            $customerPhone = $req->consignee_phone_number_1;
            $deliveryAddress = $req->consignee_address;
            $transactionNotes = $req->transactionNotes ?: null;
            $cityName = $req->consignee_city_postEx;
            $invoiceDivision = $req->invoiceDivision;
            $items = $req->item_quantity;
            // pickup address code for Multan/Nowshera get to auth data
            $pickupAddressCode = $req->pickupAddressCode === 'nowshera' ? auth()->user()->postEx_pickupAddressCode_nowshera : auth()->user()->postEx_pickupAddressCode;
            $storeAddressCode = null;
            $orderType = $req->postExOrderType;
            // Normal , Reverse , Replacement

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

            $data = $this->createOrderAndUserDetail($req, $city_post, $response, 'postEx');

            DB::commit();

            if ($data['message'] === "notDeductProfit") {
                return $this->success($data, 'Order for Post-Ex Dispatched Successfully', 2);
            } else {
                return $this->success([], $data['message'], 5);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    // create Order For PostEx
    public function storeWareHouse(WarehouseTeamRequest $req)
    {

        try {
            DB::beginTransaction();

            if (!$city_post = City::where('name', 'like', "%{$req->consignee_city_postEx}%")
                ->orWhere('c_city_name', 'like', "%{$req->consignee_city_postEx}%")
                ->orWhere('postex', 'like', "%{$req->consignee_city_postEx}%")
                ->first()) {
                return $this->success('This city in not in our database', 'Validation Error', 0);
            }


            // check if reseller has enough amount balance
            // if ($req->purchaseTotal > auth()->user()->balance) {
            //     return $this->success('You have insufficient balance to dispatch this order', 'Courier Error', 3);
            // }
            // check restrict balance
            if (auth()->user()->isRestrictBalance && ($req->purchaseTotal + auth()->user()->balance) < auth()->user()->restrictBalance) {
                return $this->success('You have restrict to retain your balance', 'Courier Error', 3);
            }

            $data = $this->createOrderAndUserDetailForWareHouse($req, $city_post);
            DB::commit();
            return $this->success($data, 'Order Confirmed to Admin Successfully', 4);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    // create New Order and user Update/add data
    public function createOrderAndUserDetailForWareHouse($req, $city)
    {
        $insert_order_items = [];
        $articles = json_decode($req->input('selected_articles'), true);
        $item_description = str_replace(' ', '', $req->item_description);
        $now = Carbon::now();
        $user = User::where('whatsapp', $req->consignee_whatsaapp)->first();
        if (!$user) {
            $user = $user = new User();
            $user->password = Hash::make($req->consignee_whatsaapp);
            $user->whatsapp = $req->consignee_whatsaapp;
            $user->name = $req->consignee_name;
            $user->city_id = $city->id;
            $user->phone = $req->consignee_phone_number_1;
            $user->address = $req->consignee_address;
            $user->admin_id = auth()->user()->id;
            $user->save();
        } else {
            $user->name = $req->consignee_name;
            $user->city_id = $city->id;
            $user->phone = $req->consignee_phone_number_1;
            $user->address = $req->consignee_address;
            $user->admin_id = auth()->user()->id;
            $user->save();
        }
        $newOrder = Order::create([
            'wao_seller_id' => auth()->user()->id,
            // admin id means order view for speciifc admin
            'admin_id' => auth()->user()->controlled_by_admin,
            'is_warehouseTeam_order' => 1,
            'user_id' => $user->id,
            'name' => $req->consignee_name,
            'phone' => $req->consignee_phone_number_1,
            'city_id' => $city->id,
            'address' => $req->consignee_address,
            'status' => 'PENDING',
            'charges' => $req->charges,
            'grandTotal' => $req->grandTotal,
            'grandProfit' => $req->grandProfit,
            'amount' => $req->total,
            'description' => $item_description,
            'adjustment_note' => $req->adjustment_note,
            'date' => $now->format('d/m/Y'),
            'time' => $now->format('g:i A'),
        ]);


        if ($newOrder) {
            ResellerAmountHistory::create([
                'admin_id' => auth()->user()->id,
                'order_id' => $newOrder->id,
                'balance' => $req->purchaseTotal,
                'status' => 'dispatch',
            ]);

            $reseller = Admin::find(auth()->user()->id);
            $reseller->update([
                'balance' => $reseller->balance - $req->purchaseTotal,
            ]);

            // insertOrderItems
            foreach ($articles as $item) {
                $product = Product::find($item['article_id']);
                if ($product->is_multan_list === 1 && $req->dataValue === 'multanItems') {
                    $product->is_multan_list = 2;
                    $product->save();

                    $newOrder->is_multan_items_contain = 1;
                    $newOrder->save();
                }
                $insert_order_items[] = [
                    'order_id' => $newOrder->id,
                    'prod_id' => $product->id,
                    'qty' => $item['count'],
                    'price' => $product->price,
                    'purchase' => $product->purchase,
                    'profit' => $product->profit,
                    'discount' => $product->discount,
                    'is_dc_free' => $product->is_dc_free,
                ];
            }
            Order_item::insert($insert_order_items);
        }
        $data = [
            'orderId' => $newOrder->id,
        ];

        return $data;
    }

    // create New Order and user Update/add data
    public function createOrderAndUserDetail($req, $city, $response, $orderType)
    {
        $insert_order_items = [];
        $postExType = null;
        if ($orderType === 'postEx') {
            $postExType = $req->pickupAddressCode === 'nowshera' ? 'nowshera' : 'multan';
        }
        $articles = json_decode($req->input('selected_articles'), true);
        $item_description = str_replace(' ', '', $req->item_description);
        $now = Carbon::now();
        $user = User::where('whatsapp', $req->consignee_whatsaapp)->first();
        if (!$user) {
            $user = $user = new User();
            $user->password = Hash::make($req->consignee_whatsaapp);
            $user->whatsapp = $req->consignee_whatsaapp;
            $user->name = $req->consignee_name;
            $user->city_id = $city->id;
            $user->phone = $req->consignee_phone_number_1;
            $user->address = $req->consignee_address;
            $user->admin_id = auth()->user()->id;
            $user->save();
        } else {
            $user->name = $req->consignee_name;
            $user->city_id = $city->id;
            $user->phone = $req->consignee_phone_number_1;
            $user->address = $req->consignee_address;
            $user->admin_id = auth()->user()->id;
            $user->save();
        }


        $newOrder = Order::create([
            'wao_seller_id' => auth()->user()->id,
            'admin_id' => Null,
            'user_id' => $user->id,
            'name' => $req->consignee_name,
            'phone' => $req->consignee_phone_number_1,
            'city_id' => $city->id,
            'address' => $req->consignee_address,
            'status' => 'DISPATCHED',
            'charges' => $req->charges,
            'grandTotal' => $req->grandTotal,
            'grandProfit' => $req->grandProfit,
            'amount' => $req->total,
            'description' => $item_description,
            'adjustment_note' => $req->adjustment_note,
            'tracking_order_type' => $orderType,
            'postex_api_type' => $postExType,
            'courier_tracking_id' => $response['cn'],
            'date' => $now->format('d/m/Y'),
            'time' => $now->format('g:i A'),
            'track_created_at' => $now,
        ]);

        if ($newOrder) {

            $reseller = Admin::find(auth()->user()->id);

            // Calculate remaining balance and apply deduction logic
            $remainingBalance = $reseller->balance - $req->purchaseTotal;
            $restrictInventory = $reseller->restrict_inventory;
            $profitDeductionPercentage = $reseller->profit_deduction_percentage;
            $profitAmount = $newOrder->grandProfit ?? 0;

            $extraDeduction = 0;
            $deductionMessage = '';

            if ($reseller->is_applied_restrict_inventory === 1) {
                if ($remainingBalance < $restrictInventory) {
                    $extraDeduction = ($profitAmount * $profitDeductionPercentage) / 100;
                    $deductionMessage = "Your balance is below the restriction threshold. Rs: " . number_format($extraDeduction) . " extra has been deducted from profit.";
                    $remainingBalance -= $extraDeduction;
                }
            }

            $reseller->update([
                'balance' => $remainingBalance,
            ]);

            ResellerAmountHistory::create([
                'admin_id' => $reseller->id,
                'order_id' => $newOrder->id,
                'balance' => $req->purchaseTotal + $extraDeduction,
                'status' => 'dispatch',
            ]);




            // insertOrderItems
            foreach ($articles as $item) {
                $product = Product::find($item['article_id']);
                if ($product->is_multan_list === 1 && $req->dataValue === 'multanItems') {
                    $product->is_multan_list = 2;
                    $product->save();

                    $newOrder->is_multan_items_contain = 1;
                    $newOrder->save();
                }
                $insert_order_items[] = [
                    'order_id' => $newOrder->id,
                    'prod_id' => $product->id,
                    'qty' => $item['count'],
                    'price' => $product->price,
                    'purchase' => $product->purchase,
                    'profit' => $product->profit,
                    'discount' => $product->discount,
                    'is_dc_free' => $product->is_dc_free,
                ];
            }
            $newOrder->save();
            Order_item::insert($insert_order_items);
        }
        $data = [
            'orderId' => $newOrder->id,
            'courier_tracking_id' => $newOrder->courier_tracking_id,
            'message' => $deductionMessage ?: 'notDeductProfit',
        ];

        return $data;
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

    public function slipStore(Request $request)
    {
        try {
            $order = Order::find($request->order_id);
            // Decode base64 image data
            if ($request->input('image')) {
                $imageData = $request->input('image');
                $file = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $imageData));
                $filename = time() . '.jpeg';
                $path = public_path('slip/' . $filename);
                file_put_contents($path, $file);
                $order->slip = $filename;
                $order->save();
                $imageUrl = asset('slip/' . $filename);
                return response()->json(['imageUrl' => $imageUrl], 200);
            }
        } catch (\Exception $e) {
            // Handle any exceptions
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // calculateTotals
    public function calculateTotals(Request $request)
    {
        $articles = $request->get('articles');
        $total = 0;
        $grandTotal = 0;
        $grandProfit = 0;
        $charges = 0;
        $perOrderCharge = 0;
        $perProductCharge = 0;
        $latestOrder = Order::latest()->first();
        $nextOrderId = $latestOrder ? $latestOrder->id + 1 : 00;

        $newYarkFlyrExpense = 0;
        $purchaseTotal = 0;

        if ($articles) {
            $chargesSuits = Charge::where('suit', $request->total_count)->first();
            if ($chargesSuits) {
                $charges = $chargesSuits->charges;
            }

            $adminId = auth()->user()->id;

            foreach ($articles as $article) {
                // Assuming you have a Product model with an 'article_id' and 'price' column
                $product = Product::where('id', $article['article_id'])->select(['id', 'price', 'profit', 'purchase'])->first();

                if ($product) {

                    $specifProfitAdd = 0;
                    // Check if there's a specific profit set for the product and the logged-in admin
                    $specificResellerProfit = ResellerSetting::where('prod_id', $product->id)
                        ->where('admin_id', $adminId)
                        ->where('is_specific_profit', 1)
                        ->first();
                    $profit = $specificResellerProfit ? $specificResellerProfit->profit : $product->profit;
                    $total += $product->price * $article['count'];
                    /** calculate purchaseTotal
                     * all products purchase amount + new yark product charge per suit(35 per product) +
                     * on order charge(25 rs per order)
                     * if user has specific profit and less than orignal profit
                     *  then deduct from origanl profit and resulting profit also add
                     * */

                    if ($specificResellerProfit && $product->profit > $profit) {
                        $specifProfitAdd = ($product->profit - $profit) *  $article['count'];
                    }
                    $perOrderCharge = 25;
                    $perProductCharge = 35;
                    $newYarkFlyrExpense = $perProductCharge * $article['count'];
                    $purchaseTotal += ($product->purchase * $article['count']) + $specifProfitAdd + $newYarkFlyrExpense;
                    $grandProfit += $profit * $article['count'];
                }
            }

            $grandTotal = ($total + $charges);
            $purchaseTotal += $perOrderCharge;
            return $this->success(['nextOrderId' => $nextOrderId, 'perOrderCharge' => $perOrderCharge, 'perProductCharge' => $perProductCharge,  'grandTotal' => $grandTotal, 'grandProfit' => $grandProfit, 'total' => $total, 'purchaseTotal' => $purchaseTotal,  'charges' => $charges]);
        }
        return $this->success(['nextOrderId' => $nextOrderId, 'perOrderCharge' => $perOrderCharge, 'perProductCharge' => $perProductCharge, 'grandTotal' => $grandTotal, 'grandProfit' => $grandProfit, 'total' => $total, 'purchaseTotal' => $purchaseTotal, 'charges' => $charges]);
    }


    public function search_whiteList(Request $request)
    {
        $article = $request->input('query');
        $dataValue = $request->input('value');


        $query = Product::query();

        if ($article) {
            if ($dataValue === 'multanItems') {
                $records = $query->where('is_multan_list', 1)->orderBy('article', 'asc')->WhereIf('article', '=', $article)->get(['id', 'name', 'article', 'is_multan_list', 'thumbnail']);
            } else {
                $records = $query->WhereIf('article', '=', $article)->orderBy('article', 'asc')->get(['id', 'name', 'article', 'is_multan_list', 'thumbnail']);
            }
        } else {

            if ($dataValue === 'multanItems') {
                // Apply filter for Multan items
                $query->where('is_multan_list', 1);
            } else {
                // Default behavior for white list
                $query->where('is_white_list', 1);
            }
            // Select specific columns
            $records = $query->orderBy('article', 'asc')->get(['id', 'name', 'article', 'is_multan_list', 'thumbnail']);
        }
        return response()->json($records);
    }

    public function updateMultanList(Request $request)
    {
        $request->validate([
            'article_id' => 'required|exists:products,id', // Validate the article ID
        ]);

        $article = Product::find($request->article_id);
        if ($article) {
            $article->is_multan_list = null;
            $article->save();

            return response()->json(['success' => true, 'message' => 'Article updated successfully.']);
        }

        return response()->json(['success' => false, 'message' => 'Article not found.']);
    }
}
