<?php

namespace App\Repositories;

use App\Models\Product;
use App\Models\Category;

class ProductRepository
{
    public function getAllProducts($request)
    {
        $paginate_record = $request->records ?: 15;
        $status = $request->status ?: 1;
        $status_app = $request->status_app ?: 1;
        $product_upload_for = $request->product_upload_for ?: 1;

        $categories = Category::select(['id', 'name'])->get();

        $query = Product::latest()->with('itemcategory:id,name')
            ->select([
                'id',
                'name',
                'price',
                'video',
                'category_id',
                'profit',
                'is_active_row',
                'increase_perMin',
                'purchase',
                'thumbnail',
                'soldItem',
                'updated_at',
                'soldstatus',
                'markeetItem',
                'discount',
                'is_freez_item',
            ])
            ->when($request->article, function ($q) use ($request) {
                return $q->where('article', $request->article);
            })
            ->when($request->product_upload_for, function ($q) use ($product_upload_for) {
                return $q->where('product_upload_for', $product_upload_for);
            })
            ->when($request->search, function ($q) use ($request) {
                return $q->where('name', 'LIKE', "%{$request->search}%");
            })
            ->filterStatus($status)
            ->filterStatusApp($status_app)
            ->withoutGlobalScope('unfreezed')
            ->where(function ($q) use ($request) {
                $q->when($request->search_input, function ($query) use ($request) {
                    $query->where('name', 'LIKE', "%{$request->search_input}%")
                        ->orWhere('price', 'LIKE', "%{$request->search_input}%");
                });
            })
            ->whereHas('itemcategory', function ($q) use ($request) {
                $q->when($request->category, function ($query) use ($request) {
                    $query->where('name', 'LIKE', "%{$request->category}%");
                });
            });
        $items = $query->paginate($paginate_record);

        return compact('items', 'categories');
    }
}
