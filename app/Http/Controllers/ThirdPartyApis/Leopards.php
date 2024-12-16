<?php

namespace App\Http\Controllers\ThirdPartyApis;

use App\Http\Controllers\Controller;
use App\Helpers\General;
use Illuminate\Http\Request;

class Mnp extends Controller{
    
    protected static $createOrderAPI = "http://new.leopardscod.com/webservice/bookPacketTest/format/json/";
    // protected static $trackOrderByCnNoAPI = "http://mnpcourier.com/mycodapi/api/Tracking/Consignment_Tracking_Location";
    // protected static $cancelOrderAPI = "http://mnpcourier.com/mycodapi/api/Booking/VoidConsignment";
    
    public static function create_order($username,$password,$c_name,$c_mobile,$c_email,$c_address,$distination_city,$weight,$pieces,$cod,$ref,$services,$content,$fragile,$remarks,$insurance){
        $insurance  = $insurance ? $insurance : "0";
        //$cod        = $cod > 0 ? $cod : "0.0001";
        $headers  = [
			'Content-Type: text/json'
		];
    $postData = array(
      // "username"              => $username,
      // "password"              => $password,
			// 'consigneeName'         => $c_name,
			// 'consigneeAddress'      => $c_address,
			// 'consigneeMobNo'        => $c_mobile,
			// 'consigneeEmail'        => $c_email,
			// 'destinationCityName'   => $distination_city,
			// 'pieces'                => $pieces,
			// 'weight'                => $weight,
			// 'codAmount'             => $cod,
			// 'productDetails'        => $content,
			// 'fragile'               => $fragile,
			// 'service'               => $services,
			// 'remarks'               => $remarks,
			// 'insuranceValue'        => $insurance,
			// 'custRefNo'             => $ref
      'api_key' => $username,//'your_api_key'
      'api_password' => $password,//'your_api_password'
      'booked_packet_weight' => $weight * 1000, // Weight should in 'Grams' e.g. '2000'
      // 'booked_packet_vol_weight_w' => int, // Optional Field (You can keep it empty), Volumetric Weight Width
      // 'booked_packet_vol_weight_h' => int, // Optional Field (You can keep it empty), Volumetric Weight Height
      // 'booked_packet_vol_weight_l' => int, // Optional Field (You can keep it empty), Volumetric Weight Length
      'booked_packet_no_piece' => $pieces, // No. of Pieces should an Integer Value
      'booked_packet_collect_amount' => $cod, // Collection Amount on Delivery
      'booked_packet_order_id' => $ref, // Optional Filed, (If any) Order ID of Given Product
      'origin_city' => 'self', /** Params: 'self' or 'integer_value' e.g. 'origin_city' => 'self' or 'origin_city' => 789 (where 789 is Lahore ID)
      * If 'self' is used then Your City ID will be used.
      * 'integer_value' provide integer value (for integer values read 'Get All Cities' api documentation)
      */
      'destination_city' => $distination_city, /** Params: 'self' or 'integer_value' e.g. 'destination_city' => 'self' or 'destination_city' => 789 (where 789 is Lahore ID)
      * If 'self' is used then Your City ID will be used.
      * 'integer_value' provide integer value (for integer values read 'Get All Cities' api documentation)
      */
      'shipment_id' => $ref,
      'shipment_name_eng' => 'self', // Params: 'self' or 'Type any other Name here', If 'self' will used then Your Company's Name will be Used here
      'shipment_email' => 'self', // Params: 'self' or 'Type any other Email here', If 'self' will used then Your Company's Email will be Used here
      'shipment_phone' => 'self', // Params: 'self' or 'Type any other Phone Number here', If 'self' will used then Your Company's Phone Number will be Used here
      'shipment_address' => 'self', // Params: 'self' or 'Type any other Address here', If 'self' will used then Your Company's Address will be Used here
      'consigneeName'         => $c_name,
			'consigneeAddress'      => $c_address,
			'consigneeMobNo'        => $c_mobile,
			'consigneeEmail'        => $c_email,
			'consignment_name_eng' => $c_name, // Type Consignee Name here
      'consignment_email' => $c_email, // Optional Field (You can keep it empty), Type Consignee Email here
      'consignment_phone' => $c_mobile, // Type Consignee Phone Number here
      // 'consignment_phone_two' => 'string', // Optional Field (You can keep it empty), Type Consignee Second Phone Number here
      // 'consignment_phone_three' => 'string', // Optional Field (You can keep it empty), Type Consignee Third Phone Number here
      'consignment_address' => $c_address, // Type Consignee Address here
      'special_instructions' => $remarks, // Type any instruction here regarding booked packet
      'shipment_type' => $services, // Optional Field (You can keep it empty so It will pick default value i.e. "overnight"), Type Shipment type name here
      // 'custom_data' => 'json array', // Optional Field (You can keep it empty), [{"key1":"value1","key2":value2,.....}]
      // 'return_address' => 'string', // Optional Field (You can keep it empty)
      // 'return_city' => 'int', // Optional Field (You can keep it empty)
      // 'is_vpc' => 'int', // Optional Field (You can keep it empty) If is_vpc =1 then booked_packet_order_id should be a CN numb
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
            //dd($msg);
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
        $_st = "Pending";
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
                //dd($delivery_status);
                $_st = self::getSystemStatus($delivery_status);
                //dd($_st);
                $msg = $shipment_details[0]["Detail"];
                $date_time = $shipment_details[0]["DateTime"];
                $current_location = $shipment_details[0]["Location"];
            }else{
                $_st = "Pending";
                $delivery_status = "Cancelled or Invalid Id";
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