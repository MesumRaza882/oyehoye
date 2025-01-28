<?php

namespace App\Http\Controllers\ThirdPartyApis;

use App\Http\Controllers\Controller;
use App\Helpers\General;
use Illuminate\Http\Request;

class Trax extends Controller
{

    protected static $createOrderAPI = "https://sonic.pk/api/shipment/book";
    protected static $trackOrderByCnNoAPI = "https://sonic.pk/api/shipment/track";
    protected static $cancelOrderAPI = "https://sonic.pk/api/shipment/cancel";

    public static function create_order(
        $api_key,
        $service_type_id,
        $pickup_address_id,
        $information_display,
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
        $charges_mode_id
    ) {
        //dd($c_address); return 1;
        $item_insurance = $item_insurance ? (int)$item_insurance : 0;
        $headers  = [
            'Content-Type: application/json',
            'Authorization: ' . $api_key
        ];

        // $postDataEmail = $consignee_email_address ? array('consignee_email_address' => $consignee_email_address) : array();
        // $postDataSpecialInstruction = $consignee_email_address ? array('special_instructions' => $remarks) : array();

        $postData = array(
            'service_type_id' => (int)$service_type_id,
            'pickup_address_id' => (int)$pickup_address_id,
            'information_display' => (int)$information_display,
            'consignee_city_id' => (int)$consignee_city_id,
            'consignee_name' => $consignee_name,
            'consignee_address' => $consignee_address,
            'consignee_phone_number_1' => $consignee_phone_number_1,
            // 'consignee_phone_number_2' => $c_mobile,
            'consignee_email_address' => $consignee_email_address,
            'order_id' => $order_id,
            'item_product_type_id' => (int)$item_product_type_id,
            'item_description' => $item_description,
            'item_quantity' => (int)$item_quantity,
            'item_insurance' => $item_insurance,
            'pieces_quantity' => $pieces_quantity,
            'pickup_date' => \Carbon\Carbon::now()->format("Y-m-d"),
            'estimated_weight' => (float)$estimated_weight,
            'shipping_mode_id' => (int)$shipping_mode_id,
            //'fragile' => $fragile,
            //'same_day_timing_id'
            'amount' => (int)$amount,
            'payment_mode_id' => (int)$payment_mode_id,
            'charges_mode_id' => (int)$charges_mode_id,
            'open_box' => 0,
        );
        // $postData = array_merge($postData,$postDataEmail);
        $postData = array_merge($postData);
        $result = General::curl_post(self::$createOrderAPI, $headers, $postData);
        $msg = "";
        $st = 0;
        $cn = null;
        // dd($result);
        if ($result["status"] == 200) {
            $api_result = json_decode($result["result"], true);
            //if(array_key_exists("tracking_number",$api_result)){
            if ($api_result["status"] == 0) {
                $cn = $api_result["tracking_number"];
                $msg = $api_result["message"];
                $st = 1;
            } else {
                $msg = $api_result["message"];
                if (array_key_exists("errors", $api_result)) {
                    $errors = $api_result["errors"];
                    $msg = array_values($errors)[0][0];
                }
            }
        } else {
            $msg = "Something wnet wrong";
        }
        //dd($result);
        return ["st" => $st, "msg" => $msg, "cn" => $cn];
    }

    public static function track_order_by_cn($api_key, $cn_no)
    {
        $headers  = [
            'Content-Type: application/json',
            'Authorization: ' . $api_key
        ];
        $params = array(
            "type" => 0,
            "tracking_number" => (int)$cn_no,
        );
        $url = self::$trackOrderByCnNoAPI . '?' . http_build_query($params);
        $result = General::curl_get($url, $headers);
        // dd($result);
        $msg = "";
        $st = 0;
        $current_location = null;
        $delivery_status = "";
        $date_time = null;
        $_st = "DISPATCHED";

        if ($result["status"] == 200) {
            // dd(12);
            $api_result = json_decode($result["result"], true);
            if ($api_result["status"] == 0) {
                $st = 1;
                $details = $api_result["details"];
                $tracking_history = $details["tracking_history"];
                $msg = $tracking_history[0]["status_reason"];
                $delivery_status = $tracking_history[0]["status"];
                $date_time = $tracking_history[0]["date_time"];
                $_st = self::getSystemStatus($delivery_status);
            } else {
                $st = 0;
                $msg = $api_result["message"];
                if (array_key_exists("errors", $api_result)) {
                    $errors = $api_result["errors"];
                    $msg = array_values($errors)[0][0];
                }
            }
        } else {
            $msg = "Something wnet wrong in Tracking Courier Order";
        }

        return ["st" => $st, "msg" => $msg, "system_status" => $_st, "delivery_status" => $delivery_status, "delivery_status_time" => $date_time, "current_location" => $current_location];
    }

    public static function cancel_order($api_key, $cn_no)
    {
        $headers  = [
            'Content-Type: application/json',
            'Authorization: ' . $api_key
        ];
        $postData = array(
            "type" => 0,
            "tracking_number" => (int)$cn_no,
        );

        $result = General::curl_post(self::$cancelOrderAPI, $headers, $postData);
        $msg = "";
        $st = 0;
        if ($result["status"] == 1) {
            $api_result = json_decode($result["result"], true);
            $msg = $api_result["message"];

            if (array_key_exists("errors", $api_result)) {
                $errors = $api_result["errors"];
                $msg = array_values($errors)[0][0];
            }

            if ($api_result["status"] == 0) {
                $st = 1;
            }
        } else {
            $msg = "Something wnet wrong";
        }
        //dd($result);
        return ["st" => $st, "msg" => $msg];
    }

    public static function getSystemStatus($status)
    {
        switch ($status) {
            case "Shipment - Delivered":
            case "Replacement - Delivered to Shipper":
            case "Shipment - Out for Delivery":
                $_st = "DELIVERED";
                break;
            case "Shipment - Arrived at Origin":
            case "Shipment - In Transit":
            case "Shipment - Arrived at Destination":
            case "Replacement - Arrived at Origin":
            case "Replacement - Arrived":
            case "Shipment - Arrival Service Center":
            case "Replacement - Collected":
            case "Shipment - Rider Picked":
                $_st = "ON-THE-WAY";
                break;
            case "Return - Confirm":
            case "Return - In Transit":
            case "Return - Arrived at Origin":
            case "Return - Dispatched":
            case "Return - Delivery Unsuccessful":
            case "Return - Delivered to Shipper":
            case "Return - Not Attempted":
            case "Return - On Hold":
                $_st = "RETURNED";
                break;
                // case "Delivered to Shipper":
            case "Shipment - Booked":
            case "Shipment - Re-Booked":
            case "Shipment - Arrived at Origin":
            case "Shipment - Rider Exchange":
            case "Shipment - Not Attempted":
            case "Replacement - In Transit":
            case "Shipment - Dispatched From Warehouse":
            case "Return - Rider Exchange":
            case "Replacement - Rider Exchange":
            case "Shipment - Misroute Forwarded":
            case "Replacement - Dispatched":
                $_st = "DISPATCHED";
                break;
            case "RD":
                $_st = "REFUSED-BY-CUSTOMER";
                break;
            case "Shipment - Cancelled":
                $_st = "CANCEL";
                break;
            default:
                $_st = "DISPATCHED";
        }
        return $_st;
        // <option value="60">Return Unsuccessful for CX and Sales</option> Problem
    }

    public static function testTraxAPI(Request $request)
    {
        $id = $request->id;
        $ot = self::track_order_by_cn("hassan_18k38", "tayyab123QWE!", "5751", $id);
        print_r($ot);
    }
}
