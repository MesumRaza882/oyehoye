<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\category;
use App\Models\Product;
use App\Models\Order;
use App\Models\User;
use App\Models\Problem;
use App\Models\ProductReview;
use App\Models\City;
use App\Models\RestAddress;
use App\Models\Charge;
use App\Models\Note;
use App\Models\Order_item;
use App\Models\Admin;
use App\Models\ResellerSetting;

use App\Models\MessageNotification;
use App\Models\message;
use Illuminate\Support\Facades\Cache;
use App\Models\LockedkFolderPassword;
use Illuminate\Support\Facades\Validator;
use DB;
use Auth;
use Session;
use Domain;
use Carbon\Carbon;

class WaoController extends Controller
{

    function register()
    {
        $user = User::get();
        return view('themes.wao.register');
    }

    public function RegisterSubmit(Request $request)
    {
        $phone = str_replace(' ', '', $request->phone);
        $request->request->remove('phone');
        $request->merge(['phone' => $phone]);
        $whatsapp = str_replace(' ', '', $request->whatsapp);
        $request->request->remove('whatsapp');
        $request->merge(['whatsapp' => $whatsapp]);

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            // 'whatsapp'=>'required|unique:users,whatsapp|regex:/^((0))(3)([0-9]{9})$/',
            'whatsapp' => 'required|regex:/^((0))(3)([0-9]{9})$/',
            // 'phone'=>'required|regex:/^((0))(3)([0-9]{9})$/',
            // 'password'=>'required',
            // 'address'=>'required',
            // 'city_id'=>'required',
            // 'remember_token' => 'required',
        ]);

        if (!$validator->passes()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        DB::beginTransaction();
        try {
            // $request->merge(['password' => \Hash::make($request->password)]);
            // check if user has blocked by admin or not
            $status = 0;
            $is_blocked = User::where(['whatsapp' => $request->whatsapp, 'status' => 1])->latest()->first();
            if ($is_blocked) {
                $status = 1;
            }

            $admin_id = $request->admin_id ? $request->admin_id : 1;
            $user = User::where('whatsapp', $request->whatsapp)->where('admin_id', $admin_id)->where('name', $request->name)->latest()->first();
            if (!$user) {
                // $user = User::create($request->all());
                $user = new User();
                $user->admin_id = $admin_id;
                $user->name = $request->name;
                $user->city_name = $request->city_name;
                $user->city_id = $request->city_id;
                $user->country = $request->country;
                $user->phone = $request->phone;
                // $user->password = \Hash::make($request->password);
                $user->password = \Hash::make($request->phone);
                $user->whatsapp = $request->whatsapp;
                $user->address = $request->address;
                $user->status = $status;
                $user->is_reseller = 0;
                // $user->remember_token = $request->remember_token;
                $user->save();
            } else {
                Auth::login($user);
                // return redirect()->back()->with('failed','Account already exists, login or contact admin');
            }

            // Auth::attempt(['whatsapp' => $request->whatsapp, 'password' => $request->password])
            // if(Auth::login($user))
            // {
            Auth::login($user);
            $login_user = Auth::guard('web')->user();
            $token = $login_user->createToken('MyApp')->plainTextToken;
            $user->setAttribute('token', $token);

            DB::commit();
            // return $this->success($user, 'Joined Successfully', 1);
            return redirect('menu')->with('success', 'Joined Successfully');
            // }else{
            //     DB::commit();
            //     return $this->error($user,'Something went wrong, contact admin', 0);
            // }

        } catch (Exception $e) {
            DB::rollback();
            return $this->error($e->getMessage(), 'Registeration Error ! something went Wrong', 0);
        }
    }

    function index()
    {
        $category = category::get();
        $user = auth('sanctum')->user();
        $user_id = $user ? $user->id : null;
        $message_notifications = MessageNotification::where('user_id', $user_id)->get()->pluck('message_id');
        $messages = message::select(['messages.id', 'messages.message'])
            ->where(function ($q) use ($user_id) {
                $q->where('messages.user_id', NULL)
                    ->orWhere('messages.user_id', $user_id);
            })
            ->whereNotIn('messages.id', $message_notifications)
            ->where('messages.created_at', '>=', Carbon::today()->subDays((int)'messages.days' + 1))
            ->get();
        return view('themes.wao.index', compact('category', 'messages'));
    }

    public function NewArrivals(Request $request)
    {
        $products = Product::limit(500)
        ->select([
            'id', 'name', 'price', 'discount', 'article', 'is_dc_free', 'category_id',
            'soldAdm', 'exceed_limit', 'video', 'thumbnail', 'increase_perMin', 'updated_at',
            DB::raw('CAST((soldItem + markeetItem) AS CHAR) as soldItem'),
        ])
        ->webQuery($request);
        // return $products;
        $plusIndex = 0;
        $layout =  request()->get('layout', 1);
        return view('themes.wao.products.new_arrivals', compact('products', 'plusIndex', 'layout'));
    }

    // i think below function not using
    public function single_product($id)
    {
        // $products = Product::latest()->find($id);
        $products = Product::with([
            'reseller_setting' => function ($q) {
                $admin_id = Domain::admin('id');
                $admin_id = $admin_id ? $admin_id : 1;
                $q->where('admin_id', $admin_id);
            }
        ])
            ->latest()
            ->where('id', $id)
            ->get()
            ->map(function ($i) {
                if ($i->reseller_setting != null && $i->reseller_setting->price != null) {
                    $i->price = $i->reseller_setting->price;
                    $i->reseller_product_profit = $i->reseller_setting->reseller_product_profit;
                } else {
                    $i->reseller_product_profit = 0;
                }
                return $i;
            });
        $plusIndex = 0;
        $layout =  request()->get('layout', 1);
        return view('themes.wao.products.single_item', compact('products', 'plusIndex', 'layout'));
    }

    public function loadMoreProducts(Request $request)
    {
        $offset = request()->get('offset', 0);

        $limit = 1;

        $products = Product::skip($offset)
            ->limit($limit)
            ->webQuery($request);

        $plusIndex = $offset;
        $layout =  request()->get('layout', 1);
        $productids = $products->pluck('id')->toArray();
        // return $products;
        if(count($productids) > 0 && $productids[0] != null){
            $html = view('themes.wao.products.partials.video-card', compact('products', 'plusIndex', 'layout'))->render();
        }else{
            $html = null;
        }
        return response()->json([
            'html' => $html,
            'productids' => $productids
        ]);
    }


    // function SmallProducts() {
    //     $products = Product::latest()->limit(20)->get();
    //     return view('themes.wao.products.small_products',compact('products'));
    // }

    // function MediumProducts() {
    //     $products = Product::latest()->limit(20)->get();
    //     return view('themes.wao.products.medium_products',compact('products'));
    // }

    // make it similar to NewArrivals/Somthing
    public function ActiveStock(Request $request)
    {
        // $products = Product::latest()->limit(5)->get();
        $products = Product::select([
            'id', 'name', 'price', 'discount', 'article', 'is_dc_free', 'category_id',
            'soldAdm', 'exceed_limit', 'video', 'thumbnail', 'increase_perMin', 'updated_at',
            DB::raw('CAST((soldItem + markeetItem) AS CHAR) as soldItem'),
        ])
            ->where('soldstatus', 0)
            ->where('soldItem', '>', 0)
            ->limit(500)
            ->groupBy('id')
            ->webQuery($request);
        $plusIndex = 0;
        $layout =  request()->get('layout', 1);
        return view('themes.wao.products.new_arrivals', compact('products', 'plusIndex', 'layout'));
    }

    // make it similar to NewArrivals/Somthing
    public function CategoryProducts(Request $request, $id)
    {
        $products = Product::where('category_id', $id)
            ->limit(35)
            ->latest()
            ->webQuery($request);
        $plusIndex = 0;
        $layout = request()->get('layout', 1);

        return view('themes.wao.products.new_arrivals', compact('products', 'plusIndex', 'layout'));
    }

    // make it similar to NewArrivals/Somthing
    public function SearchProducts(Request $request)
    {
        $products = Product::Detail()
            // ->latest('pinned_at')
            ->where('name', 'like', '%' . $request->name . '%')
            // ->orderBy('updated_at', 'desc')
            // ->orderBy('id', 'desc')
            ->groupBy('id')
            ->limit(35)
            ->where(function ($q) use ($request) {
                $q->where('show_point', 1)
                    ->orWhere('show_point', 3);
            })
            ->webQuery($request);

        $plusIndex = 0;
        $layout =  request()->get('layout', 1);
        return view('themes.wao.products.new_arrivals', compact('products', 'plusIndex', 'layout'));
    }

    // make it similar to NewArrivals/Somthing
    public function LockedProducts(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required',
        ]);

        if (!$validator->passes()) {
            return $this->error($validator->errors()->first(), 'Password Validation', 0, 422);
        }
        if ($request->password != LockedkFolderPassword::find(1)->text_password) {
            return back()->with('error', 'Locked password invalid');
        }
        $products = Product::latest()
            ->where('is_locked', 1)
            ->limit(35)
            ->webQuery($request);
        $layout =  request()->get('layout', 1);
        $plusIndex = 0;
        return view('themes.wao.products.new_arrivals', compact('products', 'layout', 'plusIndex'));
    }

    public function orders()
    {
        if (auth('sanctum')->user()) {
            $orders = Order::select('*', DB::raw('(CASE WHEN status = "Unbooked" THEN "DISPATCHED" WHEN status = "booked" THEN "DISPATCHED" ELSE status END) AS status'))
                ->where('user_id', auth('sanctum')->user()->id)
                // ->where('admin_id', $admin_id)
                ->with(['history' => function ($query) {
                    $query->latest('time')->take(1);
                }])
                // ->limit(10)
                ->latest()
                ->get();

            return view('themes.wao.Orders.index', compact('orders'));
        } else {
            return redirect()->route('register');
        }
    }

    public function OrderDetail(Request $request)
    {
        // return 123;
        $order = Order::select('*', DB::raw('(CASE WHEN status = "Unbooked" THEN "DISPATCHED" WHEN status = "booked" THEN "DISPATCHED" ELSE status END) AS status'))
            ->with([
                'userdetail:id,name,city_id,phone,courier_phone,whatsapp,is_verified,address',
                'userdetail.city:id,name', 'orderitems', 'notes',
                'orderitems.product:id,name,thumbnail'
            ])->where('id', $request->order_id)->first();
        // return $order;
        return view('themes.wao.Orders.order_detail', compact('order'));
    }

    public function OrderComplete()
    {
        return view('themes.wao.Orders.order_complete');
    }

    public function PlaceOrder()
    {
        if (auth('sanctum')->user()) {
            $cities = Cache::rememberForever('cities', function () {
                return $cities = City::select('id', 'postex as name')
                    // ->whereNotNull('c_city_name')
                    // ->whereNotNull('postex_city_id')
                    ->orderBy('c_city_name', 'ASC')
                    ->whereNotNUll('postex')
                    ->get();
            });
            return view('themes.wao.Orders.order_place', compact('cities'));
        } else {
            return redirect()->route('register');
        }
    }

    public function order_place_new(Request $request)
    {
        // return $request->all();
        $admin_id = Domain::admin('id');
        $admin_id = $admin_id ? $admin_id : 1;
        $type = 2;

        // if ($request->hasFile('advance_payment_proof')) {
        // 	$file = $request->file('advance_payment_proof');
        // 	$ext = $file->getClientOriginalExtension();
        // 	$filename = time() . '.' . $ext;
        // 	$file->move('proof-of-payments/', $filename);
        // 	$advance_payment_proof = url('proof-of-payments', $filename);
        // } else {
        // 	$advance_payment_proof = null;
        // }
        $advance_payment_proof = null;

        $phone = str_replace(' ', '', $request->phone);
        $phone = str_replace('.', '', $phone);
        $request->request->remove('phone');
        $request->merge(['phone' => $phone]);

        $whatsapp = str_replace(' ', '', $request->whatsapp);
        $request->request->remove('whatsapp');
        $request->merge(['whatsapp' => $whatsapp]);

        // $request->merge(['new_phone' => str_replace(' ', '', $request->phone)]);
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'whatsapp' => 'required|regex:/^((0))(3)([0-9]{9})$/',
            'phone' => 'required|regex:/^((0))(3)([0-9]{9})$/',
            'city_id' => 'required|exists:cities,id',
            'address' => 'required|min:20',
        ], [
            'address.min' => 'Your address is incomplete'
        ]);

        // check validation
        if (!$request->phone) {
            // return redirect()->back()->with('error', 'Phone number missing');
            return $this->error('Phone number missing', 'Phone number missing', 0, 422);
        }

        // check validation
        if (!$validator->passes()) {
            // return redirect()->back()->with('error', $validator->errors()->first());
            return $this->error($validator->errors()->all(), $validator->errors()->first(), 0, 422);
        }

        $is_reseller_order = 0;
        $reseller_profit = 0;

        // check user exists
        $user = auth()->user();
        if ($user == null && $request->password == null) {
            // check user exists in db
            $user = User::where('whatsapp', $request->whatsapp)->where('admin_id', $admin_id)->latest()->first();
            if ($user) {
                //     // uer exists in database so ask for password
                //     return $this->error([], 'Please enter password', 2);
            } else {
                $user = new User();
                $user->admin_id = $admin_id;
                $user->name = $request->name;
                $user->city_id = $request->city_id;
                $user->phone = $request->phone;
                $user->password = \Hash::make($request->whatsapp);
                $user->whatsapp = $request->whatsapp ? $request->whatsapp : $request->phone;
                $user->address = $request->address;
                $user->save();
            }

            Auth::login($user);
        }

        // if($user->is_reseller == 1){
        // $is_reseller_order = 1;

        // reseller profit
        if ($request->has('profit') && $request->profit > 0) {
            $reseller_profit = $request->profit;
            $is_reseller_order = 1;
        }

        DB::beginTransaction();
        try {
            // check if any product stock not available
            // if (auth('sanctum')->user()) {
            // 	$cart_items = Cart::with('product')->where('user_id', $user->id)->get();
            // } else {
            // 	$cart_items = Cart::with('product')->where('device_id', $request->device_id)->get();
            // }

            // $cart_items = Product::whereIn('id', $request->product_id)->get();
            $cart_items = Session::get('cartItems');
            $cart_item_deleted = false;

            $quan_pro = 0;
            foreach ($cart_items as $item) {
                $product = Product::find($item->product_id);
                $productQty = ($product->soldItem + $product->markeetItem);
                if ($productQty <= 0 || $productQty < $item->qty) {
                    $cart_item_deleted = true;
                }

                $quan_pro += $item->qty;
            }

            // Check if any cart item was deleted and return a message if needed
            if ($cart_item_deleted) {
                return $this->error([], '😔 Some Cart items Quantity has Benn Out of Stock! Plese Select Again Items', 0, 422);
            }

            // $user = auth('sanctum')->user();

            // Restrict Address Validation
            $address = RestAddress::get('address');
            if (count($address) > 0) {
                foreach ($address as $add) {
                    $myString = $request->address;
                    if ($contains = Str::contains($myString, $add->address)) {
                        return $this->error($address, 'So sorry 😔 given address is restricted for service, Contact to Admin', 0);
                    }
                }
            }
            // end restrict validation

            // if(auth('sanctum')->user())
            //     $cart_items = Cart::with('product')->where('user_id',$user->id)->get();
            // else
            //     $cart_items = Cart::with('product')->where('device_id',$request->device_id)->get();

            $my_order = Order::orderBy('id', 'DESC')->first();

            if ($my_order) {
                $my_order_id = $my_order->id + 1;
            } else {
                $my_order_id = 1;
            }

            // \Log::info($my_order_id);
            $pending_order = Order::where(['status' => 'PENDING', 'user_id' => $user->id, 'admin_id' => $admin_id])->first();
            if ($is_reseller_order == 0 && $pending_order && (($pending_order->is_blocked_customer_order == 1 && $user->status == 1) || ($pending_order->is_blocked_customer_order == 0 && $user->status == 0))) {
                $my_order_id = $pending_order->id;
            }

            // \Log::info($my_order_id);
            $insert_order_items = [];

            if (count($cart_items) > 0) {
                $total = 0;
                $profit = 0;
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
                // $quan_pro =  Cart::with('product')->where('user_id', $user->id)
                // 	->Wherehas('product', function ($q) {
                // 		$q->where('is_dc_free', 0);
                // 	})->sum('quantity');

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

                $cart_items = Session::get('cartItems');
                // $cart_items = Product::whereIn('id', $request->product_id)->get();
                foreach ($cart_items as $item) {
                    // return $item;
                    $product = Product::with('reseller_setting')->where('id', $item->product_id)->first();
                    $total += $product->price * $item->qty;
                    $discount += $product->discount * $item->qty;
                    $profit += $product->profit * $item->qty;
                    $reseller_profit += 0; //$item->reseller_profit;
                    $reseller_profit_product  = 0;
                    // check if admin has is_specific_profit in resellerSetting and also product has specific_reseller_profit column not null
                    $isAdminSpecificProfit = ResellerSetting::where(['admin_id' => $admin_id, 'prod_id'=>$item->product_id])->first();
                    if($isAdminSpecificProfit)
                    {
                        $reseller_profit_product  = (float)$isAdminSpecificProfit->profit * (int)$item->qty;
                    }
                    // if pending order then update order items qty  of pending's order
                    if ($pending_order_item = Order_item::where(['order_id' => $my_order_id, 'prod_id' => $item->product_id])->first()) {
                        $pending_order_item->qty = $pending_order_item->qty + $item->qty;
                        $pending_order_item->reseller_profit = $reseller_profit_product;
                        $pending_order_item->save();
                    }
                    // order items added to array by id of coming order
                    else {
                        $insert_order_items[] = [
                            'order_id' => $my_order_id,
                            'prod_id' => $item->product_id,
                            'qty' => $item->qty,
                            'price' => $product->price,
                            'purchase' => $product->purchase,
                            'profit' => $product->profit,
                            // 'reseller_profit' => 0,//$item->reseller_profit,
                            'reseller_profit' => $reseller_profit_product, //$item->reseller_profit,
                            'discount' => $product->discount,
                            'is_dc_free' => $product->is_dc_free,
                        ];
                    }

                    // Update item quantity of solded and remaining (before) order bcz using try catch
                    // if customer is not blocked by admin
                    if ($user->status == 0) {
                        // $product = Product::find($item->id);

                        $soldItem = $product->soldItem;
                        $quantity = $item->qty;

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
                $order_id = null;
                $gtotal = ($total + $charges + $reseller_profit) - $discount;
                $gprofit = $profit - $discount;
                // Update Pending order
                if (
                    $pending_order
                    && (($pending_order->is_blocked_customer_order == 1 && $user->status == 1)
                        || ($pending_order->is_blocked_customer_order == 0 && $user->status == 0))
                    && ($is_reseller_order == 0
                        || ($pending_order->phone == $request->phone && $pending_order->phone == $request->phone))
                ) {
                    // if ($pending_order){
                    $now = Carbon::now();
                    $pending_order->admin_id = $admin_id;
                    $pending_order->type = $type;
                    $pending_order->name = $request->name;
                    $pending_order->phone = $request->phone;
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
                    $pending_order->city_id = $request->city_id;
                    $pending_order->is_blocked_customer_order = $is_blocked_customer_order;
                    $pending_order->address = $request->address;
                    $pending_order->note = $request->note;
                    $pending_order->date = $now->format('d/m/Y');
                    $pending_order->time = $now->format('g:i A');
                    $pending_order->save();
                    $message = 'Your order updated successfully 😊';
                    $order_id = $pending_order->id;
                }
                //create new order
                else {
                    $order = new Order();
                    $now = Carbon::now();
                    $order->user_id = $user->id;
                    $order->admin_id = $admin_id;
                    $order->type = $type;
                    $order->id = $my_order_id;
                    $order->name = $request->name;
                    $order->phone = $request->phone;
                    $order->charges = $charges;
                    $order->is_reseller_order = $is_reseller_order;
                    $order->reseller_profit = $reseller_profit;
                    $order->advance_payment_proof = $advance_payment_proof;
                    $order->amount = $total;
                    $order->grandTotal = $gtotal;
                    $order->grandProfit = $gprofit;
                    $order->order_discount = $discount;
                    $order->city_id = $request->city_id;
                    $order->address = $request->address;
                    $order->note = $request->note;
                    $order->is_blocked_customer_order = $is_blocked_customer_order;
                    $order->date = $now->format('d/m/Y');
                    $order->time = $now->format('g:i A');
                    $order->courier_tracking_id = '';
                    $order->save();
                    $message = 'Order placed successfully 😊';
                    $order_id = $order->id;
                }

                // update User detail
                $user->address = $request->address;
                $user->phone = $request->phone;
                $user->city_id = $request->city_id;
                $user->save();
                // order items insert
                Order_item::insert($insert_order_items);

                // delete cart items
                // $cart_items->each->delete();

                DB::commit();

                // $token = $user->createToken('MyApp')->plainTextToken;
                // $user->setAttribute('token', $token);
                $user->order_id = $order_id;
                return $this->success($user, $message, 1);
            }
            DB::commit();
            return $this->error([], 'Sorry 😔 some cart items are not availble for order', 0, 422);
        } catch (Exception $e) {
            DB::rollback();
            // \Log::info($e->getMessage());
            return $this->error($e->getMessage(), $e->getMessage(), 0, 422);
            return $this->error($e->getMessage(), 'Catch Error message', 0, 422);
        }
    }

    public function complaint()
    {
        return view('themes.wao.complaint');
    }

    public function addComplaint(Request $request)
    {
        // return $req;
        $validator = Validator::make($request->all(), [
            'whatsapp' => 'required|digits:11',
            // 'user_id'=>'required|integer',
            'user_name' => 'required', //|digits:11
            'comment' => 'required',
            // 'image'=>'required|image',
        ]);

        if ($validator->fails()) {
            return redirect('wao/complaint')->withErrors($validator)->withInput();
        }

        $problem = new Problem();
        $problem->whatsapp = $request->whatsapp;
        $problem->user_name = $request->user_name;
        $problem->comment = $request->comment;

        if (auth()->guard('sanctum')->check()) {
            $problem->user_id = auth()->guard('sanctum')->user()->id;
        } else {
            $problem->user_id = 0;
        }

        // image
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $ext = $file->getClientOriginalExtension();
            $filename = time() . '.' . $ext;
            $file->move('complaint/', $filename);
            $problem->image = $filename;
        }
        $saveArrival = $problem->save();

        if ($saveArrival) {
            return redirect()->back()->with('success', 'We will respond to you within 24 working hours');
            // return $this->success([], 'We will respond to you within 24 working hours', 1);
        }
        // return $this->error([], 'Please try again, there is an issues', 0, 404);
        return redirect('wao/profile')->back()->with('failed', 'Please try again, there is an issues');
    }

    public function search()
    {
        return view('themes.wao.search');
    }

    public function profile(Request $request)
    {
        if (auth('sanctum')->user()) {
            $admin_id = $request->admin_id ? $request->admin_id : 1;
            $user = User::with('city')->where('admin_id', $admin_id)->find(auth('sanctum')->user()->id);
            $cities = Cache::rememberForever('cities', function () {
                return $cities = City::select('id', 'postex as name')
                    // ->whereNotNull('c_city_name')
                    // ->whereNotNull('postex_city_id')
                    ->orderBy('c_city_name', 'ASC')
                    ->whereNotNUll('postex')
                    ->get();
            });
            // return $user;
            return view('themes.wao.profile', compact('user', 'cities'));
        } elseif (auth()->guard('admin')->check()) {
            $user = auth()->guard('admin')->user();
            return view('themes.wao.admin_profile', compact('user'));
        } else {
            return redirect()->route('register');
        }
    }

    public function profit()
    {
        // if(auth('sanctum')->user()){
        $admin_id = Domain::admin('id');
        $orders = Order::select('*', DB::raw('(CASE WHEN status = "Unbooked" THEN "DISPATCHED" WHEN status = "booked" THEN "DISPATCHED" ELSE status END) AS status'))
            // ->where('user_id', auth('sanctum')->user()->id)
            ->where('admin_id', $admin_id)
            ->with(['history' => function ($query) {
                $query->latest('time')->take(1);
            }])
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get();
        $total_balance = Order::where('admin_id', $admin_id)->sum('reseller_profit');
        $paid_balance = Order::where('admin_id', $admin_id)
            ->where('status', 'DELIVERED')->where('profit_transaction_status', '!=', 'pending')
            ->sum('reseller_profit');
        $unpaid_balance = Order::where('admin_id', $admin_id)
            ->where('status', 'DELIVERED')->where('profit_transaction_status', 'pending')
            ->sum('reseller_profit');
        $pending_balance = Order::where('admin_id', $admin_id)
            ->whereNotIn('status', ['DELIVERED', 'RETURNED', 'CANCEL'])
            // ->where('profit_transaction_status','!=','pending')
            ->sum('reseller_profit');
        $cancel_balance = Order::where('admin_id', $admin_id)
            ->whereIn('status', ['RETURNED', 'CANCEL'])
            ->sum('reseller_profit');

        return view('themes.wao.admin_orders', compact(
            'orders',
            'total_balance',
            'paid_balance',
            'unpaid_balance',
            'pending_balance',
            'cancel_balance'
        ));
        // }else {
        //   return redirect()->route('register');
        // }
    }
    public function loadMoreAdminOrder(Request $request)
    {
        $admin_id = Domain::admin('id');
        $orders = Order::select('*', DB::raw('(CASE WHEN status = "Unbooked" THEN "DISPATCHED" WHEN status = "booked" THEN "DISPATCHED" ELSE status END) AS status'))
            ->where('admin_id', $admin_id)
            ->with(['history' => function ($query) {
                $query->latest('time')->take(1);
            }])
            ->orderBy('id', 'desc')
            ->skip($request->offset ?? 0)
            ->limit(10)
            ->get();
        $html = view('themes.wao.Orders.partial.all_order', compact('orders'))->render();
        return response()->json([
            'html' => $html
        ]);
    }

    //profile/update
    public function profile_update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'city_id' => 'required',
            'name' => 'required',
            'courier_phone' => 'nullable|regex:/^((0))(3)([0-9]{9})$/',
        ]);

        if ($request->city_id == 'null') {
            return $this->error('Incorrect city id', 'Incorrect city id', 0, 422);
        }

        if (!$validator->passes()) {
            return $this->error($validator->errors()->first(), 'Validation Error ! Enter Correct Details', 0, 422);
        }

        $user = User::where('id', auth('sanctum')->user()->id)->first(); // Retrieve the user first
        if ($user) {
            // Update only the specified fields, excluding the _token field
            $user->update($request->except('_token'));
            return redirect()->back()->with('success', 'User updated his profile');
        } else {
            return $this->error('User not found', 'User not found', 404);
        }
    }

    public function LockedItems()
    {
        return view('themes.wao.locked-items');
    }

    public function CustomerReviews()
    {
        return view('themes.wao.reviews');
    }

    public function reviews()
    {
        return view('themes.wao.review_form');
    }

    public function review_submit(Request $request)
    {

        $validator = Validator::make($request->all(), [
            // 'review' => 'required|in:1,2,3,4,5',
            'desc' => 'required|string',
            'image' => 'required',
        ]);

        // check validation
        if (!$validator->passes()) {
            return $this->error($validator->errors()->all(), $validator->errors()->first(), 0, 422);
        }

        $user = auth('sanctum')->user();
        if ($user) {
            $user_id = $user->id;
            $user_name = $user->name;
            // if($user_name == null){
            //   $user_name = 'no name';
            // }
        } else {
            $user_id = null;
            $user_name = 'Anonymous';
        }

        $review = new ProductReview;
        // $review->review = $request->review;
        $review->desc = $request->desc;
        // $review->attachment = $attachment;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $ext = $file->getClientOriginalExtension();
            $filename = time() . '.' . $ext;
            $file->move('reviews/', $filename);
            $attachment = url('reviews', $filename);
        }
        $review->user_id = $user_id;
        $review->cus_name = $user_name;
        $review->status = 0;
        if ($review->save()) {
            return redirect()->back()->with('success', 'Review Submit Successfully !');
        } else {
            return $this->error('', 'Sorry! 😔 There is an error please try later', 0);
        }
    }

    public function add_to_cart(Request $request)
    {
        //return $request->all();
        $cartItems = [];

        $totalQty = 0;

        foreach ($request->product_id ?? [] as $key => $product_id) {
            $product = Product::with('reseller_setting')->find($product_id);
            array_push($cartItems, (object)[
                'product_id' => (int)$product_id,
                'product_price' => (float)$product->price,
                'reseller_profit_price' => (float)$product->reseller_product_profit,
                'name' => $product->name,
                'thumbnail' => $product->thumbnail,
                'qty' => (int)$request->qty[$key],
                'reseller_product_profit' => (float)$product->reseller_product_profit * (int)$request->qty[$key]
            ]);
            $totalQty += (int)$request->qty[$key];
        }

        $total_amount = (float)$request->total_amount;

        Session::put('cartItems', $cartItems);

        $cities = City::orderBy('id', 'desc')->get();

        $delivery_charges = 0;

        $charge = Charge::where('suit', $totalQty)->first();

        if ($charge) {
            $delivery_charges = $charge->charges;
        } else {
            $delivery_charges = 0;
        }

        $total_amount = $total_amount + $delivery_charges;
        return view('themes.wao.Orders.order_place', compact('cartItems', 'total_amount', 'cities', 'delivery_charges'));
    }
    public function removeCartItem(Request $request)
    {
        $cartItems = Session::get('cartItems');
        unset($cartItems[$request->remove_item]);

        // Re-index the array if needed
        $cartItems = array_values($cartItems);
        Session::forget('cartItems');
        Session::put('cartItems', $cartItems);
        $totalQty = 0;
        $total_amount = 0;
        $totalResellerProfit = 0;
        foreach (Session::get('cartItems') ?? [] as $key => $cartItem) {
            $totalQty += $cartItem->qty;
            $total_amount += ($cartItem->qty * $cartItem->product_price);
            $totalResellerProfit += $cartItem->reseller_product_profit;
        }

        $delivery_charges = 0;
        $charge = Charge::where('suit', $totalQty)->first();
        if ($charge) {
            $delivery_charges = $charge->charges;
        } else {
            $delivery_charges = 0;
        }
        $total_amount = $total_amount + $delivery_charges;

        return response()->json([
            'delivery_charges' => $delivery_charges,
            'total_amount' => $total_amount,
            'totalResellerProfit' => $totalResellerProfit,
        ]);
    }

    public function removeAllCartItem()
    {
        Session::forget('cartItems');
        return 1;
    }


    // add ajax function here
    public function user_info_by_whatsapp(Request $request)
    {
        $admin_id = Domain::admin('id');
        $user = User::where('whatsapp', $request->whatsapp)->where('admin_id', $admin_id)->first();
        if ($user != null) {
            $order = Order::select('city_id', 'address')->where('user_id', $user->id)->latest()->first();
            $user->setAttribute('order', $order);
        }
        return $this->success($user, '', 1);
    }

    public function SubmitNote(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|numeric|exists:orders,id',
            'note' => 'required',
        ]);

        if (!$validator->passes()) {
            return $this->error($validator->errors()->first(), 'Validation Error', 422);
        }

        Note::updateorcreate([
            'order_id' => $request->order_id,
        ], [
            'order_id' => $request->order_id,
            'note' => $request->note,
        ]);
        // return redirect('/menu')->with('success','Note Submit Successfully !');
    }
    public function cancel_order(Request $request)
    {
        $order = Order::where('id', $request->order_id)->first();

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

    public function update_delivery_address_order(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'phone' => 'required',
            'city_id' => 'required',
            'address' => 'required',
        ]);

        // check validation
        if (!$validator->passes()) {
            return $this->error($validator->errors()->all(), $validator->errors()->first(), 0, 422);
        }
        $order = Order::where('id', $request->order_id)->first();
        $order->name = $request->name;
        $order->phone = $request->phone;
        $order->city_id = $request->city_id;
        $order->address = $request->address;
        $order->save();
        return $this->success($order, 'Delivery Detail updated succssfully 😊', 1);
    }


    public function web_login()
    {
        // return 1;
        if (auth()->guard('admin')->check()) {
            return redirect()->route('product.NewArrivals', ['showUpdate' => 1]);
        }

        return view('themes.wao.web_login');
    }

    public function web_login_process(Request $request)
    {
        $request->validate([
            'email' => 'required|exists:admins,email',
            'password' => 'required|min:5|max:20',
        ]);

        // Check if admin exists
        $admin_id = Domain::admin('id');
        $admin = Admin::where('id', $admin_id)->where('email', $request->email)->first();
        if (!$admin) {
            return redirect()->route('web_login')->with('failed', 'Invalid Credientials...');
        }

        // Check if the admin is inactive
        if ($admin->status === 2) {
            return redirect()->route('web_login')->with('failed', 'Your status is In-active...');
        }

        // if ($admin->id != Domain::admin('id')) {
        //     return redirect()->route('web_login')->with('failed', 'Your status is In-active...');
        // }

        $creds = $request->only('email', 'password');
        if (Auth::guard('admin')->attempt($creds)) {
            return redirect()->route('product.NewArrivals', ['showUpdate' => 1]);
        }

        return redirect()->route('web_login')->with('failed', 'Invalid Credientials...');
    }

    public function update_seller_product_profit(Request $request)
    {
        $admin_id = Domain::admin('id');
        $ResellerSetting = ResellerSetting::where('prod_id', $request->product_id)->where('admin_id', $admin_id)->first();
        if (!$ResellerSetting) {
            $now = Carbon::now()->format('Y-m-d H:i:s');
            $product = Product::find($request->product_id);
            $ResellerSetting = new ResellerSetting();
            $ResellerSetting->prod_id = $product->id;
            $ResellerSetting->admin_id = $admin_id;
            $ResellerSetting->profit = $product->profit;
            // $ResellerSetting->is_specific_profit = 1;
            // $ResellerSetting->product_upload_status = 'published';
            $ResellerSetting->price = $product->price;
            // $ResellerSetting->reseller_product_profit = $request->seller_profit;
            $ResellerSetting->for_notification = 1;
            $ResellerSetting->for_app_reseller = null;
            $ResellerSetting->created_at = $now;
            $ResellerSetting->updated_at = $now;
        }

        $ResellerSetting->product_upload_status = 'published';
        $ResellerSetting->reseller_product_profit = $request->seller_profit;
        $ResellerSetting->save();

        return response()->json([
            'status' => 1,
            'message' => 'Seller profit updated successfully',
            'data' => null,
        ]);
    }


    //logout
    public function web_logout(Request $request)
    {
        Auth::guard('admin')->logout();
        // return redirect('/');

        return redirect('menu')->with('success', 'Admin logout Successfully');
    }

    public function user_logout(Request $request)
    {
        if (auth()->guard('admin')->check()) {
            auth()->guard('admin')->logout();
        }
        auth()->guard('web')->logout();
        if (auth('sanctum')->check()) {
            auth('sanctum')->user()->tokens()->delete();
        }
        return redirect('menu')->with('success', 'Logout Successfully');
    }
}
