<?php

namespace App\Http\Controllers\Supportive;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Validator;
use Carbon\Carbon;
use Domain;

class FileDownloadController extends Controller
{
  public function download_file(Request $request)
  {

    $file_url = urldecode($request->url);

    // Fetch the file from the URL
    $file_content = file_get_contents($file_url);

    if ($file_content === FALSE) {
        // Handle errors if the file couldn't be fetched
        http_response_code(404);
        echo "File not found or cannot be accessed.";
        exit;
    }

    // Extract file name from URL
    $file_name = basename($file_url);

    // Output headers for file download
    header("Content-Type: application/octet-stream");
    header('Content-Disposition: attachment; filename="' . $file_name . '"');
    header("Content-Length: " . strlen($file_content));

    // Output the file content
    echo $file_content;
    return;


    $imageUrl = urldecode($request->url);
    $newDomain = "newdomain.com";

    // Replace the domain name using regular expressions
    $newUrl = preg_replace('/^(https?:\/\/)?([^\/]+)(.*)/', "$1$newDomain$3", $imageUrl);
    $imagePath = str_replace('https://'.$newDomain.'/', '', $newUrl);
    $file_name = basename($imagePath);
    header("Content-Type: application/octet-stream");
    header('Content-Type: image/jpg');
    header('Content-Disposition: attachment; filename="' . $file_name . '"');
    header("Content-Length: " . filesize($imagePath));

    // Read the file and output it to the browser
    readfile($imagePath);
  }
}
