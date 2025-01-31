<?php

namespace App\Http\Controllers\Admin\Seller;

use Exception;
use Carbon\Carbon;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use App\Models\{User, City, Order, Order_item, ResellerSetting, Product};


class MainController extends Controller
{
    public function getUsers(Request $req)
    {
        $this->authorize('view_users');
        $paginate_record = $req->records ? $req->records : 50;
        $startdate = $req->fromDate;
        $enddate =  $req->toDate;
        $status = $req->status === '1' ? '0' : $req->status;

        if ($req->vcf == 1) {
            return $this->VcfDownload($req);
        }

        // Generate a unique cache key based on request parameters
        $cacheKey = 'viewUsers_' . md5(json_encode($req->all())) . '_page_' . $req->page;
        // Cache results for hour
        $users = Cache::remember($cacheKey, 1 * 1, function () use ($req, $paginate_record, $startdate, $enddate, $status) {
            $records = User::select(['id', 'city_id', 'city_name', 'whatsapp', 'status', 'created_at', 'is_active_row', 'name', 'is_reseller'])
                // ->withCount([
                //     'order as real_orders' => function ($query) {
                //         $query->where('is_blocked_customer_order', 0);
                //     }
                // ])
                ->distinct('whatsapp')
                ->with('city:id,name')
                ->when($status, function ($query) use ($status) {
                    $query->where('status', 'Like', "%{$status}%");
                })
                ->when(auth()->user()->id, function ($query) use ($req) {
                    // Apply this only if admin_id is present in the request
                    $query->whereHas('order', function ($q) use ($req) {
                        $q->where(function ($subQuery) use ($req) {
                            if ($req->orderType === 'general') {
                                // When orderType is 'general', only check 'wao_seller_id'
                                $subQuery->where('wao_seller_id', auth()->user()->id);
                            } elseif ($req->orderType === 'app') {
                                // When orderType is 'app', check where 'wao_seller_id' is NULL and 'admin_id' matches
                                $subQuery->whereNull('wao_seller_id')
                                    ->where('admin_id', auth()->user()->id);
                            } else {
                                // Default behavior when orderType is empty, fetch both "general" and "app"
                                $subQuery->where(function ($orQuery) use ($req) {
                                    $orQuery->where('wao_seller_id', auth()->user()->id)  // General condition
                                        ->orWhere(function ($orSubQuery) use ($req) {
                                            $orSubQuery->whereNull('wao_seller_id')     // App condition
                                                ->where('admin_id', auth()->user()->id);
                                        });
                                });
                            }
                        });
                    });
                })
                ->filterByDate($startdate, $enddate)
                ->where(function ($q) use ($req) {
                    $q->when($req->search_input, function ($q) use ($req) {
                        $q->where('name', 'Like', "%{$req->search_input}%")
                            ->orWhere('whatsapp', 'Like', "%{$req->search_input}%");
                    });
                })
                ->when($req->city, function ($query) use ($req) {
                    $query->whereHas('city', function ($q) use ($req) {
                        $q->where('id', 'Like', "%{$req->city}%");
                    });
                })
                ->when($req->user_type === '1', function ($query) {
                    $query->whereHas('order', function ($query) {
                        $query->where('is_blocked_customer_order', '=', 0);
                    });
                })
                ->whereFilterReseller($req->is_reseller)
                ->orderByRaw("id DESC");

            return $records->paginate($paginate_record);
        });

        // Cache the total count of users separately, if needed
        $total_users_key = 'total_users_' . md5(json_encode($req->all()));
        $total_users = Cache::remember($total_users_key, 1, function () use ($users) {
            return $users->total(); // Use the `total()` method from pagination result
        });

        // Cache cities
        $cities = Cache::remember('cities', 1, function () {
            return City::get(['id', 'name']);
        });


        return view('reseller.users', compact('users', 'total_users', 'cities'));
    }

    public function VcfDownload($req)
    {
        $startdate = $req->fromDate;
        $enddate =  $req->toDate;
        $status = $req->status === '1' ? '0' : $req->status;
        // Fetch all users' WhatsApp numbers without pagination for VCF export
        $contacts = User::select('name', 'whatsapp')
            ->distinct('whatsapp')
            ->when($status, function ($query) use ($status) {
                $query->where('status', 'Like', "%{$status}%");
            })
            ->when(auth()->user()->id, function ($query) use ($req) {
                // Apply filters as in your original query
                $query->whereHas('order', function ($q) use ($req) {
                    $q->where(function ($subQuery) use ($req) {
                        if ($req->orderType === 'general') {
                            $subQuery->where('wao_seller_id', auth()->user()->id);
                        } elseif ($req->orderType === 'app') {
                            $subQuery->whereNull('wao_seller_id')
                                ->where('admin_id', auth()->user()->id);
                        } else {
                            $subQuery->where(function ($orQuery) use ($req) {
                                $orQuery->where('wao_seller_id', auth()->user()->id)
                                    ->orWhere(function ($orSubQuery) use ($req) {
                                        $orSubQuery->whereNull('wao_seller_id')
                                            ->where('admin_id', auth()->user()->id);
                                    });
                            });
                        }
                    });
                });
            })
            ->filterByDate($startdate, $enddate)
            ->whereFilterReseller($req->is_reseller)
            ->get();

        // Prepare VCF data
        $vcfData = '';
        foreach ($contacts as $contact) {
            $vcfData .= "BEGIN:VCARD\n";
            $vcfData .= "VERSION:3.0\n";
            $vcfData .= "FN:{$contact->name}\n";
            $vcfData .= "TEL;TYPE=CELL:{$contact->whatsapp}\n";
            $vcfData .= "END:VCARD\n";
        }

        // Set headers for VCF download
        $fileName = 'user_contacts_' . date('Ymd_His') . '.vcf';
        return response($vcfData)
            ->header('Content-Type', 'text/vcard')
            ->header('Content-Disposition', "attachment; filename={$fileName}");
    }


    //edit user details page
    public function editUser($id)
    {
        $user = User::with(['city:id,name'])
            ->withCount(['order' => function ($query) {
                // type 2 means website 1 for app
                $query->where('admin_id', auth()->user()->id)->where('type', 2);
            }])
            ->where('id', $id)->firstorfail();

        $orders = Order::where('user_id', $id)
            ->where('admin_id', auth()->user()->id)
            ->where('type', 2)
            ->take(10)->orderBy('id', 'desc')->withcount('orderitems')->get();

        // active row
        if ($already_active = User::where('id', '!=', $user->id)->where('is_active_row', 1)->first()) {
            $already_active->is_active_row = 0;
            $already_active->save();
        }
        $user->is_active_row = 1;
        $user->save();

        return view('reseller.userEdit', compact('user', 'orders'));
    }

    //  update User password/status
    public function updateUser(Request $req)
    {
        $user = User::find($req->user_id);
        $user_same_whatsapp = User::where('whatsapp', $user->whatsapp)
            ->where('admin_id', '=', auth()->user()->id)->get();
        $status = $req->status == 2 ? 0 : 1;

        $user_same_whatsapp->each(function ($user) use ($status, $req) {
            $user->update([
                'status' => $status,
                'password' => $req->password ? Hash::make($req->password) : $user->password,
            ]);
        });

        return redirect()->route('waoseller.getUsers')->with('message', 'Profile Updated Successfully');
        // session()->put('message', 'User with Same WhatsNumber Update Successfully');
        // session()->put('end_time',  Carbon::now()->addSecond(3));
        // echo '<script type="text/javascript">', 'history.go(-2);', '</script>';
    }

    // web orders
    public function getWebOrders(Request $req)
    {
        // if partner wants to get their team orders
        $is_partner_login = $req->is_partner;

        $make_ids_string =  strval($req->filterOrderIds);
        $ids =  explode(",", $make_ids_string);
        $paginate_record = $req->records ? $req->records : 50;
        $status = $req->input('status');
        $returnStatus = $req->input('is_returned_order');
        $orderType = $req->type;

        $orderStatus = in_array($status, ['PENDING', 'DISPATCHED', 'DELIVERED', 'ON-THE-WAY', 'RETURNED', 'CANCEL', 'Team Review your Order']) ? $status : '';
        $orderHistoryStatus = in_array($status, ['PENDING', 'DISPATCHED', 'DELIVERED', 'ON-THE-WAY', 'RETURNED', 'CANCEL']) ? '' : $status;

        $startdate = $req->fromDate;
        $enddate =  $req->toDate;
        $records = Order::select('*', DB::raw('(CASE WHEN status = "Unbooked" THEN "DISPATCHED" WHEN status = "booked" THEN "DISPATCHED" ELSE status END) AS status'))
            ->with(['userdetail:id,whatsapp,status', 'citydetail'])
            // track order type filter
            ->when($req->tracking_order_type, function ($query) use ($req) {
                return $query->WhereIf('tracking_order_type', 'Like', "%{$req->tracking_order_type}%");
            })
            ->WhereIf('status', 'Like', "%{$orderStatus}%")
            ->filterByDate($startdate, $enddate)
            ->filterBlockOrders($req->blocked_orders)
            ->whereHas('userdetail', function ($query) use ($req) {
                $query->WhereIf('whatsapp', 'Like', "%{$req->whatsapp}%");
            })
            ->where(function ($q) use ($req) {
                $q->WhereIf('name', 'Like', "%{$req->search_input}%")
                    ->OrWhereIf('id', '=', $req->search_input)
                    ->OrWhereIf('city', 'Like', "%{$req->search_input}%")
                    ->OrWhereIf('phone', 'Like', "%{$req->search_input}%")
                    ->OrWhereIf('address', 'Like', "%{$req->search_input}%");
            })
            // for order history filter
            ->when($orderHistoryStatus, function ($query) use ($orderHistoryStatus) {
                return $query->filterByOrderStatus($orderHistoryStatus);
            })
            // for return order status
            ->when($returnStatus, function ($query) use ($returnStatus) {
                return $query->WhereIf('is_returned_order', '=', $returnStatus);
            })
            // for order type filter (website + app orders)
            ->when($orderType, function ($query) use ($orderType) {
                return $query->where('type', '=', $orderType);
            })
            ->whereNotNull('user_id')
            ->when($orderType, function ($query) use ($orderType) {
                return $query->where('type', '=', $orderType);
            })
            ->where('admin_id', auth()->user()->id)
            ->withcount('orderitems')
            ->orderByRaw("id DESC, status DESC");

        if ($req->filterOrderIds) {
            $records->whereIn('id', $ids);
        }

        // if partner wants team orders
        if ($is_partner_login) {
            // Get orders app or where wao_seller_id is null or is_warehouseTeam_order is 1
            $records->where(function ($query) {
                $query->whereNull('wao_seller_id')
                    ->orWhere('is_warehouseTeam_order', '=', 1);
            });
        } else {
            // just app orders
            $records->where('wao_seller_id', null);;
        }

        $total_records = $records->count();
        $orders = $records->paginate($paginate_record);
        $count_order_items = Order_item::wherein('order_id', $orders->pluck('id'))->sum('qty');
        return view('reseller.webOrder.webOrders', compact('orders', 'count_order_items', 'total_records'));
    }

    //single order details page
    public function editOrder($id, Request $request)
    {
        $isPartner = $request->input('is_partner');
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
            ->where('admin_id', auth()->user()->id)
            ->where('type', 2)
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
        if ($isPartner) {
            return view('admin.Order.singleOrder', compact('order', 'previous_orders', 'traxCities', 'mnpCities', 'postExCities', 'currentDayOrder', 'purchaseTotal', 'perProductCharge', 'perOrderCharge'));
        }
        return view('reseller.webOrder.webSinglOrder', compact('order', 'previous_orders', 'traxCities', 'mnpCities', 'postExCities', 'currentDayOrder', 'purchaseTotal', 'perProductCharge', 'perOrderCharge'));
    }

    // Order status Update
    public function updateWebOrder(Request $req, $id)
    {
        $order = Order::findorFail($id);

        $order->amount = $req->amount;
        $order->charges = $req->charges;
        $order->order_discount = $req->order_discount;
        $order->grandProfit = $req->grandProfit;
        $order->grandTotal = $req->grandTotal;
        $order->address = $req->address;

        // address saved as user's address
        $order->userdetail->update([
            'address' => $req->address,
        ]);

        // Cancel order and hold the same qunatity (not inc to item)
        if ($req->status == 'CANCELHOLD') {
            $order->status = 'CANCEL';
            Order_item::where('order_id', $order->id)->where('order_status', '!=', 'CANCEL')->update([
                'order_status' => 'CANCELHOLD',
            ]);
        }

        // DISPATCHED (NOT COURIER WORK)
        if ($req->status == 'DISPATCHED') {
            $order->status = 'DISPATCHED';
            Order_item::where('order_id', $order->id)->update([
                'order_status' => 'DISPATCHED',
            ]);
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
        session()->put('message', 'Order Updated Successfully');
        session()->put('end_time',  Carbon::now()->addSecond(3));
        echo '<script type="text/javascript">', 'history.go(-2);', '</script>';
    }
}
