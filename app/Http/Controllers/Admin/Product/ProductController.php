<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Product, Cart, category, Admin, LockedkFolderPassword, ResellerSetting};
use Carbon\Carbon;
use App\Helpers\Helper;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Http\Requests\{ProductStoreRule, ProductUpdateRole};
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use App\Repositories\ProductRepository;

class ProductController extends Controller
{

    protected $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function add()
    {
        $categories = category::get(['id', 'name']);
        $partners = Admin::where('role', 3)->where('is_partner', 1)->get(['id', 'name']);
        $app_resellers = Admin::where('type', 3)->where('role', 3)->get(['id', 'name']);

        $randomValues = $this->getRandomValues(null);

        return view('admin.products.create', compact('categories', 'partners', 'app_resellers', 'randomValues'));
    }

    function save(ProductStoreRule $request)
    {
        try {

            DB::beginTransaction();
            // Log::info('Starting product save process');

            // Determine product status
            $productStatus = $request->has('product_status') && $request->input('product_status') === 'on' ? 'published' : 'draft';
            $data = array_merge($request->except('video', 'thumbnail', 'resellers'), ['product_status' => $productStatus]);

            // Create the product
            $product = Product::create($data);

            // Log::info('Product created with ID: ' . $product->id);

            // Get reseller settings data
            $resellerSettingsData = $this->getResellerSettingsData($request, $product, 'save');

            // Perform bulk insert into ResellerSetting model
            ResellerSetting::insert($resellerSettingsData);

            // Log::info('Reseller settings inserted');

            Session::flash('newlyAddedProduct', $product->id);

            // Handle video upload
            if ($request->file('video')) {
                $product->video = Helper::upload_digital_ocean($request->file('video'), 'video', 'video');
                $product->pinned_at = Carbon::now();
                $product->for_notification = 1;
                // Log::info('Video uploaded');
            }

            // Handle thumbnail upload
            if ($request->file('thumbnail')) {
                $product->thumbnail = Helper::upload_digital_ocean($request->file('thumbnail'), 'thumbnail', 'image');
                // Log::info('thumbail uploaded');
            }

            $product->save();
            // Log::info('Product saved');

            DB::commit();
            // Log::info('Transaction committed');
            return redirect()->back()->with('message', 'New Product Added Successfully...');
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }


    // Private function to get reseller settings data
    private function getResellerSettingsData($request, $product, $action)
    {
        // Define the product upload condition
        $product_upload_for = $request->product_upload_for;

        // Get relevant admins based on upload condition
        $admins = Admin::when($product_upload_for === '1', function ($query) {
            $query->whereIn('role', [1, 3]);
        })
            ->when($product_upload_for === '2', function ($query) {
                $query->where('id', 1);
            })
            ->when($product_upload_for === '3', function ($query) {
                $query->where('role', 3)->where('is_partner', null);
            })
            ->when($product_upload_for === '6', function ($query) {
                $query->where('role', 3)->where('is_partner', 1);
            })
            ->when($product_upload_for === '4', function ($query) {
                $query->where('role', 1)->where('id', '!=', 1);
            })
            ->when($product_upload_for === '5', function ($query) {
                $query->whereIn('role', [1, 3])->where('is_partner', 1);
            })
            ->get(['id', 'name', 'product_upload_status', 'role', 'type', 'is_partner']);

        // Prepare specific reseller profit data
        $specificResellerProfit = $request->input('specific_reseller_profit');
        $resellers = $request->input('resellers', []);

        // Create an array to hold reseller settings data
        $resellerSettingsData = [];

        // Populate the array with data for each admin
        foreach ($admins as $admin) {
            $isSpecificProfit = in_array($admin->id, $resellers) ? 1 : 0;
            $profit = $isSpecificProfit && $specificResellerProfit ? $specificResellerProfit : $product->profit;

            $resellerSettingsData[] = [
                'prod_id' => $product->id,
                'admin_id' => $admin->id,
                'price' => ($admin->role === 3 && $admin->is_partner != 1) ? ($request->input('reseller_price') ?? $product->price) : $product->price,
                'profit' => $profit,
                'product_upload_status' => $action === 'update' && ($admin->role === 3 && $admin->is_partner != 1) ? 'draft' : $admin->product_upload_status,
                'is_specific_profit' => $isSpecificProfit,
                'for_app_reseller' => $request->has('app_resellers') && in_array($admin->id, $request->input('app_resellers')) ? 1 : 0,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        return $resellerSettingsData;
    }


    // repush to reseller settings
    function re_push(Request $request, $id)
    {

        try {
            DB::beginTransaction();

            $product = Product::findorFail($id);

            // delete product related admin setting
            ResellerSetting::where('prod_id', $product->id)->delete();

            $request = $request->merge([
                'product_upload_for' => '',
                'specific_reseller_profit' => 0,
            ]);
            // Get reseller settings data
            $resellerSettingsData = $this->getResellerSettingsData($request, $product, 'update');

            // Perform bulk insert into ResellerSetting model
            ResellerSetting::insert($resellerSettingsData);

            DB::commit();
            session()->put('message', 'Product Updated Successfully');
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    // update
    function update(ProductUpdateRole $request, $id)
    {

        try {
            DB::beginTransaction();

            $product = Product::findorFail($id);
            $productStatus = $request->has('product_status') && $request->input('product_status') === 'on' ? 'published' : 'draft';
            Product::where('id', $id)->update(array_merge($request->except('_token', 'resellers', 'app_resellers'), ['product_status' => $productStatus]));

            if ($request->hasFile('video')) {
                Helper::delete_previous_image($product->video);
                Helper::delete_previous_image_digital_ocean($product->video);
                $product->video = Helper::upload_image($request->file('video'), 'video');
                $product->save();
            }

            if ($request->hasFile('thumbnail')) {
                Helper::delete_previous_image($product->thumbnail);
                $product->thumbnail = Helper::upload_image($request->file('thumbnail'), 'video/thumbnail');
                $product->save();
            }

            // delete product related admin setting
            ResellerSetting::where('prod_id', $product->id)->delete();

            // Get reseller settings data
            $resellerSettingsData = $this->getResellerSettingsData($request, $product, 'update');

            // Perform bulk insert into ResellerSetting model
            ResellerSetting::insert($resellerSettingsData);

            DB::commit();
            session()->put('message', 'Product Updated Successfully');
            session()->put('end_time',  Carbon::now()->addSecond(5));
            echo '<script type="text/javascript">', 'history.go(-2);', '</script>';
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }


    function all(Request $request)
    {
        $data = $this->productRepository->getAllProducts($request);
        return view('admin.products.index', $data);
    }
    // view single product
    function edit($id)
    {
        $product = Product::with('prodReviews', 'SingproductRev', 'resellerSpecificProduct')->where('id', $id)->first();
        $categories = category::get(['id', 'name']);
        $partners =  Admin::where('role', 3)->where('is_partner', 1)->get(['id', 'name']);
        $app_resellers =  Admin::where('type', 3)->where('role', 3)->get(['id', 'name']);

        $randomValues = $this->getRandomValues($product);

        // active row when click
        if ($already_active = Product::where('is_active_row', 1)->where('id', '!=', $product->id)->first()) {
            $already_active->is_active_row = 0;
            $already_active->timestamps = false;
            $already_active->save();
        }
        $product->is_active_row = 1;
        $product->timestamps = false;
        $product->save();
        return view('admin.products.edit', compact('product', 'categories', 'partners', 'app_resellers', 'randomValues'));
    }


    // soldStatus of product
    public function mark_as_sold(Request $req)
    {
        $product = Product::find($req->id);

        if ($product->soldstatus == 0) {
            $product->soldstatus = 1;
            $product->save();
            return response()->json([
                'check_num' => 100,
            ]);
        }
        $product->soldstatus = 0;
        $product->save();
        return response()->json([
            'check_num' => 200,
        ]);
    }

    //delete checked Problems
    public function delete_checked_products(Request $req)
    {
        $ids = $req->ids;
        $items = Product::whereIn('id', $ids)->get();
        if ($items) {
            foreach ($items as $item) {
                if ($item->video) {
                    Helper::delete_previous_image($item->video);
                }
                if ($item->thumbnail) {
                    Helper::delete_previous_image($item->thumbnail);
                }
                $cart = Cart::where('prod_id', $item->id)->get();
                if ($cart) {
                    $cart->each->delete();
                }
                // $order_items = Order_item::where('prod_id',$item->id)->get();
                // if($order_items){ $order_items->each->delete(); }
                $item->delete();
            }

            return response()->json([
                'status' => "Products Delete Successfully",
            ]);
        } else {
            return response()->json([
                'status' => "Products Not deleted",
            ]);
        }
    }

    //add to pinned products
    public function pinned_checked_products(Request $req)
    {
        $ids = $req->ids;
        $items = Product::whereIn('id', $ids)->get();

        foreach ($items as $item) {
            $item->pinned_at = Carbon::now();
            $item->save();
        }
        return response()->json(['status' => "Pin to Start Successfully"]);
    }

    //add to white list
    public function whiteItems_checked_products(Request $req)
    {
        $ids = $req->ids;
        $dataValue = $req->input('dataValue');
        $items = Product::whereIn('id', $ids)->get();

        foreach ($items as $item) {
            if ($dataValue === 'multanItems') {
                $item->is_multan_list = 1;
            } else {
                $item->is_white_list = 1;
            }
            $item->save();
        }
        return response()->json(['status' => "Added to List Successfully"]);
    }

    //add to published items
    public function published_products(Request $req)
    {
        $ids = $req->ids;
        $items = Product::whereIn('id', $ids)->get();

        foreach ($items as $item) {
            $item->product_status = 'published';
            $item->save();
        }
        return response()->json(['status' => "Added to Published Successfully"]);
    }

    public function freezUnfreezItems(Request $req)
    {
        $ids = $req->ids;
        $items = Product::withoutGlobalScope('unfreezed')->whereIn('id', $ids)->get();

        foreach ($items as $item) {
            $item->is_freez_item = $req->action === 'freez' ? 1 : Null;
            $item->save();
        }
        return response()->json(['status' => "Added to Published Successfully"]);
    }

    // whiteItems_delete
    public function whiteItems_delete(Request $request)
    {
        $whiteListProducts = Product::where('is_white_list', 1)->get();
        $whiteListProducts->each->update(
            ['is_white_list' => null]
        );
        return $this->success('White List Products Removed Successfully', 'White List Products Removed Successfully', 2);
    }

    public function updateQuantities(Request $request)
    {
        $products = $request->input('products');

        // Update product quantities in the database
        foreach ($products as $product) {

            $productId = $product['id'];
            $quantity = isset($product['quantity']) ? $product['quantity'] : 0;
            $marketItem = isset($product['marketItem']) ? $product['marketItem'] : 0;
            $markeetPickup = isset($product['markeetPickup']) ? $product['markeetPickup'] : 0;
            $discountItem = isset($product['discountItem']) ? $product['discountItem'] : 0;

            $productModel = Product::find($productId);
            if ($request->action === "resetPickup") {
                $productModel->update([
                    'markeetPickup' => $markeetPickup >= 0 ? $markeetPickup : $productModel->markeetPickup,
                ]);
            } else {
                // Update the quantity
                $productModel->update([
                    'soldItem' => $quantity >= 0 ? $quantity : $productModel->soldItem,
                    'markeetItem' => $marketItem >= 0 ? $marketItem : $productModel->markeetItem,
                    'discount' => $discountItem >= 0 ? $discountItem : $productModel->discount,
                ]);
            }
        }
        return $this->success([], 'Product quantities updated successfully', 2);
    }

    // get random values for fake sold items and start fake sold items input
    private function getRandomValues($product)
    {
        $product_fake_range = LockedkFolderPassword::where('text_password', 'update_product_fake_sold_range')->first();

        $stop_increasing_after_qty_start = $product_fake_range->stop_increasing_after_qty_start;
        $stop_increasing_after_qty_end = $product_fake_range->stop_increasing_after_qty_end;
        $start_from_items_start = $product_fake_range->start_from_items_start;
        $start_from_items_end = $product_fake_range->start_from_items_end;

        // Determine the value for randomStopFakeAfterQuantity
        if ($stop_increasing_after_qty_start == 0 || $stop_increasing_after_qty_end == 0) {
            $randomStopFakeAfterQuantity = 0;
        } else {
            $randomStopFakeAfterQuantity = rand($stop_increasing_after_qty_start, $stop_increasing_after_qty_end);
        }

        // Determine the value for randomStartFromFakeItems
        if ($start_from_items_start == 0 || $start_from_items_end == 0) {
            $randomStartFromFakeItems = 0;
        } else {
            $randomStartFromFakeItems = rand($start_from_items_start, $start_from_items_end);
        }

        return [
            'randomStopFakeAfterQuantity' => $product ? $product->stop_fake_after_quantity : $randomStopFakeAfterQuantity,
            'randomStartFromFakeItems' => $product ? $product->soldAdm :  $randomStartFromFakeItems,
        ];
    }
}
