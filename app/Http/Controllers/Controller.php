<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
  
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function success($data, $msg = 'Successfully', $status = 2, $statusCode = 200)
    {
      return response()->json([
        'status' => $status,
        'message' => $msg,
        'data' => $data,
      ], $statusCode);
    }

    public function success_paginate($data, $msg, $status = 2, $response_code = 200)
    {
      $data = $data->toArray();
      
      return response()->json([
        'st' => $status,
        'status' => $status,
        'msg' => $msg,
        'message' => $msg,
        'data'=> $data['data'],
        'current_page'=> $data['current_page'],
        'last_page'=> $data['last_page'],
        'per_page'=> $data['per_page'],
        'to'=> $data['to'],
        'total'=> $data['total'],
      ]);
    }

    // error message
    public function error($data, $msg = 'Fail', $status = 0, $statusCode = 400)
    {
      return response()->json([
        'status' => $status,
        'message' => $msg,
        'error' => $data,
      ], $statusCode);
    }

}
