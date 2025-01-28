<?php

namespace App\Http\Controllers\Api\V2\Order;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Product, Order, Order_item, Cart, User, Charge, RestAddress, Note};
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Http\Controllers\ThirdPartyApis\PostEx;

class OrderController extends Controller
{

    // Order Place Real
    public function order_place_new(Request $req)
    {

        $admin_id = $req->admin_id ? $req->admin_id : 1;

        if ($req->hasFile('advance_payment_proof')) {
            $file = $req->file('advance_payment_proof');
            $ext = $file->getClientOriginalExtension();
            $filename = time() . '.' . $ext;
            $file->move('proof-of-payments/', $filename);
            $advance_payment_proof = url('proof-of-payments', $filename);
        } else {
            $advance_payment_proof = null;
        }

        $phone = str_replace(' ', '', $req->phone);
        $phone = str_replace('.', '', $phone);
        $req->request->remove('phone');
        $req->merge(['phone' => $phone]);

        $whatsapp = str_replace(' ', '', $req->whatsapp);
        $req->request->remove('whatsapp');
        $req->merge(['whatsapp' => $whatsapp]);

        // $request->merge(['new_phone' => str_replace(' ', '', $req->phone)]);
        $validator = Validator::make($req->all(), [
            'name' => 'required|string',
            'whatsapp' => 'required|regex:/^((0))(3)([0-9]{9})$/',
            'phone' => 'required|regex:/^((0))(3)([0-9]{9})$/',
            'city_id' => 'required|exists:cities,id',
            'address' => 'required|min:20',
        ], [
            'address.min' => 'Your address is incomplete'
        ]);

        // check validation
        if (!$req->phone) {
            return $this->error('Phone number missing', 'Phone number missing', 422);
        }

        // check validation
        if (!$validator->passes()) {
            return $this->error($validator->errors()->all(), $validator->errors()->first(), 422);
        }

        $is_reseller_order = 0;
        $reseller_profit = 0;

        // check user exists
        $user = auth('sanctum')->user();
        if ($user == null && $req->password == null) {
            // check user exists in db
            $user = User::where('whatsapp', $req->whatsapp)->where('admin_id', $admin_id)->latest()->first();
            if ($user) {
                //     // uer exists in database so ask for password
                //     return $this->error([], 'Please enter password', 2);
            } else {
                $user = new User();
                $user->admin_id = $admin_id;
                $user->name = $req->name;
                $user->city_id = $req->city_id;
                $user->phone = $req->phone;
                $user->password = \Hash::make($req->whatsapp);
                $user->whatsapp = $req->whatsapp ? $req->whatsapp : $req->phone;
                $user->address = $req->address;
                $user->save();
            }
        }

        // if($user->is_reseller == 1){
        // $is_reseller_order = 1;

        // reseller profit
        if ($req->has('profit') && $req->profit > 0) {
            $reseller_profit = $req->profit;
            $is_reseller_order = 1;
        }

        // validate advance payment

        // }

        DB::beginTransaction();
        try {

            // check if any product stock not available
            if (auth('sanctum')->user()) {

                $cart_items = Cart::with('product')->where('user_id', $user->id)->get();
            } else {

                $cart_items = Cart::with('product')->where('device_id', $req->device_id)->get();
            }
            $cart_item_deleted = false;


            foreach ($cart_items as $item) {
                $product = Product::find($item->product->id);
                $productQty = ($product->soldItem + $product->markeetItem);
                if ($productQty <= 0 || $productQty < $item->quantity) {
                    $item->delete();
                    $cart_item_deleted = true;
                }
            }
            // Check if any cart item was deleted and return a message if needed
            if ($cart_item_deleted) {
                return $this->error([], '😔 Some Cart items Quantity has Benn Out of Stock! Plese Select Again Items', 422);
            }

            // $user = auth('sanctum')->user();

            // Restrict Address Validation
            $address = RestAddress::get('address');
            if (count($address) > 0) {
                foreach ($address as $add) {
                    $myString = $req->address;
                    if ($contains = Str::contains($myString, $add->address)) {
                        return $this->error($address, 'So sorry 😔 given address is restricted for service, Contact to Admin', 0);
                    }
                }
            }
            // end restrict validation

            // if(auth('sanctum')->user())
            //     $cart_items = Cart::with('product')->where('user_id',$user->id)->get();
            // else
            //     $cart_items = Cart::with('product')->where('device_id',$req->device_id)->get();

            $my_order = Order::orderBy('id', 'DESC')->first();

            if ($my_order)
                $my_order_id = $my_order->id + 1;
            else
                $my_order_id = 1;

            // \Log::info($my_order_id);
            $pending_order = Order::where(['status' => 'PENDING', 'user_id' => $user->id])->where('admin_id', $admin_id)->first();
            if ($is_reseller_order == 0 && $pending_order && (($pending_order->is_blocked_customer_order == 1 && $user->status == 1) || ($pending_order->is_blocked_customer_order == 0 && $user->status == 0))) {
                $my_order_id = $pending_order->id;
            }

            // \Log::info($my_order_id);
            $insert_order_items = [];

            if ($cart_items->count() > 0) {
                $total = 0;
                $discount = 0;
                $profit = 0;
                $gtotal = 0;
                $gprofit = 0;
                $is_blocked_customer_order = 0;

                // if admin block customer then order should be is_blocked_customer_order
                if ($user->status == 1) {
                    $is_blocked_customer_order = 1;
                }

                // charges get
                $quan_pro =  Cart::with('product')
                    ->where('user_id', $user->id)
                    ->Wherehas('product', function ($q) {
                        $q->where('is_dc_free', 0);
                    })->sum('quantity');
                $pending_charges = 0;
                if ($pending_order) {
                    $pending_charges = Order_item::where(['order_id' => $my_order_id, 'is_dc_free' => 0])->sum('qty');
                }

                $charges = Charge::where('suit', ($quan_pro + $pending_charges))->first();
                if ($charges) {
                    $charges = $charges->charges;
                } else {
                    $charges = 0;
                }


                foreach ($cart_items as $item) {
                    $price = $item->product->price;
                    if($req->reseller_price == 'true'){
                        $price = $item->product->reseller_price;
                    }
                    $total += $price * $item->quantity;
                    $discount += $item->product->discount * $item->quantity;
                    $profit += $item->product->profit * $item->quantity;
                    $reseller_profit += $item->reseller_profit;
                    // if pending order then update order items qty  of pending's order
                    if ($pending_order_item = Order_item::where(['order_id' => $my_order_id, 'prod_id' => $item->product->id])
                        ->first()
                    ) {
                        $pending_order_item->qty = $pending_order_item->qty + $item->quantity;
                        $pending_order_item->save();
                    }
                    // order items added to array by id of coming order
                    else {

                        $insert_order_items[] = [
                            'order_id' => $my_order_id,
                            'prod_id' => $item->product->id,
                            'qty' => $item->quantity,
                            'price' => $price,
                            'purchase' => $item->product->purchase,
                            'profit' => $item->product->profit,
                            'reseller_profit' => $item->reseller_profit,
                            'discount' => $item->product->discount,
                            'is_dc_free' => $item->product->is_dc_free,
                        ];
                    }

                    // Update item quantity of solded and remaining (before) order bcz using try catch
                    // if customer is not blocked by admin
                    if ($user->status == 0) {
                        $product = Product::find($item->product->id);

                        $soldItem = $product->soldItem;
                        $quantity = $item->quantity;

                        // if solItem has less than deduction qty
                        if ($soldItem < $quantity) {
                            $deductFromMarkeetItem = $quantity - $soldItem;
                            $product->soldItem -= $soldItem;
                        } else {
                            $product->soldItem -= $quantity;
                            $deductFromMarkeetItem = 0;
                        }

                        $product->markeetItem -= $deductFromMarkeetItem;
                        $product->markeetPickup += $deductFromMarkeetItem;

                        // $product->soldItem -= $item->quantity;
                        $product->soldAdm += $quantity;

                        $product->timestamps = false;
                        $product->save();
                    }
                }
                /**end Loop */

                $gtotal = ($total + $charges + $reseller_profit) - $discount;
                $gprofit = $profit - $discount;
                // Update Pending order
                if (
                    $pending_order
                    && (($pending_order->is_blocked_customer_order == 1 && $user->status == 1)
                        || ($pending_order->is_blocked_customer_order == 0 && $user->status == 0))
                    && ($is_reseller_order == 0
                        || ($pending_order->phone == $req->phone && $pending_order->phone == $req->phone))
                ) {
                    // if ($pending_order){
                    $now = Carbon::now();
                    $pending_order->name = $req->name;
                    $pending_order->phone = $req->phone;
                    $pending_order->charges = $charges;
                    if ($is_reseller_order == 1) {
                        $pending_order->is_reseller_order = $is_reseller_order;
                        $pending_order->reseller_profit = $pending_order->reseller_profit + $reseller_profit;
                        $pending_order->advance_payment_proof = $pending_order->advance_payment_proof . ',' . $advance_payment_proof;
                    }
                    $pending_order->grandTotal = ($gtotal + $pending_order->amount) - $pending_order->order_discount;
                    $pending_order->grandProfit = $pending_order->grandProfit + $gprofit;
                    $pending_order->amount = $pending_order->amount + $total;
                    $pending_order->order_discount = $pending_order->order_discount + $discount;
                    $pending_order->city_id = $req->city_id;
                    $pending_order->is_blocked_customer_order = $is_blocked_customer_order;
                    $pending_order->address = $req->address;
                    $pending_order->note = $req->note;
                    $pending_order->date = $now->format('d/m/Y');
                    $pending_order->time = $now->format('g:i A');
                    $pending_order->save();
                    $message = 'Your order updated successfully 😊';
                }
                //create new order
                else {
                    $order = new Order();
                    $now = Carbon::now();
                    $order->user_id = $user->id;
                    $order->id = $my_order_id;
										$order->admin_id = $admin_id;
										// $order->wao_seller_id = $admin_id == 1 ? null : $admin_id;
                    $order->name = $req->name;
                    $order->phone = $req->phone;
                    $order->charges = $charges;
                    $order->is_reseller_order = $is_reseller_order;
                    $order->reseller_profit = $reseller_profit;
                    $order->advance_payment_proof = $advance_payment_proof;
                    $order->amount = $total;
                    $order->grandTotal = $gtotal;
                    $order->grandProfit = $gprofit;
                    $order->order_discount = $discount;
                    $order->city_id = $req->city_id;
                    $order->address = $req->address;
                    $order->note = $req->note;
                    $order->is_blocked_customer_order = $is_blocked_customer_order;
                    $order->date = $now->format('d/m/Y');
                    $order->time = $now->format('g:i A');
                    $order->courier_tracking_id = '';
                    $order->save();
                    $message = 'Order placed successfully 😊';
                }

                // update User detail
                $user->address = $req->address;
                $user->phone = $req->phone;
                $user->city_id = $req->city_id;
                $user->save();

                // order items insert
                Order_item::insert($insert_order_items);
                // delete cart items
                $cart_items->each->delete();

                DB::commit();

                $token = $user->createToken('MyApp')->plainTextToken;
                $user->setAttribute('token', $token);
                return $this->success($user, $message, 1);
            }
            DB::commit();
            return $this->error([], 'Sorry 😔 some cart items are not availble for order', 0);
        } catch (Exception $e) {
            DB::rollback();
            // \Log::info($e->getMessage());
            return $this->error($e->getMessage(), $e->getMessage(), 0);
            return $this->error($e->getMessage(), 'Catch Error message', 0);
        }
    }

    public function order_place(Request $req)
    {
        $admin_id = $req->admin_id ? $req->admin_id : 1;

        // \Log::info('Order Place');
        $validator = Validator::make($req->all(), [
            'name' => 'required|string',
            'whatsapp' => 'required|regex:/^((0))(3)([0-9]{9})$/',
            'phone' => 'required|regex:/^((0))(3)([0-9]{9})$/',
            'city_id' => 'required|exists:cities,id',
            'address' => 'required',
        ]);

        // check validation
        if (!$req->phone) {
            return $this->error('Phone number missing', 'Phone number missing', 422);
        }

        // check validation
        if (!$validator->passes()) {
            return $this->error($validator->errors()->all(), $validator->errors()->first(), 422);
        }

        // check user exists
        $user = auth('sanctum')->user();
        if ($user == null && $req->password == null) {
            // check user exists in db
            $user = User::where('whatsapp', $req->whatsapp)->first();
            if ($user) {
                //     // uer exists in database so ask for password
                //     return $this->error([], 'Please enter password', 2);
            } else {
                $user = new User();
                $user->name = $req->name;
                $user->city_id = $req->city_id;
                $user->phone = $req->phone;
                $user->password = \Hash::make($req->whatsapp);
                $user->whatsapp = $req->whatsapp ? $req->whatsapp : $req->phone;
                $user->address = $req->address;
                $user->save();
            }
        }

        DB::beginTransaction();
        try {

            // check if any product stock not available
            if (auth('sanctum')->user()) {

                $cart_items = Cart::with('product')->where('user_id', $user->id)->get();
            } else {

                $cart_items = Cart::with('product')->where('device_id', $req->device_id)->get();
            }
            $cart_item_deleted = false;


            foreach ($cart_items as $item) {
                $product = Product::find($item->product->id);
                if ($product->soldItem <= 0 || $product->soldItem < $item->quantity) {
                    $item->delete();
                    $cart_item_deleted = true;
                }
            }
            // Check if any cart item was deleted and return a message if needed
            if ($cart_item_deleted) {
                return $this->error([], '😔 Some Cart items Quantity has Benn Out of Stock! Plese Select Again Items', 422);
            }

            // $user = auth('sanctum')->user();

            // Restrict Address Validation
            $address = RestAddress::get('address');
            if (count($address) > 0) {
                foreach ($address as $add) {
                    $myString = $req->address;
                    if ($contains = Str::contains($myString, $add->address)) {
                        return $this->error($address, 'So sorry 😔 given address is restricted for service, Contact to Admin', 0);
                    }
                }
            }
            // end restrict validation

            // if(auth('sanctum')->user())
            //     $cart_items = Cart::with('product')->where('user_id',$user->id)->get();
            // else
            //     $cart_items = Cart::with('product')->where('device_id',$req->device_id)->get();

            $my_order = Order::orderBy('id', 'DESC')->first();

            if ($my_order)
                $my_order_id = $my_order->id + 1;
            else
                $my_order_id = 1;

            // \Log::info($my_order_id);
            $pending_order = Order::where(['status' => 'PENDING', 'user_id' => $user->id])->first();
            if ($pending_order && (($pending_order->is_blocked_customer_order == 1 && $user->status == 1) || ($pending_order->is_blocked_customer_order == 0 && $user->status == 0))) {
                $my_order_id = $pending_order->id;
            }


            // \Log::info($my_order_id);
            $insert_order_items = [];

            if ($cart_items->count() > 0) {
                $total = 0;
                $discount = 0;
                $profit = 0;
                $gtotal = 0;
                $gprofit = 0;
                $is_blocked_customer_order = 0;

                // if admin block customer then order should be is_blocked_customer_order
                if ($user->status == 1) {
                    $is_blocked_customer_order = 1;
                }

                // charges get
                $quan_pro =  Cart::with('product')->where('user_id', $user->id)
                    ->Wherehas('product', function ($q) {
                        $q->where('is_dc_free', 0);
                    })->sum('quantity');
                $pending_charges = 0;
                if ($pending_order) {
                    $pending_charges = Order_item::where(['order_id' => $my_order_id, 'is_dc_free' => 0])->sum('qty');
                }

                $charges = Charge::where('suit', ($quan_pro + $pending_charges))->first();
                if ($charges) {
                    $charges = $charges->charges;
                } else {
                    $charges = 0;
                }


                foreach ($cart_items as $item) {
                    $price = $item->product->price;
                    if($req->reseller_price == 'true'){
                        $price = $item->product->reseller_price;
                    }
                    $total += $price * $item->quantity;
                    $discount += $item->product->discount * $item->quantity;
                    $profit += $item->product->profit * $item->quantity;
                    // if pending order then update order items qty  of pending's order
                    if ($pending_order_item = Order_item::where(['order_id' => $my_order_id, 'prod_id' => $item->product->id])
                        ->first()
                    ) {
                        $pending_order_item->qty = $pending_order_item->qty + $item->quantity;
                        $pending_order_item->save();
                    }
                    // order items added to array by id of coming order
                    else {

                        $insert_order_items[] = [
                            'order_id' => $my_order_id,
                            'prod_id' => $item->product->id,
                            'qty' => $item->quantity,
                            'price' => $price,
                            'purchase' => $item->product->purchase,
                            'profit' => $item->product->profit,
                            'discount' => $item->product->discount,
                            'is_dc_free' => $item->product->is_dc_free,
                        ];
                    }

                    // Update item quantity of solded and remaining (before) order bcz using try catch
                    // if customer is not blocked by admin
                    if ($user->status == 0) {
                        $product = Product::find($item->product->id);

                        $soldItem = $product->soldItem;
                        $quantity = $item->quantity;

                        // if solItem has less than deduction qty
                        if ($soldItem < $quantity) {
                            $deductFromMarkeetItem = $quantity - $soldItem;
                            $product->soldItem -= $soldItem;
                        } else {
                            $product->soldItem -= $quantity;
                            $deductFromMarkeetItem = 0;
                        }

                        $product->markeetItem -= $deductFromMarkeetItem;
                        $product->markeetPickup += $deductFromMarkeetItem;

                        // $product->soldItem -= $item->quantity;
                        $product->soldAdm += $quantity;
                        $product->timestamps = false;
                        $product->save();
                    }
                }
                /**end Loop */

                if ($req->has('reseller_profit')) {
                    $reseller_profit = $req->reseller_profit;
                } else {
                    $reseller_profit = 0;
                }

                $gtotal = ($total + $charges) - $discount;
                $gprofit = $profit - $discount;
                // Update Pending order
                if ($pending_order && (($pending_order->is_blocked_customer_order == 1 && $user->status == 1) || ($pending_order->is_blocked_customer_order == 0 && $user->status == 0))) {
                    // if ($pending_order){
                    $now = Carbon::now();
                    $pending_order->name = $req->name;
                    $pending_order->phone = $req->phone;
                    $pending_order->charges = $charges;
                    $pending_order->reseller_profit = $reseller_profit;
                    $pending_order->grandTotal = ($gtotal + $pending_order->amount) - $pending_order->order_discount;
                    $pending_order->grandProfit = $pending_order->grandProfit + $gprofit;
                    $pending_order->amount = $pending_order->amount + $total;
                    $pending_order->order_discount = $pending_order->order_discount + $discount;
                    $pending_order->city_id = $req->city_id;
                    $pending_order->is_blocked_customer_order = $is_blocked_customer_order;
                    $pending_order->address = $req->address;
                    $pending_order->note = $req->note;
                    $pending_order->date = $now->format('d/m/Y');
                    $pending_order->time = $now->format('g:i A');
                    $pending_order->save();
                    $message = 'Your order updated successfully 😊';
                }
                //create new order
                else {
                    $order = new Order();
                    $now = Carbon::now();
                    $order->user_id = $user->id;
                    $order->id = $my_order_id;
                    $order->name = $req->name;
                    $order->phone = $req->phone;
                    $order->charges = $charges;
                    $order->amount = $total;
                    $order->grandTotal = $gtotal;
                    $order->grandProfit = $gprofit;
                    $order->order_discount = $discount;
                    $order->city_id = $req->city_id;
                    $order->address = $req->address;
                    $order->note = $req->note;
                    $order->is_blocked_customer_order = $is_blocked_customer_order;
                    $order->date = $now->format('d/m/Y');
                    $order->time = $now->format('g:i A');
                    $order->courier_tracking_id = '';
                    $order->save();
                    $message = 'Order placed successfully 😊';
                }

                // update User detail
                $user->address = $req->address;
                $user->phone = $req->phone;
                $user->city_id = $req->city_id;
                $user->save();

                // order items insert
                Order_item::insert($insert_order_items);
                // delete cart items
                $cart_items->each->delete();

                DB::commit();

                $token = $user->createToken('MyApp')->plainTextToken;
                $user->setAttribute('token', $token);
                return $this->success($user, $message, 1);
            }
            DB::commit();
            return $this->error([], 'Sorry 😔 some cart items are not availble for order', 0);
        } catch (Exception $e) {
            DB::rollback();
            // \Log::info($e->getMessage());
            return $this->error($e->getMessage(), $e->getMessage(), 0);
            return $this->error($e->getMessage(), 'Catch Error message', 0);
        }
    }

    // Order Place Real
    public function reattach_payment_proof(Request $req)
    {

        $validator = Validator::make($req->all(), [
            'id' => 'required|string',
            'advance_payment_proof' => 'image',
        ]);

        // check validation
        if (!$validator->passes()) {
            return $this->error($validator->errors()->all(), $validator->errors()->first(), 422);
        }

        if ($req->hasFile('advance_payment_proof')) {
            $file = $req->file('advance_payment_proof');
            $ext = $file->getClientOriginalExtension();
            $filename = time() . '.' . $ext;
            $file->move('proof-of-payments/', $filename);
            $advance_payment_proof = url('proof-of-payments', $filename);
        } else {
            $advance_payment_proof = null;
        }

        $user = auth('sanctum')->user();

        Order::where('user_id', $user->id)->where('id', $req->id)->update([
            'advance_payment_proof' => $advance_payment_proof,
        ]);

        return $this->success(null, 'Payment Proof Submitted', 1);
    }

    //view orders
    public function view_orders(Request $req)
    {
        // $admin_id = $req->seller_id ? $req->seller_id : 1;
        $orders = Order::select('*', DB::raw('(CASE WHEN status = "Unbooked" THEN "DISPATCHED" WHEN status = "booked" THEN "DISPATCHED" ELSE status END) AS status'))
            // selectRaw('IF (status IS BOOKED) as status ELSE status')
            ->where('user_id', auth('sanctum')->user()->id)
            // ->where('admin_id', $admin_id)
            ->with(['history' => function ($query) {
                $query->latest('time')->take(1);
            }])
            // ->limit(10)
            ->latest()
            ->get();
        return $this->success($orders, 'Orders Record', 1);
    }

    // orders refuse/attemted/etc that need to check
    public function orders_in_problem(Request $req)
    {
        $orders = Order::
            // select('*', DB::raw('(CASE WHEN status = "Unbooked" THEN "DISPATCHED" WHEN status = "booked" THEN "DISPATCHED" ELSE status END) AS status'))
            // selectRaw('IF (status IS BOOKED) as status ELSE status')
            // ->
            where('user_id', auth('sanctum')->user()->id)
            ->with(['history' => function ($query) {
                $query->latest('time')->take(1);
            }])
            ->where(function ($q) {
                $q->where('status', 'Attempted')
                    ->orWhere('status', 'ATTEMPTED');
            })
            ->limit(1)
            ->get();
        return $this->success($orders, 'Orders Record', 1);
    }

    // orders re attempt
    public function mark_reattempted(Request $request, $id)
    {
        // can only handle postex at this time
        // for internal use 1 for re attempt, 2 for return back
        $orders = Order::where('id', $id)->first();

        // return $output =  PostEx::re_attempt_order($orders->tracking_id, $request->stauts, $request->remkars,);

        // if($output['st'] == 1){
        // mark re attempt
        $orders->status = 'Re-Attempted';
        $orders->re_attempt_advice_id = $request->re_attempt_advice_id;
        $orders->re_attempt_remarks = $request->remarks;
        $orders->save();

        // create order history
        // }

        return $this->success([], 'Successfully added shipper advice', 1);
    }

    // single order detail
    public function single_order(Request $req)
    {
        $order = Order::select('*', DB::raw('(CASE WHEN status = "Unbooked" THEN "DISPATCHED" WHEN status = "booked" THEN "DISPATCHED" ELSE status END) AS status'))
            ->with([
                'userdetail:id,name,city_id,phone,courier_phone,whatsapp,is_verified,address',
                'userdetail.city:id,name', 'orderitems', 'notes',
                'orderitems.product' => function($q){
									return $q->select('id', 'name', 'thumbnail', 'soldAdm', 'exceed_limit', DB::raw('CAST((soldItem + markeetItem) AS CHAR) as soldItem'), 'markeetItem');
								}
            ])->where('id', $req->order_id)->first();
        $minutes = $order->created_at->diffInMinutes(Carbon::now());

        $time_attr = [
            'day' => $order->created_at->format('d'),
            'month' => $order->created_at->format('m'),
            'year' => $order->created_at->format('y'),
            'minutes' => $minutes,
        ];
        $order->setAttribute("timeAttr", $time_attr);

        if ($order->status == 'BOOKED') {
            $order->status = 'DISPACHED';
        }

        return $this->success($order, 'Single Order Record', 1);
    }

    //Cancel Order
    public function cancel_order(Request $req)
    {
        $order = Order::where('id', $req->order_id)->first();

        //check half hour Order update
        // $diff_in_min = $check->created_at->diffInMinutes($check->updated_at);
        // if($diff_in_min > 30){
        //     return $message = 'Sorry You order time is more than 30 minutes!';
        // }

        // check if order dispatched
        if ($order->status == 'DISPATCHED') {
            return $this->error('', 'Sorry! 😔 Your order has been dispatched', 0);
        }

        // check if order already cancel
        if ($order->status == 'CANCEL') {
            return $this->error('', 'Sorry! 😔 order already cancelled', 0);
        }

        // Product quantity decreased if (user active and other status active)
        if ($order->is_blocked_customer_order == 0 && $order->userdetail->status == 0) {
            $order_product = Order_item::where('order_id', $order->id)->get();
            foreach ($order_product as $item) {
                $product = Product::where('id', $item->prod_id)->first();
                $product->soldItem += $item->qty;
                $product->soldAdm -= $item->qty;
                $product->save();
            }
        }

        $order->status = 'CANCEL';
        $order->save();
        return $this->success($order, 'Order cancelled succssfully 😊', 1);
    }

    //update order info/address
    public function update_order_detail(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'order_id' => 'required|numeric|exists:orders,id',
            'courier_phone' => 'required|regex:/^((0))(3)([0-9]{9})$/',
            'city_id' => 'required|exists:cities,id',
            'address' => 'required',
        ]);

        if (!$validator->passes()) {
            return $this->error($validator->errors()->first(), 'Validation Error', 422);
        }
        $order = Order::find($req->order_id);
        //check half hour Order update
        // $diff_in_min = $check->created_at->diffInMinutes($check->updated_at);
        // if($diff_in_min > 30){
        //     return $message = 'Sorry You order time is more than 30 minutes!';
        // }

        // check if order dispatched
        if ($order->status == 'PENDING') {
            $order->name = $req->name;
            $order->phone = $req->courier_phone ? $req->courier_phone : $req->phone;
            $order->city_id = $req->city_id;
            $order->address = $req->address;
            $order->save();

            $user = auth('sanctum')->user();
            $user->courier_phone = $req->courier_phone ? $req->courier_phone : $req->phone;
            $user->phone = $req->courier_phone ? $req->courier_phone : $req->phone;
            $user->city_id = $req->city_id;
            $user->address = $req->address;
            $user->save();
            return $this->success($order, 'Order Updated Successfully', 1);
        }
        return $this->success($order, 'Order Status Changed, You can not Update', 1);
    }

    // ADD notes order
    public function add_order_note(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'order_id' => 'required|numeric|exists:orders,id',
            'note' => 'required',
        ]);

        if (!$validator->passes()) {
            return $this->error($validator->errors()->first(), 'Validation Error', 422);
        }

        Note::updateorcreate([
            'order_id' => $req->order_id,
        ], [
            'order_id' => $req->order_id,
            'note' => $req->note,
        ]);
        return $this->success([], 'Add Order Note Succcessfully', 1);
    }

    // update order
    public function order_update(Request $req)
    {
        $order = Order::find($req->order_id);
        if($order->status != 'PENDING'){
            return $this->success([], 'Order status must be pending', 1);
        }
        $total = 0;
        $discount = 0;
        $profit = 0;
        $charges = $req->cod;
        foreach($req->items as $item){
            if($item['qty'] == 0){
                $order_item = Order_item::where('id', $item['id'])->first();
                $this->productQtyReset($order_item->prod_id, $order_item->qty, 0);
                $order_item->delete();
            }else{
                $order_item = Order_item::with(['product:id,name,price'])->where('id', $item['id'])->first();
                $this->productQtyReset($order_item->prod_id, $order_item->qty, $item['qty']);

                $order_item->qty = $item['qty'];
                $order_item->save();

                $total += $order_item->product->price * $item['qty'];
                $discount += $order_item->product->discount * $item['qty'];
                $profit += $order_item->product->profit * $item['qty'];

            }
        }
        
        $gtotal = ($total + $charges) - $discount;
        $gprofit = $profit - $discount;

        $order->charges = $charges;
        $order->amount = $total;
        $order->grandTotal = $gtotal;
        $order->grandProfit = $gprofit;
        $order->order_discount = $discount;
        $order->save();
        
        return $this->success(null, 'Order updated Succcessfully', 1);
    }

    public function productQtyReset($prod_id, $oldQty, $newQty)
    {
        $qty = $oldQty - $newQty;
        $product = Product::where('id', $prod_id)->first();
        $product->soldItem += $qty;
        $product->soldAdm -= $qty;
        $product->save();
    }
}
