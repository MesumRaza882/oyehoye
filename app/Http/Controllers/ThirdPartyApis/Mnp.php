<?php

namespace App\Http\Controllers\ThirdPartyApis;

use App\Http\Controllers\Controller;
use App\Helpers\General;
use Illuminate\Http\Request;

class Mnp extends Controller{
    
    protected static $createOrderAPI = "http://mnpcourier.com/mycodapi/api/Booking/InsertBookingData";
    protected static $trackOrderByCnNoAPI = "http://mnpcourier.com/mycodapi/api/Tracking/Consignment_Tracking_Location";
    protected static $cancelOrderAPI = "http://mnpcourier.com/mycodapi/api/Booking/VoidConsignment";
    
    public static function create_order(
    $username,
    $password,
    $locationID,
    $c_name,
    $c_mobile,
    $c_email,
    $c_address,
    $distination_city,
    $weight,$pieces,
    $cod,$ref,$services,
    $content,$fragile,$remarks,
    $insurance){
        $insurance  = $insurance ? $insurance : "0";
        //$cod        = $cod > 0 ? $cod : "0.0001";
        $headers  = [
			'Content-Type: text/json'
		];
        $postData = array(
            "username"              => $username,
            "password"              => $password,
            "locationID"              => $locationID,
			'consigneeName'         => $c_name,
			'consigneeAddress'      => $c_address,
			'consigneeMobNo'        => $c_mobile,
			'consigneeEmail'        => $c_email,
			'destinationCityName'   => $distination_city,
			'pieces'                => $pieces,
			'weight'                => $weight,
			'codAmount'             => $cod,
			'productDetails'        => $content,
			'fragile'               => $fragile,
			'service'               => $services,
			'remarks'               => $remarks,
			'insuranceValue'        => $insurance,
			'custRefNo'             => $ref
        );
        
        $result = General::curl_post(self::$createOrderAPI,$headers,$postData);
        $msg = "";
        $st = 0;
        $cn = null;
        if($result["status"] == 200){
            $returnArray = json_decode($result["result"],true)[0];
            $true_flase = $returnArray["isSuccess"];
            $msg = $returnArray["message"];
            if($true_flase){
                $cn = $returnArray["orderReferenceId"];
                if($cn){
                    $st = 1;
                    $msg = $msg." with CN: ".$cn;
                }
            }
            // dd($msg);
        }elseif($result["status"] == 401){
            $msg = "Invalid Login Detail";//json_decode($result["result"],true)["moreInformation"];
        }else{
            $msg = "Something wnet wrong";
        }
        //dd($result);
        return ["st" => $st, "msg" => $msg, "cn" => $cn];
    }
    
    public static function track_order_by_cn($username,$password,$location_id,$cn_no){
        $headers  = [
			'Content-Type: text/json'
		];       
        $params = array(
            "username" => $username,
            "password" => $password,
            "locationID" => $location_id,
            "consignment" => $cn_no,
        );
        $url = self::$trackOrderByCnNoAPI . '?' . http_build_query($params);
        $result = General::curl_get($url,$headers);
        $msg = "";
        $st = 0;
        $current_location = null;
        //$delivery_status = "";
        $delivery_status = "";
        $date_time = null;
        $_st = "DISPATCHED";
        // \Log::info($url);
        if($result["status"] == 200){
            $returnArray = json_decode($result["result"],true)[0];
            $true_flase = $returnArray["isSuccess"];
            $msg = $returnArray["message"];
            // return $returnArray;
            //dd($returnArray);
            if($true_flase == "true"){
                $tracking_detail = $returnArray["tracking_Details"][0];
                // $delivery_status = $tracking_detail["CNStatus"];
                // $date_time = $tracking_detail["CNStatus"];
                $st = 1;
                $shipment_details = $tracking_detail["Details"];
                $delivery_status = $shipment_details[0]["Status"];
                $_st = self::getSystemStatus($delivery_status);
                $msg = $shipment_details[0]["Detail"];
                $date_time = $shipment_details[0]["DateTime"];
                $current_location = $shipment_details[0]["Location"];
            }else{
                $_st = 0;
                $delivery_status = "Invalid Id or SomeThing";
            }
            
            //dd($msg);
        }elseif($result["status"] == "401" or $result["status"] == "404"){
            $msg = json_decode($result["result"],true)["Message"];
        }else{
            $msg = "Something wnet wrong";
        }
        
        return ["st" => $st, "msg" => $msg, "system_status" => $_st, "delivery_status" => $delivery_status, "delivery_status_time" => $date_time, "current_location" => $current_location];
    }

    public static function cancel_order($username,$password,$location_id,$cn_no){
        $headers  = [
			'Content-Type: text/json'
		];
        $postData = array(
            "username" => $username,
            "password" => $password,
			'locationID' => $location_id,
			'consignmentNumberList' => [$cn_no]
        );
        
        $result = General::curl_post(self::$cancelOrderAPI,$headers,$postData);
        $msg = "";
        $st = 0;
        if($result["status"] == 200){
            $returnArray = json_decode($result["result"],true)[0];
            $true_flase = $returnArray["isSuccess"];
            $msg = $returnArray["message"];
            if($true_flase){
                $msg = "CN: ".$cn_no." has been cancelled";
                $st = 1;
            }
            //dd($msg);
        }elseif($result["status"] == "401" or $result["status"] == "404"){
            $msg = json_decode($result["result"],true)["Message"];
        }else{
            $msg = "Something wnet wrong";
        }
        //dd($result);
        return ["st" => $st, "msg" => $msg];
    }
    
    public static function getSystemStatus($status){
        switch($status){
            case "Delivered":
                $_st = "Delivered";
                break;
            case "On Delivery":
                $_st = "On Route";
                break;
            case "Arrived at OPS":
            case "Loading":
            case "Unloading":
            case "ROUTED":
                $_st = "Shipped";
                break;
            case "RD":
                $_st = "Refused";
                break;
            case "Return to Origin":
                $_st = "Ready for Return";
                break;
            case "Return to Shipper":
                $_st = "Returned";
                break;
            case "Booking":
                $_st = "Pending";
                break;
            default:
                $_st = "Problem";
        }
        return $_st;
    }
    
    public static function testMnpAPI(Request $request){
        $id = $request->id;
        $ot = self::track_order_by_cn("hassan_18k38","tayyab123QWE!","5751",$id);
        print_r($ot);
    }
}