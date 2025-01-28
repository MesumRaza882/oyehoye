<?php

use App\Http\Controllers\User\MainUserHomeController;

// Route::controller(MainUserHomeController::class)->group(function(){
//   Route::get('/','index')->name('user.home');
// });

//Route::get('/', function () {
//  return view('index');
//});

// wao routes 
//Route::group(['prefix' => 'wao'], function () {
	// Route::get('/login',[App\Http\Controllers\WaoController::class,'login'])->name('login'); 

	Route::get('/menu', [App\Http\Controllers\WaoController::class,'index'])->name('home-page');

	Route::get('/register',[App\Http\Controllers\WaoController::class,'register'])->name('register'); 
	Route::get('/web/login',[App\Http\Controllers\WaoController::class,'web_login'])->name('web_login'); 
	Route::get('/admin/login/first',[App\Http\Controllers\WaoController::class,'admin_login_first'])->name('admin_login_first'); 
	Route::post('/web/login/process',[App\Http\Controllers\WaoController::class,'web_login_process'])->name('web_login_process'); 
	Route::get('/web/logout',[App\Http\Controllers\WaoController::class,'web_logout'])->name('web_logout'); 
	Route::get('/user/logout',[App\Http\Controllers\WaoController::class,'user_logout'])->name('user_logout'); 
	Route::post('/register-submit',[App\Http\Controllers\WaoController::class,'RegisterSubmit'])->name('register.submit'); 
	Route::get('/active-stocks', [App\Http\Controllers\WaoController::class,'ActiveStock'])->name('active_stock');
	Route::get('web/profit',[App\Http\Controllers\WaoController::class,'profit'])->name('web.profit');
	Route::get('web/profit/admin-order/load-more',[App\Http\Controllers\WaoController::class,'loadMoreAdminOrder'])->name('web.profit.adminOrderLoadMore');
	Route::get('orders',[App\Http\Controllers\WaoController::class,'Orders'])->name('orders');
	Route::post('order/detail',[App\Http\Controllers\WaoController::class,'OrderDetail'])->name('order.detail');
	Route::post('order/note-submit',[App\Http\Controllers\WaoController::class,'SubmitNote'])->name('order.note');
	Route::post('order/delivery-address/update',[App\Http\Controllers\WaoController::class,'update_delivery_address_order'])->name('order.update.delivery.address');
	Route::get('order/cancel/{order_id}',[App\Http\Controllers\WaoController::class,'cancel_order']);//->name('order.cancel');
	// Route::middleware(['auth:sanctum'])->group(function () {
		Route::get('order/place',[App\Http\Controllers\WaoController::class,'PlaceOrder'])->name('place.order');
		Route::post('order/order_place_new',[App\Http\Controllers\WaoController::class,'order_place_new'])->name('order.place');
		Route::get('order/remove-cart-item',[App\Http\Controllers\WaoController::class,'removeCartItem'])->name('order.remove.cart.item');
		Route::get('order/remove-all-cart-item',[App\Http\Controllers\WaoController::class,'removeAllCartItem'])->name('order.remove.all.cart.item');
	// });
	Route::get('order/complete',[App\Http\Controllers\WaoController::class,'OrderComplete'])->name('order.complete');
	Route::get('complaint/screen',[App\Http\Controllers\WaoController::class,'complaint'])->name('user.complaint');
	Route::post('complaint/submit',[App\Http\Controllers\WaoController::class,'addComplaint'])->name('user.addComplaint');
	Route::get('product/search',[App\Http\Controllers\WaoController::class,'search'])->name('product_search');
	Route::get('profile',[App\Http\Controllers\WaoController::class,'profile'])->name('profile');
	Route::post('profile/update',[App\Http\Controllers\WaoController::class,'profile_update'])->name('wao.profile.update');
	Route::get('locked-items',[App\Http\Controllers\WaoController::class,'LockedItems'])->name('locked_items');
	Route::get('category/product/{id}',[App\Http\Controllers\WaoController::class,'CategoryProducts'])->name('product.category');
	Route::post('search-products',[App\Http\Controllers\WaoController::class,'SearchProducts'])->name('product.search');
	Route::post('locked-item/products',[App\Http\Controllers\WaoController::class,'LockedProducts'])->name('locked_items.product');
	Route::get('customer-reviews',[App\Http\Controllers\WaoController::class,'CustomerReviews'])->name('customer.reviews');
	Route::get('reviews',[App\Http\Controllers\WaoController::class,'reviews'])->name('reviews');
	Route::get('customer/reviews/form',[App\Http\Controllers\WaoController::class,'reviews'])->name('customer.reviews.form');
	Route::post('review-submit',[App\Http\Controllers\WaoController::class,'review_submit'])->name('reviews.submit');
	
	Route::get('/',[App\Http\Controllers\WaoController::class,'NewArrivals'])->name('product.NewArrivals');
	Route::get('/product/{id}',[App\Http\Controllers\WaoController::class,'single_product'])->name('product.product');
	Route::post('/add-to-cart',[App\Http\Controllers\WaoController::class,'add_to_cart'])->name('product.addToCart');

	// ajax
	Route::get('/user/info/by/whatsapp',[App\Http\Controllers\WaoController::class,'user_info_by_whatsapp'])->name('user.info.by.whatsapp');
	Route::prefix('product')->group(function () {
		Route::get('load/more',[App\Http\Controllers\WaoController::class,'loadMoreProducts'])->name('product.loadMoreProducts');
		// Route::get('load/small-products',[App\Http\Controllers\WaoController::class,'SmallProducts'])->name('product.SmallProducts');
		// Route::get('load/medium-products',[App\Http\Controllers\WaoController::class,'MediumProducts'])->name('product.MediumProducts');
	});


	Route::post('/product/update-seller-profit',[App\Http\Controllers\WaoController::class,'update_seller_product_profit'])->name('product.update-seller-profit');

//});

// supportive
Route::get('/supportive/download-video',[App\Http\Controllers\Supportive\VideoController::class,'download_video'])->name('supportive.download-video');
Route::get('/supportive/download-file',[App\Http\Controllers\Supportive\FileDownloadController::class,'download_file'])->name('supportive.download-file');
