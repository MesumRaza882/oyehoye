<?php

namespace App\Helpers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Storage;
use File;
use App\Models\{Product,Order};
use App\Models\UserToken;
use Config;
use Aws\S3\S3Client;
use Carbon\Carbon;
use Cache;


// require dirname(__DIR__).'/../vendor-php/autoload.php';


class Helper extends Controller
{
  
    public static function sendWebNotification($arr, $FcmTokens)
    {
        \Log::info('sendWebNotification');


        $url = 'https://fcm.googleapis.com/fcm/send';
        $url = 'https://fcm.googleapis.com/v1/projects/wao-collection/messages:send';
        
        $authToken = self::getAccessToken();
          
        $data = [
            "message" => [
                "topic" => "products",
                // "token" => $token,
                "notification" => $arr,
            ]
        ];
        $encodedData = json_encode($data);
    
        $headers = [
            // 'Authorization: key=' . $serverKey,
            'Authorization: Bearer ' . $authToken,
            'Content-Type: application/json',
        ];
    
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedData);
        // Execute post
        $result = curl_exec($ch);
        if ($result === FALSE) {
            \Log::info('Curl failed: ' . curl_error($ch));
            return 'Curl failed: ' . curl_error($ch);
        }        
        
        // Close connection
        curl_close($ch);

        \Log::info($result);
        return $result;
    }

    public static function subscribeToFcm($FcmTokens, $type, $topic)
    {
        $authToken = self::getAccessToken();
        $topic = 'products';
        
        if($type == "add")
            $url = "https://iid.googleapis.com/iid/v1:batchAdd";
        else
            $url = "https://iid.googleapis.com/iid/v1:batchRemove";

        
        $fields = json_encode([
            "to" => "/topics/" . $topic,
            "registration_tokens" => $FcmTokens
        ]);
    
        $headers = [
            'Authorization: Bearer ' . $authToken,
            'Content-Type: application/json',
            'access_token_auth: true'
        ];
        
     
        self::callApi($url, $headers, $fields);
        return;

    }

    // get fcm/google access token
    public static function getAccessToken()
    {
        $token = Cache::get('access_token');
        if($token) {
            return $token;
        }
        $serviceAccountPath = storage_path('app/fcm-json-key-put-here.json');
        $serviceAccount = json_decode(file_get_contents($serviceAccountPath), true);

        $clientEmail = $serviceAccount['client_email'];
        $privateKey = $serviceAccount['private_key'];
        $privateKey = str_replace("\\n", "\n", $privateKey);
        $tokenUri = 'https://oauth2.googleapis.com/token';

        $jwtHeader = json_encode([
            'alg' => 'RS256',
            'typ' => 'JWT'
        ]);

        $jwtClaimSet = json_encode([
            'iss' => $clientEmail,
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud' => $tokenUri,
            'exp' => time() + 3600,
            'iat' => time()
        ]);

        $encodedHeader = base64_encode($jwtHeader);
        $encodedClaimSet = base64_encode($jwtClaimSet);
        $signature = self::signJwt($encodedHeader . '.' . $encodedClaimSet, $privateKey);

        $jwt = $encodedHeader . '.' . $encodedClaimSet . '.' . $signature;

        $response = self::makeHttpRequest($tokenUri, [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt,
        ]);

        $token = $response['response']['access_token'];
        Cache::put('access_token', $token, now()->addHour());
        return $token;
    }

    // sign jqt for fcm/google access token
    private static function signJwt($data, $privateKey)
    {
        openssl_sign($data, $signature, $privateKey, 'SHA256');
        return base64_encode($signature);
    }

    private static function makeHttpRequest($url, $postData)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        
        $response = curl_exec($ch);

        if ($response === FALSE) {
            \Log::error('cURL Error: ' . curl_error($ch));
            curl_close($ch);
            throw new \Exception('cURL Error: ' . curl_error($ch));
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return [
            'response' => json_decode($response, true),
            'http_code' => $httpCode,
        ];
    }



    public static function upload_image($file, $path = '',  $name = null)
    {
        $ext = $file->getClientOriginalExtension();
        $filename = time().'.'.$ext;
        $file->move($path,$filename);
        $upload_path = $path.'/'.$filename;
        return url($upload_path);
    }

    public static function upload_digital_ocean($file, $path = '',  $content, $name = null)
    {
			
    	$client = new S3Client([
        'version' => 'latest',
        'region'  => 'nyc3',
        'endpoint' => 'https://nyc3.digitaloceanspaces.com',
        'use_path_style_endpoint' => false,
        'credentials' => [
          'key'    => Config::get('filesystems.disks.spaces.key'),
          'secret' => Config::get('filesystems.disks.spaces.secret'),
        ],
      ]);

      $video = $file;
      $ext = $video->getClientOriginalExtension();
      $videoContents = file_get_contents($video->path());

      $folder = Carbon::now()->format('m-Y');
      $filename = $folder.'/'.$path.'/'.time().'.'.$ext;
      
			if($content  == 'video'){
				$ContentType = 'video/' . $ext;
			}else{
				$ContentType = 'image/' . $ext;
			}

      $result = $client->putObject([
        'Bucket' => 'oyehoyebridalhouses',
        'Key'    => $filename,
        'Body'   => $videoContents,
        'ACL'    => 'public-read',
        'ContentType' => $ContentType,
      ]);

      $ext = $file->getClientOriginalExtension();
      $path = Config::get('filesystems.disks.spaces.endpoint');
      $upload_path = $path.'/'.$filename;
      return $upload_path;
    }

    // // delete previous image
    public static function delete_previous_image($pre_file)
    {
        $path = str_replace(url('/').'/' , "", $pre_file);
        if(File::exists($path))
        {
            File::delete($path);
        }
    }

    public static function delete_previous_image_digital_ocean($pre_file)
    {
      $path = Config::get('filesystems.disks.spaces.endpoint');
      if (strpos($pre_file, $path) === 0) {
        $client = new S3Client([
          'version' => 'latest',
          'region'  => 'nyc3',
          'endpoint' => 'https://nyc3.digitaloceanspaces.com',
          'use_path_style_endpoint' => false,
          'credentials' => [
            'key'    => Config::get('filesystems.disks.spaces.key'),
            'secret' => Config::get('filesystems.disks.spaces.secret'),
          ],
        ]);

        $fileKey = str_replace($path.'/', '', $pre_file);
        $result = $client->deleteObject([
          'Bucket' => Config::get('filesystems.disks.spaces.bucket'),
          'Key'    => $fileKey,
        ]);
        // if ($result['@metadata']['statusCode'] === 204) {
        //   // Deletion successful
        //   return response()->json(['success' => 'File deleted successfully.'], 200);
        // } else {
        //     // Deletion failed
        //     return response()->json(['error' => 'Failed to delete file.'], 500);
        // }
      }
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


    public static function advance_payment_status($id)
    {
        if($id == 0){
            return '<span class="font-weight-bold text-primary">Pending';
        }
        if($id == 1){
            return '<span class="font-weight-bold text-success">Approved</span>';
        }
        if($id == 2){
            return '<span class="font-weight-bold text-danger">Rejected</span>';
        }
    }

    public static function advance_payment_status_text($id)
    {
        if($id == 0){
            return 'Pending';
        }
        if($id == 1){
            return 'Approved';
        }
        if($id == 2){
            return 'Rejected';
        }
    }

    public static function emoji_icon($id)
    {
        $arr = [
            "sad" => "😞",
            "happy" => "😊",
            "smile" => "😍",

            "problem" => "😞",
            "cancel" => "😞",
            "rejected" => "😞",
            "pending" => "😊",
            "approved" => "😍",
        ];

        $id = strtolower($id);
        if(isset($arr[$id])){
            return $arr[$id];
        }else{
            return '';
        }
    }

    public static function callApi($url, $headers, $fields){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Error: ' . curl_error($ch));
        }
        
        curl_close($ch);
        
        // Output result for debugging
        // echo $result;
    }
}