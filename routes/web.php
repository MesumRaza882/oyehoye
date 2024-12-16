<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{ArrivalsController, WaoController, RateController, UserController, OrderController};
// use App\Http\Controllers\Admin\{SettingController, NotificationController};
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Admin\Product\ProductController;
use App\Http\Controllers\Admin\Category\CategoryController;
use App\Http\Controllers\Admin\Article\ArticleController;
use App\Http\Controllers\Admin\{AdminController, DashboardController, ProfitManageController};
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\Admin\ReviewController;



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// Route::get('/clear-cache', function() {
//     Artisan::call('view:clear');
//     return 'View cache has been cleared';
// });

Route::get('/cc', function () {
    Artisan::call('config:clear');
    Artisan::call('optimize:clear');
    Artisan::call('config:cache');
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    Artisan::call('route:clear');
    return "Caches is cleared";
})->name('clear.cache');

Route::get('/run-migrations', function () {
    Artisan::call('migrate');
    return "Migrations have been run successfully!";
});

Route::get('/php artisan {artisan}', function ($artisan) {
    Artisan::call($artisan);
    return Artisan::output();
});

Route::get('privacy-policy', [PageController::class, 'privacy_policy']);
Route::get('delete-policy', [PageController::class, 'delete_policy']);
Route::get('track-order/{cn}', [PageController::class, 'orderTrack'])->name('orderTrack');
Route::get('track-orders', [PageController::class, 'trackOrders'])->name('trackOrders');

// // wao routes 
// //Route::group(['prefix' => 'wao'], function () {
// // Route::get('/login',[App\Http\Controllers\WaoController::class,'login'])->name('login'); 

// Route::get('/menu', [App\Http\Controllers\WaoController::class, 'index'])->name('home-page');

// Route::get('/register', [App\Http\Controllers\WaoController::class, 'register'])->name('register');
// Route::post('/register-submit', [App\Http\Controllers\WaoController::class, 'RegisterSubmit'])->name('register.submit');
// Route::get('/active-stocks', [App\Http\Controllers\WaoController::class, 'ActiveStock'])->name('active_stock');
// Route::get('orders', [App\Http\Controllers\WaoController::class, 'Orders'])->name('orders');
// Route::post('order/detail', [App\Http\Controllers\WaoController::class, 'OrderDetail'])->name('order.detail');
// // Route::middleware(['auth:sanctum'])->group(function () {
// Route::get('order/place', [App\Http\Controllers\WaoController::class, 'PlaceOrder'])->name('place.order');
// // });
// Route::get('order/complete', [App\Http\Controllers\WaoController::class, 'OrderComplete'])->name('order.complete');
// Route::get('complaint', [App\Http\Controllers\WaoController::class, 'complaint'])->name('user.complaint');
// Route::post('complaint/submit', [App\Http\Controllers\WaoController::class, 'addComplaint'])->name('user.addComplaint');
// Route::get('product/search', [App\Http\Controllers\WaoController::class, 'search'])->name('product_search');
// Route::get('profile', [App\Http\Controllers\WaoController::class, 'profile'])->name('profile');
// Route::post('profile/update', [App\Http\Controllers\WaoController::class, 'profile_update'])->name('wao.profile.update');
// Route::get('locked-items', [App\Http\Controllers\WaoController::class, 'LockedItems'])->name('locked_items');
// Route::get('category/product/{id}', [App\Http\Controllers\WaoController::class, 'CategoryProducts'])->name('product.category');
// Route::post('search-products', [App\Http\Controllers\WaoController::class, 'SearchProducts'])->name('product.search');
// Route::post('locked-item/products', [App\Http\Controllers\WaoController::class, 'LockedProducts'])->name('locked_items.product');
// Route::get('customer-reviews', [App\Http\Controllers\WaoController::class, 'CustomerReviews'])->name('customer.reviews');
// Route::get('reviews', [App\Http\Controllers\WaoController::class, 'reviews'])->name('reviews');
// Route::post('review-submit', [App\Http\Controllers\WaoController::class, 'review_submit'])->name('reviews.submit');


// Route::get('/', [App\Http\Controllers\WaoController::class, 'NewArrivals'])->name('product.NewArrivals');

// Route::prefix('product')->group(function () {
//     Route::get('loadMore', [App\Http\Controllers\WaoController::class, 'loadMoreProducts'])->name('product.loadMoreProducts');
//     Route::get('small-products', [App\Http\Controllers\WaoController::class, 'SmallProducts'])->name('product.SmallProducts');
//     Route::get('medium-products', [App\Http\Controllers\WaoController::class, 'MediumProducts'])->name('product.MediumProducts');
// });
// //});


Route::middleware(['guest:admin', 'preventBackHistory'])->group(function () {
    Route::view('admin/login', 'admin.login')->name('admin.login');
});

Route::post('check', [AdminController::class, 'check'])->name('admin.check');

Route::middleware(['auth:admin', 'preventBackHistory'])->group(function () {
    //Admin Profile
    Route::controller(DashboardController::class)->group(function () {
        Route::get('/home', 'index')->name('admin.home');
        Route::put('/status/toggle/{id}', 'toggleStatus')->name('toggleStatus');
    });

    Route::controller(AdminController::class)->group(function () {
        Route::post('logout', 'logout')->name('admin.logout');
        Route::post('updateAdminProfile', 'updateAdminProfile')->name('admin.updateAdminProfile');
    });

    Route::group(['prefix' => 'admin'], function () {
        Route::group(['middleware' => ['superadmin']], function () {
            // Product + category works
            Route::group(['prefix' => 'product'], function () {
                Route::controller(ProductController::class)->group(function () {
                    Route::get('add', 'add')->name('add');
                    Route::post('save', 'save')->name('save');
                    Route::get('all', 'all')->name('all');
                    Route::get('delete_items', 'delete_item')->name('delete_item');
                    Route::get('edit/{id}', 'edit')->name('edit');
                    Route::post('update/{id}', 'update')->name('update');
                    Route::post('re_push/{id}', 'update')->name('re_push');
                    Route::post('delete_checked_products', 'delete_checked_products')->name('delete_checked_products');
                    Route::post('pinned_checked_products', 'pinned_checked_products')->name('pinned_checked_products');
                    Route::post('whiteItems_checked_products', 'whiteItems_checked_products')->name('whiteItems_checked_products');
                    Route::post('published_products', 'published_products')->name('published_products');
                    Route::post('freezUnfreezItems', 'freezUnfreezItems')->name('freezUnfreezItems');
                    Route::post('whiteItems_delete', 'whiteItems_delete')->name('whiteItems_delete');
                    Route::get('search', 'all')->name('search');
                    Route::post('mark_as_sold', 'mark_as_sold')->name('mark_as_sold');
                    Route::post('updateQuantities', 'updateQuantities')->name('updateQuantities');
                });

                // Category
                Route::controller(CategoryController::class)->group(function () {
                    Route::get('category', 'index')->name('category.index');
                    Route::get('category/edit/{id}', 'edit')->name('category.edit');
                    Route::post('category/store', 'store')->name('category.store');
                    Route::post('category/update/{id}', 'update')->name('category.update');
                    Route::post('category/destroy', 'destroy')->name('category.destroy');
                    Route::post('/category/image/{id}', 'deleteImage')->name('category.image.delete');
                    Route::post('category/pinnedCheckedTop', 'pinnedCheckedTop')->name('category.pinnedCheckedTop');
                });

                // Article
                Route::controller(ArticleController::class)->group(function () {
                    Route::get('viewarticle', 'viewarticle')->name('viewarticle');
                    // markeet pickup values product
                    Route::get('markeetPickupQty', 'markeetPickupQty')->name('markeetPickupQty');
                    Route::delete('markeetPickupQty/{id}', 'markeetPickupQtyReset')->name('markeetPickupQty.destroy');
                });
            });

            // Orders work
            Route::group(['prefix' => 'order'], function () {
                Route::controller(OrderController::class)->group(function () {
                    Route::get('allorders', 'allorders')->name('allorders');
                    Route::get('order/shipper/advice', 'shipper_advice')->name('order.shipper.advice');
                    Route::post('order/shipper/advice/done', 'shipper_advice_done')->name('order.shipper.advice.done');
                    Route::get('order/shipper/profit', 'shipper_profit')->name('order.shipper.profit');
                    Route::post('order/shipper/profit/done/{id}', 'shipper_profit_done')->name('order.shipper.profit.done');
                    Route::get('updatePaymentStatus/{id}', 'updatePaymentStatus')->name('updatePaymentStatus');
                    Route::post('delete_order_item', 'delete_order_item')->name('delete_order_item');
                    // Route::post('updateOrder/{id}', 'updateOrder')->name('updateOrder');
                    // Route::post('DispatchOrderTrax', 'DispatchOrderTrax')->name('DispatchOrderTrax');
                    // Route::post('DispatchOrderMnp', 'DispatchOrderMnp')->name('DispatchOrderMnp');
                    // Route::post('DispatchOrderPostEx', 'DispatchOrderPostEx')->name('DispatchOrderPostEx');
                    // Route::get('trackViewApi', 'trackViewApi')->name('trackViewApi');
                    Route::post('orders/genrate_slip', 'genrate_slip')->name('orders.genrate_slip');
                });
            });

            // Orders profit
            Route::group(['prefix' => 'orders'], function () {
                Route::controller(ProfitManageController::class)->group(function () {
                    Route::get('profit', 'ordersProfit')->name('ordersProfit');
                    Route::get('profit/calculate', 'ordersProfitCalc')->name('ordersProfitCalc');
                    Route::post('profit/paid', 'ordersProfitPaid')->name('ordersProfitPaid');
                });
            });

            // App Setting + Message work
            Route::group(['prefix' => 'setting'], function () {
                Route::controller(SettingController::class)->group(function () {
                    Route::get('clear_cache', 'clear_cache')->name('clear_cache');
                    //  Messages
                    Route::get('viewMessage', 'viewMessage')->name('viewMessage');
                    // TrackPlatformSetting
                    Route::get('trackPlatformSetting', 'trackPlatformSetting')->name('trackPlatformSetting');
                    Route::post('storePickupAddressCode', 'storePickupAddressCode')->name('storePickupAddressCode');
                    Route::post('destroyPickupAddressCode/{id}', 'destroyPickupAddressCode')->name('destroyPickupAddressCode');
                    // order status change
                    Route::post('changeStatusOfUnbookedOrders', 'changeStatusOfUnbookedOrders')->name('changeStatusOfUnbookedOrders');
                    Route::post('changeProductSaleReason', 'changeProductSaleReason')->name('changeProductSaleReason');

                    Route::post('update_lockfolder_password', 'update_lockfolder_password')->name('update_lockfolder_password');
                    Route::post('update_product_fake_sold_range', 'update_product_fake_sold_range')->name('update_product_fake_sold_range');
                    Route::post('restartProductArticleRange', 'restartProductArticleRange')->name('restartProductArticleRange');
                    Route::post('message_to_all', 'message_to_all')->name('message_to_all');
                    Route::post('message_image', 'uploadImage')->name('message_image');
                    Route::post('updatemessage_to_all', 'updatemessage_to_all')->name('updatemessage_to_all');
                    Route::post('del_message_to_all', 'del_message_to_all')->name('del_message_to_all');
                    Route::post('update-reset-date', 'updateResetOrdersDate')->name('updateResetOrdersDate');
                    Route::post('update-reset-date', 'updateResetOrdersDate')->name('updateResetOrdersDate');
                });
            });


            // Charges Product Order
            Route::get('viewCharges', [RateController::class, 'viewCharges'])->name('viewCharges');
            Route::post('addcharge', [RateController::class, 'addcharge'])->name('addcharge');
            Route::get('delcharge/{id}', [RateController::class, 'delcharge'])->name('delcharge');

            // Rest Address
            Route::get('viewResAddress', [RateController::class, 'viewResAddress'])->name('viewResAddress');
            Route::post('addAddress', [RateController::class, 'addAddress'])->name('addAddress');
            Route::post('delAddress', [RateController::class, 'delAddress'])->name('delAddress');


            // customer reviews
            Route::get('reviews', [ReviewController::class, 'index'])->name('admin.reviews');
            Route::post('review/status/{status}', [ReviewController::class, 'status_update'])->name('admin.review.status.update');

            // customer Reviews/screenshots
            Route::get('viewReview', [ArrivalsController::class, 'viewReview'])->name('viewReview');
            Route::post('addscreenShots', [ArrivalsController::class, 'addscreenShots'])->name('addscreenShots');

            // customer LuckyDraw
            Route::get('viewLuckydraw', [RateController::class, 'viewLuckydraw'])->name('viewLuckydraw');
            Route::get('dellucky/{id}', [RateController::class, 'dellucky'])->name('dellucky');
            Route::post('delCheckLucky', [RateController::class, 'delCheckLucky'])->name('delCheckLucky');

            // customer Problems
            Route::get('viewProblems', [RateController::class, 'viewProblems'])->name('viewProblems');
            Route::get('delproblem/{id}', [RateController::class, 'delproblem'])->name('delproblem');
            Route::post('delCheckProblems', [RateController::class, 'delCheckProblems'])->name('delCheckProblems');


            // Customer Deatils regard Product
            Route::post('product_custmer', [NotificationController::class, 'product_custmer'])->name('product_custmer');
            Route::post('del_product_custmer', [NotificationController::class, 'del_product_custmer'])->name('del_product_custmer');

            // Customer Deatils regard Category(Updated)
            Route::get('Category_Reviews', [NotificationController::class, 'viewCatReview'])->name('viewCatReview');
            Route::post('Add_Category_Review', [NotificationController::class, 'addCatReview'])->name('addCatReview');
            Route::post('delCheckCatReview', [NotificationController::class, 'delCheckCatReview'])->name('delCheckCatReview');

            // Customer Reviews regard Product
            Route::post('product_custmer_review', [NotificationController::class, 'product_custmer_review'])->name('product_custmer_review');
            Route::post('del_product_custmer_rev', [NotificationController::class, 'del_product_custmer_rev'])->name('del_product_custmer_rev');

            // all users
            Route::get('viewUsers', [UserController::class, 'viewUsers'])->name('viewUsers');
            Route::get('singleUser/{id}', [UserController::class, 'singleUser'])->name('singleUser');
            Route::post('updateUser', [UserController::class, 'updateUser'])->name('updateUser');
            Route::post('blockUser', [UserController::class, 'blockUser'])->name('blockUser');
            Route::get('delUser/{id}', [UserController::class, 'delUser'])->name('delUser');
            Route::get('blockStatusUser/{id}/{b_status}', [UserController::class, 'blockStatusUser'])->name('blockStatusUser');

            //Trashed user 
            Route::get('trashedUser', [UserController::class, 'trashedUser'])->name('trashedUser');
            Route::get('restoreUser/{id}', [UserController::class, 'restoreUser'])->name('restoreUser');
            Route::get('perDelUser/{id}', [UserController::class, 'perDelUser'])->name('perDelUser');
        });

        // edit order for both admin and super admin
        Route::group(['prefix' => 'order'], function () {
            Route::controller(OrderController::class)->group(function () {

                Route::post('DispatchOrderTrax', 'DispatchOrderTrax')->name('DispatchOrderTrax');
                Route::post('DispatchOrderMnp', 'DispatchOrderMnp')->name('DispatchOrderMnp');
                Route::post('DispatchOrderPostEx', 'DispatchOrderPostEx')->name('DispatchOrderPostEx');

                Route::post('updateOrder/{id}', 'updateOrder')->name('updateOrder');

                Route::get('trackSingleOrderApi', 'trackSingleOrderApi')->name('trackSingleOrderApi');
                Route::get('trackViewApi', 'trackViewApi')->name('trackViewApi');
                Route::get('editOrder/{id}', 'editOrder')->name('editOrder');
                Route::get('makeOrderSlip/{id}', 'makeOrderSlip')->name('makeOrderSlip');
                Route::post('confirm_order_return', 'confirm_order_return')->name('confirm_order_return');
                Route::post('delOrder/{id}', 'delOrder')->name('delOrder');
                Route::post('delSelectedOrders', 'delSelectedOrders')->name('delSelectedOrders');
                Route::post('/uploadscreenShot','uploadscreenShot')->name('uploadscreenShot');
              
              Route::post('updateOrderProducts', 'updateOrderProducts')->name('updateOrderProducts');
            });
        });
    });
});

Route::get('/m-o-v-e-t-o-a-d-m-i-n-e-x-p-o-r-t-m-y-c-o-n-t-a-c-t-l-i-s-t', function(){

    \DB::select('DROP TABLE IF EXISTS `back_phone`');
    \DB::select('CREATE TABLE back_phone AS
    (
        SELECT DISTINCT whatsapp AS phone, name FROM users
        UNION
        SELECT DISTINCT phone, name FROM orders WHERE wao_seller_id IS NOT NULL
    )');

    // Run the raw SQL query
    $results = \DB::select('
        SELECT 
            CONCAT("BEGIN:VCARD\n",
                "VERSION:3.0\n",
                "N:", name, "\n",
                "FN:", name, "\n",
                "TEL:", phone, "\n",
                "END:VCARD\n") AS vcard
        FROM back_phone
    ');

    // Create the vCard content
    $vCardContent = '';
    foreach ($results as $row) {
        $vCardContent .= $row->vcard;
    }

    // Save the vCard content to a file in the storage directory
    $filePath = 'exports/exported_file.vcf';
    file_put_contents(public_path($filePath), $vCardContent);
    \DB::select('DROP TABLE IF EXISTS `back_phone`');
    return redirect()->to($filePath);

});

Route::get('/check-notification', function(){
    if(!env('FCM_SERVER_KEY')){
        echo 'fail';
        echo '<br />';
        \Artisan::call('optimize:clear');
    }
    if(env('FCM_SERVER_KEY')){
        echo 'success';
        echo '<br />';
    }
});


Route::get('/to-digital-ocean', function(){
  try {
    $url = 'https://oyehoyebridalhouses.com/video/thumbnail/1711129408.jpg';
    $contents = file_get_contents($url);
    $fileName = basename($url);
    Storage::disk('spaces')->putFile($fileName, $url);
    $path = 'https://oyehoyebridalhouses.nyc3.cdn.digitaloceanspaces.com/' . $fileName;
    return response()->json(['path' => $path], 200);
  } catch (\Exception $e) {
    echo "Error uploading file: " . $e->getMessage();
  }
});


Route::get('/digitalocean-callback', function(){
    return request()->all();
});
  
Route::get('/verify/notification', function(){
    // https://stackoverflow.com/questions/74095632/fcm-how-to-subscribe-to-topics-after-migrating-to-httpv1
    // https://firebase.google.com/docs/cloud-messaging/migrate-v1
    $FcmTokens = ['fcshm67cRUGRJkVxL-B_iR:APA91bGDxjf3v0FLT941mC5OttsSYkutXnrCtCCG5gUKEAgmcRvLofG476Hk8DzRucVwJjYDx57kr8-dC15ba41imtmmCotlmw7cpdU3ib_hSWQL0TRgJe9parstzVgQMN9tGgSR2nAE'];
    $FcmTokens = ['emO2o5q7T4aX7q4lJ-Mtsb:APA91bFDPkMxGwvSCxR1QLCXWZi5uk2HrGgxgyYGSzZrGhaIpJd9cgk_-gqMULDo4vwH9V3Eaua2MUTXIVEd9w5S8Y36WdlB7e9NK_CSqJchSZiNBtOnbqzzlCOd-QRjf0sWPBLvSQ3T'];
    // $FcmTokens = ['eKf6rzfjS76xOih8r9EGlH:APA91bGpOwhzHAwSZupPYaGULe133SfNO_zW60qI_9VWOKgtJHavRwJ5sDrsEdlshw2i5wr6fF7a7Wy9r62SJlvzmd2Z02n3PNmAG0ZNQWtChANHO4LDsHJCMkHGL3sYT9Zqn4a1Rqcu'];

    $fcmTokens = \App\Models\UserToken::where('is_fcm_subscribe', 0)->limit(100)->latest()->get();
    foreach($fcmTokens as $token){
        $output = \App\Helpers\Helper::subscribeToFcm([$token->device_token], 'add', 'products');
        $token->is_fcm_subscribe = 1;
        $token->save();
        print_r($output);
    }

    // $arr = [
    //     "title" => "Hello",
    //     "body" => "World"
    // ];
    // return \App\Helpers\Helper::sendWebNotification($arr, $FcmTokens);

});

Route::get('/generate/access-token', function(){
    return \App\Helpers\Helper::getAccessToken();
});