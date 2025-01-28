<?php

namespace App\Http\Controllers\ThirdPartyApis;

use App\Http\Controllers\Controller;
use App\Helpers\General;

class Tcs extends Controller{
    
    protected static $createOrderAPI = "https://api.tcscourier.com/production/v1/cod/create-order";
    protected static $trackOrderByRefNoAPI = "https://api.tcscourier.com/production/v1/cod/track-order";
    protected static $trackOrderByCnNoAPI = "https://api.tcscourier.com/production/track/v1/shipments/detail";
    protected static $cancelOrderAPI = "https://api.tcscourier.com/production/v1/cod/cancel-order";
    
    public static function create_order($ibm_clint_id,$username,$password,$ccc,$c_name,$c_mobile,$c_email,$c_address,$origin_city,$distination_city,$weight,$pieces,$cod,$ref,$services,$content,$fragile,$remarks,$insurance){
        $headers  = [
			'Content-type: application/json',
			'X-IBM-Client-Id: '.$ibm_clint_id,
		];
        $postData = array(
            "userName" => $username,
            "password" => $password,
            "costCenterCode" => $ccc,
            "consigneeName" => $c_name,
            "consigneeAddress" => $c_address,
            "consigneeMobNo" => $c_mobile,
            "consigneeEmail" => $c_email,
            "originCityName" => $origin_city,
            "destinationCityName" => $distination_city,
            "weight" => $weight,
            "pieces" => $pieces,
            "codAmount" => $cod,
            "customerReferenceNo" => $ref,
            "services" => $services,
            "productDetails" => $content,
            "fragile" => $fragile,
            "remarks" => $remarks,
            "insuranceValue" => $insurance,
        );
        
        $result = General::curl_post(self::$createOrderAPI,$headers,$postData);
        $msg = "";
        $st = 0;
        $cn = null;
        if($result["status"] == 200){
            $returnArray = json_decode($result["result"],true)["returnStatus"];
            if($returnArray["code"] != "0200"){
                $msg = $returnArray["message"];
            }else{
                $bookingReply = json_decode($result["result"],true)["bookingReply"];
                $cn_string = $bookingReply["result"];
                $get_booked_cn = (int) filter_var($cn_string, FILTER_SANITIZE_NUMBER_INT);
                $cn = $get_booked_cn;
                $st = 1;
                $msg = "TCS Shipment has booked with CN: ".$cn;
            }
        }elseif($result["status"] == 401){
            $msg = json_decode($result["result"],true)["moreInformation"];
        }else{
            $msg = "Something wnet wrong on TCS API >> ".$result["result"];
        }
        //dd($result);
        return ["st" => $st, "msg" => $msg, "cn" => $cn];
    }

    
    /* not working
    public static function track_order($ibm_clint_id,$username,$password,$ref_no){
        $headers  = [
			'Content-Type: application/json',
			'X-IBM-Client-Id: '.$ibm_clint_id,
		];       
        $postData = array(
            "userName" => $username,
            "password" => $password,
            "referenceNo" => $ccc,
        );
        
        $result = General::curl_post(self::$createOrderAPI,$headers,$postData);
        $msg = "";
        $st = 0;
        if($result["status"] == 200){
            $returnArray = json_decode($result["result"],true)["returnStatus"];
            if($returnArray["code"] != "0200"){
                $msg = $returnArray["message"];
            }else{
                $bookingReply = json_decode($result["result"],true)["bookingReply"];
                $cn_string = $bookingReply["result"];
                $get_booked_cn = (int) filter_var($cn_string, FILTER_SANITIZE_NUMBER_INT);
                $msg = $get_booked_cn;
                $st = 1;
            }
        }elseif($result["status"] == 401){
            $msg = json_decode($result["result"],true)["moreInformation"];
        }else{
            $msg = "Something wnet wrong";
        }
        
        return ["st" => $st, "msg" => $msg];
    }
    */
    
    public static function track_order_by_cn($ibm_clint_id/*,$username,$password*/,$cn_no){
        $headers  = [
			'Content-Type: application/json',
			'X-IBM-Client-Id: '.$ibm_clint_id,
		];
        // $postData = array(
        //     /*"userName" => $username,
        //     "password" => $password,*/
        //     "consignmentNo" => $cn_no,
        // );
        
        $url = self::$trackOrderByCnNoAPI."?consignmentNo=".$cn_no;
        $result = General::curl_get($url,$headers);
        $msg = "";
        $st = 0;
        $_st = "Pending";
        //$delivery_status = "";
        $delivery_status = "";
        $date_time = null;
        $station = null;
        //return $result;
        \Log::info($result);
        if($result["status"] == 200){
            $returnArray = json_decode($result["result"],true)["returnStatus"];
            if($returnArray["code"] != "0200"){
                $msg = $returnArray["message"];
            }else{
                // return $result["result"];
                $st = 1;
                $api_result = json_decode($result["result"],true);
                if(array_key_exists("TrackDetailReply",$api_result)){
                    $trackDeliveryReply = $api_result["TrackDetailReply"];
                    if(array_key_exists("DeliveryInfo",$trackDeliveryReply)){
                        $deliveryInfo = $trackDeliveryReply["DeliveryInfo"];
                        //print_r($deliveryInfo);
                        $cn_api = $deliveryInfo[0]["consignmentNo"];
                        if($cn_api == $cn_no){
                            $status_code = $deliveryInfo[0]["code"];
                            $delivery_status = $status = $deliveryInfo[0]["status"];
                            $received_by = $deliveryInfo[0]["recievedBy"];
                            $date_time = $deliveryInfo[0]["dateTime"];
                            $station = $deliveryInfo[0]["station"];
                            //$status_info = $deliveryInfo[0]["status"];
                            $_st = self::getSystemStatus($status_code,$delivery_status);
                            // dd($_st);
                            if($received_by){
                                $msg = $status." received by ".$received_by;
                            }else{
                                $msg = $status;
                            }
                        }else{
                            $msg = "No Record Found at TCS";
                        }
                    }elseif(array_key_exists("Checkpoints",$trackDeliveryReply)){
                        $_st = "Shipped";
                        $delivery_status = "In Transit";
                        $deliveryInfo = $trackDeliveryReply["Checkpoints"];
                        $status = $trackDeliveryReply["Checkpoints"][0]["status"];
                        $received_by = $trackDeliveryReply["Checkpoints"][0]["recievedBy"];
                        $date_time = $trackDeliveryReply["Checkpoints"][0]["dateTime"];
                        //if($received_by){
                            $msg = $status." received by ".$received_by;
                        //}else{
                        //    $msg = $status;
                        //}
                    }else{
                        $_st = "Shipped";
                        $delivery_status = "In Transit";
                        $date_time = null;
                        $msg = "No Information Tracked";
                        
                    }
                }else{
                    $_st = $delivery_status = "Pending";
                    $msg = "Pending or not shipped CN";
                }
            }
        }elseif($result["status"] == 401){
            $msg = json_decode($result["result"],true)["moreInformation"];
        }else{
            $msg = "May be you have not shipped or there is error on TCS server";//"Something wnet wrong2";
        }
        
        return ["st" => $st, "msg" => $msg, "system_status" => $_st, "delivery_status" => $delivery_status, "delivery_status_time" => $date_time, "current_location" => $station];
    }
    
    public static function cancel_order($ibm_clint_id,$username,$password,$cn_no){
        $headers = [
			'Content-type: application/json',
			'X-IBM-Client-Id: '.$ibm_clint_id,
		];
        $postData = array(
            "userName" => $username,
            "password" => $password,
            "consignmentNumber" => $cn_no
        );
        
        $result = General::curl_custom(self::$cancelOrderAPI,$headers,$postData,"PUT");
        // dd($result);
        $msg = "";
        $st = 0;
        if($result["status"] == 200){
            $returnArray = json_decode($result["result"],true)["returnStatus"];
            $msg = "CN: ".$cn_no." ".$returnArray["message"];
            if($returnArray["code"] == "0200"){
                $st = 1;
            }
            // dd($msg);
        }elseif($result["status"] == "401" or $result["status"] == "404"){
            $msg = json_decode($result["result"],true)["moreInformation"];
        }else{
            $msg = "Something wnet wrong";
        }
        //dd($result);
        return ["st" => $st, "msg" => $msg];
    }
    
    public static function getSystemStatus($status_code,$delivery_status){
        switch($status_code){
            /*case"OK":
                if($status == "DELIVERED"){
                    $delivery_status = "Delivered";
                    //$delivery_status_info = "Delivered";
                }else{
                    $delivery_status = "On Route";
                    //$delivery_status_info = $status;
                }
                break;
            case "SD":
                $delivery_status = "Scheduled for Delivery";
                break;
            case "OH":
                $delivery_status = "Hold";
                break;
            case "RR":
                $delivery_status = "Ready for Return";
                break;
            case "RS":
                $delivery_status = "Returned";
                break;
            default:
                $delivery_status = "Pending";*/
            case "OK":
                if($delivery_status == "DELIVERED"){
                    $_st = "Delivered";
                }else{
                    $_st = "On Route";
                }
                break;
            // case "OH" || "BA" || "CN" || "FOK" || "SC" || "NP":
            //     $_st = "Problem";
            //     break;
            case "SD":
                $_st = "Shipped";
                break;
            case "RD":
                $_st = "Refused";
                break;
            case "RR":
                $_st = "Ready for Return";
                break;
            case "RS":
                $_st = "Returned";
                break;
            default:
                $_st = "Problem";
        }
        return $_st;
        /*
            TCS statuses
            UV unable to contact
        */
    }
                            
    public static function testTcsAPI(){
        $ot = self::track_order_by_cn("b5994152-7d46-40e5-b441-48773184866b"/*,"hassan00942api","tayyab123QWE!"*/,"776272500727");
        print_r($ot);
    }

}