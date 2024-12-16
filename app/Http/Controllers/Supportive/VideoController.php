<?php

namespace App\Http\Controllers\Supportive;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Validator;
use Carbon\Carbon;
use Domain;

class VideoController extends Controller
{
  public function download_video(Request $request)
  {

    // $file_url = 'https://oyehoyebridalhouses.nyc3.cdn.digitaloceanspaces.com/03-2024/video/1711869285.mp4';
    $file_url = urldecode($request->url);
    
    // if(Domain::admin('mute_video') == 1){ // 2 for not mute
      $unique_string = md5(uniqid());
      $name = $unique_string.time();
      $ext = explode('.', $file_url);
      $ext = end($ext);
      $file_name = "temp/$name.$ext";

      file_put_contents($file_name, file_get_contents($file_url));
      $file_url = $file_name;
      $ffmpeg_command = "ffmpeg -i $file_url -an -c:v copy -y temp_$file_url 2>&1";
      exec($ffmpeg_command, $output, $return_code);
      // echo "FFmpeg Output: ";
      // print_r($output);
      // echo "<br>";
      // echo "Return Code: $return_code<br>";
      
      if ($return_code === 0) {
        $file_url = "temp_$file_url";
      } else {
        die("FFmpeg command failed. Please check if FFmpeg is installed and the file URL is correct.");
      }
      $delete_file = 1;
    // }else{
    //   $file_name = basename($file_url);
    //   $delete_file = 0;
    // }

    header('Content-Description: File Transfer');
    header('Content-Type: video/mp4');
    header('Content-Disposition: attachment; filename="' . $file_name . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    readfile($file_url);

    if($delete_file == 1){
      unlink($file_name);
      unlink($file_url);        
    }
  }
}
