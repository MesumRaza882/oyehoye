<?php

namespace App\Http\Controllers\Api\V2\Order;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Product, Order, Order_item, Cart, User, Charge, RestAddress};
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use Exception;
use DB;
use Validator;
use Laravel\Sanctum\PersonalAccessToken;

class CartController extends Controller
{
    // add to cart
    public function add_to_cart(Request $req)
    {
        $admin_id = $req->admin_id ? $req->admin_id : 1;

        $validator = Validator::make($req->all(), [
            'prod_id' => 'required|numeric|exists:products,id',
            'quantity' => 'required|numeric',
        ]);

        if (!$validator->passes()) {
            return $this->error($validator->errors()->first(), 'Validation Error', 422);
        }
        // 1.exceed_limit Quantity Validation 
        $product = Product::find($req->prod_id);
        if ($req->quantity > $product->exceed_limit && $product->exceed_limit != Null) {
            return $this->error([], 'Exceed Limit Sorry! you reached maximum limit for this product!', 0);
        }
        //2. if Cart quantity increase than Real Product (Quantity + markeet item qty)
        if ($req->quantity > ($product->soldItem + $product->markeetItem)) {
            return $this->error(['item-quantity' => ($product->soldItem + $product->markeetItem)], 'Required Quantity not available', 0);
        }
        $total = 0;
        $user_id = auth('sanctum')->user()->id;
        // Add or Update Cart Model
        $checkCart = Cart::where('user_id', $user_id)->where('admin_id', $admin_id)->where('prod_id', $req->prod_id)->first();
        // if already add to cart this product 
        if ($checkCart) {

            $items = Cart::where('user_id', $user_id)->where('admin_id', $admin_id)->get();
            $orderscount = $items->count();

            // if quantity less than 1 means O (delete cart item) else update
            if ($req->quantity < 1) {
                $checkCart->delete();
            } else {

                $checkCart->quantity = $req->quantity;
                $checkCart->save();
            }
            // count total cart items and amount of cart items products
            $items = Cart::where('user_id', $user_id)->where('admin_id', $admin_id)->get();
            $orderscount = Cart::where('user_id', $user_id)->where('admin_id', $admin_id)->count();
            foreach ($items as $item) {
                $total += $item->product->price * $item->quantity;
            }
            return $this->success([
                'cart_item' => $checkCart,
                'total' => $total,
                'cart_Items_count' => $orderscount,
                'item_Quantity' => $product->soldItem + $product->markeetItem,
            ], 'Product Cart Quantity Update', 2);
        }
        // New Cart Item 
        else {
            // if quantity less than 1 means O (delete cart item) else update
            if ($req->quantity < 1) {
                return $this->error([], 'Quantity Must be greater than 0', 0);
            }
            $cart = new Cart();
            $cart->user_id = $user_id;
            $cart->admin_id = $admin_id;
            $cart->prod_id = $req->prod_id;
            $cart->quantity = $req->quantity;
            $newCart = $cart->save();
            // count item total 
            $items = Cart::where('admin_id', $admin_id)->where('user_id', $user_id)->get();
            $orderscount = Cart::where('admin_id', $admin_id)->where('user_id', $user_id)->count();
            foreach ($items as $item) {
                $total += $item->product->price * $item->quantity;
            }
            return $this->success([
                'cart_item' => $cart,
                'total' => $total,
                'cart_Items_count' => $orderscount,
                'item_Quantity' => $product->soldItem + $product->markeetItem,
            ], 'Product Cart Quantity Update', 2);
        }
    }

    //View Cart items
    public function view_cart(Request $req)
    {
        $admin_id = $req->admin_id ? $req->admin_id : 1;

        $user_id = auth('sanctum')->user()->id;
        // get all cart items and check  item sold out  or solded by admin
        $items_filter = Cart::with('product')
            ->where('user_id', $user_id)
            ->where('admin_id', $admin_id)
            ->get();

        if ($items_filter->count() > 0) {
            $total = 0;
            $discount = 0;

            foreach ($items_filter as $item) {
                /** check if product item soldOut/more than cart item quantity  **/
                /** if cart product accurate then count total bill  **/
                $checkProQty = ($item->product->soldItem + $item->product->markeetItem);
                if ($item->product->soldstatus == 1 || $item->quantity > $checkProQty) {
                    $item->delete();
                } else {
                    $total += $item->product->price * $item->quantity;
                    $discount += $item->product->discount * $item->quantity;
                }
            }

            // after delete cart items now check total details of cart items
            $items = Cart::select(['id', 'quantity', 'prod_id'])
                ->with('product:id,name,price,article,discount,category_id,is_dc_free,soldAdm,markeetItem,thumbnail')
                ->where('user_id', $user_id)
                ->where('admin_id', $admin_id);
            if ($items->count() > 0) {
                $cart_item = $items->get();
                $quan_pro =  $items->Wherehas('product', function ($q) {
                    $q->where('is_dc_free', 0);
                })->sum('quantity');
                $orderscount = $cart_item->count();

                // charges
                $charge = Charge::where('suit', $quan_pro)->first();
                if ($charge) {
                    $charge = $charge->charges;
                } else {
                    $charge = 0;
                }

                $user_detail = User::select(['id', 'name', 'city_id', 'phone', 'courier_phone', 'whatsapp', 'address'])
                    ->find($user_id);

                //grand total 
                $grandTot = ($charge + $total) - $discount;

                return $this->success([
                    'cart_items_count' => $orderscount,
                    'total' => $total,
                    'charges' => $charge,
                    'discount' => $discount,
                    'grandTotal' => $grandTot,
                    'cart_items' => $cart_item,
                    'user_detail' => $user_detail,
                ], 'Cart-Items Detail', 2);
            }
            return $this->error([], 'Not Cart Items are availble', 0);
        }

        // No Cart Items Available
        return $this->error([], 'Not Cart Items are availble', 0);
    }

    //remove cart item
    public function remove_single_cart_item(Request $req)
    {
        $admin_id = $req->admin_id ? $req->admin_id : 1;

        $user = auth('sanctum')->user();
        $cartDelete = Cart::where('prod_id', $req->prod_id)->where('admin_id', $admin_id);
        if ($user) {
            $user_id = $user->id;
            $device_id = null;
            // delete item in cart
            $cartDelete->where('user_id', $user_id)->delete();
        } else {
            $user_id = null;
            $device_id = $req->device_id;
            // delete item in cart
            $cartDelete->where('device_id', $device_id)->delete();
        }


        if ($cartDelete) {
            return $this->success([], 'Cart Item Remove Successfully', 1);
        }

        return $this->error('Item not found or already remove', 'Item not found or already remove', 422);
    }

    public function remove_all_cart_items(Request $req)
    {
        $admin_id = $req->admin_id ? $req->admin_id : 1;
        $user = auth('sanctum')->user();
        // \Log::info($user);
        if ($user) {
            $user_id = $user->id;
            $device_id = null;
            // delete item in cart
            $carts = Cart::where('admin_id', $admin_id)->where('user_id', $user_id)->delete();
            // \Log::info($carts);

        } else {
            $user_id = null;
            $device_id = $req->device_id;
            // delete item in cart
            Cart::where('device_id', $device_id)->delete();
        }

        return $this->success([], 'Cart items Deleted Successfully', 2);
    }

    public function sync_cart(Request $req)
    {
        $admin_id = $req->admin_id ? $req->admin_id : 1;

        // validate data
        // item nullbale array with prod_id, quantity

        $user = auth('sanctum')->user();
        if ($user) {
            $user_id = $user->id;
            $device_id = null;
            // delete item in cart
            Cart::where('user_id', $user_id)->where('admin_id', $admin_id)->delete();
        } else {
            $user_id = null;
            $device_id = $req->device_id;
            // delete item in cart
            Cart::where('device_id', $device_id)->where('admin_id', $admin_id)->delete();
        }

        $cart = [];
        $now = Carbon::now()->format('Y-m-d H:i:s');

        if ($req->item != null) {
            foreach ($req->item as $index => $item) {
                if (isset($item['reseller_profit']) && (int)$item['reseller_profit'] > 0) {
                    $reseller_profit = $item['reseller_profit'];
                } else {
                    $reseller_profit = 0;
                }
                $cart[] = [
                    'user_id' => $user_id,
                    'admin_id' => $admin_id,
                    'device_id' => $device_id,
                    'prod_id' => $item['prod_id'],
                    'quantity' => $item['quantity'],
                    'reseller_profit' => 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        // return $cart;
        $carts = Cart::insert($cart);

        if ($req->get_product == 0) {
            return $this->success([], 'Cart has been synce', 1);
        }

        $price_colum_cast = 'products.price';
        if($req->reseller_price == 'true2'){
            $price_colum_cast = 'products.reseller_price';
        }

        // return Cart::where('carts.user_id',$user_id)->get();
        $items_filter = DB::table('carts')->leftJoin('products', function ($join) {
            $join->on('carts.prod_id', '=', 'products.id');
        })
            // ->selectRaw('CAST(products.id) as INT) as id')
            ->select([
                'reseller_profit',
                'products.id',
                DB::raw("carts.quantity as quantity_in_cart"), /* quantity_in_cart can not be changed */
                'products.name',
                DB::raw("CAST($price_colum_cast AS CHAR) as price"),
                'products.discount',
                'products.thumbnail',
                'products.is_dc_free',
                // 'products.soldItem',
                DB::raw('CAST((products.soldItem + products.markeetItem) AS CHAR) as soldItem'),
                'products.markeetItem',

            ])
            ->where('products.soldstatus', '=', '0');
        // ->where('carts.quantity', '<=', 'products.soldItem')
        if ($user_id)
            $items_filter = $items_filter->where('carts.user_id', $user_id);
        if ($device_id)
            $items_filter = $items_filter->where('carts.device_id', $device_id);

        $items_filter = $items_filter->get()->toArray();

        $total = 0;
        $discount = 0;
        $qty = 0;
        $charge = 0;
        $orderscount = 0;

        $msg = '';
        foreach ($items_filter as $index => $item) {
            $checkQty = ($item->soldItem + $item->markeetItem);
            if ($item->quantity_in_cart <= $checkQty) {
                $total += $item->price * $item->quantity_in_cart;
                $discount += $item->discount * $item->quantity_in_cart;

                if ((int) $item->is_dc_free == 0)
                    $qty += $item->quantity_in_cart;

                ++$orderscount;
            } else {
                // unset($items_filter[$index]);
                $msg = 'Some items has been out of stock';
            }
        }

        if ($qty > 0) {
            $charge = Charge::where('suit', '>=', $qty)->first();
            $charge = $charge ? $charge->charges : 0;
        }

        $grandTot = ($charge + $total) - $discount;
        // $user_detail = User::select(['id','name','city_id','phone','courier_phone','whatsapp','address'])->find($user_id);

        return $this->success([
            'cart_items_count' => $orderscount,
            'total'         => $total,
            'charges'       => $charge,
            'discount'      => $discount,
            'grandTotal'    => $grandTot,
            'cart_items'    => $items_filter,
            'user_detail'   => [], //$user_detail,
        ], $msg, 1);
    }

    public function update_cart(Request $req, $prod_id)
    {
        $admin_id = $req->admin_id ? $req->admin_id : 1;


        $cart = Cart::where('admin_id', $admin_id)->where('prod_id', $prod_id)->first();
        $cart->reseller_profit = $req->reseller_profit;
        $cart->save();

        return $this->success([], 'Profit updated Successfully', 1);
    }
}
