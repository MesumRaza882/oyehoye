<?php

namespace App\Http\Controllers\ThirdPartyApis;

use App\Http\Controllers\Controller;
use App\Helpers\General;
use Illuminate\Http\Request;
use Psy\Readline\Hoa\Console;

class PostEx extends Controller
{



    protected static $createOrderAPI = "https://api.postex.pk/services/integration/api/order/v3/create-order";
    protected static $trackOrderByCnNoAPI = "https://api.postex.pk/services/integration/api/order/v1/track-order";
    protected static $saveShipperAdvice = "https://api.postex.pk/service/integration/api/order/v2/save-shipper-advice";
    protected static $apiToken = 'YTMxODRmZWIwYmMzNDRkMmI2NTMzZmFlOWRlOGYzMzk6ODViNmFhMzg2ZDVkNDQ0Zjg3MTAyMTA3MDQ5ZTMzZWE=';

    public static function create_order(
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
        $orderType
    ) {
        $url = self::$createOrderAPI;
        $headers  = [
            'Content-Type: application/json',
            'token: ' . $apiToken,
        ];
        $postData = array(
            "orderRefNumber"              => $orderRefNumber,
            "invoicePayment"              => $invoicePayment,
            "orderDetail"              => $orderDetail,
            'customerName'         => $customerName,
            'customerPhone'        => $customerPhone,
            'deliveryAddress'        => $deliveryAddress,
            'transactionNotes'      => $transactionNotes,
            'cityName'   => $cityName,
            'invoiceDivision'                => $invoiceDivision,
            'items'                => $items,
            'pickupAddressCode'             => $pickupAddressCode,
            'storeAddressCode'        => $storeAddressCode,
            'orderType'               => $orderType,
        );
        $result = General::curl_post($url, $headers, $postData);
        $msg = "";
        $st = 0;
        $cn = null;
        if ($result["status"] == 200) {
            $returnArray = json_decode($result["result"], true);
            $cn = $returnArray['dist']['trackingNumber'];
            $msg =  $returnArray['statusMessage'];
            $st = 1;
        } elseif ($result["status"] == 400) {
            $returnArray = json_decode($result["result"], true);
            $msg = $returnArray['statusMessage'];
        } else {
            $msg = "Something wnet wrong";
        }
        return ["st" => $st, "msg" => $msg, "cn" => $cn];
    }

    public static function track_order_by_cn($apiToken, $cn_no)
    {
        $headers  = [
            'Content-Type: application/json',
            'token: ' . $apiToken,
        ];
        $params = array(
            "trackingNumber" => (int)$cn_no,
        );
        $url = self::$trackOrderByCnNoAPI . '/' . $cn_no;
        $result = General::curl_get($url, $headers);

        $msg = "";
        $st = 0;
        $current_location = null;
        $delivery_status = "";
        $date_time = null;
        $_st = "DISPATCHED";

        if ($result["status"] == 200) {
            $api_result = json_decode($result["result"], true);

            if ($api_result && isset($api_result['dist']['transactionStatus'])) {
                $st = 1;

                // Check if transactionStatusHistory key exists and is not empty
                if (isset($api_result['dist']['transactionStatusHistory']) && !empty($api_result['dist']['transactionStatusHistory'])) {
                    $tracking_history = $api_result['dist']['transactionStatusHistory'];
                    // Assuming the first transaction status message contains current transaction history status
                    $mostRecentStatus = reset($tracking_history);
                    $date_time = $mostRecentStatus["updatedAt"];
                }

                $msg = $api_result['statusMessage'];
                $delivery_status_check = $api_result['dist']['transactionStatus'];
                $_st = self::getSystemStatus($delivery_status_check);
                // \Log::info($api_result);
                // $_st = $api_result;
                $delivery_status = self::getSystemStatus($delivery_status_check);
            }
        } else {
            $msg = "Something wnet wrong in Tracking Courier Order";
        }

        return ["st" => $st, "msg" => $msg, "system_status" => $_st, "delivery_status" => $delivery_status, "delivery_status_time" => $date_time];
    }

    public static function getSystemStatus($status)
    {
        switch ($status) {
            case "En-Route to Lahore warehouse":
                $_st = "ON-THE-WAY";
                break;
            case "Un-Assigned By Me":
                $_st = "CANCEL";
                break;
            case "Unbooked":
                $_st = "Team Review your Order";
                break;
            case "Attempted":
                $_st = "Refused By Customer";
                break;
            case "Picked By PostEx":
                $_st = "Rider Picked";
                break;
            case "Booked":
                $_st = "DISPATCHED";
                break;
            default:
                $_st = $status;
        }
        return $_st;
    }

    public static function re_attempt_order($cn_no, $status, $remkars)
    {
        $headers  = [
            'Content-Type: application/json',
            'token: ' . self::$apiToken,
        ];
        $params = array(
            "trackingNumber" => (int)$cn_no,
            "statusId" => (int)$cn_no,
            "statusId" => $remkars,
        );
        $url = self::$saveShipperAdvice;
        $result = General::curl_custom($url, $headers, $params, 'PUT');
        $msg = "";
        $st = 0;

        if ($result["status"] == 200) {
            $st = 1;
            $msg = 'Successfully mark as re attempted';
        } else {
            $arr = (array)json_decode($result["result"]);
            if (isset($arr["error"])) {
                $msg = $arr["error"];
            } elseif (isset($arr["message"])) {
                $msg = $arr["message"];
            } else {
                $msg = 'Something went wrong';
            }
        }

        return ["st" => $st, "msg" => $msg];
    }
}
