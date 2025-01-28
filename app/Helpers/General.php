<?php

namespace App\Helpers;

use Illuminate\Support\Str;

use File;
use Carbon\Carbon;

class General
{
    public static function curl_post($url,$headers,$postData){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));           
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		$array["result"] = curl_exec ($ch);
		$array["status"] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		return $array;
	}
	
    public static function curl_custom($url,$headers,$postData,$method){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));           
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		$array["result"] = curl_exec ($ch);
		$array["status"] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		return $array;
	}

    public static function curl_get($url,$headers){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');           
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		$array["result"] = curl_exec ($ch);
		$array["status"] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		return $array;
	}
	
    public static function pagination($array){
		$request = request()->fullUrlWithQuery(['page' => null]);
        $url_parameters = parse_url($request, PHP_URL_QUERY);
	    
        $total_size_of_links = sizeof($array) - 1;
        $current_page = request()->page;
	    
	    if(!$current_page or $current_page <= 1){
            $current_page = 1;
            $previous_page = null;
        }else{
            $previous_page = $current_page - 1;
        }
        if($current_page == $total_size_of_links){
            $next_page = null;
        }else{
            $next_page = $current_page + 1;
        }
        
        if($current_page < $total_size_of_links){
	        if($array[0]["url"]){
		        $url = $array[0]["url"].'&'.$url_parameters;
	            $active = $array[0]["active"];
	            $label = $array[0]["label"];
	            $class = 'btn-secondary';
	            echo '<a href="'.$url.'" class="me-2 btn '.$class.'" >'.$label.'</a>';
	        }
	        if($previous_page){
		        $url = $array[$previous_page]["url"].'&'.$url_parameters;
	            $active = $array[$previous_page]["active"];
	            $label = $array[$previous_page]["label"];
	            $class = $active == null ? 'btn-secondary' : 'btn-primary';
	            echo '<a href="'.$url.'" class="me-2 btn '.$class.'" >'.$label.'</a>';
	        }

	        if($current_page){
		        $url = $array[$current_page]["url"].'&'.$url_parameters;
	            $active = $array[$current_page]["active"];
	            $label = $array[$current_page]["label"];
	            $class = $active == null ? 'btn-secondary' : 'btn-primary';
	            echo '<a href="'.$url.'" class="me-2 btn '.$class.'" >'.$label.'</a>';
	        }
	        
	        if($next_page && ($next_page < $total_size_of_links)){
		        $url = $array[$next_page]["url"].'&'.$url_parameters;
	            $active = $array[$next_page]["active"];
	            $label = $array[$next_page]["label"];
	            $class = $active == null ? 'btn-secondary' : 'btn btn-primary';
	            echo '<a href="'.$url.'" class="me-2 btn '.$class.'" >'.$label.'</a>';
	        }
	        
            if($current_page == 1 && $total_size_of_links > 2){
		        $url = $array[3]["url"].'&'.$url_parameters;
	            $active = $array[3]["active"];
	            $label = $array[3]["label"];
	            $class = 'btn-secondary';
	            echo '<a href="'.$url.'" class="me-2 btn '.$class.'" >'.$label.'</a>';
	        }

	        if($array[$total_size_of_links]["url"] && $total_size_of_links > 3){
		        $url = $array[$total_size_of_links]["url"].'&'.$url_parameters;
	            $active = $array[$total_size_of_links]["active"];
	            $label = $array[$total_size_of_links]["label"];
	            $class = 'btn-secondary';
	            echo '<a href="'.$url.'" class="me-2 btn '.$class.'" >'.$label.'</a>';
	        }
        }
    }

		public static function status($status){
			
			$arr = [
				0 => 'Pending',
				1 => 'Approved',
				2 => 'Rejected'
			];

			return $arr[$status];
		}
}