<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Admin, Message, Product, TrackPlatformSetting, LockedkFolderPassword, User, MessageNotification, Order, OrderHistory};
use Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Exception;

class SettingController extends Controller
{
    //view Messages
    public function viewMessage()
    {
        $messages = Message::latest()->whereNull(['user_id', 'order_id'])
            ->select(['id', 'message', 'days', 'views', 'created_at'])->get();
        $locked_pass = LockedkFolderPassword::first();
        $product_fake_range = LockedkFolderPassword::where('text_password', 'update_product_fake_sold_range')->first();
        return view('admin.Charges.viewMessage', compact('messages', 'locked_pass', 'product_fake_range'));
    }

    public function update_product_fake_sold_range(Request $request)
    {
        $request->validate([
            'stop_increasing_after_qty_start' => 'required|integer|min:0|max:500',
            'stop_increasing_after_qty_end' => 'required|integer|min:0|max:500',
            'start_from_items_start' => 'required|integer|min:0|max:500',
            'start_from_items_end' => 'required|integer|min:0|max:500',
        ]);


        // Assuming you have a settings model or any other method to store these values
        $product_fake_range = LockedkFolderPassword::where('text_password', 'update_product_fake_sold_range')->first();
        $product_fake_range->stop_increasing_after_qty_start = $request->input('stop_increasing_after_qty_start');
        $product_fake_range->stop_increasing_after_qty_end = $request->input('stop_increasing_after_qty_end');
        $product_fake_range->start_from_items_start = $request->input('start_from_items_start');
        $product_fake_range->start_from_items_end = $request->input('start_from_items_end');
        $product_fake_range->save();
        return redirect()->back()->with('message', 'Product fake sold range updated successfully.');
    }

    public function restartProductArticleRange(Request $request)
    {
        $validatedData = $request->validate([
            'artcle_start' => 'required',
            'artcle_end' => 'required',
            'text_artcle' => 'required',
        ]);

        $startRange = $validatedData['artcle_start'];
        $endRange = $validatedData['artcle_end'];
        $text = $validatedData['text_artcle'];

        // Retrieve products within the range
        $products = Product::whereRaw('CAST(SUBSTRING_INDEX(article, "-", 1) AS UNSIGNED) BETWEEN ? AND ?', [$startRange, $endRange])->get(['id', 'article']);
        if(count($products))
        {
            // Update each product's article column
            foreach ($products as $product) {
                $newArticle = $text . '-' . $product->article;
                $product->article = $newArticle;
                $product->save();
            }
            return redirect()->back()->with('message', 'Products Article Range updated successfully!');
        }

    }

    // update lock folder message
    public function update_lockfolder_password(Request $req)
    {
        if ($req->password) {
            $pass = LockedkFolderPassword::find(1);
            $pass->text_password = $req->password;
            $pass->password = \Hash::make($req->password);
            $pass->save();
        }
        return redirect()->route('viewMessage')->with('message', 'Message Updated Successfull');
    }

    // meesage send to all users
    public function message_to_all(Request $req)
    {
        $req->validate([
            'message' => 'required',
        ]);
        $message = Message::create([
            'message' => $req->message,
            'days' => $req->days,
        ]);
        if ($message) {
            $all_messages = [];
            $user_ids = User::whereNotNull('remember_token')->get()->pluck('id');
            foreach ($user_ids as $id) {
                $all_messages[] = [
                    'user_id'  => $id,
                    'message_id' => $message->id,
                ];
            }
            MessageNotification::insert($all_messages);
            return redirect()->back()->with('message', 'Message Send To all Users Successfully!');
        }
    }

    //updatemessage_to_all
    public function updatemessage_to_all(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'message' => 'required',
            'days' => 'nullable|integer',
        ]);
        // validation error
        if (!$validator->passes()) {
            return $this->success($validator->errors()->first(), 'Validation Error', 0);
        }

        Message::where('id', $req->message_id)->update([
            'message' => $req->message,
            'days' => $req->days,
        ]);
        return $this->success('Message Updated Successfully', 'Message Updated Successfully', 2);
    }

    //  del_message_to_all
    public function del_message_to_all(Request $req)
    {
        $message = Message::find($req->message_id);
        $all_notifications = MessageNotification::where('message_id', $message->id)->get();
        $all_notifications->each->delete();
        $message->delete();
        return $this->success('Message Deleted Successfullly', 'Message Deleted Successfullly');
    }

    // clear cache
    public function clear_cache()
    {
        \Artisan::call('route:clear');
        \Artisan::call('config:clear');
        \Artisan::call('view:clear');
        \Artisan::call('cache:clear');
        return redirect()->back()->with('message', 'Cache Clear Successfully!');
    }

    public function updateResetOrdersDate()
    {
        $currentDate = Carbon::now();
        $resetOrdersDate = LockedkFolderPassword::where('text_password', 'reset_type')->first();

        if ($resetOrdersDate) {
            $resetOrdersDate->update(['reset_orders_date' => $currentDate]);
        } else {
            LockedkFolderPassword::create(['reset_orders_date' => $currentDate]);
        }

        return redirect()->back()->with('message', 'Reset Date Updated/Inserted successfully!');
    }
    public function changeStatusOfUnbookedOrders(Request $request)
    {
        $startDateTime = Carbon::createFromFormat('Y-m-d\TH:i', $request->input('startDateTime'));
        $endDateTime = Carbon::createFromFormat('Y-m-d\TH:i', $request->input('endDateTime'));

        $message = $request->input('message');

        $orders = Order::whereBetween('created_at', [
            $startDateTime->format('Y-m-d H:i:s'),
            $endDateTime->format('Y-m-d H:i:s'),
        ])
            ->where('status', 'Team Review your Order')
            ->get(['id', 'status', 'is_cancel', 'cancel_note', 'updated_at']);
        if (count($orders) > 0) {


            try {
                DB::beginTransaction();

                foreach ($orders as $order) {

                    $order->update([
                        'status' => 'CANCIL',
                        'cancel_note' => $message ? $message : 'Cancel Order',
                    ]);

                    OrderHistory::updateorCreate(
                        [
                            'order_id' => $order->id,
                            'history' => $message
                        ],
                        [
                            'order_id' => $order->id,
                            'history' => $message ? $message : 'Cancel Order',
                            'time' => now(),
                        ]
                    );
                }

                DB::commit();
                return redirect()->back()->with('message', 'Orders Status Updated Successfully!');
            } catch (Exception $e) {
                DB::rollback();
                return redirect()->back()->with('error', $e->getMessage());
            }
        }
        return redirect()->back()->with('warning', 'No Records Available');
    }

    public function changeProductSaleReason(Request $request)
    {
        $locked_pass = LockedkFolderPassword::first();
        $locked_pass->product_sale_reason = $request->product_sale_reason;
        $locked_pass->save();
        return redirect()->back()->with('message', 'Updated Successfully!');
    }

    public function trackPlatformSetting()
    {
        $perPage = 10;
        $settings  = TrackPlatformSetting::latest()->paginate($perPage);
        return view('admin.Charges.trackPlatformSetting', compact('settings'));
    }

    public function storePickupAddressCode(Request $request)
    {
        $request->validate([
            'postEx_pickupAddressCode' => 'required|unique:track_platform_settings,postEx_pickupAddressCode',
        ]);

        TrackPlatformSetting::create($request->all());
        return redirect()->back()->with('message', 'Address Code saved successfully.');
    }

    public function destroyPickupAddressCode($id)
    {
        $setting = TrackPlatformSetting::findOrFail($id);
        // Add your custom logic to check if the code exists in the admin table
        if ($this->codeExistsInAdmin($setting->postEx_pickupAddressCode)) {
            return redirect()->back()
                ->with('error', 'Cannot delete this code as it exists in the admin table');
        }
        $setting->delete();
        return redirect()->back()->with('message', 'deleted successfully');
    }

    private function codeExistsInAdmin($code)
    {
        return Admin::where('postEx_pickupAddressCode', $code)->exists();
    }
}
