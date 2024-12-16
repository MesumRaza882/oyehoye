<?php

namespace App\Http\Controllers\Admin\Article;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Product, Order, Order_item};
use Validator;
use Carbon\Carbon;

class ArticleController extends Controller
{
    //viewarticle
    function viewarticle(Request $req)
    {
        $articles = Product::latest()
            ->select(['id', 'name', 'article', 'thumbnail', 'soldstatus', 'soldItem', 'soldstatus', 'markeetPickup'])
            ->orderByRaw("pinned_at Desc")
            ->withSum('orderItemsPending', 'qty')
            ->withSum('orderItemsDispatchedDelivered', 'qty')
            ->paginate(20);
        return view('admin.products.viewarticle', compact('articles'));
    }

    function markeetPickupQty(Request $req)
    {
        $articles = Product::latest()
            ->select(['id', 'name', 'article', 'thumbnail', 'markeetPickup'])
            ->where('markeetPickup', '>', 0)
            ->paginate(20);
        return view('admin.products.markeetPickup', compact('articles'));
    }

    public function markeetPickupQtyReset($id)
    {
        // Retrieve all products where markeetPickup > 0
        $productsToUpdate = Product::where('markeetPickup', '>', 0)->get();

        // Loop through each retrieved product and update markeetPickup to 0
        foreach ($productsToUpdate as $product) {
            $product->markeetPickup = 0;
            $product->save();
        }

        return redirect()->back()->with('message','All Products Markeet Pickup Reset successfully');
    }
}
