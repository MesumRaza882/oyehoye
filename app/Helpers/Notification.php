<?php

namespace App\Helpers;

use App\Http\Controllers\Controller;
use Storage;
use File;
use App\Models\UserToken;
use App\Models\{Product,Order};
use App\Helpers\Helper;

class Notification extends Controller
{
  
    public static function orderPaymentStatusUpdate($order)
    {

        $status_text = Helper::advance_payment_status_text($order->advance_payment_status);
        $emoji = Helper::emoji_icon($status_text);
        
        $arr = [
            'title' => "Payment $status_text $emoji",
            'body' => "#$order->id payment status chagned to $status_text",
        ];

        
        $FcmTokens = UserToken::where('user_id', $order->user_id)->get()->pluck('device_token')->toArray();
        return Helper::sendWebNotification($arr, $FcmTokens);
    }

    public static function orderStatusChange($order, $msg)
    {

        $status_text = $order->status;
        $emoji = Helper::emoji_icon($order->status);
        
        if($msg && $status_text == "CANCEL"){
            $body = "Cancel Reason: $msg";
        }else{
            $body = "Order #$order->id status chagned to $status_text";
        }
        $arr = [
            'title' => "Order #$order->id $status_text $emoji",
            'body' => $body,
        ];

        
        $FcmTokens = UserToken::where('user_id', $order->user_id)->get()->pluck('device_token')->toArray();
        return Helper::sendWebNotification($arr, $FcmTokens);
    }

    public static function orderInProblemAlert($order)
    {

        $status_text = $order->status;
        $emoji = Helper::emoji_icon('problem');
        
        $body = "Order #$order->id has issue please check it";

        $arr = [
            'title' => "Check Order #$order->id $status_text $emoji",
            'body' => $body,
        ];

        
        $FcmTokens = UserToken::where('user_id', $order->user_id)->get()->pluck('device_token')->toArray();
        return Helper::sendWebNotification($arr, $FcmTokens);
    }
}