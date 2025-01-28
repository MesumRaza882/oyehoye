<?php



use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\ImReseller\V1\AuthController;
use App\Http\Controllers\Api\ImReseller\V1\HomeController;
use App\Http\Controllers\Api\ImReseller\V1\ProductController;
use App\Http\Controllers\Api\ImReseller\V1\CategoryController;


// no need for auth
Route::controller(AuthController::class)->group(function(){
  Route::post('send-otp','send_otp');
  Route::post('verify-otp','verify_otp');
});

Route::controller(HomeController::class)->group(function(){
  Route::get('home','home');
  Route::get('sell-all-item-group/{type}','sell_all_item_group');
});

Route::controller(ProductController::class)->group(function(){
  Route::get('products/new-arrivals','new_arrivals');
  Route::get('products/out-of-stock','out_of_stock');
  Route::get('products/offers','offers');
});

Route::controller(CategoryController::class)->group(function(){
  Route::get('categories','categories');
  Route::get('categories/top','top_categories');
});


// need auth fir routs
// Route::middleware('auth:sanctum')->group(function () {
  Route::controller(AuthController::class)->group(function(){
    Route::post('logout','logout');
    Route::post('profile/update','profile_update');
  });
// });



