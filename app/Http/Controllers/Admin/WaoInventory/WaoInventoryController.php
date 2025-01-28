<?php

namespace App\Http\Controllers\Admin\WaoInventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use App\Models\{Order,TrackPlatformSetting, WaoInventoryRecord, Admin, ResellerAmountHistory, Product, ResellerSetting};
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Helpers\Helper;
use Illuminate\Support\Facades\Crypt;

class WaoInventoryController extends Controller
{

    public function index(Request $request)
    {
        // $records  = WaoInventory::latest();
        // $total_records = $records->count();
        // $inventories = $total_records ? $records->paginate(10) : [];
        $inventoryRecord = WaoInventoryRecord::first();
        $inventoryUsers = Admin::where('role', 3)->count();
        $returAppOrders = Order::where('is_returned_order', 1)->where('wao_seller_id', null)->count();
        $returSellerOrders = Order::where('is_returned_order', 1)->where('wao_seller_id', '!=', null)->count();
        return view('admin.wao_inventory.inventoryIndex', compact('inventoryRecord', 'inventoryUsers', 'returAppOrders', 'returSellerOrders'));
    }

    public function store(Request $request)
    {
        // $currentDate = now()->format('Y-m-d');
        // $existingInventory = WaoInventory::whereDate('created_at', $currentDate)->first();

        // if ($existingInventory) {
        //     $existingInventory->update([
        //         'inventory' => $existingInventory->inventory + $request->inventory,
        //     ]);
        // } else {
        //     $inventoryRecord = WaoInventory::create($request->all());
        // }

        $inventoryRecord = WaoInventoryRecord::first();

        if (!$inventoryRecord) {
            WaoInventoryRecord::create([
                'total_inventory' => $request->inventory,
                // 'sale_inventory' => $request->inventory,
            ]);
        } else {
            $inventoryRecord->increment('total_inventory', $request->inventory);
            // $inventoryRecord->increment('sale_inventory', $request->inventory);
        }

        return $this->success('Inventory Added Successfully', 'Inventory Added Successfully', 2);
    }

    public function minusInventory(Request $request)
    {
        $inventoryRecord = WaoInventoryRecord::first();
        // return inventory should less than total
        if ($request->minus_inventory <= $inventoryRecord->total_inventory && ($request->minus_inventory + $inventoryRecord->minus_inventory) <= $inventoryRecord->total_inventory) {
            if ($request->minus_inventory <= $inventoryRecord->sale_inventory && ($request->minus_inventory + $inventoryRecord->minus_inventory) <= $inventoryRecord->sale_inventory) {
                $inventoryRecord->increment('minus_inventory', $request->minus_inventory);
                return $this->success([], 'Minus Inventory Added Successfully', 2);
            }
            return $this->success([], 'Minus Inventory should Less than Sale/Picked Inventory', 0);
        }
        return $this->success([], 'Minus Inventory should Less than Total Inventory', 0);
    }


    public function destroy(Request $request)
    {
        // $inventory = WaoInventory::find($request->RecordId);
        $inventoryRecord = WaoInventoryRecord::first();

        // if ($inventoryRecord) {
        //     $inventoryRecord->decrement('total_inventory', $inventory->inventory);

        //     // if ($inventoryRecord->sale_inventory > 0) {
        //     //     $inventoryRecord->decrement('sale_inventory', $inventory->inventory);
        //     // }
        // }
        $inventoryRecord->delete();
        return $this->success('Inventory Deleted Successfully', 'Inventory Deleted Successfully', 2);
    }

    // Seller+ managers
    public function sellerList(Request $request)
    {
        $role = $request->role;
        $records  = Admin::latest()
            ->where(function ($q) use ($request) {
                $q->WhereIf('name', 'Like', "%{$request->search_input}%")
                    ->OrWhereIf('email', 'Like', "%{$request->search_input}%");
            })
            ->when($role, function ($query) use ($role) {
                if ($role === 'partners') {
                    return $query->where('is_partner', 1);
                } else {
                    return $query->WhereIf('role', 'Like', "%{$role}%")->where('is_partner', null);
                }
            })->where('controlled_by_admin',auth()->user()->id);
        $total_records = $records->count();
        $sellers = $records->paginate(20);
        $codes = TrackPlatformSetting::all();
        return view('admin.wao_inventory.inventorySeller', compact('sellers','codes','total_records'));
    }

    public function sellerEdit($id)
    {
        $seller = Admin::findorfail($id);
        $permissions = Permission::all();
        $codes = TrackPlatformSetting::all();
        return view('admin.wao_inventory.inventorySellerEdit', compact('seller','codes','permissions'));
    }

    public function sellerStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:admins',
            'password' => 'required|min:8',
            'role' => 'required',
        ]);

        if (!$validator->passes()) {
            return $this->success($validator->errors()->first(), $validator->errors()->first(), 0);
        }

        try {
            DB::beginTransaction();
            $admin = Admin::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'byc_password' => Crypt::encryptString($request->password),
                'postEx_apiToken' => $request->postEx_apiToken,
                'trax_api_key' => $request->trax_api_key,
                'trax_pickup_address_id' => $request->trax_pickup_address_id,
                'postEx_apiToken' => $request->postEx_apiToken,
                'postEx_pickupAddressCode' => $request->postEx_pickupAddressCode,
                'product_upload_status' => $request->product_upload_status,
                'mnp_username' => $request->mnp_username,
                'mnp_password' => $request->mnp_password,
                'locationID' => $request->locationID,
                'trax_allow' => $request->trax_allow === 'on' ? 1 : null,
                'mnp_alllow' => $request->mnp_alllow === 'on' ? 1 : null,
                'postEx_allow' => $request->postEx_allow  === 'on' ? 1 : null,
                'role' => intval($request->role) === 2 ? 3  : intval($request->role),
                'is_partner' => intval($request->role) === 2 ? 1 : null,
                // if current user not super admin then partner login means
                'controlled_by_admin' => auth()->user()->role != 1 ? auth()->user()->id : 1,
            ]);
            // for resellers/partners current active products add 
            if($admin->role === 3)
            {
                $products = Product::select(['id','price','profit','specific_reseller_profit','reseller_price','soldstatus'])->filterStatus(1)->Wherehas('itemcategory')->get();
                foreach ($products as $product) {
                    // check partner or reseller for reseller price or partner netprofit
                    ResellerSetting::updateorCreate(
                        ['prod_id' => $product->id,'admin_id' => $admin->id],
                        [
                        'prod_id' => $product->id,
                        // for reseller set specific reseller price  and for partnet set specific profit
                        'price' => $admin->is_partner === 1 ? $product->price : ($product->reseller_price ?? $product->price),
                        'profit' => $admin->is_partner === 1 ? ($product->specific_reseller_profit ?? $product->profit) : $product->profit,
                        'is_specific_profit' => 1,
                        'admin_id' => $admin->id,
                        'product_upload_status' => $admin->product_upload_status,
                        'is_specific_profit' => $admin->is_partner === 1 ? ($product->specific_reseller_profit ?? 1) : null,
                    ]);
                }
            }
            DB::commit();
            return $this->success('Inventory-Seller Record Saved Successfully', 'Inventory-Seller Record Saved Successfully', 2);
        } catch (Exception $e) {
            DB::rollback();
            return $this->success($e->getMessage(), $e->getMessage(), 1);
        }
    }

    public function sellerUpdate(Request $request, $sellerId)
    {
        $request->validate([
            'name' => 'required',
            'email' => [
                'required',
                'email',
                Rule::unique('admins')->ignore($sellerId),
            ],
            'website' => 'nullable|unique:admins,website,' . $sellerId,
            'password' => 'nullable|min:8',
            'balance' => 'nullable|integer',
            'deductBalance' => 'nullable|integer',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);


        try {
            DB::beginTransaction();

            $seller = Admin::findOrFail($sellerId);
            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password ? Hash::make($request->password) : $seller->password,
                'byc_password' => $request->password ? Crypt::encryptString($request->password) : $seller->byc_password,
                'postEx_apiToken' => $request->postEx_apiToken,
                'trax_api_key' => $request->trax_api_key,
                'trax_pickup_address_id' => $request->trax_pickup_address_id,
                'postEx_apiToken' => $request->postEx_apiToken,
                'postEx_pickupAddressCode' => $request->postEx_pickupAddressCode,
                'postEx_apiToken_nowshera' => $request->postEx_apiToken_nowshera,
                'postEx_pickupAddressCode_nowshera' => $request->postEx_pickupAddressCode_nowshera,
                'mnp_username' => $request->mnp_username,
                'product_upload_status' => $request->product_upload_status,
                'mnp_password' => $request->mnp_password,
                'locationID' => $request->locationID,
                'trax_allow' => $request->trax_allow === 'on' ? 1 : null,
                'mnp_alllow' => $request->mnp_alllow === 'on' ? 1 : null,
                'postEx_allow' => $request->postEx_allow  === 'on' ? 1 : null,
                'mute_video' => $request->mute_video  === 'on' ? 1 : 2,
                'status' => $request->status  === 'on' ? 1 : 2,
                'balance' => $request->balance ? ($request->balance + $seller->balance) : $seller->balance,
                'restrictBalance' => $request->restrictBalance,
                'isRestrictBalance' => $request->isRestrictBalance  === 'on' ? 1 : Null,
                // colors
                'color_1' => $request->color_1 ? $request->color_1 : $seller->color_1,
                'color_2' => $request->color_2 ? $request->color_2 : $seller->color_2,
                'color_3' => $request->color_3 ? $request->color_3 : $seller->color_3,
                'color_4' => $request->color_4 ? $request->color_4 : $seller->color_4,
                'color_5' => $request->color_5 ? $request->color_5 : $seller->color_5,
                'website' => $request->website ? $request->website : $seller->website,
                'controlled_by_admin' => auth()->user()->role != 1 ? auth()->user()->id : 1,
            ];
            $seller->update($data);

            if ($request->deductBalance) {
                $seller->balance -= $request->deductBalance;
            }

            // for role update
            if ($request->role) {
                if ($request->role === 'partners') {
                    $seller->role = 3;
                    $seller->is_partner = 1;
                } else {
                    $seller->role = $request->role;
                    $seller->is_partner = null;
                }
            }

            // for logo website
            if ($request->hasFile('logo')) {
                Helper::delete_previous_image($seller->logo);
                $seller->logo = Helper::upload_image($request->file('logo'), 'image/seller');
            }

            // for vhost
            if ($seller->website) {
                $dummyVhost = Admin::where('id', 1)->first()->dummy_vhost;
                $newText = str_replace('importedcheez.com', $seller->website, $dummyVhost);
                $seller->vhost = $newText;
            }

            // Sync permissions
            $seller->syncPermissions($request->permissions);
            // if amount update then history create
            if ($request->balance) {
                ResellerAmountHistory::create([
                    'admin_id' => $sellerId,
                    'balance' => $request->balance,
                    'note' => $request->add_balance_note,
                    'status' => 'add',
                ]);
            }

            if ($request->deductBalance) {
                ResellerAmountHistory::create([
                    'admin_id' => $sellerId,
                    'balance' => $request->deductBalance,
                    'note' => $request->deduct_balance_note,
                    'status' => 'deduct',
                ]);
            }

            $seller->save();

            DB::commit();
            return redirect()->route('inventory.seller.index')->with('message', 'Inventory-Seller Record Updated Successfully');
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }


    public function destroySeller(Request $request)
    {
        $seller = Admin::find($request->RecordId);
        $seller->delete();
        return $this->success('Inventory-Seller Record Deleted Successfully', 'Inventory-Seller Record Deleted Successfully', 2);
    }
}
