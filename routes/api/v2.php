<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V2\AuthController;
use App\Http\Controllers\Api\V2\Home\HomeController;
use App\Http\Controllers\Api\V2\Order\CartController;
use App\Http\Controllers\Api\V2\Order\OrderController;
use App\Http\Controllers\Api\V2\Product\ProductController;
use App\Http\Controllers\Api\V2\Product\ReviewController;

// Route::middleware(['cors'])->group(function () {

// Authentication
Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
    Route::get('cities', 'cities');
    
    Route::get('user/info', 'user_info');
    
    // Authenticte User Route
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('profile', 'profile');
        Route::post('profile/update', 'profile_update');
        Route::put('otp/verify', 'otp_verify');
        Route::post('profile/reseller', 'register_as_reseller');
        Route::get('profile/reseller', 'reseller_profile');
    });
});

// Home
Route::controller(HomeController::class)->group(function () {
    Route::get('admin', 'admin');
    Route::get('home', 'home');
    Route::get('charges', 'charges');
    Route::get('categories', 'categories');
    Route::post('problem/add', 'addProblem');
    Route::get('read/home/message', 'read_home_message');

       // track  web whatsapp/contact search orders
       Route::get('track-orders', 'trackOrders');
});

Route::controller(ProductController::class)->group(function () {
    Route::get('products', 'products');
    Route::get('products/search', 'search_products');
    Route::get('new_arrivals', 'new_arrivals');
    Route::get('active_stock', 'active_stock');
    Route::get('sold_out', 'sold_out');
    Route::get('play_video', 'play_video');
    Route::get('play_video_2', 'play_video_2');
    Route::get('products/reseller', 'reseller_products');
});

Route::controller(ReviewController::class)->group(function () {
    Route::get('reviews', 'reviews');
    Route::post('review/add', 'add_review');
});

Route::middleware('auth:sanctum')->group(function () {

    // Home
    Route::controller(HomeController::class)->group(function () {
        Route::get('locked_folder_items', 'locked_folder_items');

        // message
        // Route::get('raed/home/message','read_home_message');
    });

    // Cart items
    Route::controller(CartController::class)->group(function () {
        /* is using ?*/
        Route::post('add_to_cart', 'add_to_cart');
        Route::post('cart/sync', 'sync_cart');
        Route::post('cart/update/{prod_id}', 'update_cart');
        Route::get('view_cart', 'view_cart');
        Route::post('remove_single_cart_item', 'remove_single_cart_item');
        Route::post('remove_all_cart_items', 'remove_all_cart_items');
    });

    // Orders
    Route::controller(OrderController::class)->group(function () {
        Route::post('order_place_new', 'order_place_new');
        Route::post('order/reattach_payment_proof', 'reattach_payment_proof');
        Route::post('order_place', 'order_place');
        Route::post('order_update', 'order_update');
        Route::get('view_orders', 'view_orders');
        Route::get('orders/in-problem', 'orders_in_problem');
        Route::post('order/re-attempt/{id}', 'mark_reattempted');
        Route::get('single_order', 'single_order');
        Route::post('cancel_order', 'cancel_order');
        Route::post('update_order_detail', 'update_order_detail');
        Route::post('add_order_note', 'add_order_note');
    });
});

Route::controller(OrderController::class)->group(function () {
    Route::post('order/re-attempt/{id}', 'mark_reattempted');
});


// Orders
Route::controller(OrderController::class)->group(function () {
    Route::post('order_place', 'order_place');
});
// });