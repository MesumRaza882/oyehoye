<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\Order_item;
use App\Models\Cart;
use App\Models\User;
use App\Models\Note;
use App\Models\Charge;
use App\Models\RestAddress;
use Illuminate\Support\Facades\File;
use Validator;
use Carbon\Carbon;
Use Exception;
use DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    //save cart
    public function addcart(Request $req)
        {
        $validator = Validator::make($req->all(), [
                'prod_id'=>'required|numeric|exists:products,id',
                'user_id'=>'required|numeric|exists:users,id',
                'quantity' => 'required|numeric',
        ]);
            
        if (!$validator->passes()) 
        {
                return response()->json([
                'statusCode'=>500,
            //   'message'=>'Valiadtion Error',
                'message'=>$validator->errors()->first(),
                'error'=>$validator->errors()->toArray()]);
        }
        else
        {
            
            // 1.exceed_limit Quantity Validation 
            $product = Product::find($req->prod_id);
            if($req->quantity > $product->exceed_limit && $product->exceed_limit != Null )
            {
                    return response()->json([
                    'statusCode'=>500,
                    'message'=>' Exceed Limit Sorry! you reached maximum limit for this product!',
                ]);
            }
                //2. if Cart quantity increase than Real Product Quantity
            if($req->quantity > $product->soldItem)
            {
                return response()->json([
                    'statusCode'=>500,
                    'message'=>'Required Quantity not available',
                    'item-Quantity'=>$product->soldItem,
                ]);
            }

            $total = 0;

            // Add or Update Cart Model
            $checkCart = Cart::where('user_id',$req->user_id)->where('prod_id',$req->prod_id)->first();
            // if already add to cart this product 
            if($checkCart){

                    $items = Cart::where('user_id',$req->user_id)->get();  
                    $orderscount = $items->count();
                
                // if quantity less than 1 means O (delete cart item) else update
                if($req->quantity < 1)
                {
                    $checkCart->delete();
                }
                else{

                    $checkCart->quantity = $req->quantity;
                    $checkCart->save();
                }
                // count total cart items and amount of cart items products
                $items = Cart::where('user_id',$req->user_id)->get();   
                $orderscount = Cart::where('user_id',$req->user_id)->count();
                foreach($items as $item){
                    $total += $item->product->price*$item->quantity;
                }
                return response()->json([
                    'statusCode'=>200,
                    'message'=>'Product Cart Quantity Update',
                    'data'=>[
                        'cartitem' => $checkCart,
                        'total' => $total,
                        'orderItems'=>$orderscount,
                        'item-Quantity'=>$product->soldItem,
                    ]
                ]);
            }
            // New Cart Item 
            else{
                // if quantity less than 1 means O (delete cart item) else update
                if($req->quantity < 1)
                {
                    return response()->json([
                        'statusCode'=>200,
                        'message'=>'Quantity Must be greater than 0',
                    ]);
                }
                $cart = new Cart();
                $cart->user_id = $req->user_id;
                $cart->prod_id = $req->prod_id;
                $cart->quantity = $req->quantity;
                $newCart = $cart->save();
                // count item total 
                $items = Cart::where('user_id',$req->user_id)->get();   
                $orderscount = Cart::where('user_id',$req->user_id)->count();
                foreach($items as $item){
                    $total += $item->product->price*$item->quantity;
                }
                return response()->json([
                    'statusCode'=>200,
                    'message'=>'Product added to Cart successfully!',
                    'data'=>[
                        'cartitem' => $cart,
                        'total' => $total,
                        'orderItems'=>$orderscount,
                        'item-Quantity'=>$product->soldItem,
                    ]
                ]);
            }
            
        }
    }
      
      
    //   Real View Cart items
    public function viewcart(Request $req)
    {
        \Log::info('View Cart Real working');
        $items_filter = Cart::with('product')
        ->where('user_id',$req->user_id)->get();

        if($items_filter->count() > 0 ){
            $total = 0;
            $discount = 0;
            $profit = 0;
            $charge = 0;
            $message = 'Cart Items';

            foreach($items_filter as $item){
                /** check if product item soldOut/more than cart item quantity  **/
                /** if cart product accurate then count total bill  **/
                if($item->product->soldstatus == 1 ||  $item->quantity > $item->product->soldItem )
                {
                    $item->delete();
                }
                else{
                    $total += $item->product->price*$item->quantity;
                    $discount += $item->product->discount*$item->quantity;
                    $profit += $item->product->profit*$item->quantity;
                }
            }

            // after dlete cart items now check total details of cart items
           $items = Cart::with('product')
                ->where('user_id',$req->user_id);
            if($items->count() > 0)
            {
               $cart_item = $items->get();

                $quan_pro =  $items->Wherehas('product',function($q){ $q->where('is_dc_free',0);}) ->sum('quantity');
                $orderscount = $cart_item->count();
                // charges
                
                $charge = Charge::where('suit',$quan_pro)->first()->charges;

                $user_detail = User::find($req->user_id);
                //grand total 
                $grandTot = ($charge + $total) - $discount;
                
                // response
                return response()->json([
                    'statusCode'=>200,
                    'message'=>'succes',
                    'data'=>[
                        'cardOrders'=>$orderscount,
                        'total'=>$total,
                        'charges'=>$charge,
                        'discount'=>$discount,
                        'grandTotal'=>$grandTot,
                        'profitOfOrder'=>$profit,
                        'message'=>$message,
                        'items'=>$cart_item,
                        'user_det'=>$user_detail,
                    ],
                ]);
            }
            return response()->json([
                'statusCode'=>404,
                'message'=>'Not Cart Items are availble for this user',
            ]);
        }

        // No Cart Items Available
        else{
            return response()->json([
                'statusCode'=>404,
                'message'=>'Not Cart Items are availble for this user',
            ]);
        }
    }


    //remove cart item
    public function removecart(Request $req){
        $validator = Validator::make($req->all(), [
            'user_id'=>'required|numeric|exists:carts,user_id',
            'prod_id'=>'required|numeric|exists:carts,prod_id',
        ]);
        if (!$validator->passes()) 
         {
            return response()->json([
            'statusCode'=>500,
            'message'=>'Valiadtion Error',
            'error'=>$validator->errors()->toArray()]);
        }
        else
        {
            $check = Cart::WHERE('user_id',$req->user_id)->WHERE('prod_id',$req->prod_id)->first();
            if($check){
                $delcart = Cart::find($check->id)->delete();
                return response()->json([
                    'statusCode'=>200,
                    'message'=>'Remove Cart Item',        
                ]);
            }
            return response()->json([
                'statusCode'=>400,
                'message'=>'Not Found',
    
            ]);
    
        }
        
        
    }

    // Order Place Real
    public function orderPlace(Request $req)
    {
        \Log::info('Order Place');
        $request->merge(['new_phone' => str_replace(' ', '', $req->phone)]);
        $validator = Validator::make($req->all(), [
            'user_id'=>'required',
            'name'=>'required|string',
            'new_phone'=>'required|digits:11',
            'city'=>'required|string',
            'address'=>'required',
        ]);
        
        if (!$validator->passes()) 
        {
            return response()->json([
            'statusCode'=>500,
            'message'=>'Valiadtion Error',
            'error'=>$validator->errors()->toArray()]);
        }
        DB::beginTransaction();
        try{

            // Restrict Address Validation
            $address = RestAddress::get('address');
            if(count($address)>0)
            {
                foreach($address as $add)
                {
                    $myString = $req->address;
                    if($contains = Str::contains($myString, $add->address))
                    {
                        return response()->json([
                            'statusCode'=>500,
                            'message'=>'The Address is Restricted',
                            'restricted_addressess'=>$address,
                        ]);
                    }
                    
                }
                
            }
            // end restrict validation

            
            // end block restriction
            
            $cart_items = Cart::with('product')->where('user_id',$req->user_id)->get();
            $my_order_id = Order::latest()->first()->id + 1;
            if($pending_order = Order::where(['status'=>'PENDING','user_id'=>$req->user_id])->first())
            {
                $my_order_id = $pending_order->id;
            }

            $insert_order_items = [];
            
            if($cart_items->count() > 0 ){
                $total = 0;  $discount = 0;  $profit = 0;  $gtotal = 0;  $gprofit = 0; $is_blocked_customer_order = 0;

                // if admin block customer then order should be is_blocked_customer_order
                $userStatus = User::find($req->user_id);
                if($userStatus->status == 1)
                {
                    $is_blocked_customer_order = 1;
                }

                // charges get
                $quan_pro =  Cart::with('product')->where('user_id',$req->user_id)
                ->Wherehas('product',function($q){ $q->where('is_dc_free',0);}) ->sum('quantity');
                $pending_charges = 0;
                if($pending_order)
                {
                    $pending_charges = Order_item::where(['order_id'=>$my_order_id,'is_dc_free' => 0])->sum('qty');
                }

                $charges = Charge::where('suit',($quan_pro+$pending_charges))->first();
                if($charges){
                    $charges = $charges->charges;
                }else{
                    $charges = 0;
                }
                
                
                foreach($cart_items as $item){
                    $total += $item->product->price*$item->quantity;
                    $discount += $item->product->discount*$item->quantity;
                    $profit += $item->product->profit*$item->quantity;
                    // if pending order then update order items qty  of pending's order
                    if($pending_order_item = Order_item::
                            where(['order_id'=>$my_order_id,'prod_id'=>$item->product->id])
                            ->first())
                    {
                        $pending_order_item->qty = $pending_order_item->qty+$item->quantity;
                        $pending_order_item->save();
                    }
                    // order items added to array by id of coming order
                    else{

                        $insert_order_items[] = [
                            'order_id' => $my_order_id,
                            'prod_id'=>$item->product->id,
                            'qty' => $item->quantity,
                            'price'=>$item->product->price,
                            'purchase'=>$item->product->purchase,
                            'profit'=>$item->product->profit,
                            'discount'=>$item->product->discount,
                            'is_dc_free'=>$item->product->is_dc_free,
                        ];
                    }

                    // Update item quantity of solded and remaining before order bcz using try catch
                    // if customer is not blocked by admin
                    if($userStatus->status == 0)
                    {
                        $product = Product::find($item->product->id);
                        $product->soldItem -= $item->quantity;
                        $product->soldAdm += $item->quantity;
                        $product->timestamps = false;
                        $product->save();
                    }
                }

                 $gtotal = ($total + $charges) - $discount;
                 $gprofit = $profit - $discount;
                // Update Pending order
                if($pending_order)
                {
                    $now = Carbon::now();
                    $pending_order->name = $req->name;
                    // $pending_order->phone = $req->phone;
                    $pending_order->phone = $req->new_phone;
                    $pending_order->charges = $charges;
                    $pending_order->grandTotal = ($gtotal + $pending_order->amount) - $pending_order->order_discount;
                    $pending_order->grandProfit = $pending_order->grandProfit+$gprofit;
                    $pending_order->amount = $pending_order->amount+$total;
                    $pending_order->order_discount = $pending_order->order_discount+$discount;
                    $pending_order->city = $req->city;
                    $pending_order->is_blocked_customer_order = $is_blocked_customer_order;
                    $pending_order->address = $req->address;
                    $pending_order->note = $req->note;
                    $pending_order->date = $now->format('d/m/Y');
                    $pending_order->time = $now->format('g:i A');
                    $pending_order->save();
                    $message = 'Your Order Updated Successfully!';
                }
                //create new order
                else{
                    $order = new Order();
                    $now = Carbon::now();
                    $order->user_id = $req->user_id;
                    $order->id = $my_order_id;
                    $order->name = $req->name;
                    // $order->phone = $req->phone;
                    $order->phone = $req->new_phone;
                    $order->charges = $charges;
                    $order->amount = $total;
                    $order->grandTotal = $gtotal;
                    $order->grandProfit = $gprofit;
                    $order->order_discount = $discount;
                    $order->country = 'Pakistan';
                    $order->city = $req->city;
                    $order->address = $req->address;
                    $order->note = $req->note;
                    $order->is_blocked_customer_order = $is_blocked_customer_order;
                    $order->date = $now->format('d/m/Y');
                    $order->time = $now->format('g:i A');
                    $order->save();
                    $message = 'Order Placed Successfully!';
                }
                // order items insert
                Order_item::insert($insert_order_items);
                // delete cart items
                $cart_items->each->delete();
                DB::commit();
                return response()->json([
                    'statusCode'=>200,
                    'message'=>$message,
                ]);
            }
            DB::commit();
            return response()->json([
                'statusCode'=>200,
                'message'=>'Cart Items are Not availble for Order',
            ]);
        }
        catch (Exception $e) {
            DB::rollback();
            \Log::info($e->getMessage());
            return response()->json([
                'statusCode'=>404,
                'message'=>$e->getMessage(),
            ]);
        }
    }

    //view orders
     public function viewOrder(Request $req){
        $validator = Validator::make($req->all(), [
            'user_id'=>'required|numeric|exists:users,id',
        ]);
         
        if (!$validator->passes()) 
         {
            return response()->json([
            'statusCode'=>500,
            'message'=>'Valiadtion Error',
            'error'=>$validator->errors()->toArray()]);
        }else{
            $orders = Order::latest()->where('user_id',$req->user_id)->get();
            $orderArr=[];
            foreach($orders as $order){
                if($order->slip){
                    $order->slip=asset('Order_slips/'.$order->slip);
                }
                array_push($orderArr,$order);
            }
        
            if($orderArr){
                return response()->json([
                    'statusCode'=>200,
                    'message'=>'succes',
                    'data'=>$orderArr,
                ]);
            }
            return response()->json([
                'statusCode'=>404,
                'message'=>'No found orders',
            ]);
        }
    }

    // single order detail
    public function orderDetial(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'order_id'=>'required|numeric|exists:orders,id',
        ]);
         
        if (!$validator->passes()) 
        {
            return response()->json([
            'statusCode'=>500,
            'message'=>'Valiadtion Error',
            'error'=>$validator->errors()->toArray()]);
        }else{
            $orders = Order::with(['userdetail','orderitems','notes','orderitems.product'])->where('id',$req->order_id)->first();
            
            if($orders->slip){
                 $orders->slip=asset('Order_slips/'.$orders->slip);
            }
             
            $minutes = $orders->created_at->diffInMinutes(Carbon::now());
            if($orders){
                return response()->json([
                    'statusCode'=>200,
                    'message'=>'succes',
                    'day'=>$orders->created_at->format('d'),
                    'month'=>$orders->created_at->format('m'),
                    'year'=>$orders->created_at->format('y'),
                    'minutes'=>$minutes,
                    'data'=>$orders,
                    
                ]);
            }
            return response()->json([
                'statusCode'=>404,
                'message'=>'No found orders',
            ]);
        }
        return $order;
    }
    
    // ADD note 
    public function givenote(Request $req){
         $validator = Validator::make($req->all(), [
            'order_id'=>'required|numeric',
            'note'=>'required',
        ]);
         
        if (!$validator->passes()) 
         {
            return response()->json([
            'statusCode'=>500,
            'message'=>'Valiadtion Error',
            'error'=>$validator->errors()->toArray()]);
        }
        else
        {
            $check = Order::where('id',$req->order_id)->first();
            if($check){
                $note = new Note();
                $note->order_id = $req->order_id;
                $note->note = $req->note;
                $note->save();
                return response()->json([
                    'statusCode'=>200,
                    'message'=>'Note save',
                    'data'=> $note,
                ]);
            }
             return response()->json([
                    'statusCode'=>404,
                    'message'=>'No found order',
                ]);
        }
    }

    //updateOrder
    public function updateOrder(Request $req){
        
        $request->merge(['new_phone' => str_replace(' ', '', $req->phone)]);
         $validator = Validator::make($req->all(), [
            'order_id'=>'required|numeric|exists:orders,id',
            'name'=>'required|string',
            'country'=>'required|string',
            'city'=>'required|string',
            'new_phone'=>'required|digits:11',
        ]);
         
        if (!$validator->passes()) 
         {
            return response()->json([
            'statusCode'=>500,
            'message'=>'Valiadtion Error',
            'error'=>$validator->errors()->toArray()]);
        }
        else{
             $check = Order::where('id',$req->order_id)->first();
              if($check->status == 'CANCEL'){
                return 'Not Allowed this order has been  canceled';
            }
            
            //check half hour Order update
            // $diff_in_min = $check->created_at->diffInMinutes($check->updated_at);
            // if($diff_in_min > 30){
            //     return $message = 'Sorry You order time is more than 30 minutes!';
            // }
            
            // check if order dispatched
            if($check->status == 'DISPATCHED')
            {
               return $message = 'Sorry You order Has been Dispatched'; 
            }
            $check->name = $req->name;
            // $check->phone = $req->phone;
            $check->phone = $req->new_phone;
            $check->country = $req->country;    
            $check->city = $req->city;
            $check->save();
            return response()->json([
                'statusCode'=>200,
                '/message'=>'Update Succssfully',
                'data'=>$check,
            ]);
        }
       
    }
    
    
    //Cancel Order
    public function cancelOrder(Request $req){
         $validator = Validator::make($req->all(), [
            'order_id'=>'required|numeric|exists:orders,id',
        ]);
         
        if (!$validator->passes()) 
         {
            return response()->json([
            'statusCode'=>500,
            'message'=>'Valiadtion Error',
            'error'=>$validator->errors()->toArray()]);
        }
        else{
            $check = Order::where('id',$req->order_id)->first();
            
             //check half hour Order update
            // $diff_in_min = $check->created_at->diffInMinutes($check->updated_at);
            // if($diff_in_min > 30){
            //     return $message = 'Sorry You order time is more than 30 minutes!';
            // }
            
            // check if order dispatched
            if($check->status == 'DISPATCHED')
            {
               return $message = 'Sorry You order Has been Dispatched'; 
            }
            
            // Product quantity decreased
            $order_product = Order_item::where('order_id',$check->id)->get();
            foreach($order_product as $item){
                
                $product = Product::where('id',$item->prod_id)->first();
                if($product){
                    $product->soldItem += $item->qty;
                    $product->soldAdm -= $item->qty;
                    $product->save();
                }
            }
            
            $check->status = 'CANCEL';
            $check->save();
             return response()->json([
                'statusCode'=>200,
                'message'=>'Order Cancel Succssfully',
                'data'=>$check,
                ]);
        }
    }
    
    
    
    public function removeAllCart(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'user_id'=>'required|numeric|exists:users,id|exists:carts,user_id',
        ]);
        if (!$validator->passes()) 
        {
            return response()->json([
                'statusCode'=>422,
                'message'=>'Validation Error',
                'error'=>$validator->errors()->toArray()
            ]);
        }
        else
        {
            $carts = Cart::where('user_id',$req->user_id)->get();
            if($carts->count()>0){
                if(Cart::where('user_id',$req->user_id)->delete()){
                    return response()->json([
                        'statusCode'=>200,
                        'status'=>'success',
                        'message'=>'All cart item for this user is deleted',
                    ]);
                }else{
                    return response()->json([
                        'statusCode'=>500,
                        'status'=>'error',
                        'message'=>'something wents wrong',
                    ]);
                }
            }
            else{
                return response()->json([
                    'statusCode'=>404,
                    'message'=>'No Cart Items are availble for this user',
                ]);
            }
        }
    }
    
      //save cart
      public function UpdateOrderQty(Request $req)
      {
        // return $updatedOrder=Order::with('orderitems')->where('user_id',$req->user_id)->where('status','PENDING')->first();
        $validator = Validator::make($req->all(), [
              'prod_id'=>'required|numeric|exists:products,id',
              'user_id'=>'required|numeric|exists:users,id',
              'quantity' => 'required|numeric',
        ]);
           
        if (!$validator->passes()) 
        {
          return response()->json([
          'statusCode'=>500,
          'status'=>'error',
          'message'=>'Valiadtion Error',
          'error'=>$validator->errors()->toArray()]);
        }else{
            // Prodcut Quantity Vlidation for all insertion
            // 1.exceed_limit Quantity Validation 
            $product_check_exceedLimit = Product::find($req->prod_id);
            if($req->quantity > $product_check_exceedLimit->exceed_limit && $product_check_exceedLimit->exceed_limit != Null )
            {
                return response()->json([
                    'statusCode'=>500,
                    'status'=>'error',
                    'message'=>$product_check_exceedLimit->exceed_limit.' Exceed Limit '.' Sorry! you reached maximum limit for this product!',
                ]);
            }
             //2. if Cart quantity increase than Real Product Quantity
            if($req->quantity > $product_check_exceedLimit->soldItem)
            {
                 return response()->json([
                    'statusCode'=>500,
                    'status'=>'success',
                    'message'=>'The requested qty is not available',
                    'item-Quantity'=>$product_check_exceedLimit->soldItem,
                ]);
            }
                
            $total = 0;
            $gtotal = 0;
            $gprofit = 0;
            // If User has placed Order and Add or update Items in Pending Status Order.........................................//
            $pending_order = Order::where('user_id',$req->user_id)->where('status','PENDING')->first();
            if($pending_order)
            {
                // if already Order-Item exists related to this Order
                $checkOrderCart = Order_item::where('order_id',$pending_order->id)->where('prod_id',$req->prod_id)->first();
                if($checkOrderCart){
                    if($checkOrderCart->qty>$req->quantity){
                        $qty=(int)$checkOrderCart->qty-$req->quantity;
                        $product = Product::where('id',$req->prod_id)->first();
                        $product->soldItem += (int)$qty;
                        $product->soldAdm -= (int)$qty;
                        $product->save();
                    }elseif($checkOrderCart->qty<$req->quantity){
                        $qty=$req->quantity-(int)$checkOrderCart->qty;
                        if($product->soldItem<(int)$qty){
                            return response()->json([
                                'statusCode'=>500,
                                'status'=>'error',
                                'message'=>'Product '.$product->name.' did not have enough item left',
                            ]);
                        }
                        $product = Product::where('id',$req->prod_id)->first();
                        $product->soldItem -= (int)$qty;
                        $product->soldAdm += (int)$qty;
                        $product->save();
                    }
                    
                    $checkOrderCart->qty = $req->quantity;
                    $checkOrderCart->price = $checkOrderCart->product->price;
                    $checkOrderCart->purchase = $checkOrderCart->product->purchase;
                    $checkOrderCart->profit = $checkOrderCart->product->profit;
                    $checkOrderCart->discount = $checkOrderCart->product->discount;
                    $checkOrderCart->save();
                    
                    // total or orderItems details
                    $items = Order_item::where('order_id',$pending_order->id)->get();   
                    $orderscount = $items->count();
                    // total bill without charges and also calculate Profit
                    foreach($items as $item){
                        $total += $item->price*$item->qty;
                        $gprofit += (($item->profit*$item->qty) - ($item->discount*$item->qty));
                    }
                    
                    // if  order item Update Then uPdate Order Pending status
                    // quantiy suits
                    $quan_pro =  $items->sum('qty');
                     // Charges find
                    if($quan_pro >= 20){
                        $charge = Charge::where('suit','>=',20)->first()->charges;
                    }else{
                        $charge = Charge::where('suit',$quan_pro)->first();
                        if(!$charge){
                            $charge = 250;
                        }
                        else{
                            $charge = Charge::where('suit',$quan_pro)->first()->charges;
                        }
                    }
                    // grand total (total + charge)
                    $gtotal = $total + $charge;
                    // Order Update
                    $pending_order->charges = $charge;
                    $pending_order->amount = $total;
                    $pending_order->grandTotal = $gtotal;
                    $pending_order->grandProfit = $gprofit;
                    $pending_order->save();
                    $updatedOrder=Order::with('orderitems')->where('user_id',$req->user_id)->where('status','PENDING')->first();
                    return response()->json([
                        'statusCode'=>200,
                        'status'=>'success',
                        'message'=>'Order-item Quantity Update',
                        'data'=>$updatedOrder
                    ]);
                }else{
                    return response()->json([
                        'statusCode'=>404,
                        'status'=>'error',
                        'message'=>'Product Not Found In Pending Order',
                    ]);
                }
            }else{
                return response()->json([
                    'statusCode'=>404,
                    'status'=>'error',
                    'message'=>'Pending Order Not Found',
                ]);
            }
        }
    }
}
