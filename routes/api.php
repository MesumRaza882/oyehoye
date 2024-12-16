<?php



use Illuminate\Http\Request;

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\ArrivalController;

use App\Http\Controllers\Api\BagController;

use App\Http\Controllers\Api\LuxuryController;

use App\Http\Controllers\Api\RateController;

use App\Http\Controllers\Api\UserController;

use App\Http\Controllers\Api\OrderController;

use App\Http\Controllers\Api\NotificationController;

// use App\Http\Controllers\Api\CahrgeController;




/*profile

|--------------------------------------------------------------------------

| API Routes

|--------------------------------------------------------------------------

|

| Here is where you can register API routes for your application. These

| routes are loaded by the RouteServiceProvider within a group which

| is assigned the "api" middleware group. Enjoy building your API!

|

*/



// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {

    // return $request->user();
// });

    // Guest User
    // Route::middleware(['guest:web'])->group(function(){
    
        Route::post('login',[UserController::class,'login']);
        Route::post('register',[UserController::class,'register']);
        Route::post('create',[UserController::class,'register']);
        Route::get('profile',[UserController::class,'profile']);
        Route::post('updateprofile',[UserController::class,'updateprofile']);
        Route::get('forget_phonenum',[UserController::class,'forget_phonenum']);
        Route::post('resetpassword',[UserController::class,'resetpassword']);
    
    // });

    // Authenticate User
    // Route::middleware(['auth:web'])->group(function(){
        
        
    // });


    Route::group(['prefix' => 'admin'], function()
    
    {
        // Categories
        Route::get('allcategories',[BagController::class,'allcategories']);
        Route::get('category_items',[BagController::class,'category_items']);
    
    
    
        Route::group(['prefix' => 'Arrivals'], function ()
    
        {
            Route::get('viewArrival',[ArrivalController::class,'viewArrival']);
    
        });


    //Bags
    
    // Route::group(['prefix' => 'Bags'], function ()

    // {

    //     Route::get('viewBags',[BagController::class,'viewBags']);

    // });



    //luxury

    // Route::group(['prefix' => 'Luxury'], function ()

    // {
    //     Route::get('viewLuxury',[LuxuryController::class,'viewLuxury']);

    // });
    
    
    

    

    Route::group(['prefix' => 'Rate'], function ()

    {

        // Route::get('viewrates',[RateController::class,'viewrates']);

        Route::post('addrate',[RateController::class,'addrate']);

    });

    //LuckyDraw

    Route::group(['prefix' => 'Lucky'], function ()

    {

        // Route::get('viewlucky',[RateController::class,'viewlucky']);

        Route::post('addlucky',[RateController::class,'addlucky']);

    });

    //problem report

    Route::group(['prefix' => 'Problem'], function ()

    {

        // Route::get('viewproblem',[RateController::class,'viewproblem']);

        Route::post('addproblem',[RateController::class,'addproblem']);

    });

    Route::get('search',[ArrivalController::class,'search']);



    //Product Cart

    Route::group(['prefix' => 'Product'], function ()

    {
        Route::get('viewcart',[OrderController::class,'viewcart']);
        Route::get('allproduct',[OrderController::class,'allproduct']);
        Route::get('orderdetail',[OrderController::class,'orderdetail']);
        Route::post('addcart',[OrderController::class,'addcart']);
        Route::post('removecart',[OrderController::class,'removecart']);
        Route::post('removeallcart',[OrderController::class,'removeAllCart']);

    });



    Route::post('orderPlace',[OrderController::class,'orderPlace']);
    Route::get('viewOrder',[OrderController::class,'viewOrder']);
    Route::get('orderDetial',[OrderController::class,'orderDetial']);
    Route::post('update-order-qty',[OrderController::class,'UpdateOrderQty']);
    //notes

    Route::post('givenote',[OrderController::class,'givenote']);
    Route::post('updateOrder',[OrderController::class,'updateOrder']);
    Route::post('holdOrder',[OrderController::class,'holdOrder']);
    Route::post('cancelOrder',[OrderController::class,'cancelOrder']);














    
    
    //message display and update
    Route::post('updatemessage',[ArrivalController::class,'updatemessage']);
    Route::get('message',[ArrivalController::class,'message']);
    
     //Product Cart
     Route::group(['prefix' => 'Notification'], function ()
     {
         Route::get('arrivalNot',[NotificationController::class,'arrivalNot']);
         Route::post('delarrivalNot',[NotificationController::class,'delarrivalNot']);
          //  Bag Notification
         Route::get('bagNot',[NotificationController::class,'bagNot']);
         Route::post('delbagNot',[NotificationController::class,'delbagNot']);
          //  Luxury Notification
         Route::get('luxuryNot',[NotificationController::class,'luxuryNot']);
         Route::post('delluxuryNot',[NotificationController::class,'delluxuryNot']);
     });
     
     //sold product 
      Route::group(['prefix' => 'sold'], function (){
          
        Route::get('viewsoldpro',[NotificationController::class,'viewsoldpro']);
    });
    
         //single Product Review 
        Route::get('viewSingleProductRev',[NotificationController::class,'viewSingleProductRev']);
        
        // screenshots
        Route::get('screenshots',[ArrivalController::class,'screenshots']);
     
});
