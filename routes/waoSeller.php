<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\WaoInventory\{WaoInventoryController, WaoInventoryOrderController};
use App\Http\Controllers\{ResellerController};
use App\Http\Controllers\Admin\Seller\MainController;

/** Here start wao seller work 
 * 1-add seller account , inventory and manage by superadmin
 * 2-seller can perform their work
 */

//   super admin
Route::middleware(['auth:admin'])->group(function () {
    Route::group(['prefix' => 'admin'], function () {
        Route::group(['middleware' => ['superadmin']], function () {

            // wao inventory 
            Route::group(['prefix' => 'seller/inventory'], function () {
                Route::controller(WaoInventoryController::class)->group(function () {
                    Route::get('index', 'index')->name('inventory.index');
                    Route::post('store', 'store')->name('inventory.store');
                    Route::post('minusInventory', 'minusInventory')->name('inventory.minusInventory');
                    Route::post('delete', 'destroy')->name('inventory.delete');

                    // inventory user
                    // Route::get('seller', 'sellerList')->name('inventory.seller.index');
                    // Route::post('seller/store', 'sellerStore')->name('inventory.seller.store');
                    // Route::get('seller/edit/{id}', 'sellerEdit')->name('inventory.seller.edit');
                    // Route::post('seller/update/{id}', 'sellerUpdate')->name('inventory.seller.update');
                    // Route::post('seller/delete', 'destroySeller')->name('inventory.seller.delete');
                });
            });
        });

        // admin and partner can acsess
        Route::group(['prefix' => 'seller/inventory'], function () {
            Route::controller(WaoInventoryController::class)->group(function () {
                Route::get('seller', 'sellerList')->name('inventory.seller.index');
                Route::post('seller/store', 'sellerStore')->name('inventory.seller.store');
                Route::get('seller/edit/{id}', 'sellerEdit')->name('inventory.seller.edit');
                Route::post('seller/update/{id}', 'sellerUpdate')->name('inventory.seller.update');
                Route::post('seller/delete', 'destroySeller')->name('inventory.seller.delete');
                Route::get('seller/renew-ssl', 'renew_ssl')->name('inventory.seller.renew_ssl');
                Route::get('seller/add-domain', 'add_domain');
            });
        });


        // wao inventory Order Submit Trax 
        Route::group(['prefix' => 'seller/order'], function () {
            Route::controller(WaoInventoryOrderController::class)->group(function () {
                Route::get('index', 'index')->name('waoseller.order.index');
                Route::get('history', 'history')->name('waoseller.order.history');
                Route::get('create', 'create')->name('waoseller.order.create');
                Route::post('store', 'store')->name('waoseller.order.store');
                Route::post('storeMnp', 'storeMnp')->name('waoseller.order.storeMnp');
                Route::post('storeWareHouse', 'storeWareHouse')->name('waoseller.order.storeWareHouse');
                Route::post('storePostEx', 'storePostEx')->name('waoseller.order.storePostEx');
                Route::get('calculate-totals', 'calculateTotals')->name('waoseller.order.calculateTotals');
                Route::get('search_whiteList', 'search_whiteList')->name('waoseller.search_whiteList');
                // slip image store
                Route::post('slip/store', 'slipStore')->name('waoseller.slip.store');
                Route::post('/update-multan-list', 'updateMultanList')->name('waoseller.update_multan_list');

            });
        });

        // Reseller Products manage
        // wao inventory Order Submit Trax 
        Route::group(['prefix' => 'seller/profile'], function () {
            Route::controller(ResellerController::class)->group(function () {
                Route::get('products', 'products')->name('waoseller.products');
                Route::get('products/edit/{id}', 'productsEdit')->name('waoseller.products.edit');
                Route::post('products/update/{id}', 'productsUpdate')->name('waoseller.products.update');
                // balance history
                Route::get('balance/history', 'balanceHistory')->name('waoseller.balance.history');
            });
        });

        // Reseller website manage 
        Route::group(['prefix' => 'seller'], function () {
            Route::controller(MainController::class)->group(function () {
                Route::get('getUsers', 'getUsers')->name('waoseller.getUsers');
                Route::get('editUser/{id}', 'editUser')->name('waoseller.editUser');
                Route::post('updateUser', 'updateUser')->name('waoseller.updateUser');
                // web orders
                Route::get('getWebOrders', 'getWebOrders')->name('waoseller.getWebOrders');
                Route::get('editWebOrder/{id}', 'editOrder')->name('waoseller.editOrder');
                Route::post('updateWebOrder/{id}', 'updateWebOrder')->name('waoseller.updateWebOrder');
            });
        });
    });
});
