<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Product;

class MainUserHomeController extends Controller
{
    public function index(Request $req)
    {
        return view('users.index');
    }
}
